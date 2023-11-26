<?php

namespace App\Listeners;

use App\Events\InterestCalculationEvent;
use App\CustomerInterest;
use App\DebtInterest;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InterestCalculationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  InterestCalculationEvent  $event
     * @return void
     */
    public function handle(InterestCalculationEvent $event)
    {
        $current_date = Carbon::now();
        $end_date_of_month = $current_date->daysInMonth;

        // Get all the customer's debts with associated debt interests and loan settings
        $debt_interests = DebtInterest::join("debts", "debt_interests.debt_id", "=", "debts.id")
            ->join("loan_settings", "debt_interests.loan_setting_id", "=", "loan_settings.id")
            ->select([
                "debt_interests.end_date",
                "debt_interests.start_date",
                "debts.amount",
                "debts.updated_at",
                "debts.due_date",
                "debts.debt_status_id",
                "loan_settings.interest_rate",
                "debts.customer_id",
                "debts.id as debt_id",
            ])
            ->get();
        
        foreach ($debt_interests as $debt_interest) {
            $updated_days_payment = Carbon::parse($debt_interest->updated_at);
            $payment_days = $updated_days_payment->diffInDays($current_date);
            if ($payment_days >= $end_date_of_month) {

                // Calculate the number of days with no payment on this month
                $start_loan_date = Carbon::parse($debt_interest->start_date);
                // Proceed to calculation
                $loan_amount = $debt_interest->amount;
                $interest_rate = $debt_interest->interest_rate;
                $days_overdue = $current_date->diffInDays($debt_interest->due_date);

                $interest = $loan_amount * $interest_rate * $days_overdue / 365;

                // Save the interest to the database
                CustomerInterest::create([
                    "customer_id" => $debt_interest->customer_id,
                    "debt_id" => $debt_interest->debt_id,
                    "interest_rate" => $debt_interest->interest_rate,
                    "calculation_date" => $current_date->format("Y-m-d"),
                    "calculated_interest_amount" => $interest,
                    "debt_status_id" => 2,
                ]);
            }
        }
    }
}
