<?php

namespace App\Http\Controllers;

use App\Models\EmailQueue;
use App\Models\SmsConfig;
use App\Models\SmsTemplate;
use App\Models\SmsHistory;
use App\Models\PushConfig;
use App\Models\PushHistory;
use App\Models\NotificationType;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class AdminNotificationController extends Controller
{
    // Email Queue
    public function getEmailQueue(Request $request)
    {
        $query = EmailQueue::query();

        if ($request->search) {
            $query->where('to', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $emails = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'pending' => EmailQueue::where('status', 'pending')->count(),
            'processing' => EmailQueue::where('status', 'processing')->count(),
            'sent' => EmailQueue::where('status', 'sent')->count(),
            'failed' => EmailQueue::where('status', 'failed')->count(),
        ];

        return response()->json([
            'emails' => $emails,
            'stats' => $stats
        ]);
    }

    public function retryEmail($id)
    {
        $email = EmailQueue::findOrFail($id);
        $email->status = 'pending';
        $email->save();

        return response()->json(['message' => 'Email kuyruğa eklendi']);
    }

    // SMS Config
    public function getSmsConfig()
    {
        $config = SmsConfig::first();
        return response()->json($config ?? [
            'provider' => '',
            'username' => '',
            'password' => '',
            'header' => '',
            'api_key' => '',
            'api_secret' => '',
            'sender' => '',
            'enabled' => false
        ]);
    }

    public function saveSmsConfig(Request $request)
    {
        $config = SmsConfig::firstOrCreate([]);
        $config->fill($request->all());
        $config->save();

        return response()->json(['message' => 'SMS ayarları kaydedildi']);
    }

    // SMS Templates
    public function getSmsTemplates()
    {
        return SmsTemplate::orderBy('name')->get();
    }

    public function storeSmsTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:sms_templates,code',
            'content' => 'required|string'
        ]);

        $template = SmsTemplate::create($request->all());
        return response()->json($template);
    }

    public function updateSmsTemplate(Request $request, $id)
    {
        $template = SmsTemplate::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:sms_templates,code,' . $id,
            'content' => 'required|string'
        ]);

        $template->update($request->all());
        return response()->json($template);
    }

    public function sendTestSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'template_id' => 'required|exists:sms_templates,id'
        ]);

        $template = SmsTemplate::findOrFail($request->template_id);
        $config = SmsConfig::first();

        if (!$config || !$config->enabled) {
            return response()->json(['message' => 'SMS servisi etkin değil'], 400);
        }

        // Netgsm
        if ($config->provider === 'netgsm') {
            $response = Http::get('https://api.netgsm.com.tr/sms/send/get', [
                'usercode' => $config->username,
                'password' => $config->password,
                'gsmno' => $request->phone,
                'message' => $template->content,
                'msgheader' => $config->header
            ]);

            if ($response->successful() && str_starts_with($response->body(), '00')) {
                SmsHistory::create([
                    'phone' => $request->phone,
                    'message' => $template->content,
                    'status' => 'sent',
                    'sent_at' => now()
                ]);
                return response()->json(['message' => 'Test SMS gönderildi']);
            }
        }
        
        // İleti Merkezi
        if ($config->provider === 'iletimerkezi') {
            $response = Http::withBasicAuth($config->api_key, $config->api_secret)
                ->post('https://api.iletimerkezi.com/v1/send-sms', [
                    'sender' => $config->sender,
                    'receiver' => $request->phone,
                    'message' => $template->content
                ]);

            if ($response->successful()) {
                SmsHistory::create([
                    'phone' => $request->phone,
                    'message' => $template->content,
                    'status' => 'sent',
                    'sent_at' => now()
                ]);
                return response()->json(['message' => 'Test SMS gönderildi']);
            }
        }

        return response()->json(['message' => 'SMS gönderilemedi'], 500);
    }

    public function getSmsHistory()
    {
        return SmsHistory::orderBy('sent_at', 'desc')->limit(50)->get();
    }

    // Push Notifications
    public function getPushConfig()
    {
        $config = PushConfig::first();
        return response()->json($config ?? [
            'firebase_server_key' => '',
            'firebase_sender_id' => '',
            'enabled' => false
        ]);
    }

    public function savePushConfig(Request $request)
    {
        $config = PushConfig::firstOrCreate([]);
        $config->fill($request->all());
        $config->save();

        return response()->json(['message' => 'Push notification ayarları kaydedildi']);
    }

    public function sendPushNotification(Request $request)
    {
        $request->validate([
            'audience' => 'required|in:all,buyers,sellers,segment',
            'title' => 'required|string',
            'message' => 'required|string'
        ]);

        $config = PushConfig::first();

        if (!$config || !$config->enabled) {
            return response()->json(['message' => 'Push notification servisi etkin değil'], 400);
        }

        // Firebase Cloud Messaging
        $tokens = $this->getDeviceTokens($request->audience, $request->segment);

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $config->firebase_server_key,
            'Content-Type' => 'application/json'
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $request->title,
                'body' => $request->message,
                'icon' => $request->icon,
                'click_action' => $request->click_action
            ]
        ]);

        if ($response->successful()) {
            PushHistory::create([
                'title' => $request->title,
                'message' => $request->message,
                'audience' => $request->audience,
                'sent_count' => count($tokens),
                'click_count' => 0
            ]);

            return response()->json(['message' => 'Push notification gönderildi']);
        }

        return response()->json(['message' => 'Push notification gönderilemedi'], 500);
    }

    public function getPushHistory()
    {
        return PushHistory::orderBy('created_at', 'desc')->limit(50)->get();
    }

    private function getDeviceTokens($audience, $segment = null)
    {
        // Simplified - should query device_tokens table based on audience
        return [];
    }

    // Notification Types
    public function updateNotificationType(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'enabled' => 'required|boolean',
            'channels' => 'required|array'
        ]);

        $type = NotificationType::updateOrCreate(
            ['code' => $request->code],
            [
                'enabled' => $request->enabled,
                'channels' => $request->channels
            ]
        );

        return response()->json($type);
    }

    public function getRecentNotifications()
    {
        return Notification::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    public function sendTestNotification()
    {
        // Send test notification to admin
        $admin = auth()->user();

        Notification::create([
            'user_id' => $admin->id,
            'type' => 'test',
            'message' => 'Bu bir test bildirimidir',
            'channel' => 'database'
        ]);

        return response()->json(['message' => 'Test bildirimi gönderildi']);
    }
}
