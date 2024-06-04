<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get all transactions of the authenticated user and return total balance by type order by id
        $transactions = auth()->user()->transactions()->orderBy('id', 'desc')->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $totalBalance = $totalIncome - $totalExpense;

        return response()->json([
            'status' => true,
            'message' => 'Successfully retrieved transactions',
            'data' => TransactionResource::collection($transactions),
            'balance' => $totalBalance,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 200);
        }

        // store the image
        $image = request()->file('image');

        $image->storeAs('public/images', $image->hashName());

        // create the transaction
        $transaction = new Transaction([
            'name' => request()->name,
            'description' => request()->description,
            'amount' => request()->amount,
            'type' => request()->type,
            'image' => $image->hashName(),
        ]);

        auth()->user()->transactions()->save($transaction);

        return response()->json([
            'status' => true,
            'message' => 'Successfully created transaction',
            'data' => TransactionResource::make($transaction),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // find the transaction by user id and transaction id
        $transaction = auth()->user()->transactions()->find($id);

        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found',
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'Successfully retrieved transaction',
            'data' => TransactionResource::make($transaction),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = auth()->user()->transactions()->find($id);
        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found',
            ], 200);
        }

        // validate the request where is image is optional
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'amount' => 'required|numeric',
            'type' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 200);
        }

        // store the image
        if (request()->hasFile('image')) {
            $image = request()->file('image');

            $image->storeAs('public/images', $image->hashName());

            // delete the old image
            Storage::delete('public/images/' . $transaction->image);

            $transaction->image = $image->hashName();
        }

        $transaction->name = request()->name;
        $transaction->description = request()->description;
        $transaction->amount = request()->amount;
        $transaction->type = request()->type;

        $transaction->save();

        return response()->json([
            'status' => true,
            'message' => 'Successfully updated transaction',
            'data' => TransactionResource::make($transaction),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // find the transaction by user id and transaction id
        $transaction = auth()->user()->transactions()->find($id);

        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found',
            ], 200);
        }

        // delete the image
        Storage::delete('public/images/' . $transaction->image);

        $transaction->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully deleted transaction',
        ], 200);
    }
}
