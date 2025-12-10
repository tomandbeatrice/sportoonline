<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceModuleController extends Controller
{
    public function index()
    {
        $modules = \App\Models\ServiceModule::where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json($modules);
    }
}
