<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class ExportLogHelper
{
    public static function getLatestCleanupLog(): JsonResponse
    {
        $disk = Storage::disk('local');

        $latest = collect($disk->files('exports/logs'))
            ->filter(fn($f) => str_contains($f, 'cleanup_'))
            ->sortDesc()
            ->first();

        if (!$latest || !$disk->exists($latest)) {
            return response()->json(['error' => 'Log bulunamadı'], 404);
        }

        return response()->json(json_decode($disk->get($latest), true));
    }

    public static function getCleanupLogHistory(): \Illuminate\Support\Collection
    {
        $disk = Storage::disk('local');

        return collect($disk->files('exports/logs'))
            ->filter(fn($f) => str_contains($f, 'cleanup_'))
            ->sortDesc()
            ->map(function ($file) use ($disk) {
                $data = json_decode($disk->get($file), true);
                return array_merge(['file' => $file], $data ?? []);
            });
    }
}