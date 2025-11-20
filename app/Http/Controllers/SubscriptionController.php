<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * Get all active subscription plans
     */
    public function getPlans()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();

        return response()->json($plans);
    }

    /**
     * Get current user's subscription status
     */
    public function mySubscription()
    {
        $user = auth()->user();
        $subscription = Subscription::with('plan')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'status' => 'no_subscription',
                'message' => 'Aktif abonelik bulunamadı',
            ]);
        }

        return response()->json([
            'subscription' => $subscription,
            'plan' => $subscription->plan,
            'is_active' => $subscription->isActive(),
            'on_trial' => $subscription->onTrial(),
            'days_remaining' => $subscription->daysRemaining(),
            'product_count' => $user->products()->count(),
            'product_limit' => $subscription->plan->product_limit,
            'remaining_products' => $subscription->plan->getRemainingProducts($user),
        ]);
    }

    /**
     * Subscribe to a plan
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'payment_method' => 'required|string',
        ]);

        $user = auth()->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user already has active subscription
        $existingSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($existingSubscription) {
            return response()->json([
                'error' => 'Zaten aktif bir aboneliğiniz var',
            ], 400);
        }

        // Calculate amount and dates
        $amount = $request->billing_cycle === 'yearly' ? $plan->yearly_price : $plan->price;
        $startsAt = now();
        $endsAt = $request->billing_cycle === 'yearly' 
            ? Carbon::now()->addYear()
            : Carbon::now()->addMonth();

        // Create subscription
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => $plan->trial_days > 0 ? 'trial' : 'active',
            'trial_ends_at' => $plan->trial_days > 0 ? Carbon::now()->addDays($plan->trial_days) : null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'billing_cycle' => $request->billing_cycle,
            'amount' => $amount,
            'commission_rate' => $plan->commission_rate,
            'payment_method' => $request->payment_method,
            'auto_renew' => true,
        ]);

        // Update user subscription info
        $user->update([
            'subscription_plan_id' => $plan->id,
            'subscription_status' => $subscription->status,
            'subscription_ends_at' => $endsAt,
        ]);

        // Create payment record (if not trial)
        if ($plan->trial_days === 0) {
            SubscriptionPayment::create([
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Abonelik başarıyla oluşturuldu',
            'subscription' => $subscription->load('plan'),
        ]);
    }

    /**
     * Cancel subscription
     */
    public function cancel()
    {
        $user = auth()->user();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'error' => 'Aktif abonelik bulunamadı',
            ], 404);
        }

        $subscription->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Abonelik iptal edildi. Mevcut dönem sonuna kadar kullanabilirsiniz.',
        ]);
    }

    /**
     * Upgrade subscription plan
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = auth()->user();
        $newPlan = SubscriptionPlan::findOrFail($request->plan_id);
        
        $currentSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$currentSubscription) {
            return response()->json([
                'error' => 'Önce bir abonelik başlatmalısınız',
            ], 400);
        }

        $currentPlan = $currentSubscription->plan;

        // Check if it's actually an upgrade
        if ($newPlan->price <= $currentPlan->price) {
            return response()->json([
                'error' => 'Bu bir yükseltme değil',
            ], 400);
        }

        // Calculate prorated amount
        $daysRemaining = $currentSubscription->daysRemaining();
        $totalDays = $currentSubscription->billing_cycle === 'yearly' ? 365 : 30;
        $proratedRefund = ($currentPlan->price / $totalDays) * $daysRemaining;
        $newAmount = $newPlan->price - $proratedRefund;

        // Update subscription
        $currentSubscription->update([
            'subscription_plan_id' => $newPlan->id,
            'amount' => $newPlan->price,
        ]);

        // Update user
        $user->update([
            'subscription_plan_id' => $newPlan->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Abonelik yükseltildi',
            'prorated_amount' => round($newAmount, 2),
        ]);
    }

    /**
     * Downgrade subscription plan
     */
    public function downgrade(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = auth()->user();
        $newPlan = SubscriptionPlan::findOrFail($request->plan_id);
        
        $currentSubscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$currentSubscription) {
            return response()->json([
                'error' => 'Aktif abonelik bulunamadı',
            ], 404);
        }

        // Check product count doesn't exceed new plan limit
        $productCount = $user->products()->count();
        if ($productCount > $newPlan->product_limit) {
            return response()->json([
                'error' => "Mevcut ürün sayınız ($productCount) yeni planın limitini ({$newPlan->product_limit}) aşıyor. Önce ürün sayısını azaltın.",
            ], 400);
        }

        // Downgrade will take effect at next renewal
        $currentSubscription->update([
            'auto_renew' => true,
        ]);

        // Store pending downgrade info
        $user->update([
            'pending_subscription_plan_id' => $newPlan->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Düşürme talebi alındı. Mevcut dönem sonunda aktif olacak.',
        ]);
    }

    /**
     * Renew subscription
     */
    public function renew()
    {
        $user = auth()->user();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'error' => 'Yenilenecek abonelik bulunamadı',
            ], 404);
        }

        $subscription->renew();

        $user->update([
            'subscription_status' => 'active',
            'subscription_ends_at' => $subscription->ends_at,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Abonelik yenilendi',
            'subscription' => $subscription->load('plan'),
        ]);
    }

    /**
     * Get payment history
     */
    public function paymentHistory()
    {
        $payments = SubscriptionPayment::where('user_id', auth()->id())
            ->with('subscription.plan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($payments);
    }

    /**
     * Check subscription limits
     */
    public function checkLimits()
    {
        $user = auth()->user();
        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        if (!$subscription) {
            return response()->json([
                'has_subscription' => false,
                'can_add_products' => false,
                'message' => 'Ürün eklemek için bir abonelik planı seçmelisiniz',
            ]);
        }

        $plan = $subscription->plan;
        $productCount = $user->products()->count();
        $canAddProducts = $productCount < $plan->product_limit;

        return response()->json([
            'has_subscription' => true,
            'plan_name' => $plan->name,
            'product_count' => $productCount,
            'product_limit' => $plan->product_limit,
            'remaining_products' => max(0, $plan->product_limit - $productCount),
            'can_add_products' => $canAddProducts,
            'bulk_upload_enabled' => $plan->bulk_upload,
            'image_limit_per_product' => $plan->image_limit_per_product,
            'max_file_size_mb' => $plan->max_file_size_mb,
            'days_remaining' => $subscription->daysRemaining(),
        ]);
    }
}
