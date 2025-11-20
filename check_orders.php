<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Order;

echo "Today is: " . today()->toDateTimeString() . "\n";

$count = DB::table('orders')->whereDate('created_at', today())->count();
echo "Orders today: " . $count . "\n";

$sum = DB::table('orders')->whereDate('created_at', today())->sum('total_price');
echo "Revenue today: " . $sum . "\n";

$allOrders = Order::orderBy('created_at', 'desc')->take(5)->get();
echo "Last 5 orders:\n";
foreach ($allOrders as $order) {
    echo "ID: " . $order->id . " - Created: " . $order->created_at . " - Total: " . $order->total_price . "\n";
}
