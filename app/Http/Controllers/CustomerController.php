<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\User;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customer.index');
    }

    public function customerData()
    {
        $users = Customer::join('users', 'customers.id', '=', 'users.customer_id')
            ->leftJoin('debt_statuses', 'customers.debt_status_id', '=', 'debt_statuses.id')
            ->select(['customers.id', 'customers.customer_id', 'customers.name', 'users.email as email', 'debt_statuses.name as status'])
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
        return view('customer.settings');
    }
}
