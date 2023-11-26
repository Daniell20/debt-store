<?php

namespace App\Http\Controllers;

use App\Debt;
use App\DebtInterest;
use App\LoanSetting;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Customer;
use App\Merchant;
use App\Product;
use App\Store;
use App\User;
use Illuminate\Validation\Rule;


class MerchantController extends Controller
{
    public function index()
    {
        return view('merchant.index');
    }
    public function dashboard()
    {
        $merchant = Merchant::where("user_id", \Auth::user()->id)->first();

        $customers = Customer::where("merchant_id", $merchant->id)
            ->select(['id', 'created_at'])
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

        $products = \DB::table('products')->join('stores', 'products.store_id', '=', 'stores.id')
            ->join('merchants', 'stores.merchant_id', '=', 'merchants.id')
            ->where('merchants.user_id', \Auth::user()->id)
            ->select(['products.name', 'products.price', 'products.description', 'products.image'])
            ->get();

        $merchant = Merchant::where("user_id", \Auth::user()->id)->first();
        $customers = Customer::where("merchant_id", $merchant->id)->pluck("id");
        $transactions = Transaction::whereIn("transactions.customer_id", $customers);
        $customer_payment_sum = $transactions->sum("transactions.amount");
        $earnings = $transactions->get(["transactions.amount"]);

        $top_debtors_arr = [];
        
        $top_debtors = Debt::join("customers", "debts.customer_id", "=", "customers.id")
            ->whereIn("debts.customer_id", $customers)
            ->get(["debts.amount", "customers.name"]);

        $total_debt_amount = 0;

        foreach ($top_debtors as $top_debtor) {
            $total_debt_amount += $top_debtor->amount;
            $top_debtors_arr[$top_debtor->name] = $total_debt_amount;
        }

        $daily_transactions = Transaction::select(
            \DB::raw('DATE(transaction_date) as date'),
            \DB::raw('SUM(amount) as total_amount')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();
    
        // Calculate the percentage change
        $percentage_changes = [];
        $previous_total = null;
    
        foreach ($daily_transactions as $daily_transaction) {
            $percentage_change = 0;
    
            if ($previous_total !== null) {
                $percentage_change = ($daily_transaction->total_amount - $previous_total) / $previous_total * 100;
            }
    
            $percentage_changes[$daily_transaction->date] = $percentage_change;
            $previous_total = $daily_transaction->total_amount;
        }

        return view('merchant.dashboard', compact('months', 'customerCounts', 'percentageChange', 'products', "merchant", "customer_payment_sum", "top_debtors_arr", "earnings", "daily_transactions", "percentage_changes"));
    }

    public function product()
    {
        $products = \DB::table('products')->join('stores', 'products.store_id', '=', 'stores.id')
            ->join('merchants', 'stores.merchant_id', '=', 'merchants.id')
            ->where('merchants.user_id', \Auth::user()->id)
            ->select(['products.id', 'products.name', 'price', 'description', 'image'])
            ->orderBy('price', 'ASC')
            ->get();

        $stores = Store::join('merchants', 'stores.merchant_id', '=', 'merchants.id')
            ->join('users', 'merchants.user_id', '=', 'users.id')
            ->where('users.id', \Auth::user()->id)
            ->select(['stores.id as store_id', 'stores.name as store_name'])
            ->get();

        return view('product.index', compact('products', 'stores'));
    }

    public function saveProduct(Request $request)
    {
        $validator = \Validator::make(\Request::all(), [
            'product_name' => 'required',
            'product_price' => 'required|numeric',
            'product_description' => 'nullable',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product_image = $request->file('product_image');
        $get_file_name = $product_image->getClientOriginalName();
        $product_image->move(public_path('images/products'), $get_file_name); // public/images/abc.jpg (public_path()

        $save_product = Product::firstOrCreate([
            'store_id' => \Request::get('store_id'),
            'name' => $request->product_name,
            'price' => $request->product_price,
        ]);
        $save_product->description = $request->product_description;
        $save_product->image = 'images/products/' . $get_file_name; // 'images/abc.jpg
        $save_product->save();

        return response()->json(['success' => 'Product saved successfully!'], 200);
    }

    public function getProduct()
    {
        $products = Product::where('id', \Request::get('id'))
            ->select(['id', 'name', 'price', 'description', 'image'])
            ->orderBy('price', 'ASC')
            ->get();

        return response()->json(['data' => $products], 200);
    }

    public function getStore()
    {
        $stores = Store::where("stores.id", \Request::get("store_id"))
            ->get();

        return response()->json(["data"=> $stores], 200);
    }

    public function updateProduct(Request $request)
    {
        $rules = [
            'product_name' => 'required',
            'product_price' => 'required|numeric|min:9',
            'product_description' => 'nullable',
        ];
        
        if (\Request::hasFile('product_image')) {
            $rules['product_image'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        $validator = \Validator::make(\Request::all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $update_product = Product::find(\Request::get('id'));

        //check image if the user upload new image
        if ($request->hasFile('product_image')) {
            // Process and save the image as you were doing before
            $file = $request->file('product_image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('images/products/'), $filename);

            // Update the product image field in the database
            $update_product->image = 'images/products/' . $filename;
        }
        $update_product->name = \Request::get('product_name');
        $update_product->price = \Request::get('product_price');
        $update_product->description = \Request::get('product_description');
        $update_product->update();

        // update debts product price
        $update_debts_products = Debt::where("debts.product_id", $update_product->id)->get();

        foreach ($update_debts_products as $debt) {
            $debt->current_amount = $update_product->price;
            $debt->product_price_change_date = $update_product->updated_at;
            $debt->update();
        }

        return response()->json(['success' => 'Product updated successfully!', 200]);
    }

    public function updateStore(Request $request)
    {
        $validator = \Validator::make(\Request::all(), [
            'store_name' => 'required',
            'store_phone' => 'required|numeric|min:9',
            'store_address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            
            $update_store = Store::find(\Request::get('id'));
            //check image if the user upload new image
            if ($request->hasFile('store_logo')) {
                // Process and save the image as you were doing before
                $file = $request->file('store_logo');
                $filename = $file->getClientOriginalName();
                $file->move(public_path('images/stores/'), $filename);
    
                // Update the store image field in the database
                $update_store->logo = 'images/stores/' . $filename;
            }

            $update_store->name = \Request::get('store_name');
            $update_store->address = \Request::get('store_address');
            $update_store->phone = \Request::get('store_phone');
            $update_store->update();
    
            return response()->json(['success' => 'Store updated successfully!', 200]);
        }

    }

    public function deleteProduct()
    {
        $delete_product = Product::find(\Request::get('id'));

        $image_path = $delete_product->image;
        $full_path = public_path($image_path);
        if (file_exists($full_path)) {
            unlink($full_path);
        }

        $delete_product->delete();

        return response()->json(['success' => 'Product deleted successfully!'], 200);
    }

    public function deleteStore() {
        $delete_store = Store::find(\Request::get('id'));

        $image_path = $delete_store->logo;
        $full_path = public_path($image_path);
        if (file_exists($full_path)) {
            unlink($full_path);
        }

        $delete_store->delete();

        return response()->json(['success' => 'Product deleted successfully!'], 200);
    }

    public function store()
    {
        $stores = \DB::table('stores')->join('merchants', 'stores.merchant_id', '=', 'merchants.id')
            ->where('merchants.user_id', \Auth::user()->id)
            ->select(['stores.id', 'stores.name', 'stores.address', 'stores.phone', 'stores.email', 'stores.logo'])
            ->get();

        return view('store.index', compact('stores'));
    }

    public function saveStore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'store_name' => 'required|unique:stores,name',
            'store_address' => 'required',
            'store_phone' => 'numeric|required',
            'store_email' => 'required',
            'store_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['error_message' => $validator->errors()], 422);
        }

        $store_logo = $request->file('store_logo');
        $get_file_name = $store_logo->getClientOriginalName();
        $store_logo->move(public_path('images/stores'), $get_file_name);

        $merchant_id = Merchant::join('users', 'merchants.user_id', '=', 'users.id')
            ->where('users.id', \Auth::user()->id)
            ->select(['merchants.id as merchant_id'])
            ->first();
        $save_store = Store::firstOrCreate([
            'merchant_id' => $merchant_id['merchant_id'],
            'name' => $request->store_name,
            'address' => $request->store_address,
            'phone' => $request->store_phone,
            'email' => $request->store_email,
        ]);

        $save_store->logo = 'images/stores/' . $get_file_name;
        $save_store->save();

        return response()->json(['message' => "Store added successfully!"]);
    }


    public function customer()
    {
        $merchant = Merchant::where("user_id", \Auth::user()->id)->first();
        $loan_settings = LoanSetting::where("merchant_id", $merchant->id)->count();

        return view('customer.index', compact("loan_settings"));
    }

    public function customerLoanStatus()
    {
        return view("customer.customer_loan_status");
    }

    public function customerLoanStatusData()
    {
        $merchant = Merchant::where("user_id", \Auth::user()->id)->first();
        
        $customer_debts = Debt::join("customers", "debts.customer_id", "=", "customers.id")
            ->join("products", "debts.product_id", "=" , "products.id")
            ->join("debt_statuses", "debts.debt_status_id", "=", "debt_statuses.id")
            ->join("debt_interests", "debt_interests.debt_id", "=", "debts.id")
            ->join("loan_settings", "debt_interests.loan_setting_id", "=", "loan_settings.id")
            ->where("customers.merchant_id", $merchant->id)
            ->get(["customers.name as customer_name", "products.name as product_name", "debt_statuses.name as debt_status_name", "debts.debt_status_id", "debts.due_date" , "debts.id as debt_id", "loan_settings.interest_rate", "debts.amount"]);

        $data_table = collect($customer_debts)
            ->map(function ($customer_debt) {
                if ($customer_debt->debt_status_id == 1) {
                    $bg = "bg-success";
                } else if ($customer_debt->debt_status_id == 2) {
                    $bg = "bg-danger";
                } else if ($customer_debt->debt_status_id == 4) {
                    $bg = "bg-warning";
                } else {
                    $bg = "bg-info";
                }

                $debt_status = '<span class="badge ' . $bg . '">' . $customer_debt["debt_status_name"] . '</span>';

                $action = '<div class="d-flex content-align-center">
                                <button data-debts_id="' . $customer_debt["debt_id"] . '" class="btn btn-primary btn-sm viewInterestButton"><span class="ti ti-eye"></span> View</button>
                            </div>';


                return [
                    "customer_name" => $customer_debt["customer_name"],
					"product_owed" => $customer_debt["product_name"],
					"amount" => "<span class='ti ti-currency-peso'></span>" . $customer_debt["amount"],
					"interest_rate" => $customer_debt["interest_rate"] . "%",
					"debt_status" => $debt_status,
					"action" => $action,
                ];
            });

        return response()->json($data_table);
    }


    public function customerData()
    {
        $merchant = Merchant::where("user_id", \Auth::user()->id)->first();
        $users = Customer::join("users", "customers.user_id", "=", "users.id")
            ->where("users.is_customer", 1)
            ->where("customers.merchant_id", $merchant->id)
            ->get(['customers.id as customer_id', 'customers.customer_no', 'customers.name', "customers.credit_limit", "users.id  as user_id", "users.is_active"]);

        $data_table = collect($users)
            ->map(function ($user) {

                $status_name = $user->is_active == 1 ? "Deactivate" : "Activate";

                $action = '
                    <a class="btn btn-primary btn-sm viewCustomerDetail" data-customer_id="' .$user["customer_id"]. '"><span class="ti ti-eye-check"></span> View</a>
                    <a class="btn btn-danger btn-sm deactivateCustomerButton" data-status_id="' .$user["is_active"]. '" data-user_id="' .$user["user_id"]. '"><span class="ti ti-trash"></span> ' .$status_name. '</a>
                ';

                return [
                    "customer_id" => $user["customer_no"],
                    "name" => $user["name"],
                    "credit_limit" => $user["credit_limit"],
                    "action" => $action,
                ];
            });

        return response()->json($data_table);
    }

    public function deactivateCustomer()
    {
        $deactivate_user = User::find(\Request::get("user_id"));
        $deactivate_user->is_active = (\Request::get("status_id") == 1) ? 0 : 1;
        $deactivate_user->save();

        if ($deactivate_user->is_active == 1) {
            $status = "activated";
        } else if ($deactivate_user->is_active == 0) {
            $status = "deactivated";
        }
        return response()->json($status);
    }
    public function customerDetail()
    {
        $customer = Customer::join("users", "customers.user_id", "=", "users.id")
            ->where("customers.id", \Request::get("customer_id"))
            ->select("customers.id as customer_id", "users.email", "users.secret", "customers.name", "customers.address", "customers.contact_number", "customers.credit_limit", "users.is_active")
            ->get()->first();

        return view("customer.modal-data", compact("customer"));
    }

    public function saveCustomerDetail()
    {
        $validator = \Validator::make(\Request::all(), [
            "customer_name" => "required",
            "customer_address" => "required",
            "customer_contact_number" => "required|numeric",
            "customer_email" => "required|unique:customers,email",
            "credit_limit" => "required|numeric",
        ]);
        if ($validator->fails()) {
            return response()->json(["errors" => $validator->messages()]);
        } else {
            $customer_name = \Request::get("customer_name");
            $customer_address = \Request::get("customer_address");
            $customer_contact_number = \Request::get("customer_contact_number");
            $customer_email = \Request::get("customer_email");
            $credit_limit = \Request::get("credit_limit");

            $merchant = Merchant::where("user_id", auth()->user()->id)->first();

            $name_parts = explode(" ", $customer_name);
            $initials = "";
            foreach ($name_parts as $name_part) {
                $initials .= substr($name_part, 0, 1);
            }
            $user_name = strtoupper($initials);
            $unique_identifier = uniqid();
            $customer_credential = $user_name . $unique_identifier;

            // generate user credetial
            $user_create = User::create([
                "email" => $customer_email,
                "password" => bcrypt($customer_credential),
                "secret" => $customer_credential,
                "is_password_change" => 0,
                "is_customer" => true,
                "is_merchant" => false,
                "is_admin" => false,
                "is_active" => 1,
            ]);

            // create customer data
            $customer_create = Customer::create([
                "user_id" => $user_create->id,
                "merchant_id" => $merchant->id, 
                "customer_no" => $customer_credential, 
                "name" => $customer_name, 
                "address" => $customer_address, 
                "contact_number" => $customer_contact_number, 
                "email" => $customer_email,
                "credit_limit" => $credit_limit,
            ]);

            return response()->json(true);
        }
    }

    public function viewProfile(Request $request)
    {
        $view_customer = User::join('customers', 'users.customer_id', '=', 'customers.id')
            ->where('customers.id', $request->id)
            ->select(['customers.customer_id', 'email', 'name'])
            ->get();
        return view('customer.view-profile', compact('view_customer'));
    }

    public function editProfile(Request $request)
    {
        return 'Edit profile is under development!';
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

    public function customerUpdateDetail() {
        $customer_id = \Request::get("customer_id");
        $name = \Request::get("name");
        $address = \Request::get("address");
        $contact_number = \Request::get("contact_number");
        $email = \Request::get("email");
        $credit_limit = \Request::get("credit_limit");

        $customer = Customer::find($customer_id);

        $validator = \Validator::make(\Request::all(), [
            "name" => "required",
            "address" => "required",
            "contact_number" => [
                "bail",
                "required",
                "numeric",
                Rule::unique("customers", "contact_number")->ignore($customer->id),
            ],
            "email" => "bail|email|required",
            "credit_limit" => "bail|numeric|required",
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => $validator->messages()]);
        } else {
            $customer->name = $name;
            $customer->address = $address;
            $customer->contact_number = $contact_number;
            $customer->email = $email;
            $customer->credit_limit = $credit_limit;
            $customer->update();

            return response()->json(["success" => true]);
        }
    }

}