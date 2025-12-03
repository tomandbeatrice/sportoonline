<?php

namespace App\Modules\Wallet\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Wallet\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class WalletController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get current user's wallet balance and transactions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $wallet = $user->wallet ?? $this->walletService->createWallet($user);
            
            $transactions = $wallet->transactions()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => [
                    'wallet' => [
                        'balance' => $wallet->balance,
                        'currency' => $wallet->currency,
                        'is_frozen' => $wallet->is_frozen,
                    ],
                    'transactions' => $transactions,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch wallet data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add funds to the user's wallet (mock implementation).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deposit(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'description' => 'sometimes|string|max:255',
            ]);

            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $amount = (float) $request->input('amount');
            $description = $request->input('description', 'Deposit to wallet');

            $transaction = $this->walletService->deposit($user, $amount, $description);

            return response()->json([
                'success' => true,
                'message' => 'Funds deposited successfully',
                'data' => [
                    'transaction' => $transaction,
                    'new_balance' => $this->walletService->getBalance($user),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deposit funds: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Request withdrawal from the user's wallet.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function withdraw(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'required|numeric|min:0.01',
                'description' => 'sometimes|string|max:255',
            ]);

            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            $amount = (float) $request->input('amount');
            $description = $request->input('description', 'Withdrawal from wallet');

            $transaction = $this->walletService->withdraw($user, $amount, $description);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'data' => [
                    'transaction' => $transaction,
                    'new_balance' => $this->walletService->getBalance($user),
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process withdrawal: ' . $e->getMessage(),
            ], 400);
        }
    }
}
