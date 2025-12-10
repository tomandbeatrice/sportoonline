<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminActivityController extends Controller
{
    public function index()
    {
        // Mock data for now, to be replaced with DB call
        $activities = [
            [
                'id' => 1,
                'name' => 'Yamaç Paraşütü',
                'category' => 'Ekstrem Sporlar',
                'difficulty' => 'Orta',
                'price' => 2500,
                'rating' => 4.9,
                'reviews' => 450,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1476611317561-60e1b778edfb?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'description' => 'Ölüdeniz\'in muhteşem manzarası eşliğinde, profesyonel pilotlar ile güvenli uçuş deneyimi.',
                'gear' => ['Kask', 'Uçuş Tulumu', 'GoPro']
            ],
            [
                'id' => 2,
                'name' => 'Rafting Macerası',
                'category' => 'Su Sporları',
                'difficulty' => 'Zor',
                'price' => 850,
                'rating' => 4.7,
                'reviews' => 320,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1530866495561-507c9faab2ed?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'description' => 'Köprülü Kanyon\'un serin sularında, takım ruhunu geliştiren heyecan dolu rafting turu.',
                'gear' => ['Can Yeleği', 'Kask', 'Kürek', 'Neopren Kıyafet']
            ],
            [
                'id' => 3,
                'name' => 'ATV Safari',
                'category' => 'Doğa Sporları',
                'difficulty' => 'Kolay',
                'price' => 600,
                'rating' => 4.5,
                'reviews' => 280,
                'status' => 'active',
                'image' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'description' => 'Tozlu yollarda, çamurlu parkurlarda doğa ile iç içe, adrenalin dolu ATV sürüş keyfi.',
                'gear' => ['Kask', 'Eldiven', 'Gözlük']
            ],
            [
                'id' => 4,
                'name' => 'Dalış Eğitimi',
                'category' => 'Su Sporları',
                'difficulty' => 'Orta',
                'price' => 1500,
                'rating' => 4.8,
                'reviews' => 150,
                'status' => 'pending',
                'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80',
                'description' => 'Sualtı dünyasının gizemlerini keşfetmek isteyenler için başlangıç seviyesi dalış eğitimi.',
                'gear' => ['Dalış Tüpü', 'Maske', 'Palet', 'Denge Yeleği']
            ]
        ];

        return response()->json([
            'data' => $activities
        ]);
    }

    public function stats()
    {
        return response()->json([
            'active_activities' => 32,
            'daily_participation' => 180,
            'safety_score' => 9.8,
            'revenue' => '150K'
        ]);
    }
}
