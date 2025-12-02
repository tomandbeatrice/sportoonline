<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Wallet\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WalletFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_allows_authenticated_user_to_view_wallet_balance_and_transactions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/wallet');

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertArrayHasKey('wallet', $response->json('data'));
        $this->assertArrayHasKey('transactions', $response->json('data'));
        $this->assertEquals('0.00', $response->json('data.wallet.balance'));
        $this->assertEquals('TRY', $response->json('data.wallet.currency'));
    }

    public function test_requires_authentication_to_view_wallet(): void
    {
        $response = $this->getJson('/api/wallet');

        $response->assertStatus(401);
    }

    public function test_allows_authenticated_user_to_deposit_funds(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/deposit', [
                'amount' => 150.75,
                'description' => 'Test deposit',
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertEquals('Funds deposited successfully', $response->json('message'));
        $this->assertEquals(150.75, $response->json('data.new_balance'));
        
        $this->assertDatabaseHas('wallet_transactions', [
            'type' => 'deposit',
            'amount' => 150.75,
            'description' => 'Test deposit',
            'status' => 'completed',
        ]);
    }

    public function test_validates_deposit_amount_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/deposit', [
                'description' => 'Test deposit',
            ]);

        $response->assertStatus(422);
    }

    public function test_validates_deposit_amount_is_positive(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/deposit', [
                'amount' => -50,
                'description' => 'Invalid deposit',
            ]);

        $response->assertStatus(400);
        $this->assertFalse($response->json('success'));
    }

    public function test_requires_authentication_to_deposit_funds(): void
    {
        $response = $this->postJson('/api/wallet/deposit', [
            'amount' => 100,
            'description' => 'Test deposit',
        ]);

        $response->assertStatus(401);
    }

    public function test_allows_authenticated_user_to_withdraw_funds(): void
    {
        $user = User::factory()->create();
        
        // First deposit some funds
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/deposit', [
                'amount' => 200,
                'description' => 'Initial deposit',
            ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/withdraw', [
                'amount' => 75,
                'description' => 'Test withdrawal',
            ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertEquals('Withdrawal request submitted successfully', $response->json('message'));
        $this->assertEquals(125.0, $response->json('data.new_balance'));
        
        $this->assertDatabaseHas('wallet_transactions', [
            'type' => 'withdrawal',
            'amount' => 75.00,
            'description' => 'Test withdrawal',
            'status' => 'pending',
        ]);
    }

    public function test_prevents_withdrawal_with_insufficient_balance(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/withdraw', [
                'amount' => 100,
                'description' => 'Test withdrawal',
            ]);

        $response->assertStatus(400);
        $this->assertFalse($response->json('success'));
        $this->assertStringContainsString('Insufficient balance', $response->json('message'));
    }

    public function test_validates_withdrawal_amount_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/withdraw', [
                'description' => 'Test withdrawal',
            ]);

        $response->assertStatus(422);
    }

    public function test_requires_authentication_to_withdraw_funds(): void
    {
        $response = $this->postJson('/api/wallet/withdraw', [
            'amount' => 50,
            'description' => 'Test withdrawal',
        ]);

        $response->assertStatus(401);
    }

    public function test_shows_transaction_history(): void
    {
        $user = User::factory()->create();

        // Make multiple transactions
        $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/deposit', [
                'amount' => 100,
                'description' => 'First deposit',
            ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/deposit', [
                'amount' => 50,
                'description' => 'Second deposit',
            ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/wallet');

        $response->assertStatus(200);
        $this->assertEquals(2, $response->json('data.transactions.total'));
        $this->assertEquals('150.00', $response->json('data.wallet.balance'));
    }

    public function test_uses_default_description_when_not_provided(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/wallet/deposit', [
                'amount' => 100,
            ]);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('wallet_transactions', [
            'type' => 'deposit',
            'amount' => 100.00,
            'description' => 'Deposit to wallet',
        ]);
    }

    public function test_creates_wallet_automatically_on_first_access(): void
    {
        $user = User::factory()->create();

        $this->assertFalse(Wallet::where('user_id', $user->id)->exists());

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/wallet');

        $response->assertStatus(200);
        $this->assertTrue(Wallet::where('user_id', $user->id)->exists());
    }
}

