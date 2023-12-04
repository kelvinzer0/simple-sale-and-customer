<?php

namespace App\Http\Controllers;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;

class SaleController extends Controller
{
        public function index()
    {
        $Sale = Sale::all();
        return response()->json(['data' => $Sale]);
    }

    public function show($id)
    {
        $Sale = Sale::findOrFail($id);
        return response()->json(['data' => $Sale]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customers_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'name_product' => 'required|string',
            'total_amount' => 'required|numeric',
        ]);

        $customer = Customer::findOrFail($request->input('customers_id'));

        if ($request->input('total_amount') < 0) {
            return response()->json(['errors' => ['total_amount' => ['Amount must be non-negative.']]], 400);
        }

        if ($customer->emoney < $request->input('total_amount')) {
            return response()->json(['errors' =>  ['total_amount' => ['Insufficient balance.']]], 400);
        }

        $sale = Sale::create($request->all());

        // Check if subtracting the total_amount will result in a negative balance
        if ($customer->emoney - $request->input('total_amount') < 0) {
            // Handle the case where the subtraction would result in a negative balance
            return response()->json(['error' => ['emoney' => 'Invalid operation.']], 400);
        }

        // Update the customer's emoney
        $customer->emoney -= $request->input('total_amount');
        $customer->save();

        return response()->json(['data' => $sale], 201);
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'customers_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'name_product' => 'required|string',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $sale = Sale::findOrFail($id);
        $customer = Customer::findOrFail($request->input('customers_id'));

       
        $amountDifference = $request->input('total_amount') - $sale->total_amount;

        if ($amountDifference > 0 && $customer->emoney < $amountDifference) {
            return response()->json(['error' => ['total_amount' => ['Insufficient balance.']]], 400);
        }

       
        $customer->emoney += $sale->total_amount;
        $sale->update($request->all());

        
        $customer->emoney -= $request->input('total_amount');
        $customer->save();

        return response()->json(['data' => $sale]);
    }

    public function destroy($id)
    {
        $Sale = Sale::findOrFail($id);
        $Sale->delete();

        return response()->json(['message' => 'Sale deleted successfully']);
    }
}
