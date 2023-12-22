<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Customer;
use App\Merchant;
use App\Product;
use App\Store;
use App\User;

use Hash;
use DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function index()
    {
        
        $user_array = [];
        $user = User::where("is_active", 1)->where("is_admin", "!=", 1)->get();
        $users = $user->count();
        $merchants = $user->where("is_merchant", 1)->count();
        $customers = $user->where("is_customer", 1)->count();

        $inactive_merchants = User::where("is_merchant", 1)
            ->where("is_active", 0)->count();
        
        $inactive_customers = User::where("is_customer", 1)
            ->where("is_active", 0)->count();

        $customers_today = Customer::where("created_at", ">=", Carbon::today())->get()->count();
        $merchants_today = Merchant::where("created_at", ">=", Carbon::today())->get()->count();

        // Number of users registered in the last week
        // $merchants_last_week = User::where("is_active", 1)->where("is_admin", "!=", 1)->where("is_merchant", 1)->whereDate('created_at', '>', Carbon::now()->startOfWeek()->subWeek())->count();
        // $customers_last_week = User::where("is_active", 1)->where("is_admin", "!=", 1)->where("is_customer", 1)->whereDate('created_at', '>', Carbon::now()->startOfWeek()->subWeek())->count();

        // Calculate the percentage
        if ($merchants != 0) {
            $merchants_percentage = ($merchants_today / $merchants) * 100;
        } else {
            $merchants_percentage = 0; // or some other appropriate value
        }
        
        if ($customers != 0) {
            $customers_percentage = ($customers_today / $customers) * 100;
        } else {
            $customers_percentage = 0; // or some other appropriate value
        }
        // $merchants_percentage = ($merchants_today / $merchants) * 100;
        // $customers_percentage = ($customers_today / $customers) * 100;
        // return $user;

        return view('admin.index', compact("users", "merchants", "customers", "customers_today", "merchants_today", "inactive_merchants", "inactive_customers", "merchants_percentage", "customers_percentage"));
    }

    public function createMerchant()
    {
        return view('admin.create-merchant');
    }

    public function storeMerchant()
    {

        
        \Validator::make(\Request::all(), [
            'merchant_name' => 'required|unique:merchants,name',
        ])->validate();

        $merchant_no = Merchant::count() + 1;
        $password = 'MERCH-' . $merchant_no;

        DB::beginTransaction();

        try {
            $user = User::create([
                'email' => 'MERCH-' . $merchant_no . '@debtstore.com',
                'password' => Hash::make($password),
                "secret" => $password,
                "is_password_change" => 0,
                'is_admin' => false,
                'is_customer' => false,
                'is_merchant' => true,
            ]);

            $store_merchant = Merchant::create([
                'user_id' => $user->id,
                'merchants_no' => 'MERCH-' . $merchant_no,
                'name' => \Request::get('merchant_name'),
            ]);

            DB::commit();

            return response()->json(['message' => 'Merchant created successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Merchant creation failed!', 'error' => $e->getMessage()], 500);
        }
    }

    public function storeMerchantData()
    {
        $merchants = Merchant::join('users', 'merchants.user_id', '=', 'users.id')
            ->where("users.is_merchant", 1)
            ->get([
                "users.is_active",
                "merchants.user_id",
                "merchants.name",
                "users.email",
                "users.secret",
                "merchants.id as merchant_id",
            ]);

        $data_table = collect($merchants)
            ->map(function ($merchants) {

                $is_active = $merchants->is_active == 1 ? "checked" : "";
                $button_disabled = $merchants->is_active == 0 ? "disabled" : "";

                $action = '
                    <div class="form-check form-switch d-flex align-items-center">
                        <input class="form-check-input" data-user_id="' . $merchants->user_id . '" data-is_active="' . $merchants->is_active . '" type="checkbox" role="switch" id="activeMerchant" ' . $is_active . '>
                        <button class="btn btn-primary btn-sm viewMerchant" data-merchant_id="' . $merchants->merchant_id . '" style="margin-left: 13px;" ' . $button_disabled . '><span class="ti ti-eye"></span> View</button>
                    </div>
                ';

                return [
                    "merchant_name" => $merchants->name,
                    "merchant_email" => $merchants->email,
                    "merchant_password" => $merchants->secret,
                    "action" => $action,
                ];

            });

        return response()->json($data_table);
    }

    public function storeMerchantUpdateStatus()
    {
        $merchant_status = \Request::get("merchant_status");
        $user_id = \Request::get("user_id");

        $update_merchant = User::find($user_id);
        $update_merchant->is_active = $merchant_status == 1 ? 0 : 1;
        $update_merchant->save();

        return response()->json(true);

    }

    public function merchantsInfo() {
        $merchant_id = \Request::get("merchant_id");

        $merchant = Merchant::join("users", "merchants.user_id", "=", "users.id")
            ->where("merchants.id", $merchant_id)
            ->select([
                "merchants.id as merchant_id",
                "users.email",
                "users.secret",
                "merchants.name",
            ])
            ->first();

        return view("admin.merchants_info", compact("merchant"));
    }

    public function merchantsUpdateInfo() {
        $merchant_id = \Request::get("merchant_id");
        $merchant_name = \Request::get("merchant_name");

        $validator = \Validator::make(\Request::all(), [
            "merchant_name" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => $validator->messages()]);
        } else {
            $merchant = Merchant::find($merchant_id);
            $merchant->name = $merchant_name;
            $merchant->update();

            return response()->json(["success" => true]);
        }
    }

    public function customers() {
        return view("admin.customers");
    }

    public function customersData() {

        $customers = Customer::join("users", "customers.user_id", "=", "users.id")
            ->get([
                "customers.name",
                "users.email",
                "users.secret",
                "users.is_active",
                "customers.id as customer_id"
            ]);

        $data_table = collect($customers)
            ->map(function ($customer) {

                $is_active = $customer->is_active == 1 ? "Active" : "Deactivated";
                $badge_color = $customer->is_active == 1 ? "bg-success" : "bg-danger";

                $status = '
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge ' . $badge_color . ' rounded-3 fw-semibold">' . $is_active . '</span>
                    </div>
                ';

                $action = '
                    <div>
                        <button class="btn btn-primary btn-sm customerData" data-customer_id="' . $customer->customer_id . '"><span class="ti ti-eye"></span> View</button>
                    </div>
                ';

                return [
                    "customer_name" => $customer->name,
                    "customer_username" => $customer->email,
                    "customer_password" => $customer->secret,
                    "status" => $status,
                    "action" => $action,
                ];
            });

        return response()->json($data_table);
    }

    public function customersInfo() {
        $customer_id = \Request::get("customer_id");

        $customer = Customer::join("users", "customers.user_id", "=", "users.id")
            ->where("customers.id", $customer_id)
            ->select([
                "customers.id as customer_id",
                "customers.email",
                "users.secret",
                "customers.name",
                "customers.contact_number",
                "customers.address",
                "users.is_active"
            ])
            ->first();

        return view("admin.customers_info", compact("customer"));
    }

    public function customersUpdate() {
        $customer_id = \Request::get("customer_id");
        $name = \Request::get("name");
        $contact_number = \Request::get("contact_number");
        $address = \Request::get("address");

        $validator = \Validator::make(\Request::all(), [
            "name" => "required",
            "contact_number" => [
                "bail",
                "required",
                "numeric",
                Rule::unique("customers")->ignore($customer_id),
            ],
            "address" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => $validator->messages()]);
        } else {
            $customer = Customer::find($customer_id);
            $customer->name = $name;
            $customer->contact_number = $contact_number;
            $customer->address = $address;
            $customer->update();

            return response()->json(["success" => true]);
        }
    }

    public function customerDeactivate() {
        $customer_id = \Request::get("customer_id");

        $customer = Customer::find($customer_id); 
        $user = User::find($customer->user_id);
        $user->is_active = $user->is_active == 1 ? 0 : 1;
        $user->update();

        return response()->json(true);
    }
}