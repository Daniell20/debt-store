<?php

namespace App\Http\Controllers;

use App\LoanSetting;
use App\Merchant;
use Illuminate\Http\Request;

class LoanSettingController extends Controller
{
    public function index()
    {
        return view('settings.loan_setup');
    }
    public function create()
    {
        $interest_rate = \Request::get("interest_rate");
        $months_to_pay = \Request::get("months_to_pay");
        $merchant = Merchant::where("user_id", \Auth::user()->id)->first();

        $create_loan_settings = LoanSetting::firstOrCreate([
            "merchant_id" => $merchant->id,
            "interest_rate" => $interest_rate,
            "months_to_pay" => $months_to_pay,
        ]);

        return response()->json($create_loan_settings);
    }

    public function show()
    {
        $merchant = Merchant::where("user_id", \Auth::user()->id)->first();
        $loan_settings_query = LoanSetting::where("merchant_id", $merchant->id)->get();

        $data_table = collect($loan_settings_query)
            ->map(function ($loan_settings) {

                $loan_settings_id = $loan_settings["id"];

                $action = '<div class="d-flex justify-content-start">
                            <button style="margin-right: 10px;" data-loan_settings_id="' . $loan_settings_id . '" class="editLoanSettingsButton btn btn-sm btn-primary"><span class="ti ti-pencil"></span> Edit</button>
                            <button style="margin-right: 10px;" data-loan_settings_id="' . $loan_settings_id . '" class="deleteLoanSettingsButton btn btn-sm btn-danger"><span class="ti ti-trash"></span> Delete</button>
                        </div>';

                return [
                    "interest_rate" => $loan_settings["interest_rate"] . "%",
                    "months_to_pay" => $loan_settings["months_to_pay"] . " Months",
                    "action" => $action,
                ];
            });

        return response()->json($data_table);
    }

    public function edit()
    {
        $action = 1;
        $loan_settings_id = \Request::get("loan_settings_id");

        $loan_settings = LoanSetting::find($loan_settings_id);

        return view("settings.edit_loan_setup", compact("loan_settings"));

    }

    public function update()
    {   
        $loan_settings_id = \Request::get("loan_settings_id");
        $interest_rate = \Request::get("interest_rate");
        $months_to_pay = \Request::get("months_to_pay");

        $loan_settings = LoanSetting::find($loan_settings_id);
        $loan_settings->interest_rate = $interest_rate;
        $loan_settings->months_to_pay = $months_to_pay;
        $loan_settings->update();

        return response()->json($loan_settings);
    }
    public function destroy()
    {
        $loan_settings_id = \Request::get("loan_settings_id");

        $loan_settings = LoanSetting::find($loan_settings_id);
        $loan_settings->delete();

        return response()->json(true);
    }
}
