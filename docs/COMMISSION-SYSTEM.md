# Commission System Documentation

## Overview
The commission system integrates subscription plans with sales commissions, allowing sellers to pay a monthly fee plus a percentage commission on sales. This hybrid pricing model incentivizes both platform commitment and sales performance.

## Pricing Model

### Subscription Plans with Commission Rates

| Plan | Monthly Fee | Commission Rate | Product Limit | Best For |
|------|-------------|----------------|---------------|----------|
| **Basic** | ₺0 | 15% | 100 | New sellers testing the platform |
| **Premium** | ₺99 | 12% | 1,000 | Growing sellers with steady sales |
| **Enterprise** | ₺499 | 10% | 10,000 | Established sellers with high volume |
| **Unlimited** | ₺999 | 8% | 999,999 | Large enterprises and wholesalers |

### How It Works

**Monthly Commission Calculation:**
```
Net Commission = (Total Sales × Commission Rate) - Subscription Fee
```

**Example (Premium Plan):**
- Monthly sales: ₺10,000
- Commission rate: 12%
- Gross commission: ₺10,000 × 0.12 = ₺1,200
- Subscription fee: ₺99
- **Net commission: ₺1,200 - ₺99 = ₺1,101**

**Break-Even Points:**
- Basic (₺0/month + 15%): ₺0 (always profitable)
- Premium (₺99/month + 12%): ₺825 in sales
- Enterprise (₺499/month + 10%): ₺4,990 in sales
- Unlimited (₺999/month + 8%): ₺12,488 in sales

## Database Schema

### Monthly Commissions Table
```sql
CREATE TABLE monthly_commissions (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    subscription_id BIGINT,
    month VARCHAR(7), -- YYYY-MM format
    total_sales DECIMAL(12,2),
    commission_rate DECIMAL(5,2),
    commission_amount DECIMAL(10,2),
    subscription_fee DECIMAL(10,2),
    net_commission DECIMAL(10,2),
    order_count INTEGER,
    status ENUM('pending', 'calculated', 'paid'),
    paid_at DATE,
    UNIQUE(user_id, month)
);
```

### Subscription Enhancement
```sql
ALTER TABLE subscriptions ADD COLUMN commission_rate DECIMAL(5,2);
ALTER TABLE subscriptions ADD COLUMN monthly_commission_cap DECIMAL(10,2);
ALTER TABLE subscriptions ADD COLUMN commission_settings JSON;
```

### Order Item Tracking
```sql
ALTER TABLE order_items ADD COLUMN subscription_commission_rate DECIMAL(5,2);
ALTER TABLE order_items ADD COLUMN subscription_commission_amount DECIMAL(10,2);
```

### Subscription Payment Enhancement
```sql
ALTER TABLE subscription_payments ADD COLUMN subscription_month VARCHAR(7);
ALTER TABLE subscription_payments ADD COLUMN commission_rate DECIMAL(5,2);
```

## API Endpoints

### Seller Commission Endpoints

#### Get Commission History
```http
GET /api/seller/commissions?year=2025
Authorization: Bearer {token}

Response:
{
  "year": 2025,
  "total_sales": 120000.00,
  "total_gross_commission": 14400.00,
  "total_subscription_fees": 1188.00,
  "total_net_commission": 13212.00,
  "months_active": 12,
  "monthly_breakdown": [...]
}
```

#### Get Current Month Summary
```http
GET /api/seller/commissions/current
Authorization: Bearer {token}

Response:
{
  "has_subscription": true,
  "plan_name": "Premium",
  "subscription_fee": 99.00,
  "commission_rate": 12.00,
  "month": "2025-11",
  "total_sales": 10000.00,
  "gross_commission": 1200.00,
  "subscription_fee_deducted": 99.00,
  "net_commission": 1101.00,
  "order_count": 45,
  "status": "calculated"
}
```

#### Get Commission Forecast
```http
GET /api/seller/commissions/forecast
Authorization: Bearer {token}

Response:
{
  "current_month": "2025-11",
  "days_elapsed": 15,
  "days_remaining": 15,
  "current_sales": 5000.00,
  "projected_sales": 10000.00,
  "current_commission": 600.00,
  "projected_commission": 1200.00,
  "subscription_fee": 99.00,
  "current_net": 501.00,
  "projected_net": 1101.00,
  "daily_average": 333.33
}
```

#### Compare Plans
```http
GET /api/seller/commissions/compare-plans?estimated_sales=10000
Authorization: Bearer {token}

Response:
{
  "estimated_sales": 10000.00,
  "recommended": {
    "recommended_plan": "Premium",
    "reason": "₺10000 aylık satış için en karlı seçenek",
    "net_benefit": 1101.00
  },
  "all_plans": [
    {
      "plan": "Premium",
      "monthly_fee": 99.00,
      "commission_rate": 12.00,
      "gross_commission": 1200.00,
      "net_commission": 1101.00,
      "effective_commission_rate": 12.99
    },
    ...
  ]
}
```

