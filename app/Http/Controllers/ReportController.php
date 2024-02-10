<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Merchant;
use App\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $customer = Customer::where("customers.user_id", $user->id)->first();
        $merchants = Merchant::where("id", $customer->merchant_id)->get();

        return view("reports.index", compact("merchants"));
    }

    public function reportPost()
    {
        $merchant_id = \Request::get("merchant_id");
        $description = \Request::get("description");
        $customer = Customer::where("customers.user_id", Auth::user()->id)->first();


        // create report
        $report = Report::create([
            "customer_id" => $customer->id,
            "merchant_id" => $merchant_id,
            "description" => $description,
        ]);

        return redirect()->route("report.index")->with("success", "Report submitted successfully.");
    }

    public function reportsDataIndex()
    {
        return view("reports.data.index");
    }

    public function data()
    {
        $reports = Report::join("customers", "reports.customer_id", "=", "customers.id")
            ->join("merchants", "reports.merchant_id", "=", "merchants.id")
            ->orderBy("reports.created_at", "DESC")
            ->select("customers.name as customer_name", "merchants.name as merchant_name", "reports.description")
            ->get();

        $data_table = collect($reports)
            ->map(function ($report) {
                return [
                    "customer" => $report["customer_name"],
                    "merchant" => $report["merchant_name"],
                    "description" => $report["description"],
                ];
            });

        return response()->json($data_table);
    }
}
