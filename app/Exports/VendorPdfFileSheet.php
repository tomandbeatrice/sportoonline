<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VendorPdfFileSheet implements FromArray, WithHeadings
{
    protected $files;

    public function __construct($files)
    {
        $this->files = $files;
    }

    public function array(): array
    {
        return collect($this->files)->map(function ($item) {
            return [
                'Dosya Adı' => $item['file'],
                'Boyut (MB)' => number_format($item['size_mb'], 2),
                'Başlık' => $item['title'],
                'İçerik Önizleme' => $item['preview'],
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return ['Dosya Adı', 'Boyut (MB)', 'Başlık', 'İçerik Önizleme'];
    }
}