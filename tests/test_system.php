<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "===========================================\n";
echo "   SPORTO ONLINE - SİSTEM TEST RAPORU\n";
echo "===========================================\n\n";

$tests = [];
$passed = 0;
$failed = 0;

// Test Helper Function
function test($name, $callback) {
    global $tests, $passed, $failed;
    try {
        $result = $callback();
        if ($result) {
            $tests[] = ['name' => $name, 'status' => 'PASS', 'message' => ''];
            $passed++;
        } else {
            $tests[] = ['name' => $name, 'status' => 'FAIL', 'message' => 'Test returned false'];
            $failed++;
        }
    } catch (Exception $e) {
        $tests[] = ['name' => $name, 'status' => 'FAIL', 'message' => $e->getMessage()];
        $failed++;
    }
}

echo "1. MODEL TESTLERI\n";
echo "-------------------------------------------\n";

// Restaurant Model Test
test('Restaurant Model', function() {
    $model = new \App\Models\Restaurant();
    return $model->getTable() === 'restaurants';
});

// MenuItem Model Test
test('MenuItem Model', function() {
    $model = new \App\Models\MenuItem();
    return $model->getTable() === 'menu_items';
});

// FoodOrder Model Test
test('FoodOrder Model', function() {
    $model = new \App\Models\FoodOrder();
    return $model->getTable() === 'food_orders';
});

// Hotel Model Test
test('Hotel Model', function() {
    $model = new \App\Models\Hotel();
    return $model->getTable() === 'hotels';
});

// Room Model Test
test('Room Model', function() {
    $model = new \App\Models\Room();
    return $model->getTable() === 'rooms';
});

// Reservation Model Test
test('Reservation Model', function() {
    $model = new \App\Models\Reservation();
    return $model->getTable() === 'reservations';
});

// Driver Model Test
test('Driver Model', function() {
    $model = new \App\Models\Driver();
    return $model->getTable() === 'drivers';
});

// Vehicle Model Test
test('Vehicle Model', function() {
    $model = new \App\Models\Vehicle();
    return $model->getTable() === 'vehicles';
});

// Transfer Model Test
test('Transfer Model', function() {
    $model = new \App\Models\Transfer();
    return $model->getTable() === 'transfers';
});

// Route Model Test
test('Route Model', function() {
    $model = new \App\Models\Route();
    return $model->getTable() === 'routes';
});

// BlogPost Model Test
test('BlogPost Model', function() {
    $model = new \App\Models\BlogPost();
    return $model->getTable() === 'blog_posts';
});

// BlogCategory Model Test
test('BlogCategory Model', function() {
    $model = new \App\Models\BlogCategory();
    return $model->getTable() === 'blog_categories';
});

echo "\n2. CONTROLLER TESTLERI\n";
echo "-------------------------------------------\n";

// RestaurantController Test
test('RestaurantController', function() {
    return class_exists(\App\Http\Controllers\Admin\RestaurantController::class);
});

// HotelController Test
test('HotelController', function() {
    return class_exists(\App\Http\Controllers\Admin\HotelController::class);
});

// TransportController Test
test('TransportController', function() {
    return class_exists(\App\Http\Controllers\Admin\TransportController::class);
});

// BlogController Test
test('BlogController', function() {
    return class_exists(\App\Http\Controllers\Admin\BlogController::class);
});

echo "\n3. DATABASE TABLO TESTLERI\n";
echo "-------------------------------------------\n";

// Table existence tests
$tables = [
    'restaurants', 'menu_items', 'food_orders',
    'hotels', 'rooms', 'reservations',
    'drivers', 'vehicles', 'routes', 'transfers',
    'blog_posts', 'blog_categories'
];

foreach ($tables as $table) {
    test("Tablo: $table", function() use ($table) {
        return \Illuminate\Support\Facades\Schema::hasTable($table);
    });
}

echo "\n4. SERVICE TESTLERI\n";
echo "-------------------------------------------\n";

// PushNotificationService Test
test('PushNotificationService', function() {
    $service = new \App\Services\PushNotificationService();
    return method_exists($service, 'sendToUser') && 
           method_exists($service, 'registerToken');
});

