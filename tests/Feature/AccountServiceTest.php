<?php

namespace Tests\Feature;

use App\Services\AccountService;
use App\Models\Account;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AccountService $accountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountService = new AccountService();
    }

    public function test_it_creates_an_account_successfully()
    {
        $account = $this->accountService->createAccount('John Doe');

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'name' => 'John Doe',
            'balance' => 0,
        ]);
    }

    public function test_it_top_ups_balance_successfully()
    {
        $account = Account::factory()->create(['balance' => 0]);

        $updatedAccount = $this->accountService->topUp($account->id, 100);

        $this->assertEquals(100, $updatedAccount->balance);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'amount' => 100,
            'type' => TransactionType::TOP_UP,
        ]);
    }

    public function test_it_blocks_duplicate_top_up_transactions()
    {
        $account = Account::factory()->create(['balance' => 0]);

        $this->accountService->topUp($account->id, 100);

        $this->expectException(ValidationException::class);
        $this->accountService->topUp($account->id, 100); // Should throw an exception

        $this->assertEquals(1, Transaction::where('account_id', $account->id)->count());
    }

    public function test_it_charges_account_successfully()
    {
        $account = Account::factory()->create(['balance' => 100]);

        $updatedAccount = $this->accountService->charge($account->id, 50);

        $this->assertEquals(50, $updatedAccount->balance);

        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'amount' => 50,
            'type' => TransactionType::CHARGE,
        ]);
    }

    public function test_it_blocks_charges_if_balance_is_insufficient()
    {
        $account = Account::factory()->create(['balance' => 50]);

        $this->expectException(ValidationException::class);
        $this->accountService->charge($account->id, 100);
    }

    public function test_it_blocks_duplicate_charge_transactions()
    {
        $account = Account::factory()->create(['balance' => 100]);

        $this->accountService->charge($account->id, 50);

        $this->expectException(ValidationException::class);
        $this->accountService->charge($account->id, 50); // Should throw an exception

        $this->assertEquals(1, Transaction::where('account_id', $account->id)
            ->where('type', TransactionType::CHARGE)
            ->count());
    }
}
