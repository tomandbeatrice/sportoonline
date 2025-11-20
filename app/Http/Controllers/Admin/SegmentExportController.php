<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SegmentExportController extends Controller
{
    public function export(Request $request)
    {
        return response()->json([
            'message' => 'Segment export functionality not yet implemented',
        ], 501);
    }

    public function history(Request $request)
    {
        return response()->json([
            'message' => 'Segment export history not yet implemented',
        ], 501);
    }
}
