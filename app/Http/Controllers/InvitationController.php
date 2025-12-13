<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\InvitationEmail;

class InvitationController extends Controller
{
    /**
     * Get invitation analytics (Admin)
     */
    public function getAnalytics()
    {
        $stats = [
            'total_invitations' => Invitation::count(),
            'accepted_invitations' => Invitation::where('status', 'accepted')->count(),
            'pending_invitations' => Invitation::where('status', 'pending')->count(),
            'conversion_rate' => Invitation::count() > 0 
                ? round((Invitation::where('status', 'accepted')->count() / Invitation::count()) * 100, 2) 
                : 0,
            'top_inviters' => User::withCount(['invitations' => function($q) {
                    $q->where('status', 'accepted');
                }])
                ->orderByDesc('invitations_count')
                ->take(10)
                ->get(['id', 'name', 'email'])
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'accepted_count' => $user->invitations_count
                    ];
                }),
            'monthly_trend' => Invitation::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get()
        ];

        return response()->json($stats);
    }

    /**
     * Get user's invitations
     */
    public function index()
    {
        $invitations = Auth::user()->invitations()
            ->with('invitedUser:id,name,email')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($invitations);
    }

    /**
     * Create new invitation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:invitations,email'
        ]);

        $invitation = Invitation::create([
            'user_id' => Auth::id(),
            'email' => $validated['email'],
            'code' => Str::random(32),
            'status' => 'pending',
            'expires_at' => now()->addDays(7)
        ]);

        // Send invitation email
        Mail::to($validated['email'])->send(new InvitationEmail($invitation));

        return response()->json([
            'message' => 'Davet gönderildi',
            'invitation' => $invitation
        ], 201);
    }

    /**
     * Accept invitation
     */
    public function accept(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|exists:invitations,code'
        ]);

        $invitation = Invitation::where('code', $validated['code'])
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return response()->json([
                'message' => 'Davet geçersiz veya süresi dolmuş'
            ], 400);
        }

        $invitation->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'invited_user_id' => Auth::id()
        ]);

        return response()->json([
            'message' => 'Davet kabul edildi'
        ]);
    }
}
