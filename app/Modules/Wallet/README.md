# Wallet Module

The Wallet module provides comprehensive wallet and transaction management for users and sellers in the Sportoonline platform.

## Features

- ✅ User wallet management with balance tracking
- ✅ Transaction logging (deposits, withdrawals, payments, refunds, commissions)
- ✅ Wallet freeze functionality for security
- ✅ Internal transfers between users
- ✅ Atomic operations with database transactions
- ✅ Multi-currency support (default: TRY)
- ✅ RESTful API with Sanctum authentication

## Database Schema

### Wallets Table
```sql
- id (bigint, primary)
- user_id (bigint, foreign key to users, unique)
- balance (decimal, 10,2, default 0)
- currency (string, default 'TRY')
- is_frozen (boolean, default false)
- timestamps
```

### Wallet Transactions Table
```sql
- id (bigint, primary)
- wallet_id (bigint, foreign key to wallets)
- type (enum: 'deposit', 'withdrawal', 'payment', 'refund', 'commission')
- amount (decimal, 10,2)
- reference_id (nullable string - e.g., order_id)
- description (string)
- status (enum: 'pending', 'completed', 'failed')
- timestamps
```

## Installation

The Wallet module is already registered in `config/app.php`. To use it:

1. Run migrations:
```bash
php artisan migrate
```

2. The module will automatically load routes and be available at `/api/wallet` endpoints.

## API Endpoints

### Get Wallet Information
```http
GET /api/wallet
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "wallet": {
      "balance": "150.00",
      "currency": "TRY",
      "is_frozen": false
    },
    "transactions": {
      "current_page": 1,
      "data": [...],
      "total": 10
    }
  }
}
```

### Deposit Funds
```http
POST /api/wallet/deposit
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 100.50,
  "description": "Payment received"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Funds deposited successfully",
  "data": {
    "transaction": {...},
    "new_balance": 100.50
  }
}
```

### Withdraw Funds
```http
POST /api/wallet/withdraw
Authorization: Bearer {token}
Content-Type: application/json

{
  "amount": 50.00,
  "description": "Withdrawal to bank account"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Withdrawal request submitted successfully",
  "data": {
    "transaction": {...},
    "new_balance": 50.50
  }
}
```

## Service Usage

### Creating a Wallet
```php
use App\Modules\Wallet\Services\WalletService;
use App\Models\User;

$walletService = app(WalletService::class);
$user = User::find(1);

$wallet = $walletService->createWallet($user);
```

### Checking Balance
```php
$balance = $walletService->getBalance($user);
```

### Depositing Funds
```php
$transaction = $walletService->deposit(
    $user,
    100.50,
    'Payment from order #123'
);
```

### Withdrawing Funds
```php
try {
    $transaction = $walletService->withdraw(
        $user,
        50.00,
        'Withdrawal to bank account'
    );
} catch (Exception $e) {
    // Handle insufficient balance or frozen wallet
}
```

### Transferring Between Users
```php
$fromUser = User::find(1);
$toUser = User::find(2);

try {
    $result = $walletService->transfer($fromUser, $toUser, 75.00);
    // $result contains both 'withdrawal' and 'deposit' transactions
} catch (Exception $e) {
    // Handle errors (insufficient balance, frozen wallet, etc.)
}
```

## Model Relationships

### User Model
```php
$user = User::find(1);
$wallet = $user->wallet; // Access user's wallet
```

### Wallet Model
```php
$wallet = Wallet::find(1);
$user = $wallet->user; // Access wallet owner
$transactions = $wallet->transactions; // Access all transactions
```

### WalletTransaction Model
```php
$transaction = WalletTransaction::find(1);
$wallet = $transaction->wallet; // Access related wallet
```

## Error Handling

The WalletService throws exceptions for various error conditions:

- `Deposit amount must be greater than zero.`
- `Withdrawal amount must be greater than zero.`
- `Transfer amount must be greater than zero.`
- `Wallet is frozen.`
- `Insufficient balance.`
- `Cannot transfer to the same user.`

Always wrap service calls in try-catch blocks:

```php
try {
    $transaction = $walletService->withdraw($user, 100, 'Test');
} catch (Exception $e) {
    Log::error('Wallet operation failed: ' . $e->getMessage());
    return response()->json(['error' => $e->getMessage()], 400);
}
```

## Transaction Types

- **deposit**: Funds added to wallet
- **withdrawal**: Funds removed from wallet (status: pending)
- **payment**: Payment made from wallet (e.g., for orders)
- **refund**: Refund added to wallet
- **commission**: Commission earned by seller

## Security Features

- All API endpoints require Sanctum authentication
- Wallets can be frozen to prevent transactions
- All operations use database transactions for atomicity
- Amount validation prevents negative or zero values
- Balance checks prevent overdrafts

## Testing

Run tests with:

```bash
# Run all wallet tests
php artisan test --filter=Wallet

# Run unit tests only
php artisan test --filter=WalletServiceTest

# Run feature tests only
php artisan test --filter=WalletFlowTest
```

## Module Structure

```
app/Modules/Wallet/
├── Controllers/
│   └── WalletController.php
├── Database/
│   └── Migrations/
│       ├── 2025_12_02_200000_create_wallets_table.php
│       └── 2025_12_02_200001_create_wallet_transactions_table.php
├── Models/
│   ├── Wallet.php
│   └── WalletTransaction.php
├── Routes/
│   └── api.php
├── Services/
│   └── WalletService.php
└── WalletServiceProvider.php
```

## Future Enhancements

Potential features to add:

- [ ] Multi-currency conversion
- [ ] Transaction approval workflow
- [ ] Scheduled withdrawals
- [ ] Transaction fees/limits
- [ ] Email notifications for transactions
- [ ] Export transaction history
- [ ] Admin dashboard for wallet management
- [ ] Recurring deposits/subscriptions