#### Get Specific Month Details
```http
GET /api/seller/commissions/2025-11
Authorization: Bearer {token}

Response: Same as current month summary
```

### Admin Commission Endpoints

#### List All Sellers' Commissions
```http
GET /api/admin/commissions?month=2025-11&status=calculated
Authorization: Bearer {admin-token}

Response:
{
  "month": "2025-11",
  "commissions": {
    "data": [...],
    "current_page": 1,
    "per_page": 20
  },
  "summary": {
    "total_sales": 500000.00,
    "total_commission": 55000.00,
    "total_subscription_fees": 5000.00,
    "total_net_commission": 50000.00,
    "seller_count": 50
  }
}
```

#### Get Seller Details
```http
GET /api/admin/commissions/{userId}/{month}
Authorization: Bearer {admin-token}

Response:
{
  "seller": {
    "id": 123,
    "name": "John Doe",
    "email": "john@example.com"
  },
  "month": "2025-11",
  "summary": {...},
  "commission_record": {...}
}
```

#### Mark Commission as Paid
```http
POST /api/admin/commissions/{commissionId}/mark-paid
Authorization: Bearer {admin-token}

Response:
{
  "message": "Komisyon ödendi olarak işaretlendi",
  "commission": {...}
}
```

#### Calculate Month Commissions
```http
POST /api/admin/commissions/calculate-month?month=2025-10
Authorization: Bearer {admin-token}

Response:
{
  "month": "2025-10",
  "calculated": 45,
  "errors": 2,
  "results": [...],
  "error_details": [...]
}
```

#### Get Statistics
```http
GET /api/admin/commissions/statistics?year=2025
Authorization: Bearer {admin-token}

Response:
{
  "year": 2025,
  "monthly_breakdown": [...],
  "top_sellers": [...]
}
```

## Commission Service

### Usage Examples

```php
use App\Services\CommissionService;

$commissionService = app(CommissionService::class);

// Calculate order item commission
$commission = $commissionService->calculateOrderItemCommission($orderItem);
// Returns: ['rate' => 12.00, 'amount' => 120.00, 'subscription_plan' => 'Premium']

// Get seller's active subscription
$subscription = $commissionService->getActiveSubscription($seller);

// Calculate monthly commission
$monthlyCommission = $commissionService->calculateMonthlyCommission($seller, '2025-11');

// Get seller commission summary
$summary = $commissionService->getSellerCommissionSummary($seller, '2025-11');

// Get yearly summary
$yearly = $commissionService->getYearlyCommissionSummary($seller, 2025);

// Compare plans
$comparison = $commissionService->compareCommissionPlans(10000.00);

// Get recommended plan
$recommended = $commissionService->getRecommendedPlan(10000.00);
```

## Frontend Components

### Seller Commission Dashboard
```vue
<template>
  <SellerCommissionDashboard />
</template>

<script>
import SellerCommissionDashboard from '@/components/seller/SellerCommissionDashboard.vue';

export default {
  components: {
    SellerCommissionDashboard
  }
}
</script>
```

**Features:**
- Current month summary with status
- Monthly forecast based on daily average
- Yearly breakdown with statistics
- Plan comparison tool with slider
- Visual charts and progress bars

## Automated Commission Calculation

### Artisan Command
```bash
# Calculate commissions for previous month
php artisan commissions:calculate

# Calculate for specific month
php artisan commissions:calculate --month=2025-10
```

### Scheduled Task
The command runs automatically on the 1st of each month at 00:00:

```php
// app/Console/Kernel.php
$schedule->command('commissions:calculate')->monthlyOn(1, '00:00');
```

### Manual Cron Setup
```bash
# Add to crontab
0 0 1 * * cd /path/to/sportoonline && php artisan commissions:calculate
```

## Order Integration

When an order is created, the commission is automatically tracked:

```php
// In OrderController::store()
$subscription = auth()->user()->subscriptions()->where('status', 'active')->first();

foreach ($orderItems as $item) {
    $item->subscription_commission_rate = $subscription->commission_rate;
    $item->subscription_commission_amount = $item->subtotal * ($subscription->commission_rate / 100);
    $item->save();
}
```

## Commission Workflow

### 1. Subscription Creation
```
Seller subscribes to Premium plan
↓
Subscription created with commission_rate = 12.00
↓
Subscription active, seller can make sales
```

### 2. Sales Period
```
Seller makes sales throughout the month
↓
Each order_item stores:
  - subscription_commission_rate = 12.00
  - subscription_commission_amount = subtotal × 0.12
↓
Sales tracked in real-time
```

### 3. Month End Calculation
```
On 1st of next month at 00:00
↓
Artisan command runs: commissions:calculate
↓
For each active seller:
  - Sum all order_items for previous month
  - Calculate total_sales
  - Calculate commission_amount = total_sales × commission_rate
  - Calculate net_commission = commission_amount - subscription_fee
  - Create monthly_commissions record with status='calculated'
```

