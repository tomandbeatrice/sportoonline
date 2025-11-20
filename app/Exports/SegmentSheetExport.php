<?php

namespace App\Exports;

use App\Models\Segment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;

class SegmentSheetExport implements FromCollection, WithHeadings
{
    protected $vendorId;
    protected $segmentKey;

    public function __construct($vendorId, $segmentKey)
    {
        $this->vendorId = $vendorId;
        $this->segmentKey = $segmentKey;
    }

    public function collection()
    {
        return Segment::where('vendor_id', $this->vendorId)
                      ->where('key', $this->segmentKey)
                      ->select('id', 'name', 'description', 'created_at')
                      ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Segment Adı',
            'Açıklama',
            'Oluşturulma Tarihi',
        ];
    }
}