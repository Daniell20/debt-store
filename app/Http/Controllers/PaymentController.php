<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerInterest;
use App\Debt;
use App\Transaction;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payments()
    {

        $request = session("chargable_status");

        $source_id = $request["data"]["id"];
        $amount = $request["data"]["attributes"]["amount"];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paymongo.com/v1/payments",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                "data" => [
                    "attributes" => [
                        "amount" => $amount,
                        "currency" => "PHP",
                        "source" => [
                            "id" => $source_id,
                            "type" => "source"
                        ]
                    ]
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Basic c2tfdGVzdF9BS2pacVhQUFhyYWdaalFzdFdKcFpuZkI6",
                "content-type: application/json"
            ],
            CURLOPT_SSL_VERIFYPEER => false, // true if deployed
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 0,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
            if (isset($response["data"]["attributes"]["status"]) == "paid") {

                $customer = Customer::where("user_id", \Auth::user()->id)->first();

                foreach ($response as $value) {
                    $date = \Carbon\Carbon::createFromTimestamp($value["attributes"]["created_at"]);
                    $payment_method = ucfirst($value["attributes"]["source"]["type"]);
                    $amount_in_cents = $value["attributes"]["amount"];
                    $amount_in_peso = $amount_in_cents / 100;

                    $create_transaction = Transaction::create([
                        "customer_id" => $customer->id,
                        "transaction_id" => $value["id"],
                        "amount" => $amount_in_peso,
                        "status" => 1,
                        "payment_method" => $payment_method,
                        "transaction_date" => $date,
                    ]);

                    // get all the customers debts
                    $customer_debts = Debt::where("debts.customer_id", $create_transaction->customer_id)
                        ->where("debts.debt_status_id", "!=", 1)
                        ->get();

                    // Get all the customer's interest
                    $customer_interests = CustomerInterest::where("debt_status_id", "!=", 1)
                        ->where("customer_id", $create_transaction->customer_id)->get();

                    $customer_debt_count = $customer_debts->count();
                    $amount_divided = $amount_in_peso / ($customer_debt_count + $customer_interests->count());

                    $customer_paid_amount = $amount_in_peso; // The amount the customer has paid

                    foreach ($customer_debts as $customer_debt) {
                        $debt_remaining = $customer_debt->amount - $customer_debt->amount_paid; // Calculate the remaining debt for this item

                        if ($customer_paid_amount >= $debt_remaining) { // If the paid amount covers the entire debt
                            $customer_debt->amount_paid += $debt_remaining; // Pay off the entire debt
                            $customer_paid_amount -= $debt_remaining; // Deduct the debt amount from the paid amount
                            $customer_debt->debt_status_id = 1;
                        } else { // If the paid amount doesn't cover the entire debt
                            $customer_debt->amount_paid += $customer_paid_amount; // Pay off as much as possible
                            $customer_debt->debt_status_id = 3;
                            $customer_paid_amount = 0; // All the paid amount has been used
                        }

                        $customer_debt->update(); // Update the debt item

                        if ($customer_paid_amount == 0) { // If there's no paid amount left, no need to continue
                            break;
                        }
                    }

                    // Distribute the payment among interests
                    foreach ($customer_interests as $interest) {
                        if ($amount_divided > 0) {
                            if ($interest->calculated_interest_amount >= $amount_divided) {
                                $interest->calculated_interest_amount -= $amount_divided;
                                $interest->update();
                                $amount_divided = 0;

                                // Check if the interest is fully paid and update the debt_status_id accordingly
                                if ($interest->calculated_interest_amount == 0) {
                                    $interest->debt_status_id = 1;
                                    $interest->update();
                                }
                            } else {
                                $amount_divided -= $interest->calculated_interest_amount;
                                $interest->calculated_interest_amount = 0;
                                $interest->debt_status_id = 1;
                                $interest->update();
                            }
                        }
                    }

                }

                if ($create_transaction) {
                    return redirect(route("users.dashboard"))->with("status", "success");
                }

            } else if (isset($response["errors"])) {
                return redirect(route("users.dashboard"))->with(["status" => "error", "error_message" => $response["errors"][0]["code"]]);
            }
        }
    }

    public function loanPaymentFail()
    {

        $response = session('response');
        $source = $response['data']['id'];
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paymongo.com/v1/sources/" . $source,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Basic c2tfdGVzdF9BS2pacVhQUFhyYWdaalFzdFdKcFpuZkI6"
            ],
            CURLOPT_SSL_VERIFYPEER => false, // true if deployed
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 0,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
            return redirect(route('payment.loan.payments'))->with(["chargable_status" => $response]);
        }
    }
}
