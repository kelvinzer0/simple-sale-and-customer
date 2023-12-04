<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        return response()->json(['data' => $customers]);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json(['data' => $customer]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'emoney' => 'nullable|numeric',
            'phone_number' => 'required|numeric|digits_between:5,20',
            'address' => 'required',
        ]);

        $customer = Customer::create($request->all());

        return response()->json(['data' => $customer], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'emoney' => 'nullable|numeric',
            'phone_number' => 'required|numeric|digits_between:5,20',
            'address' => 'required',
        ]);

        $customer = Customer::findOrFail($id);
        $customer->update($request->all());

        return response()->json(['data' => $customer]);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
