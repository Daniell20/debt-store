<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.users-dashboard');
    }

    public function store()
    {
        return view('users.store.add-store');
    }
}
