<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AccountService
{
    public function createAccount(string $name): Account
    {
        return Account::create(['name' => $name, 'balance' => 0]);
    }

    public function topUp(int $accountId, float $amount): Account
    {
        return DB::transaction(function () use ($accountId, $amount) {
            $account = Account::findOrFail($accountId);

            if ($this->isDuplicateTransaction($accountId, $amount, TransactionType::TOP_UP)) {
                throw ValidationException::withMessages(['transaction' => 'Duplicate transaction detected']);
            }

            Transaction::create([
                'account_id' => $accountId,
                'type' => TransactionType::TOP_UP->value,
                'amount' => $amount,
            ]);

            $account->increment('balance', $amount);

            return $account;
        });
    }

    public function charge(int $accountId, float $amount): Account
    {
        return DB::transaction(function () use ($accountId, $amount) {
            $account = Account::findOrFail($accountId);

            if ($amount > $account->balance) {
                throw ValidationException::withMessages(['amount' => 'Insufficient balance']);
            }

            if ($this->isDuplicateTransaction($accountId, $amount, TransactionType::CHARGE)) {
                throw ValidationException::withMessages(['transaction' => 'Duplicate transaction detected']);
            }

            Transaction::create([
                'account_id' => $accountId,
                'type' => TransactionType::CHARGE->value,
                'amount' => $amount,
            ]);

            $account->decrement('balance', $amount);

            return $account;
        });
    }

    /**
     * Check if a duplicate transaction exists within the configured time window.
     */
    private function isDuplicateTransaction(int $accountId, float $amount, TransactionType $type): bool
    {
        $timeWindow = config('wallet.duplicate_transaction_time_window');

        return Transaction::where('account_id', $accountId)
            ->where('type', $type->value)
            ->where('amount', $amount)
            ->where('created_at', '>=', now()->subSeconds($timeWindow))
            ->exists();
    }
}
