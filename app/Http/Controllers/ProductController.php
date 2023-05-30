<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ProductController extends Controller
{
    public function index()
    {
        return view('product.index');
    }

    public function saveProduct(Request $request)
    {
        $validator = \Validator::make(\Request::all(), [
            'product_name' => 'required',
            'product_price' => 'required|numeric',
            'product_description' => 'required'
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    }
}