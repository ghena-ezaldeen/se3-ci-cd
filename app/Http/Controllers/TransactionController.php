<?php

namespace App\Http\Controllers;

use App\Services\BankingFacade;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private BankingFacade $bankingFacade
    ) {}

    public function store(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:accounts,id',
            'to_account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $transaction = $this->bankingFacade->transferFunds(
                $request->from_account_id,
                $request->to_account_id,
                $request->amount,
                $request->description ?? 'No description'
            );

            return response()->json([
                'message' => 'Transaction completed successfully',
                'data' => $transaction
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function deposit(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $transaction = $this->bankingFacade->deposit(
            $request->account_id,
            $request->amount,
            $request->description ?? 'Deposit'
        );

        return response()->json(['message' => 'Deposit successful', 'data' => $transaction]);
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $transaction = $this->bankingFacade->withdraw(
                $request->account_id,
                $request->amount,
                $request->description ?? 'Withdraw'
            );
            return response()->json(['message' => 'Withdraw successful', 'data' => $transaction]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
