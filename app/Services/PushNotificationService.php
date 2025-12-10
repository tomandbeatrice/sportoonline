<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PushNotificationService
{
    protected $fcmServerKey;
    protected $vapidPublicKey;
    protected $vapidPrivateKey;

    public function __construct()
    {
        $this->fcmServerKey = config('services.fcm.server_key');
        $this->vapidPublicKey = config('services.webpush.public_key');
        $this->vapidPrivateKey = config('services.webpush.private_key');
    }

    /**
     * Send push notification to a single user
     */
    public function sendToUser(User $user, string $title, string $body, array $data = [], ?string $icon = null): bool
    {
        $tokens = $this->getUserTokens($user);

        if (empty($tokens)) {
            Log::warning("No push tokens found for user {$user->id}");
            return false;
        }

        return $this->sendToTokens($tokens, $title, $body, $data, $icon);
    }

    /**
     * Send push notification to multiple users
     */
    public function sendToUsers(array $users, string $title, string $body, array $data = [], ?string $icon = null): array
    {
        $results = [];

        foreach ($users as $user) {
            $results[$user->id] = $this->sendToUser($user, $title, $body, $data, $icon);
        }

        return $results;
    }

    /**
     * Send push notification to a topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [], ?string $icon = null): bool
    {
        $message = [
            'to' => '/topics/' . $topic,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'icon' => $icon ?? config('app.logo'),
                'click_action' => config('app.url'),
            ],
            'data' => $data,
        ];

        return $this->sendFcmMessage($message);
    }

    /**
     * Send push notification to specific tokens
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = [], ?string $icon = null): bool
    {
        if (empty($tokens)) {
            return false;
        }

        $message = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'icon' => $icon ?? config('app.logo'),
                'click_action' => config('app.url'),
            ],
            'data' => $data,
            'priority' => 'high',
        ];

        return $this->sendFcmMessage($message);
    }

    /**
     * Subscribe user to a topic
     */
    public function subscribeToTopic(User $user, string $topic): bool
    {
        $tokens = $this->getUserTokens($user);

        if (empty($tokens)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $this->fcmServerKey,
            'Content-Type' => 'application/json',
        ])->post('https://iid.googleapis.com/iid/v1:batchAdd', [
            'to' => '/topics/' . $topic,
            'registration_tokens' => $tokens,
        ]);

        return $response->successful();
    }

    /**
     * Unsubscribe user from a topic
     */
    public function unsubscribeFromTopic(User $user, string $topic): bool
    {
        $tokens = $this->getUserTokens($user);

        if (empty($tokens)) {
            return false;
        }

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $this->fcmServerKey,
            'Content-Type' => 'application/json',
        ])->post('https://iid.googleapis.com/iid/v1:batchRemove', [
            'to' => '/topics/' . $topic,
            'registration_tokens' => $tokens,
        ]);

        return $response->successful();
    }

    /**
     * Register a push token for a user
     */
    public function registerToken(User $user, string $token, string $platform = 'web'): bool
    {
        $tokens = $user->push_tokens ?? [];

        // Check if token already exists
        foreach ($tokens as $existingToken) {
            if ($existingToken['token'] === $token) {
                return true;
            }
        }

        $tokens[] = [
            'token' => $token,
            'platform' => $platform,
            'created_at' => now()->toDateTimeString(),
        ];

        $user->update(['push_tokens' => $tokens]);

        return true;
    }

    /**
     * Remove a push token for a user
     */
    public function removeToken(User $user, string $token): bool
    {
        $tokens = $user->push_tokens ?? [];

        $tokens = array_filter($tokens, function ($t) use ($token) {
            return $t['token'] !== $token;
        });

        $user->update(['push_tokens' => array_values($tokens)]);

        return true;
    }

    /**
     * Send notification types
     */
    public function sendOrderNotification(User $user, array $orderData): bool
    {
        return $this->sendToUser(
            $user,
            'Sipariş Güncellendi',
            "Sipariş #{$orderData['order_number']} durumu: {$orderData['status']}",
            [
                'type' => 'order_update',
                'order_id' => $orderData['id'],
                'status' => $orderData['status'],
            ]
        );
    }

    public function sendCampaignNotification(array $users, array $campaignData): array
    {
        return $this->sendToUsers(
            $users,
            $campaignData['title'],
            $campaignData['description'],
            [
                'type' => 'campaign',
                'campaign_id' => $campaignData['id'],
                'url' => $campaignData['url'] ?? null,
            ]
        );
    }

    public function sendReservationNotification(User $user, array $reservationData): bool
    {
        return $this->sendToUser(
            $user,
            'Rezervasyon Bildirimi',
            $reservationData['message'],
            [
                'type' => 'reservation',
                'reservation_id' => $reservationData['id'],
                'status' => $reservationData['status'],
            ]
        );
    }

    public function sendTransferNotification(User $user, array $transferData): bool
    {
        return $this->sendToUser(
            $user,
            'Transfer Bildirimi',
            $transferData['message'],
            [
                'type' => 'transfer',
                'transfer_id' => $transferData['id'],
                'status' => $transferData['status'],
            ]
        );
    }

    /**
     * Get user's push tokens
     */
    protected function getUserTokens(User $user): array
    {
        $tokens = $user->push_tokens ?? [];

        return array_map(function ($t) {
            return $t['token'];
        }, $tokens);
    }

    /**
     * Send FCM message
     */
    protected function sendFcmMessage(array $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->fcmServerKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $message);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Push notification sent successfully', [
                    'success' => $result['success'] ?? 0,
                    'failure' => $result['failure'] ?? 0,
                ]);
                return true;
            }

            Log::error('Push notification failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Push notification exception', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get VAPID public key for Web Push
     */
    public function getVapidPublicKey(): ?string
    {
        return $this->vapidPublicKey;
    }
}
