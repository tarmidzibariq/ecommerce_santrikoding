<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\CustomerResource;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'name'  => 'required|string|max:255',
            'email'  => 'required|unique:customers',
            'password'  => 'required|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // create customer
        $customer = Customer::create([
            'name'  =>  $request->name,
            'email'  =>  $request->email,
            'password'  =>  Hash::make($request->password),
        ]);

        if ($customer) {
            // return success with Api Resource
            return new CustomerResource(true, 'Data Customer Berhasil Disimpan!', $customer);
        }

        // return failed with Api Resource
        return new CustomerResource(false, 'Data Customer Gagal Disimpan', null);
    }
}
