<?php

namespace App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Account Controller
|--------------------------------------------------------------------------
| Handles account lifecycle operations and exposes endpoints to work with
| composite balances. It keeps validation, persistence, and service calls
| together so routes can register, modify, close, and inspect accounts in
| one place.
*/

use App\Models\Account;
use App\Services\Account\AccountBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'parent_id' => ['nullable', 'exists:accounts,id'],
            'type' => ['required', 'string'],
            'currency' => ['nullable', 'string', 'size:3'],
            'balance' => ['nullable', 'numeric'],
            'interest_rate' => ['nullable', 'numeric'],
        ]);

        $account = Account::create([
            'user_id' => $validated['user_id'],
            'parent_id' => $validated['parent_id'] ?? null,
            'account_number' => $this->generateAccountNumber(),
            'type' => $validated['type'],
            'state' => 'active',
            'balance' => $validated['balance'] ?? 0,
            'currency' => $validated['currency'] ?? 'USD',
            'interest_rate' => $validated['interest_rate'] ?? 0,
        ]);

        return response()->json($account, 201);
    }

    public function update(Account $account, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'size:3'],
            'balance' => ['nullable', 'numeric'],
            'interest_rate' => ['nullable', 'numeric'],
        ]);

        $account->fill($validated);
        $account->save();

        return response()->json($account);
    }

    public function close(Account $account): JsonResponse
    {
        $account->state = 'closed';
        $account->save();

        return response()->json([
            'message' => 'Account closed successfully.',
            'account' => $account,
        ]);
    }

    public function aggregateBalance(Account $account, AccountBalanceService $service): JsonResponse
    {
        $total = $service->computeAggregateBalance($account);

        return response()->json([
            'account_id' => $account->id,
            'aggregate_balance' => $total,
            'currency' => $account->currency,
        ]);
    }

    private function generateAccountNumber(): string
    {
        return sprintf('ACC-%s', Str::upper(Str::random(10)));
    }
}

