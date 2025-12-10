<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PushNotificationController extends Controller
{
    protected $pushService;

    public function __construct(PushNotificationService $pushService)
    {
        $this->pushService = $pushService;
    }

    /**
     * Register a push notification token
     */
    public function registerToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'platform' => 'sometimes|string|in:web,android,ios',
        ]);

        $user = $request->user();
        $platform = $request->input('platform', 'web');

        $success = $this->pushService->registerToken($user, $request->token, $platform);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Token kaydedildi' : 'Token kaydedilemedi',
        ]);
    }

    /**
     * Remove a push notification token
     */
    public function removeToken(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = $request->user();

        $success = $this->pushService->removeToken($user, $request->token);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Token kaldırıldı' : 'Token kaldırılamadı',
        ]);
    }

    /**
     * Subscribe to a topic
     */
    public function subscribeToTopic(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|max:100',
        ]);

        $user = $request->user();
        $topic = $request->topic;

        // Validate allowed topics
        $allowedTopics = ['campaigns', 'promotions', 'news', 'orders', 'general'];
        if (!in_array($topic, $allowedTopics)) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz topic',
            ], 400);
        }

        $success = $this->pushService->subscribeToTopic($user, $topic);

        return response()->json([
            'success' => $success,
            'message' => $success ? "'{$topic}' konusuna abone olundu" : 'Abone olunamadı',
        ]);
    }

    /**
     * Unsubscribe from a topic
     */
    public function unsubscribeFromTopic(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|max:100',
        ]);

        $user = $request->user();

        $success = $this->pushService->unsubscribeFromTopic($user, $request->topic);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Abonelik iptal edildi' : 'Abonelik iptal edilemedi',
        ]);
    }

    /**
     * Get VAPID public key for Web Push
     */
    public function getVapidKey(): JsonResponse
    {
        return response()->json([
            'vapid_public_key' => $this->pushService->getVapidPublicKey(),
        ]);
    }

    /**
     * Get user's notification preferences
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $user = $request->user();

        $preferences = $user->notification_preferences ?? [
            'push_enabled' => true,
            'email_enabled' => true,
            'sms_enabled' => false,
            'topics' => [
                'orders' => true,
                'campaigns' => true,
                'promotions' => true,
                'news' => false,
            ],
        ];

        return response()->json([
            'success' => true,
            'preferences' => $preferences,
        ]);
    }

    /**
     * Update user's notification preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'push_enabled' => 'sometimes|boolean',
            'email_enabled' => 'sometimes|boolean',
            'sms_enabled' => 'sometimes|boolean',
            'topics' => 'sometimes|array',
            'topics.orders' => 'sometimes|boolean',
            'topics.campaigns' => 'sometimes|boolean',
            'topics.promotions' => 'sometimes|boolean',
            'topics.news' => 'sometimes|boolean',
        ]);

        $user = $request->user();

        $currentPreferences = $user->notification_preferences ?? [];
        $newPreferences = array_merge($currentPreferences, $request->only([
            'push_enabled',
            'email_enabled',
            'sms_enabled',
            'topics',
        ]));

        $user->update(['notification_preferences' => $newPreferences]);

        return response()->json([
            'success' => true,
            'message' => 'Bildirim tercihleri güncellendi',
            'preferences' => $newPreferences,
        ]);
    }

    /**
     * Test push notification (admin only)
     */
    public function testNotification(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:500',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        $user = $request->has('user_id')
            ? \App\Models\User::find($request->user_id)
            : $request->user();

        $success = $this->pushService->sendToUser(
            $user,
            $request->title,
            $request->body,
            ['type' => 'test']
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Test bildirimi gönderildi' : 'Bildirim gönderilemedi',
        ]);
    }

    /**
     * Send notification to topic (admin only)
     */
    public function sendToTopic(Request $request): JsonResponse
    {
        $request->validate([
            'topic' => 'required|string|max:100',
            'title' => 'required|string|max:100',
            'body' => 'required|string|max:500',
            'data' => 'sometimes|array',
        ]);

        $success = $this->pushService->sendToTopic(
            $request->topic,
            $request->title,
            $request->body,
            $request->input('data', [])
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Topic bildirimi gönderildi' : 'Bildirim gönderilemedi',
        ]);
    }
}
