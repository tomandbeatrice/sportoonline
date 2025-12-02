<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Wallet\Services\WalletService;
use App\Modules\Wallet\Models\Wallet;
use App\Modules\Wallet\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WalletService $walletService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->walletService = app(WalletService::class);
    }

    public function test_creates_wallet_for_user(): void
    {
        $user = User::factory()->create();

        $wallet = $this->walletService->createWallet($user);

        $this->assertInstanceOf(Wallet::class, $wallet);
        $this->assertEquals($user->id, $wallet->user_id);
        $this->assertEquals('0.00', $wallet->balance);
        $this->assertEquals('TRY', $wallet->currency);
        $this->assertFalse($wallet->is_frozen);
    }

    public function test_returns_existing_wallet_if_already_created(): void
    {
        $user = User::factory()->create();

        $wallet1 = $this->walletService->createWallet($user);
        $wallet2 = $this->walletService->createWallet($user);

        $this->assertEquals($wallet1->id, $wallet2->id);
        $this->assertEquals(1, Wallet::where('user_id', $user->id)->count());
    }

    public function test_gets_balance_of_user_wallet(): void
    {
        $user = User::factory()->create();
        $this->walletService->createWallet($user);

        $balance = $this->walletService->getBalance($user);

        $this->assertEquals(0.0, $balance);
    }

    public function test_deposits_funds_into_user_wallet(): void
    {
        $user = User::factory()->create();
        $this->walletService->createWallet($user);

        $transaction = $this->walletService->deposit($user, 100.50, 'Test deposit');

        $this->assertInstanceOf(WalletTransaction::class, $transaction);
        $this->assertEquals('deposit', $transaction->type);
        $this->assertEquals('100.50', $transaction->amount);
        $this->assertEquals('completed', $transaction->status);
        $this->assertEquals(100.50, $this->walletService->getBalance($user));
    }

    public function test_throws_exception_for_invalid_deposit_amount(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Deposit amount must be greater than zero.');

        $user = User::factory()->create();
        $this->walletService->createWallet($user);

        $this->walletService->deposit($user, -10, 'Invalid deposit');
    }

    public function test_throws_exception_for_zero_deposit_amount(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Deposit amount must be greater than zero.');

        $user = User::factory()->create();
        $this->walletService->createWallet($user);

        $this->walletService->deposit($user, 0, 'Invalid deposit');
    }

    public function test_withdraws_funds_from_user_wallet(): void
    {
        $user = User::factory()->create();
        $this->walletService->createWallet($user);
        $this->walletService->deposit($user, 200, 'Initial deposit');

        $transaction = $this->walletService->withdraw($user, 50, 'Test withdrawal');

        $this->assertInstanceOf(WalletTransaction::class, $transaction);
        $this->assertEquals('withdrawal', $transaction->type);
        $this->assertEquals('50.00', $transaction->amount);
        $this->assertEquals('pending', $transaction->status);
        $this->assertEquals(150.0, $this->walletService->getBalance($user));
    }

    public function test_throws_exception_for_insufficient_balance(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient balance.');

        $user = User::factory()->create();
        $this->walletService->createWallet($user);

        $this->walletService->withdraw($user, 100, 'Test withdrawal');
    }

    public function test_throws_exception_for_frozen_wallet_on_deposit(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Wallet is frozen.');

        $user = User::factory()->create();
        $wallet = $this->walletService->createWallet($user);
        $wallet->update(['is_frozen' => true]);

        $this->walletService->deposit($user, 100, 'Test deposit');
    }

    public function test_throws_exception_for_frozen_wallet_on_withdrawal(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Wallet is frozen.');

        $user = User::factory()->create();
        $wallet = $this->walletService->createWallet($user);
        $wallet->update(['balance' => 100, 'is_frozen' => true]);

        $this->walletService->withdraw($user, 50, 'Test withdrawal');
    }

    public function test_transfers_funds_between_users(): void
    {
        $userFrom = User::factory()->create();
        $userTo = User::factory()->create();
        
        $this->walletService->createWallet($userFrom);
        $this->walletService->createWallet($userTo);
        $this->walletService->deposit($userFrom, 200, 'Initial deposit');

        $result = $this->walletService->transfer($userFrom, $userTo, 75);

        $this->assertArrayHasKey('withdrawal', $result);
        $this->assertArrayHasKey('deposit', $result);
        $this->assertEquals(125.0, $this->walletService->getBalance($userFrom));
        $this->assertEquals(75.0, $this->walletService->getBalance($userTo));
    }

    public function test_throws_exception_when_transferring_to_same_user(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Cannot transfer to the same user.');

        $user = User::factory()->create();
        $this->walletService->createWallet($user);
        $this->walletService->deposit($user, 100, 'Initial deposit');

        $this->walletService->transfer($user, $user, 50);
    }

    public function test_throws_exception_when_transferring_with_insufficient_balance(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient balance.');

        $userFrom = User::factory()->create();
        $userTo = User::factory()->create();
        
        $this->walletService->createWallet($userFrom);
        $this->walletService->createWallet($userTo);

        $this->walletService->transfer($userFrom, $userTo, 100);
    }

    public function test_creates_transaction_records_for_deposits(): void
    {
        $user = User::factory()->create();
        $this->walletService->createWallet($user);

        $this->walletService->deposit($user, 100, 'Test deposit');

        $this->assertDatabaseHas('wallet_transactions', [
            'type' => 'deposit',
            'amount' => 100.00,
            'description' => 'Test deposit',
            'status' => 'completed',
        ]);
    }

    public function test_maintains_transaction_history(): void
    {
        $user = User::factory()->create();
        $wallet = $this->walletService->createWallet($user);

        $this->walletService->deposit($user, 100, 'First deposit');
        $this->walletService->deposit($user, 50, 'Second deposit');
        $this->walletService->withdraw($user, 30, 'Withdrawal');

        $this->assertEquals(3, $wallet->transactions()->count());
    }
}

