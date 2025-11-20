<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorPdfTitleSheet implements FromArray, WithHeadings
{
    protected $titles;

    public function __construct(array $titles)
    {
        $this->titles = $titles;
    }

    public function array(): array
    {
        return collect($this->titles)
            ->map(fn($count, $title) => [$title, $count])
            ->values()
            ->toArray();
    }

    public function headings(): array
    {
        return ['Başlık', 'Frekans'];
    }
}