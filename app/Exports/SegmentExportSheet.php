<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\User;

class SegmentExportSheet implements FromCollection
{
    protected $segmentId;

    public function __construct($segmentId)
    {
        $this->segmentId = $segmentId;
    }

    public function collection()
    {
        return User::where('segment_id', $this->segmentId)->get();
    }
}