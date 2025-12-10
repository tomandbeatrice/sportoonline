<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminInsuranceController extends Controller
{
    public function index()
    {
        // Mock data for now, to be replaced with DB call
        $policies = [
            [
                'id' => 1,
                'holder' => 'Ahmet Yılmaz',
                'number' => 'POL-2025-001',
                'type' => 'Seyahat Sağlık',
                'startDate' => '10.12.2025',
                'expiryDate' => '20.12.2025',
                'premium' => 450,
                'coverage' => 30000,
                'status' => 'active',
                'coverageDetails' => ['Tıbbi Tedavi Teminatı', 'Bagaj Kaybı', 'Seyahat İptali'],
                'claims' => []
            ],
            [
                'id' => 2,
                'holder' => 'Ayşe Demir',
                'number' => 'POL-2025-045',
                'type' => 'Kasko',
                'startDate' => '01.01.2025',
                'expiryDate' => '01.01.2026',
                'premium' => 12500,
                'coverage' => 850000,
                'status' => 'active',
                'coverageDetails' => ['Çarpma/Çarpışma', 'Hırsızlık', 'Doğal Afetler', 'İhtiyari Mali Mesuliyet'],
                'claims' => [
                    ['id' => 1, 'type' => 'Cam Kırılması', 'date' => '15.06.2025', 'amount' => 3500, 'status' => 'paid']
                ]
            ],
            [
                'id' => 3,
                'holder' => 'Mehmet Kaya',
                'number' => 'POL-2025-089',
                'type' => 'Konut Sigortası',
                'startDate' => '15.03.2025',
                'expiryDate' => '15.03.2026',
                'premium' => 3200,
                'coverage' => 2500000,
                'status' => 'pending',
                'coverageDetails' => ['Yangın', 'Su Baskını', 'Hırsızlık', 'Deprem'],
                'claims' => []
            ],
            [
                'id' => 4,
                'holder' => 'Zeynep Çelik',
                'number' => 'POL-2024-990',
                'type' => 'Seyahat Sağlık',
                'startDate' => '01.11.2024',
                'expiryDate' => '15.11.2024',
                'premium' => 380,
                'coverage' => 30000,
                'status' => 'expired',
                'coverageDetails' => ['Tıbbi Tedavi Teminatı', 'Bagaj Kaybı'],
                'claims' => []
            ]
        ];

        return response()->json([
            'data' => $policies
        ]);
    }

    public function stats()
    {
        return response()->json([
            'active_policies' => 1240,
            'total_premium' => '850K',
            'claim_ratio' => 18,
            'pending_claims' => 12
        ]);
    }
}
