<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Campaign;
use App\Models\Product;
use App\Models\UserTask;
use App\Models\Category;

class MarketplaceController extends Controller
{
    /**
     * Get aggregated data for the marketplace homepage.
     */
    public function index()
    {
        return response()->json([
            'banners' => Banner::where('is_active', true)->orderBy('order')->get(),
            'campaigns' => Campaign::where('status', 'active')->take(5)->get(),
            'featured_products' => Product::where('is_active', true)->inRandomOrder()->take(10)->get(),
            'categories' => Category::where('parent_id', null)->get(),
            'tasks' => auth()->check() ? UserTask::where('user_id', auth()->id())->where('status', 'pending')->get() : [],
        ]);
    }

    /**
     * Get active campaigns.
     */
    public function campaigns()
    {
        $campaigns = Campaign::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'description' => $campaign->description,
                    'image' => $campaign->image_url ?? 'https://placehold.co/600x400?text=Campaign',
                    'colorFrom' => $campaign->color_from ?? '#f59e0b',
                    'colorTo' => $campaign->color_to ?? '#ef4444',
                    'endDate' => $campaign->end_date,
                    'discount_rate' => $campaign->discount_rate,
                ];
            });

        return response()->json($campaigns);
    }

    /**
     * Get active banners.
     */
    public function banners()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'image' => $banner->image_url,
                    'title' => $banner->title,
                    'link' => $banner->link,
                    'button_text' => $banner->button_text,
                ];
            });

        return response()->json($banners);
    }

    /**
     * Get daily tasks for the user.
     */
    public function tasks()
    {
        // Return real user tasks from database if authenticated
        if (auth()->check()) {
            $tasks = UserTask::where('user_id', auth()->id())
                ->orderBy('due_date', 'asc')
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'title' => $task->title,
                        'is_completed' => $task->is_completed,
                        'due_date' => $task->due_date,
                        'icon' => '📌',
                        'color' => $task->is_completed ? 'bg-slate-50 text-slate-400' : 'bg-indigo-100 text-indigo-600'
                    ];
                });

            // If user has tasks, return them
            if ($tasks->count() > 0) {
                return response()->json($tasks);
            }
        }

        // Return mock tasks for guests or users with no tasks
        $tasks = [
            [
                'id' => 1,
                'title' => 'Profilini Tamamla',
                'description' => 'Profil bilgilerini %100 doldur',
                'reward' => 50,
                'progress' => 75,
                'status' => 'pending',
                'icon' => '👤'
            ],
            [
                'id' => 2,
                'title' => 'İlk Siparişini Ver',
                'description' => 'Marketplace\'den ilk alışverişini yap',
                'reward' => 100,
                'progress' => 0,
                'status' => 'pending',
                'icon' => '🛍️'
            ],
            [
                'id' => 3,
                'title' => '3 Ürün Favorile',
                'description' => 'Beğendiğin 3 ürünü favorilerine ekle',
                'reward' => 25,
                'progress' => 33,
                'status' => 'pending',
                'icon' => '❤️'
            ]
        ];

        if (auth()->check()) {
            // In a real scenario, fetch from DB
            // $tasks = UserTask::where('user_id', auth()->id())->get();
        }

        return response()->json($tasks);
    }

    /**
     * Get product bundles.
     */
    public function bundles()
    {
        // Mock bundles for now
        $bundles = [
            [
                'id' => 1,
                'title' => 'Başlangıç Paketi',
                'description' => 'Spor yapmaya yeni başlayanlar için',
                'price' => 1250,
                'original_price' => 1500,
                'image' => 'https://placehold.co/400x300?text=Bundle+1',
                'products' => ['Mat', 'Dambıl', 'Su Matarası']
            ],
            [
                'id' => 2,
                'title' => 'Koşu Paketi',
                'description' => 'Profesyonel koşucular için set',
                'price' => 3500,
                'original_price' => 4200,
                'image' => 'https://placehold.co/400x300?text=Bundle+2',
                'products' => ['Koşu Ayakkabısı', 'Şort', 'T-Shirt']
            ]
        ];

        return response()->json($bundles);
    }
}
