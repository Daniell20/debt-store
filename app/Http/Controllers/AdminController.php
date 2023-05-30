<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Merchant;
use App\Product;
use App\Store;
use App\User;

use Hash;
use DB;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function createMerchant()
    {
        $merchants = Merchant::join('users', 'merchants.user_id', '=', 'users.id')
            ->get();
        return view('admin.create-merchant', compact('merchants'));
    }

    public function storeMerchant()
    {
        \Validator::make(\Request::all(), [
            'merchant_name' => 'required|unique:merchants,name',
        ])->validate();

        $merchant_no = Merchant::count() + 1;

        DB::beginTransaction();

        try {
            $user = User::create([
                'email' => 'MERCH-' . $merchant_no . '@debtstore.com',
                'password' => Hash::make('MERCH-' . $merchant_no),
                // Generate a random password
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
}