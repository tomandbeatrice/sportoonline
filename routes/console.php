<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

// İlham verici mesaj
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Son siparişi göster
Artisan::command('orders:latest', function () {
    $order = \App\Models\Order::latest()->with('user')->first();

    if (!$order) {
        $this->warn('Sipariş bulunamadı.');
        return;
    }

    $this->info("🧾 Sipariş ID: {$order->id}");
    $this->info("👤 Kullanıcı: {$order->user->name}");
    $this->info("💳 Durum: {$order->status}");
    $this->info("💰 Toplam: ₺" . number_format($order->total_price, 2));
    $this->info("📅 Tarih: " . $order->created_at->format('d.m.Y H:i'));
})->purpose('Son siparişi terminalde göster');

// Toplam sipariş sayısını göster
Artisan::command('orders:count', function () {
    $count = DB::table('orders')->count();
    $this->info("🧮 Toplam Sipariş Sayısı: {$count}");
})->purpose('Veritabanındaki toplam sipariş adedini göster');

Artisan::command('test:storage', function () {
    Storage::put('exports/logs/test_log.json', json_encode(['test' => true]));
    $this->info('Test log dosyası oluşturuldu.');
});

Artisan::command('test:storage-path', function () {
    $path = Storage::path('exports/logs/test_log.json');
    $this->info("Laravel dosyayı buraya yazmaya çalışıyor:");
    $this->line($path);
});