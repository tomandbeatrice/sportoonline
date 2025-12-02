<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function handleInviteCode(User $user, $inviteCode)
    {
        $campaigns = [
            'SPORTOON100' => ['rate' => 0.00, 'tag' => 'early_access_100'],
            'BETA50' => ['rate' => 0.05, 'tag' => 'beta_invite']
        ];

        if (isset($campaigns[strtoupper($inviteCode)])) {
            $user->commission_rate = $campaigns[strtoupper($inviteCode)]['rate'];
            $user->campaign_tag = $campaigns[strtoupper($inviteCode)]['tag'];
            $user->save();
        }

        return $user;
    }
}
