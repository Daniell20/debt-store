<?php

namespace App\Console\Commands;

use App\CustomerInterest;
use App\DebtInterest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CalculateInterest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interest:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
                "debts.amount_paid"
            ])
            ->get();

        foreach ($debt_interests as $debt_interest) {
            $updated_days_payment = Carbon::parse($debt_interest->updated_at);
            $payment_days = $updated_days_payment->diffInDays($current_date);
            $interest_rate = $debt_interest->interest_rate / 100;
            $loan_amount = $debt_interest->amount;

            if ($debt_interest->amount_paid == 0 && $current_date > $debt_interest->start_date) {
                // First part: No payment since the day of the loan
                $loan_start_date = Carbon::parse($debt_interest->start_date);
                $total_months = $loan_start_date->diffInMonths($current_date);

                $total_interest = 0;

                for ($month = 1; $month <= $total_months; $month++) {
                    $interest_for_month = round($loan_amount * $interest_rate / 12, 2);

                    CustomerInterest::create([
                        "customer_id" => $debt_interest->customer_id,
                        "debt_id" => $debt_interest->debt_id,
                        "interest_rate" => $debt_interest->interest_rate,
                        "calculation_date" => $current_date->format("Y-m-d"),
                        "calculated_interest_amount" => $interest_for_month,
                        "debt_status_id" => 2,
                    ]);

                    $total_interest += $interest_for_month;
                }
            } elseif ($debt_interest->amount_paid != $debt_interest->amount && $debt_interest->updated_at < Carbon::now()) {
                // Second part: Customer initially pays but not fully paid, and it takes for another month, then the interest will apply
                // Initialize variables
                $loan_amount = $debt_interest->amount;
                $interest_rate = $debt_interest->interest_rate;

                // Calculate the number of days with no payment on this month
                $start_loan_date = Carbon::parse($debt_interest->start_date);

                // Proceed to calculation
                $days_overdue = $current_date->diffInDays($debt_interest->due_date);
                $interest = round($loan_amount * $interest_rate * $days_overdue / 365, 2);

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

        $this->info('Interest calculation has been triggered.');
    }
}
