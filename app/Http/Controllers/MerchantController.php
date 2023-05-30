<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Customer;
use App\Merchant;
use App\Product;
use App\Store;


class MerchantController extends Controller
{
    public function index()
    {
        return view('merchant.index');
    }
    public function dashboard()
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

        $products = \DB::table('products')->join('stores', 'products.store_id', '=', 'stores.id')
            ->join('merchants', 'stores.merchant_id', '=', 'merchants.id')
            ->where('merchants.user_id', \Auth::user()->id)
            ->select(['products.name', 'products.price', 'products.description', 'products.image'])
            ->get();

        return view('merchant.dashboard', compact('months', 'customerCounts', 'percentageChange', 'products'));
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
            'product_price' => 'required|numeric|min:9',
            'product_description' => 'nullable',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validator->fails()) {
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

    public function updateProduct(Request $request)
    {
        $validator = \Validator::make(\Request::all(), [
            'product_name' => 'required',
            'product_price' => 'required|numeric|min:9',
            'product_description' => 'nullable',
            'product_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validator->fails()) {
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
            $update_product -> image = 'images/products/' . $filename;
        }
        $update_product -> name = \Request::get('product_name');
        $update_product -> price = \Request::get('product_price');
        $update_product -> description = \Request::get('product_description');
        $update_product -> save();

        return response()->json(['success' => 'Product updated successfully!', 200]);
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
            return response()->json(['error_message' => $validator->errors()] , 422);
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
        return view('customer.index');
    }

    public function customerData()
    {
        $users = Customer::leftJoin('debt_statuses', 'customers.debt_status_id', '=', 'debt_statuses.id')
            ->select(['customers.id', 'customers.customer_no', 'customers.name', 'debt_statuses.name as status'])
            ->get();
        return response()->json(['data' => $users]);
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

    public function settings()
    {
        return view('settings.index');
    }
}
