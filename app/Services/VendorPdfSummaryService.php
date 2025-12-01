<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class VendorPdfSummaryService
{
    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function getSummary($vendorId)
    {
        $files = Storage::files("vendors/{$vendorId}/pdf");
        $titles = [];

        foreach ($files as $file) {
            try {
                $pdf = $this->parser->parseFile(Storage::path($file));
                $text = $pdf->getPages()[0]->getText();
                $lines = explode("\n", trim($text));
                $title = $lines[0] ?? 'Başlık Yok';
                $titles[] = trim($title);
            } catch (\Exception $e) {
                $titles[] = 'Hata: ' . $e->getMessage();
            }
        }

        return collect($titles)
            ->groupBy(fn($title) => strtolower($title))
            ->map(fn($group) => $group->count())
            ->sortDesc()
            ->toArray();
    }
}