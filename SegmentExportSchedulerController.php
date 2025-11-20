public function schedule(Request $request)
{
    $segmentId = $request->input('segmentId');
    $time = $request->input('time');
    $day = $request->input('day');

    $configPath = config_path('scheduled_exports.php');
    $current = file_exists($configPath) ? require_once $configPath : [];

    $current[] = [
        'segmentId' => $segmentId,
        'time' => $time,
        'day' => $day
    ];

return response()->json(["message" => "Segment #{$segmentId} için export planlandı: {$day} - {$time}"]);
    file_put_contents($configPath, $content);

    return response()->json([
        'message' => "Segment #{$segmentId} için export planlandı: {$day} - {$time}"
    ]);
}