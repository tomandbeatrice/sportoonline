<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicCampaignController extends Controller
{
    public function index()
    {
        $campaigns = \App\Models\Campaign::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>', now())
            ->orderBy('end_date', 'asc')
            ->take(10)
            ->get();

        return response()->json($campaigns);
    }
}