### 4. Payment Processing
```
Admin reviews calculated commissions
↓
Admin marks commission as paid
↓
Status changes to 'paid'
↓
paid_at timestamp set
↓
Seller receives payment notification
```

## Status Management

### Commission Statuses
- **pending**: Month not yet calculated
- **calculated**: Commission calculated, awaiting payment
- **paid**: Payment completed

### Status Transitions
```
pending → calculated (automated on month end)
calculated → paid (manual by admin)
```

## Business Logic

### Negative Net Commission
If a seller's sales don't cover the subscription fee:
```
Sales: ₺500
Commission (12%): ₺60
Subscription fee: ₺99
Net: ₺60 - ₺99 = -₺39
```

**Handling:**
- Record shows negative net_commission
- Admin can:
  - Waive the difference (promotional period)
  - Carry debt to next month
  - Charge seller's payment method
  - Pause subscription until payment

### Commission Rate Changes
When a seller upgrades/downgrades:
```
Day 1-15: Premium plan (12% commission)
Day 16-30: Enterprise plan (10% commission)
```

**Solution:**
- Each order_item stores commission_rate at sale time
- Monthly calculation uses actual rates applied
- No retroactive adjustments needed

### Prorated Subscriptions
If seller subscribes mid-month:
```
Subscribed on Nov 15 (half month)
Subscription fee: ₺99 / 2 = ₺49.50
Commission: Calculated normally on all sales from Nov 15-30
```

## Performance Optimization

### Indexing
```sql
CREATE INDEX idx_monthly_commissions_user_month ON monthly_commissions(user_id, month);
CREATE INDEX idx_order_items_commission ON order_items(subscription_commission_rate);
CREATE INDEX idx_subscriptions_active ON subscriptions(status, ends_at);
```

### Caching
```php
// Cache current month commission for 1 hour
$commission = Cache::remember("commission.{$userId}.{$month}", 3600, function() {
    return $commissionService->getSellerCommissionSummary($seller, $month);
});
```

### Query Optimization
```php
// Eager load relationships
MonthlyCommission::with(['user', 'subscription.plan'])
    ->where('month', $month)
    ->get();
```

## Testing

### Manual Testing
```bash
# 1. Create test seller with Premium subscription
php artisan tinker
> $user = User::find(1);
> $plan = SubscriptionPlan::where('slug', 'premium')->first();
> $subscription = Subscription::create([
    'user_id' => $user->id,
    'subscription_plan_id' => $plan->id,
    'commission_rate' => 12.00,
    'amount' => 99.00,
    'status' => 'active',
    'starts_at' => now(),
    'ends_at' => now()->addMonth(),
  ]);

# 2. Create test orders with commission tracking
> $order = Order::create([...]);
> $orderItem = OrderItem::create([
    'order_id' => $order->id,
    'product_id' => 1,
    'quantity' => 1,
    'price' => 1000.00,
    'subscription_commission_rate' => 12.00,
    'subscription_commission_amount' => 120.00,
  ]);

# 3. Calculate commission
php artisan commissions:calculate --month=2025-11

# 4. Verify in database
> MonthlyCommission::where('user_id', 1)->where('month', '2025-11')->first();
```

## Security

### Authorization
All commission endpoints require authentication:
```php
Route::middleware('auth:sanctum')->group(function () {
    // Seller can only view their own commissions
    Route::get('/seller/commissions', [SellerCommissionController::class, 'index']);
    
    // Admin can view all commissions
    Route::get('/admin/commissions', [AdminCommissionController::class, 'index']);
});
```

### Data Validation
```php
// Month format validation
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    return response()->json(['error' => 'Invalid month format'], 400);
}

// Amount validation
$request->validate([
    'estimated_sales' => 'required|numeric|min:0|max:1000000',
]);
```

## Future Enhancements

1. **Volume-Based Tiering**: Lower commission rates for high-volume months
2. **Performance Bonuses**: Extra rewards for exceeding sales targets
3. **Referral Commissions**: Earn commission on referred sellers' sales
4. **Payment Gateway Integration**: Automated payouts via Iyzico/Stripe
5. **Tax Reporting**: Automated tax forms and compliance
6. **Commission Disputes**: Appeal system for calculation errors
7. **Multi-Currency**: Support for international sellers
8. **Commission Caps**: Maximum commission per month/year
9. **A/B Testing**: Test different commission rates
10. **Predictive Analytics**: AI-powered commission forecasting

## Support

For questions or issues:
- **Seller Dashboard**: Access commission details and forecasts
- **Admin Panel**: Review and manage all commissions
- **API Documentation**: Detailed endpoint specifications
- **Command Line**: Run manual calculations when needed

## Changelog

### Version 1.0.0 (2025-11-19)
- Initial commission system implementation
- 4 subscription plans with hybrid pricing
- Monthly commission tracking
- Seller and admin dashboards
- Automated monthly calculation
- Comprehensive API endpoints
