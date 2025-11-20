<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExportLog;

class ExportLogSeeder extends Seeder
{
    public function run(): void
    {
        $segments = ['customer', 'vendor'];
        $types = ['CSV', 'PDF', 'XLS'];
        $statuses = ['completed', 'failed', 'pending'];

        for ($i = 0; $i < 50; $i++) {
            ExportLog::create([
                'segment' => $segments[array_rand($segments)],
                'export_type' => $types[array_rand($types)],
                'status' => $statuses[array_rand($statuses)],
                'duration' => round(mt_rand(10, 50) / 10, 1),
                'file_name' => 'export_' . $i . '.csv',
                'file_size' => mt_rand(100, 5000) / 100,
                'exported_at' => now()->subMinutes(mt_rand(0, 1440)),
                'created_at' => now()->subMinutes(mt_rand(0, 1440))
            ]);
        }
    }
}