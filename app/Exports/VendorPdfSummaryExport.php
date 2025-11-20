<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VendorPdfSummaryExport implements WithMultipleSheets
{
    protected $summary;

    public function __construct(array $summary)
    {
        $this->summary = $summary;
    }

    public function sheets(): array
    {
        return [
            new VendorPdfFileSheet($this->summary['files']),
            new VendorPdfTitleSheet($this->summary['titles']),
        ];
    }
}