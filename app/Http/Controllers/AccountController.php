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

use App\Contracts\AccountServiceInterface;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Services\Account\AccountBalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    protected AccountServiceInterface $accountService;

    public function __construct(AccountServiceInterface $accountService)
    {
        $this->accountService = $accountService;
    }

    public function store(StoreAccountRequest $request): JsonResponse
    {

        $this->authorize('create', Account::class);
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $account = $this->accountService->createAccount($data);
        return response()->json($account, 201);
    }

    public function update(Account $account,  UpdateAccountRequest $request): JsonResponse
    {

        $this->authorize('update', $account);
        $data = $request->validated();
        if (empty($data)) {
            return response()->json(['message' => 'لا توجد بيانات صالحة للتحديث'], 422);
        }

        $updated = $this->accountService->updateAccount($account, $data);

        return response()->json([
            'message' => ' Account updated successfully',
            'account' => $updated
        ]);
    }

    public function close(Account $account): JsonResponse
    {
        $this->accountService->closeAccount($account);

        return response()->json([
            'message' => 'Account closed successfully',
        ]);
    }

    public function aggregateBalance(Account $account,AccountBalanceService $balanceService): JsonResponse
    {

        return response()->json([
            'account_id' => $account->id,
            'aggregate_balance' => $balanceService->computeAggregateBalance($account),
            'currency' => $account->currency,
        ]);
    }


}

