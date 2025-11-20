<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VendorBrandingController extends Controller
{
    public function index()
    {
        return view('admin.branding.index');
    }

    public function update(Request $request, $vendor)
    {
        // Güncelleme işlemi
        return response()->json(['message' => 'Branding güncellendi']);
    }

    public function preview($vendor)
    {
        // Önizleme işlemi
        return view('admin.branding.preview', compact('vendor'));
    }
}