<?php

namespace App\Exports;

use App\Models\VendorBranding;
use Maatwebsite\Excel\Concerns\FromCollection;

class VendorBrandingExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return VendorBranding::all();
    }
}
