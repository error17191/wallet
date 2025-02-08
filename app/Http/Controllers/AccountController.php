<?php

namespace App\Http\Controllers;

use App\Http\Resources\AccountResource;
use App\Models\Account;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function index()
    {
        return AccountResource::collection(Account::paginate(10));
    }

    public function show($id)
    {
        return new AccountResource(Account::findOrFail($id));
    }


    public function create(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $account = $this->accountService->createAccount($data['name']);

        return response()->json(AccountResource::make($account), 201);
    }

    public function topUp(Request $request, int $id)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $account = $this->accountService->topUp($id, $data['amount']);

        return new AccountResource($account);
    }

    public function charge(Request $request, int $id)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $account = $this->accountService->charge($id, $data['amount']);

        return new AccountResource($account);
    }
}
