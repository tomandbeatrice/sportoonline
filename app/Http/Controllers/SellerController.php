public function campaignSuggestionList($sellerId)
{
    $campaigns = ['early_access_100', 'beta_invite', null];

    $types = [
        'early_access_100' => 'davet',
        'beta_invite'      => 'trend',
        null               => 'organik'
    ];

    $weights = Cache::get('suggestion_weights', [
        'davet'   => [0.6, 0.3, 0.1],
        'trend'   => [0.5, 0.3, 0.2],
        'organik' => [0.4, 0.4, 0.2]
    ]);

    $logs = [
        ['campaign' => 'early_access_100', 'timestamp' => now()->subDays(2)],
        ['campaign' => 'beta_invite',      'timestamp' => now()->subDays(5)],
        ['campaign' => 'organik',          'timestamp' => now()->subDays(10)],
    ];

    $suggestions = collect($campaigns)->map(function ($tag) use ($sellerId, $logs, $types, $weights) {
        $log = collect($logs)->firstWhere('campaign', $tag ?? 'organik');
        $timestamp = $log['timestamp'] ?? now()->subDays(30);

        $orders = Order::where('seller_id', $sellerId)
            ->where('created_at', '>', $timestamp)
            ->whereHas('buyer', fn($q) => $tag ? $q->where('campaign_tag', $tag) : $q->whereNull('campaign_tag'))
            ->where('status', 'tamamlandı')
            ->count();

        $views = Product::where('seller_id', $sellerId)
            ->where('campaign_tag', $tag)
            ->where('updated_at', '>', $timestamp)
            ->sum('views');

        $comments = Product::where('seller_id', $sellerId)
            ->where('campaign_tag', $tag)
            ->get()
            ->flatMap(fn($p) => $p->comments()->get(['rating']));

        $avgRating = $comments->avg('rating') ?? 0;
        $conversionRate = $views > 0 ? round($orders / $views * 100, 2) : 0;
        $postRestartConversion = $this->getPostRestartConversion($sellerId, $tag, $timestamp);

        $type = $types[$tag ?? 'organik'];
        [$w1, $w2, $w3] = $weights[$type];

        $score = round(
            ($conversionRate * $w1) +
            ($avgRating * 10 * $w2) +
            ($postRestartConversion * $w3),
            2
        );

        return [
            'campaign'              => $tag ?? 'organik',
            'type'                  => $type,
            'lastRestart'           => $timestamp->toDateTimeString(),
            'orders'                => $orders,
            'views'                 => $views,
            'conversionRate'        => $conversionRate,
            'avgRating'             => round($avgRating, 1),
            'postRestartConversion' => $postRestartConversion,
            'score'                 => $score
        ];
    });

    return response()->json($suggestions->sortByDesc('score')->values());
}