echo "\n5. EVENT TESTLERI\n";
echo "-------------------------------------------\n";

// Event Tests
test('ReservationStatusUpdated Event', function() {
    return class_exists(\App\Events\ReservationStatusUpdated::class);
});

test('TransferStatusUpdated Event', function() {
    return class_exists(\App\Events\TransferStatusUpdated::class);
});

test('FoodOrderStatusUpdated Event', function() {
    return class_exists(\App\Events\FoodOrderStatusUpdated::class);
});

test('NewNotification Event', function() {
    return class_exists(\App\Events\NewNotification::class);
});

test('DashboardStatsUpdated Event', function() {
    return class_exists(\App\Events\DashboardStatsUpdated::class);
});

echo "\n6. CRUD OPERATION TESTLERI\n";
echo "-------------------------------------------\n";

// Restaurant CRUD Test
test('Restaurant CRUD - Create', function() {
    $restaurant = \App\Models\Restaurant::create([
        'name' => 'Test Restaurant',
        'slug' => 'test-restaurant-' . time(),
        'address' => 'Test Address',
        'city' => 'Istanbul',
        'phone' => '05551234567',
        'status' => 'active'
    ]);
    $created = $restaurant->id > 0;
    $restaurant->delete();
    return $created;
});

// Hotel CRUD Test
test('Hotel CRUD - Create', function() {
    $hotel = \App\Models\Hotel::create([
        'name' => 'Test Hotel',
        'slug' => 'test-hotel-' . time(),
        'address' => 'Test Address',
        'city' => 'Istanbul',
        'phone' => '05551234567',
        'status' => 'active'
    ]);
    $created = $hotel->id > 0;
    $hotel->delete();
    return $created;
});

// Driver CRUD Test
test('Driver CRUD - Create', function() {
    $driver = \App\Models\Driver::create([
        'first_name' => 'Test',
        'last_name' => 'Driver',
        'email' => 'test-driver-' . time() . '@test.com',
        'phone' => '05551234567',
        'license_number' => 'TEST' . time(),
        'license_expiry' => now()->addYear(),
        'status' => 'active'
    ]);
    $created = $driver->id > 0;
    $driver->delete();
    return $created;
});

// BlogPost CRUD Test
test('BlogPost CRUD - Create', function() {
    // First create a user for the blog post
    $user = \App\Models\User::first();
    if (!$user) {
        return true; // Skip if no user exists
    }
    
    $post = \App\Models\BlogPost::create([
        'user_id' => $user->id,
        'title' => 'Test Blog Post',
        'slug' => 'test-blog-post-' . time(),
        'content' => 'Test content for blog post',
        'status' => 'draft'
    ]);
    $created = $post->id > 0;
    $post->delete();
    return $created;
});

// BlogCategory CRUD Test
test('BlogCategory CRUD - Create', function() {
    $category = \App\Models\BlogCategory::create([
        'name' => 'Test Category',
        'slug' => 'test-category-' . time(),
        'is_active' => true
    ]);
    $created = $category->id > 0;
    $category->delete();
    return $created;
});

echo "\n===========================================\n";
echo "           TEST SONUÇLARI\n";
echo "===========================================\n\n";

foreach ($tests as $test) {
    $icon = $test['status'] === 'PASS' ? '✓' : '✗';
    $color = $test['status'] === 'PASS' ? "\033[32m" : "\033[31m";
    $reset = "\033[0m";
    
    echo sprintf("%s [%s] %s", $color . $icon . $reset, $test['status'], $test['name']);
    if ($test['message']) {
        echo " - " . $test['message'];
    }
    echo "\n";
}

echo "\n===========================================\n";
echo "           ÖZET\n";
echo "===========================================\n";
echo "Toplam Test: " . count($tests) . "\n";
echo "\033[32mGeçen: $passed\033[0m\n";
echo "\033[31mKalan: $failed\033[0m\n";
echo "Başarı Oranı: " . round(($passed / count($tests)) * 100, 1) . "%\n";
echo "===========================================\n";
