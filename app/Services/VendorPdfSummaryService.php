<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Smalot\PdfParser\Parser;

class VendorPdfSummaryService
{
    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function getSummary($vendorId, $filters = [])
    {
        $files = Storage::files("vendors/{$vendorId}/pdf");
        $fileData = [];

        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            $sizeBytes = Storage::size($filePath);
            $sizeMb = $sizeBytes / (1024 * 1024);

            // Filtreleme
            if (
                isset($filters['search']) && !str_contains(strtolower($fileName), strtolower($filters['search']))
            ) continue;

            if (
                isset($filters['min_size']) && $sizeMb < floatval($filters['min_size'])
            ) continue;

            if (
                isset($filters['max_size']) && $sizeMb > floatval($filters['max_size'])
            ) continue;

            // PDF içeriği
            try {
                $pdf = $this->parser->parseFile(Storage::path($filePath));
                $text = $pdf->getPages()[0]->getText();
                $lines = explode("\n", trim($text));
                $title = $lines[0] ?? 'Başlık Yok';
                $preview = implode(' ', array_slice($lines, 0, 3));
            } catch (\Exception $e) {
                $title = 'Hata';
                $preview = 'PDF okunamadı: ' . $e->getMessage();
            }

            $fileData[] = [
                'file' => $fileName,
                'size_mb' => $sizeMb,
                'title' => trim($title),
                'preview' => trim($preview),
            ];
        }

        // Başlık gruplama
        $titleGroups = collect($fileData)
            ->groupBy(fn($item) => strtolower($item['title']))
            ->map(fn($group) => count($group))
            ->sortDesc()
            ->toArray();

        // Pagination
        $page = request()->get('page', 1);
        $perPage = 15;
        $paginatedFiles = new LengthAwarePaginator(
            collect($fileData)->slice(($page - 1) * $perPage, $perPage)->values(),
            count($fileData),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return [
            'files' => $paginatedFiles,
            'titles' => $titleGroups,
        ];
    }
}