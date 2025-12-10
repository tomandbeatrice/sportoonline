<?php

namespace App\Exports;

use App\Models\VendorLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorLogExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return VendorLog::all()->map(function ($log) {
            return [
                $log->vendor_name,
                $log->token,
                $log->active ? 'Aktif' : 'Pasif',
                $log->access_count,
                $log->last_access
            ];
        });
    }

    public function headings(): array
    {
        return ['Vendor', 'Token', 'Durum', 'Erişim Sayısı', 'Son Erişim'];
    }
}