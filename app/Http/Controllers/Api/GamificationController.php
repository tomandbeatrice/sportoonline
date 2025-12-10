<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    public function status(Request $request)
    {
        $user = $request->user();
        
        // Load badges if not loaded
        $user->load('badges');

        // Calculate progress to next level (simple logic: level * 500)
        $nextLevelPoints = $user->level * 500;
        $progress = ($user->points % 500) / 500 * 100;

        return response()->json([
            'points' => $user->points,
            'level' => $user->level,
            'streak' => $user->current_streak,
            'next_level_points' => $nextLevelPoints,
            'progress' => $progress,
            'badges' => $user->badges
        ]);
    }
}
