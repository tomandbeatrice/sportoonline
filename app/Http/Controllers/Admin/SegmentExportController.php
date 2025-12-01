public function history(Request $request)
{
    $segmentId = $request->query('segmentId');
    $startDate = $request->query('startDate');
    $endDate = $request->query('endDate');

    $path = storage_path('logs/laravel.log');
    if (!file_exists($path)) {
        return response()->json(['error' => 'Log dosyası bulunamadı'], 404);
    }

    $lines = file($path);
    $exports = collect($lines)
        ->filter(fn($line) => str_contains($line, '[SegmentExport]'))
        ->map(function ($line) {
            preg_match('/Segment #(\d+) export oluşturuldu: (segment_export_\d+_\d+\.xlsx)/', $line, $matches);
            return [
                'segmentId' => $matches[1] ?? null,
                'filename' => $matches[2] ?? null,
                'timestamp' => substr($line, 0, 19),
                'url' => isset($matches[2]) ? asset("storage/app/exports/segments/{$matches[2]}") : null
            ];
        })
        ->filter(fn($item) =>
            $item['segmentId'] &&
            $item['filename'] &&
            (!$segmentId || $item['segmentId'] == $segmentId) &&
            (!$startDate || $item['timestamp'] >= $startDate) &&
            (!$endDate || $item['timestamp'] <= $endDate)
        )
        ->values();

    return response()->json($exports);
}