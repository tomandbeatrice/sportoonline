public function test_score_calculation()
{
    $service = new CampaignService();

    $inputs = [
        'response_time' => 20,
        'comment_response_rate' => 80,
        'update_count' => 5,
        'export_usage' => 2
    ];

    $score = $service->calculateScore($inputs);
    $this->assertGreaterThan(0, $score);
}