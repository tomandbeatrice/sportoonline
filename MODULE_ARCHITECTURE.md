# Sportoonline - Modular Architecture

## 🏗️ Modular Structure

Proje modüler mimariye geçirilmiştir. Her modül kendi Controllers, Services, Models ve Routes yapısına sahiptir.

### Module Structure

```
app/Modules/
├── Ecommerce/
│   ├── EcommerceServiceProvider.php
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   │   ├── ProductService.php
│   │   └── OrderService.php
│   ├── Routes/
│   │   └── ecommerce.php
│   └── Requests/
├── Campaign/
│   ├── CampaignServiceProvider.php
│   ├── Services/
│   │   └── CampaignService.php
│   └── Controllers/
└── Seller/
    ├── SellerServiceProvider.php
    ├── Services/
    │   └── SellerService.php
    └── Controllers/
```

## 🚀 Quick Start

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 3. Start Services

```bash
# Docker services (MySQL, Redis, MailHog, MinIO)
docker-compose up -d

# Laravel development server
php artisan serve

# Vite dev server
npm run dev
```

### 4. Access Application

- **Application**: http://localhost:8000
- **Vite Frontend**: http://localhost:5173
- **API Documentation**: http://localhost:8000/api/documentation
- **MailHog**: http://localhost:8025
- **MinIO Console**: http://localhost:8900

## 📚 API Documentation

Swagger/OpenAPI documentation is available at `/api/documentation`

### Generate Documentation

```bash
php artisan l5-swagger:generate
```

### Example API Calls

```bash
# Get all products (cached for 1 hour)
curl http://localhost:8000/api/v1/products

# Get product by ID (cached for 1 hour)
curl http://localhost:8000/api/v1/products/1

# Get categories (cached for 2 hours)
curl http://localhost:8000/api/v1/categories
```

## ⚡ Cache Strategy

### Redis Configuration

All GET requests are automatically cached using Redis:

- **Products**: 1 hour (3600s)
- **Categories**: 2 hours (7200s)
- **Campaigns**: 15 minutes (900s)
- **Sellers**: 30 minutes (1800s)

### Cache Middleware Usage

```php
Route::get('/products', [ProductController::class, 'index'])
    ->middleware('cache.response:3600'); // Cache for 1 hour
```

### Clear Cache

```bash
php artisan cache:clear
```

## 🧪 Testing

### Run All Tests

```bash
php artisan test
```

### Run with Coverage

```bash
php artisan test --coverage --min=50
```

### Run Specific Test

```bash
php artisan test --filter=CheckoutFlowTest
```

## 🔧 Module Development

### Create New Module

1. Create module structure:
```bash
mkdir -p app/Modules/YourModule/{Controllers,Services,Models,Routes}
```

2. Create Service Provider:
```php
// app/Modules/YourModule/YourModuleServiceProvider.php
namespace App\Modules\YourModule;

use Illuminate\Support\ServiceProvider;

class YourModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(YourService::class);
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/Routes/yourmodule.php');
    }
}
```

3. Register in `config/app.php`:
```php
'providers' => [
    // ...
    App\Modules\YourModule\YourModuleServiceProvider::class,
],
```

## 📦 Available Modules

### Ecommerce Module
- **Services**: ProductService, OrderService
- **Features**: Product management, Order processing, Stock management
- **Routes**: `/api/v1/products`, `/api/v1/orders`, `/api/v1/cart`

### Campaign Module
- **Services**: CampaignService
- **Features**: Campaign management, Active campaigns
- **Cache**: 15 minutes TTL

### Seller Module
- **Services**: SellerService
- **Features**: Seller dashboard, Application management
- **Cache**: 30 minutes TTL

### Ride Module
- **Services**: TransferService
- **Features**: Transfer/Ride suggestions, Hotel booking cross-sell, Transfer bookings
- **Routes**: `/api/v1/transfers/suggestions`, `/api/v1/transfers/available`, `/api/v1/transfers/book`
- **Cross-Sell**: Automatically suggests airport transfers after hotel bookings

## 🔐 Payment Gateways

All payment services implement `PaymentGatewayInterface`:

- **Iyzico**: `App\Services\IyzicoService`
- **Stripe**: `App\Services\StripeService`
- **PayTR**: `App\Services\PaytrService`

### Usage Example

```php
use App\Contracts\PaymentGatewayInterface;
use App\Services\StripeService;

$gateway = app(StripeService::class);
$result = $gateway->initiatePayment($order, $customerData);
```

## 📊 Performance Optimizations

### Route Caching
```bash
php artisan route:cache
```

### Config Caching
```bash
php artisan config:cache
```

### View Caching
```bash
php artisan view:cache
```

### Optimize Autoloader
```bash
composer dump-autoload --optimize
```

## 🔄 Laravel 11 Upgrade

Upgrade guide available at: `docs/LARAVEL_11_UPGRADE.md`

### Timeline
- **Now**: Test coverage improvements
- **1 Month**: Development environment testing
- **2 Months**: Staging deployment
- **3 Months**: Production deployment

## 📝 Git Workflow

### Commits
```bash
git add .
git commit -m "feat: Your feature description"
git push origin main
```

### CI/CD Pipeline

GitHub Actions automatically runs:
- Frontend tests (npm test)
- Backend tests (phpunit)
- Static analysis (phpstan)
- Security audits

## 🆘 Troubleshooting

### Clear All Caches
```bash
php artisan optimize:clear
```

### Regenerate Autoload
```bash
composer dump-autoload
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

## 📞 Support

For issues and questions, please create an issue on GitHub or contact the development team.
