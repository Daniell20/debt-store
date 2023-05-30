<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::select(['id', 'created_at'])
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m'); // Group by year and month
            });

        $months = [];
        $customerCounts = [];

        foreach ($customers as $month => $customerData) {
            $months[] = $month;
            $customerCounts[] = count($customerData);
        }

        $percentageChange = 0;
        if (count($customerCounts) >= 2) {
            $lastMonthCount = end($customerCounts);
            $previousMonthCount = $customerCounts[count($customerCounts) - 2];
            $percentageChange = (($lastMonthCount - $previousMonthCount) / $previousMonthCount) * 100;
        }


        return view('merchant.dashboard', compact('months', 'customerCounts', 'percentageChange'));
    }
}