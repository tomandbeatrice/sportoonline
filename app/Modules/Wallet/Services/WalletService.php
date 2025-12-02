<?php

namespace App\Modules\Wallet\Services;

use App\Models\User;
use App\Modules\Wallet\Models\Wallet;
use App\Modules\Wallet\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Exception;

class WalletService
{
    /**
     * Create a wallet for a user.
     *
     * @param User $user
     * @return Wallet
     */
    public function createWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 0,
                'currency' => 'TRY',
                'is_frozen' => false,
            ]
        );
    }

    /**
     * Get the balance of a user's wallet.
     *
     * @param User $user
     * @return float
     */
    public function getBalance(User $user): float
    {
        $wallet = $this->getOrCreateWallet($user);
        return (float) $wallet->balance;
    }

    /**
     * Deposit funds into a user's wallet.
     *
     * @param User $user
     * @param float $amount
     * @param string $description
     * @return WalletTransaction
     * @throws Exception
     */
    public function deposit(User $user, float $amount, string $description): WalletTransaction
    {
        if ($amount <= 0) {
            throw new Exception('Deposit amount must be greater than zero.');
        }

        return DB::transaction(function () use ($user, $amount, $description) {
            $wallet = $this->getOrCreateWallet($user);

            if ($wallet->is_frozen) {
                throw new Exception('Wallet is frozen.');
            }

            // Create transaction
            $transaction = $wallet->transactions()->create([
                'type' => 'deposit',
                'amount' => $amount,
                'description' => $description,
                'status' => 'completed',
            ]);

            // Update wallet balance
            $wallet->increment('balance', $amount);

            return $transaction;
        });
    }

    /**
     * Withdraw funds from a user's wallet.
     *
     * @param User $user
     * @param float $amount
     * @param string $description
     * @return WalletTransaction
     * @throws Exception
     */
    public function withdraw(User $user, float $amount, string $description): WalletTransaction
    {
        if ($amount <= 0) {
            throw new Exception('Withdrawal amount must be greater than zero.');
        }

        return DB::transaction(function () use ($user, $amount, $description) {
            $wallet = $this->getOrCreateWallet($user);

            if ($wallet->is_frozen) {
                throw new Exception('Wallet is frozen.');
            }

            if ($wallet->balance < $amount) {
                throw new Exception('Insufficient balance.');
            }

            // Create transaction
            $transaction = $wallet->transactions()->create([
                'type' => 'withdrawal',
                'amount' => $amount,
                'description' => $description,
                'status' => 'pending',
            ]);

            // Update wallet balance
            $wallet->decrement('balance', $amount);

            return $transaction;
        });
    }

    /**
     * Transfer funds from one user to another.
     *
     * @param User $from
     * @param User $to
     * @param float $amount
     * @return array
     * @throws Exception
     */
    public function transfer(User $from, User $to, float $amount): array
    {
        if ($amount <= 0) {
            throw new Exception('Transfer amount must be greater than zero.');
        }

        if ($from->id === $to->id) {
            throw new Exception('Cannot transfer to the same user.');
        }

        return DB::transaction(function () use ($from, $to, $amount) {
            $fromWallet = $this->getOrCreateWallet($from);
            $toWallet = $this->getOrCreateWallet($to);

            if ($fromWallet->is_frozen) {
                throw new Exception('Sender wallet is frozen.');
            }

            if ($toWallet->is_frozen) {
                throw new Exception('Recipient wallet is frozen.');
            }

            if ($fromWallet->balance < $amount) {
                throw new Exception('Insufficient balance.');
            }

            $reference = 'TRANSFER-' . time() . '-' . $from->id . '-' . $to->id;

            // Create withdrawal transaction for sender
            $withdrawalTransaction = $fromWallet->transactions()->create([
                'type' => 'payment',
                'amount' => $amount,
                'reference_id' => $reference,
                'description' => 'Transfer to user #' . $to->id,
                'status' => 'completed',
            ]);

            // Create deposit transaction for recipient
            $depositTransaction = $toWallet->transactions()->create([
                'type' => 'deposit',
                'amount' => $amount,
                'reference_id' => $reference,
                'description' => 'Transfer from user #' . $from->id,
                'status' => 'completed',
            ]);

            // Update balances
            $fromWallet->decrement('balance', $amount);
            $toWallet->increment('balance', $amount);

            return [
                'withdrawal' => $withdrawalTransaction,
                'deposit' => $depositTransaction,
            ];
        });
    }

    /**
     * Get or create wallet for a user.
     *
     * @param User $user
     * @return Wallet
     */
    protected function getOrCreateWallet(User $user): Wallet
    {
        return $user->wallet ?? $this->createWallet($user);
    }
}
