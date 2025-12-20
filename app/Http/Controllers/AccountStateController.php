<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeAccountStateRequest;
use App\Models\Account;

use App\Services\Account\AccountStateService;
use Illuminate\Http\Request;

class AccountStateController extends Controller
{
    public function change(ChangeAccountStateRequest $request, Account $account, AccountStateService $service)
    {
        $this->authorize('changeState', $account);
        $validated = $request->validated();

        // تغيير الحالة
        $service->changeState($account, $validated['action']);

        return response()->json([
            'message' => 'Account state updated',
            'state' => $account->state,
        ]);
    }
}
