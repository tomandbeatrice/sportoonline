<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SegmentSheetExportController extends Controller
{
    public function export()
    {
        return response()->json(['message' => 'Segment sheet export başarılı']);
    }
}