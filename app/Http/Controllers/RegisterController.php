<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $campaigns = [
            'SPORTOON100' => ['rate' => 0.00, 'tag' => 'early_access_100'],
            'BETA50' => ['rate' => 0.05, 'tag' => 'beta_invite']
        ];

        $inviteCode = $request->input('invite_code');
        
        if (isset($campaigns[strtoupper($inviteCode)])) {
            $user->commission_rate = $campaigns[strtoupper($inviteCode)]['rate'];
            $user->campaign_tag = $campaigns[strtoupper($inviteCode)]['tag'];
        }
        
        // Additional registration logic would go here
    }
}