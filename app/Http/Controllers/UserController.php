<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerInterest;
use App\Debt;
use App\DebtInterest;
use App\LoanSetting;
use App\Merchant;
use App\Product;
use App\Store;
use App\Transaction;
use \App\User;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $user_customer = Customer::where("user_id", auth()->user()->id)->first();
        $user_debts = Debt::join("products", "debts.product_id", "=", "products.id")
            ->where("debts.customer_id", $user_customer->id)
            ->get();
        $transactions = Transaction::where("transactions.customer_id", $user_customer->id)->get();
        $previous_credit_usage = Debt::join("customers", "debts.customer_id", "=", "customers.id")->where("customers.user_id", \Auth::user()->id)->get()->last();
        $loan_amount_data = Debt::join("products", "debts.product_id", "=", "products.id")
            ->where("debts.customer_id", $user_customer->id)
            ->orderBy("debts.id", "ASC")
            ->get(["amount"]);

        $customer_interests = CustomerInterest::where("customer_id", $user_customer->id)
            ->where("debt_status_id", 2);
            
        return view('users.users-dashboard', compact("user_customer", "user_debts", "transactions", "previous_credit_usage", "loan_amount_data", "customer_interests"));
    }

    public function shop()
    {
        $user_customer = Customer::where("user_id", auth()->user()->id)->first();
        $total_debt = Debt::where("debts.customer_id", $user_customer->id)
            ->sum("amount");
        $total_payments = Transaction::where("transactions.customer_id", $user_customer->id)->sum("amount");
        $user_credit_left = $user_customer->credit_limit - ($total_debt - $total_payments);
        $products = Product::join("stores", "products.store_id", "=", "stores.id")
            ->where("stores.merchant_id", $user_customer->merchant_id)
            ->where("products.price", "<=", $user_credit_left)
            ->get(["products.id as product_id", "products.store_id", "products.price", "products.image"]);

        return view('users.user-shop', compact('user_customer', "products"));
    }

    public function userDetails()
    {

        // return view('errors.404');

        $user = Auth()->user();

        $user_detail = [];

        if ($user->is_customer == 1) {
            $customer = Customer::where("customers.user_id", $user->id)
                ->join("users", "customers.user_id", "=", "users.id")
                ->select("users.email as username", "users.secret", "users.profile_picture", "customers.name", "customers.address", "customers.contact_number", "customers.email", "users.id as user_id")
                ->first();
            
            $user_detail = $customer;
        } else if ($user->is_merchant == 1) {
            $merchant = Merchant::where("merchants.user_id", $user->id)
                ->join("users", "merchants.user_id", "=", "users.id")
                ->select("users.email as username", "users.secret", "users.profile_picture", "merchants.name", "merchants.address", "merchants.contact_number", "merchants.email", "users.id as user_id")
                ->first();
            
            $user_detail = $merchant;
        }

        return view('users.user-details', compact("user_detail"));
    }

    public function loanPayment()
    {
        $paid_amount = \Request::get("amount") . "00";
        $converted_paid_amount = (int) $paid_amount;
        $customer = Customer::where("user_id", auth()->user()->id)->first();
        $secret_key = config('app.paymongo_secret_key');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paymongo.com/v1/sources",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 500,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'data' => [
                    'attributes' => [
                        'amount' => $converted_paid_amount,
                        'redirect' => [
                            'success' => route("users.loan.payment.success"),
                            'failed' => route("users.loan.payment.fail")
                        ],
                        'type' => 'gcash',
                        'currency' => 'PHP'
                    ]
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Basic c2tfdGVzdF9BS2pacVhQUFhyYWdaalFzdFdKcFpuZkI6",
                "content-type: application/json"
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            // true if deployed
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
            if (isset($response["errors"])) {
                return back()->with(["status" => "error", "error_message" => $response["errors"][0]["detail"]]);
            } else {
                $checkout_url = $response['data']['attributes']['redirect']['checkout_url'];
                return redirect($checkout_url)->with("response", $response);
            }
        }
    }

    public function loanPaymentSuccess()
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
            CURLOPT_SSL_VERIFYPEER => false,
            // true if deployed
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

    public function loanPaymentCreateWebhook()
    {

        $response = session("chargable_status");
        $status = $response["data"]["attributes"]["status"];
        $source = $response["data"]["type"];
        $events = $source . "." . $status;
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.paymongo.com/v1/webhooks",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                'data' => [
                    'attributes' => [
                        'url' => route("users.loan.payment.successWebhook"),
                        'events' => [
                            $events,
                        ]
                    ]
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Basic c2tfdGVzdF9BS2pacVhQUFhyYWdaalFzdFdKcFpuZkI6",
                "content-type: application/json"
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            // true if deployed
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 0,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }

    public function loanPaymentSuccessWebhook()
    {

        // header('Content-Type: application/json');
        // $request = file_get_contents('php://input');
        // $payload = json_decode($request, true);
        // $type = $payload['data']['attributes']['type'];

        // //If event type is source.chargeable, call the createPayment API
        // if ($type == 'source.chargeable') {
        // $amount = $payload['data']['attributes']['data']['attributes']['amount'];
        // $id = $payload['data']['attributes']['data']['id'];
        // $description = "GCash Payment Description";
        // $curl = curl_init();
        // $fields = array("data" => array ("attributes" => array ("amount" => $amount, "source" => array ("id" => $id, "type" => "source"), "currency" => "PHP", "description" => $description)));
        // $jsonFields = json_encode($fields);

        // curl_setopt_array($curl, [
        //     CURLOPT_URL => "https://api.paymongo.com/v1/payments",
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => "",
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 30,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => "POST",
        //     CURLOPT_POSTFIELDS => $jsonFields,
        //     CURLOPT_HTTPHEADER => [
        //     "Accept: application/json",
        //     //Input your encoded API keys below for authorization
        //     "Authorization: Basic c2tfdGVzdF9BS2pacVhQUFhyYWdaalFzdFdKcFpuZkI6" ,
        //     "Content-Type: application/json"
        //     ],
        //     CURLOPT_SSL_VERIFYPEER => false, // true if deployed
        //     CURLOPT_FOLLOWLOCATION => false,
        //     CURLOPT_CONNECTTIMEOUT => 0, 
        // ]);

        // $response = curl_exec($curl);
        // //Log the response
        // $fp = file_put_contents( 'test.log', $response );
        // $err = curl_error($curl);

        // curl_close($curl);

        // if ($err) {
        //     echo "cURL Error #:" . $err;
        //     //Log the response
        //     $fp = file_put_contents( 'test.log', $err );
        // } else {
        //     echo $response;
        // }
        // }
    }

    public function loanDetails()
    {   
        $products = Product::where("store_id", \Request::get("store_id"))
            ->where("products.id", \Request::get('product_id'))
            ->first();
        $store = Store::where("id", $products->store_id)->first();
        $loan_settings = LoanSetting::where("merchant_id", $store->merchant_id)->orderBy("months_to_pay", "ASC")->get();

        return view("users.loan-details", compact("products", "loan_settings"));
    }

    public function loanProduct()
    {
        $loan_settings_id = LoanSetting::where("id", \Request::get("loan_settings_id"))->first();
        $product_id = \Request::get("product_id");
        $months_to_pay = $loan_settings_id->months_to_pay;
        
        $current_date = Carbon::now();
        $due_date = Carbon::now()->addMonths($months_to_pay);
        
        $user_customer = Customer::where("customers.user_id", auth()->user()->id)->first();
        $product = Product::where("products.id", $product_id)->first();

        // save to debts table
        $create_debt = Debt::create([
            "customer_id" => $user_customer->id,
            "product_id" => $product->id,
            "amount" => $product->price,
            "due_date" => $due_date->format('Y-m-d'),
            "amount_paid" => 0,
            "debt_status_id" => 2, // 2 means unpaid
            "current_amount" => $product->price, 
            "product_price_change_date" => $product->updated_at,
        ]);

        // save to debt_interests table
        $create_debt_interests = DebtInterest::create([
            "debt_id" => $create_debt->id,
            "loan_setting_id" => $loan_settings_id->id,
            "start_date" => $current_date,
            "end_date" => $due_date,
        ]);

        return response()->json(true);
    }

    public function changePassword()
    {
        $user = User::where("users.id", \Auth::user()->id)
            ->get()->first();

        return view("settings.change_password", compact("user"));
    }

    public function updatePassword()
    {
        $confirmed_password = \Request::get("confirm_password");
        $current_password = \Auth::user()->password;

        if (\Hash::check($confirmed_password, $current_password)) {
            return response()->json(["status" => "error"]);
        } else {
            $update_merchant_password = User::find(\Auth::user()->id);
            $update_merchant_password->password = \Hash::make($confirmed_password);
            $update_merchant_password->secret = $confirmed_password;
            $update_merchant_password->is_password_change = 1;
            $update_merchant_password->save();

            return response()->json(true);
        }

    }

    public function recentTransactionData()
    {   
        $user_customer = Customer::where("user_id", \Auth::user()->id)->first();

        $user_debts = Debt::join("products", "debts.product_id", "=", "products.id")
            ->where("debts.customer_id", $user_customer->id)
            ->select([
                "products.name",
                "products.description",
                "debts.created_at",
                "debts.due_date",
                "debts.amount",
                "debts.id as debt_id",
                "debts.is_claimed",
            ])
            ->get();

        $data_table = collect($user_debts)

            ->map(function ($user_debt) {

                $name = '<h6 class="fw-semibold mb-1">'.$user_debt["name"].'</h6>';
                $description = '<p class="mb-0 fw-normal">'.$user_debt["description"].'</p>';
                $date_loaned = '<div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success rounded-3 fw-semibold">'. Carbon::parse($user_debt["created_at"])->format("m/d/Y").'</span>
                            </div>';
                $due_date = '<div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger rounded-3 fw-semibold">'. Carbon::parse($user_debt["due_date"])->format("m/d/Y") .'</span>
                            </div>';
                $amount = '<h6 class="fw-semibold mb-0 fs-4"><span class="ti ti-currency-peso"></span>'. $user_debt["amount"] .'</h6>';

                $action = '<div class="d-flex align-items-center gap-2">
                            <a type="button" data-debts_id="' . $user_debt["debt_id"] . '" class="btn btn-primary btn-sm viewDebtsStatusButton"><span class="ti ti-eye"></span> View Status</a>
                        </div>';

                $disabled = $user_debt["is_claimed"] == 1 ? "disabled" : "";

                $is_claimed = "<button data-debt_id='" . $user_debt['debt_id'] . "' class='btn btn-primary btn-sm isClaimedButton' type='button' ". $disabled ."><span class='ti ti-receipt'></span> Received?</button>";
                
                return [
                    "product" => $name,
                    "name" => $description,
                    "date_loaned" => $date_loaned,
                    "due_date" => $due_date,
                    "amount" => $amount,
                    "is_claimed" => $is_claimed,
                    "action" => $action,
                ];
            });
        
        return response()->json($data_table);
    }

    public function isClaimed()
    {
        $debt = Debt::find(\Request::get("debt_id"));

        // update is_claimed status
        $debt->is_claimed = 1;
        $debt->update();

        return response()->json(true);
    }

    public function recentPaymentTransactionData()
    {
        $customer = Customer::where("user_id", \Auth::user()->id)->first();
        $transactions = Transaction::where("transactions.customer_id", $customer->id)->get();

        $data_table = collect($transactions)
            ->map(function ($transaction) {
                $transaction_id = '<h6 class="fw-semibold mb-1">' . $transaction["transaction_id"] . '</h6>';
                $amount = '<h6 class="fw-semibold mb-0 fs-4"><span class="ti ti-currency-peso"></span>' . $transaction["amount"] . '</h6>';
                $status = '<div class="d-flex align-items-center gap-2">
                                <span class="badge bg-' . ($transaction["status"] == "success" ? "success" : "danger") . ' rounded-3 fw-semibold">' . $transaction["status"] . '</span>
                            </div>';
                $payment_method = '<h6 class="fw-semibold mb-0 fs-4">' . $transaction["payment_method"] . '</h6>';
                $transaction_date = '<div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-3 fw-semibold">' . Carbon::parse($transaction["transaction_date"])->format("m/d/Y") . '</span>
                                    </div>';

                return [
                    "transaction_id" => $transaction_id,
                    "amount" => $amount,
                    "status" => $status,
                    "payment_method" => $payment_method,
                    "transaction_date" => $transaction_date,
                ];
            });

        return response()->json($data_table);
    }

    public function updateProfile() {
        $user_id = \Request::get("user_id");
        $profile_image = \Request::file("profile_image");
        $name = \Request::get("name");
        $address = \Request::get("address");
        $contact_number = \Request::get("contact_number");
        $email = \Request::get("email");
        $merchant = Merchant::where("user_id", $user_id)->first();
        $customer = Customer::where("user_id", $user_id)->first();
        $users = User::find($user_id);

        $user = [];
        if (isset($merchant)) {
            $user = $merchant;
            $table = "merchants";
        } else {
            $user = $customer;
            $table = "customers";
        }

        $validator = \Validator::make(\Request::all(), [
            "name" => "required",
            "address" => "required",
            "email" => "bail|email|required",
            "contact_number" => [
                "bail",
                "required",
                "numeric",
                Rule::unique($table, "contact_number")->ignore($user->id),
            ],
            "profile_image" => "image|mimes:jpeg,png,jpg,gif|max:2048", 
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => $validator->messages()]);
        } else {

            if ($profile_image) {
                $original_filename =  $profile_image->getClientOriginalName();
                $image_path = $profile_image->move(public_path('images/profile_images'), $original_filename);

                $users->profile_picture = "images/profile_images/" . $original_filename;
                $users->update();
            }

            $user->name = $name;
            $user->address = $address;
            $user->contact_number = $contact_number;
            $user->email = $email;
            $user->update();

            return response()->json(["success" => true]);
        }
        
    }

    public function updateNewPassword()
    {
        $old_password = \Request::get("old_password");
        $new_password = \Request::get("new_password");
        $confirm_password = \Request::get("confirm_password");

        $user = User::find(auth()->user()->id);

        // check old password
        if (Hash::check($old_password, $user->password)) {
            // update user password

            $user->password = Hash::make($confirm_password);
            $user->secret = $confirm_password;
            $user->save();

            return response()->json(true);

        } else {
            return response()->json(false);
        }
    }
}