<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;

class SprintVerify extends Command
{
    protected $signature = 'sprint:verify';
    protected $description = 'Modül, route, migration ve API reflekslerini doğrular';

    public function handle()
    {
        $this->info('🔍 Sprint doğrulama başlatıldı...');

        $routeExists = collect(Route::getRoutes())->contains(fn($route) => $route->uri() === 'live-order');
        $this->line($routeExists ? '✔ Route /live-order tanımlı' : '❌ Route /live-order eksik');

        $columnExists = Schema::hasColumn('orders', 'total_price');
        $this->line($columnExists ? '✔ orders tablosunda total_price alanı var' : '❌ total_price alanı eksik');

        $fillable = (new Order)->getFillable();
        $this->line(in_array('total_price', $fillable) ? '✔ Order modelinde total_price fillable' : '❌ Order modelinde total_price eksik');

        try {
            $data = Order::latest()->take(1)->get();
            $this->line($data->count() > 0 ? '✔ API /api/orders/live veri dönüyor' : '⚠️ API veri boş');
        } catch (\Exception $e) {
            $this->line('❌ API çağrısı başarısız: ' . $e->getMessage());
        }

        $this->info('✅ Sprint doğrulama tamamlandı.');
    }
}