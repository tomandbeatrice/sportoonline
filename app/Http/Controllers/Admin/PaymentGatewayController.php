<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\Payment\PaymentManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentGatewayController extends Controller
{
    private PaymentManager $paymentManager;

    public function __construct(PaymentManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    /**
     * Get all payment gateways
     */
    public function index()
    {
        $gateways = PaymentGateway::with(['transactions' => function ($query) {
            $query->select('payment_gateway_id', 
                          DB::raw('COUNT(*) as total_count'),
                          DB::raw('SUM(CASE WHEN status = "success" THEN amount ELSE 0 END) as total_amount'));
            $query->groupBy('payment_gateway_id');
        }])
        ->orderBy('sort_order')
        ->get();

        return response()->json([
            'success' => true,
            'gateways' => $gateways->map(function ($gateway) {
                return [
                    'id' => $gateway->id,
                    'name' => $gateway->name,
                    'display_name' => $gateway->display_name,
                    'description' => $gateway->description,
                    'is_active' => $gateway->is_active,
                    'is_test_mode' => $gateway->is_test_mode,
                    'sort_order' => $gateway->sort_order,
                    'min_amount' => $gateway->min_amount,
                    'max_amount' => $gateway->max_amount,
                    'commission_rate' => $gateway->commission_rate,
                    'fixed_commission' => $gateway->fixed_commission,
                    'has_credentials' => !empty($gateway->credentials),
                    'stats' => $this->paymentManager->getGatewayStats($gateway->name),
                ];
            }),
        ]);
    }

    /**
     * Get available gateway types
     */
    public function available()
    {
        return response()->json([
            'success' => true,
            'available' => $this->paymentManager->getAvailableGatewayNames(),
        ]);
    }

    /**
     * Create new gateway
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:payment_gateways',
            'display_name' => 'required|string',
            'description' => 'nullable|string',
            'provider' => 'required|string',
            'credentials' => 'required|array',
            'is_test_mode' => 'boolean',
            'min_amount' => 'numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'commission_rate' => 'numeric|min:0|max:100',
            'fixed_commission' => 'numeric|min:0',
        ]);

        // Validate credentials
        if (!$this->paymentManager->validateGatewayConfig($validated['name'], $validated['credentials'])) {
            return response()->json([
                'success' => false,
                'error' => 'Geçersiz gateway yapılandırması. Gerekli tüm alanları doldurun.',
            ], 400);
        }

        $gateway = PaymentGateway::create($validated);
        $this->paymentManager->clearCache();

        return response()->json([
            'success' => true,
            'gateway' => $gateway,
        ], 201);
    }

    /**
     * Update gateway
     */
    public function update(Request $request, $id)
    {
        $gateway = PaymentGateway::findOrFail($id);

        $validated = $request->validate([
            'display_name' => 'string',
            'description' => 'nullable|string',
            'credentials' => 'array',
            'is_active' => 'boolean',
            'is_test_mode' => 'boolean',
            'sort_order' => 'integer',
            'min_amount' => 'numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'commission_rate' => 'numeric|min:0|max:100',
            'fixed_commission' => 'numeric|min:0',
        ]);

        // Validate credentials if provided
        if (isset($validated['credentials'])) {
            $credentials = array_merge($gateway->credentials ?? [], $validated['credentials']);
            if (!$this->paymentManager->validateGatewayConfig($gateway->name, $credentials)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Geçersiz gateway yapılandırması',
                ], 400);
            }
            $validated['credentials'] = $credentials;
        }

        $gateway->update($validated);
        $this->paymentManager->clearCache();

        return response()->json([
            'success' => true,
            'gateway' => $gateway,
        ]);
    }

    /**
     * Toggle gateway active status
     */
    public function toggleActive($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->update(['is_active' => !$gateway->is_active]);
        $this->paymentManager->clearCache();

        return response()->json([
            'success' => true,
            'is_active' => $gateway->is_active,
        ]);
    }

    /**
     * Toggle test mode
     */
    public function toggleTestMode($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        $gateway->update(['is_test_mode' => !$gateway->is_test_mode]);
        $this->paymentManager->clearCache();

        return response()->json([
            'success' => true,
            'is_test_mode' => $gateway->is_test_mode,
        ]);
    }

    /**
     * Delete gateway
     */
    public function destroy($id)
    {
        $gateway = PaymentGateway::findOrFail($id);
        
        // Check if gateway has transactions
        if ($gateway->transactions()->exists()) {
            return response()->json([
                'success' => false,
                'error' => 'Bu gateway ile işlem yapılmış. Silinemez, devre dışı bırakabilirsiniz.',
            ], 400);
        }

        $gateway->delete();
        $this->paymentManager->clearCache();

        return response()->json([
            'success' => true,
        ]);
    }
}
