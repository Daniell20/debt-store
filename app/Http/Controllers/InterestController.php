<?php

namespace App\Http\Controllers;

use App\CustomerInterest;
use App\Debt;
use App\DebtInterest;
use App\Events\InterestCalculationEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InterestController extends Controller
{

    public function viewLoanInterestStatus()
    {
        $debt_id = \Request::get("debt_id");
        
        return view("customer.interest_setup", compact("debt_id"));
    }
    
    public function interestData()
    {
        $debt_id = \Request::get("debt_id");

        // $customerInterest = CustomerInterest::find(1);

        // // Get the related debt status
        // $debtStatus = $customerInterest->debtStatus;

        // // Now, you can access properties of the related debt status
        // $statusName = $debtStatus->name;

        $customer_interests = CustomerInterest::with("debtStatus")
            ->where("customer_interests.debt_id", $debt_id)
            ->get();

        $data_table = collect($customer_interests)
            ->map(function ($customer_interest) {
                $debt_status = $customer_interest->debtStatus;
                if ($debt_status->id == 1) { // paid
                    $class_design = "badge bg-success rounded-3 fw-semibold";
                } elseif ($debt_status->id == 2) { // unpaid
                    $class_design = "badge bg-danger rounded-3 fw-semibold";
                } elseif ($debt_status->id == 3) { // Initial
                    $class_design = "badge bg-info rounded-3 fw-semibold";
                } elseif ($debt_status == 4) { // interest
                    $class_design = "badge bg-warning rounded-3 fw-semibold";
                }


                return [
                    "interest_rate" => $customer_interest->interest_rate . "%",
                    "calculation_date" => $customer_interest->calculation_date,
                    "calculation_amount" => "<span class='ti ti-currency-peso'></span>" . $customer_interest->calculated_interest_amount,
                    "status" => '<div class="d-flex align-items-center gap-2">
                                    <span class=" ' . $class_design . ' ">' . $debt_status->name . '</span>
                                </div>',
                ];
            });
        
        return response()->json($data_table);
    }

    public function runInterestCalculation()
    {
        event(new InterestCalculationEvent());

        return 'Interest calculation has been triggered.';
    }
}
