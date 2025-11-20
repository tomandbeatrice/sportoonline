<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BulkProductController extends Controller
{
    /**
     * CSV ile toplu ürün yükleme
     * Formatı: name,description,price,stock,category_id,sku
     */
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $file = $request->file('csv_file');
        $products = [];
        $errors = [];
        $successCount = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle); // İlk satır başlık
            
            $row = 0;
            while (($data = fgetcsv($handle)) !== false) {
                $row++;
                
                try {
                    $product = Product::create([
                        'user_id' => auth()->id(),
                        'name' => $data[0],
                        'description' => $data[1],
                        'price' => (float) $data[2],
                        'stock' => (int) $data[3],
                        'category_id' => (int) $data[4],
                        'sku' => $data[5] ?? Str::random(10),
                        'is_active' => true,
                    ]);
                    
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Satır $row: " . $e->getMessage();
                }
            }
            
            fclose($handle);
        }

        return response()->json([
            'success' => true,
            'message' => "$successCount ürün başarıyla yüklendi",
            'total_rows' => $row,
            'success_count' => $successCount,
            'errors' => $errors,
        ]);
    }

    /**
     * Excel ile toplu ürün yükleme (PhpSpreadsheet kullanarak)
     */
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:20480', // 20MB max
        ]);

        // PhpSpreadsheet ile okuma yapılabilir
        return response()->json([
            'message' => 'Excel yükleme henüz aktif değil. CSV kullanın.',
        ], 501);
    }

    /**
     * Toplu ürün güncelleme
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'products' => 'required|array|max:1000', // Tek seferde max 1000 ürün
            'products.*.id' => 'required|exists:products,id',
            'products.*.price' => 'sometimes|numeric|min:0',
            'products.*.stock' => 'sometimes|integer|min:0',
            'products.*.is_active' => 'sometimes|boolean',
        ]);

        $updated = 0;
        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            
            if ($product->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
                continue; // Sadece kendi ürünlerini güncelleyebilir
            }
            
            $product->update(array_filter($productData, fn($key) => $key !== 'id', ARRAY_FILTER_USE_KEY));
            $updated++;
        }

        return response()->json([
            'success' => true,
            'message' => "$updated ürün güncellendi",
        ]);
    }

    /**
     * Toplu ürün silme
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|max:1000',
            'product_ids.*' => 'required|exists:products,id',
        ]);

        $deleted = Product::whereIn('id', $request->product_ids)
            ->where(function($query) {
                $query->where('user_id', auth()->id())
                    ->orWhereHas('user', fn($q) => $q->where('role', 'admin'));
            })
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "$deleted ürün silindi",
        ]);
    }

    /**
     * Satıcı ürün limiti kontrolü
     */
    public function checkProductLimit()
    {
        $user = auth()->user();
        $currentCount = Product::where('user_id', $user->id)->count();

        // Satıcı planına göre limit
        $limits = [
            'basic' => 100,
            'premium' => 1000,
            'enterprise' => 10000,
            'unlimited' => PHP_INT_MAX,
        ];

        $plan = $user->subscription_plan ?? 'basic';
        $limit = $limits[$plan] ?? 100;

        return response()->json([
            'current_count' => $currentCount,
            'limit' => $limit,
            'remaining' => max(0, $limit - $currentCount),
            'plan' => $plan,
            'percentage_used' => round(($currentCount / $limit) * 100, 2),
        ]);
    }
}
