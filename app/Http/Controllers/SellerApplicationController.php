<?php

namespace App\Http\Controllers;

use App\Models\SellerApplication;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SellerApplicationController extends Controller
{
    /**
     * Satıcı başvuru formu gönderimi
     */
    public function register(Request $request)
    {
        // Temel validasyon
        $baseRules = [
            'service_type' => 'required|string|in:products,food,hotel,transport,services,career',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:seller_applications,email|unique:users,email',
            'phone' => 'required|string|max:20',
            'company_name' => 'required|string|max:255',
            'tax_number' => 'nullable|string|max:11',
            'tax_office' => 'nullable|string|max:255',
            'company_address' => 'required|string',
            'bank_name' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:26',
            'account_holder' => 'nullable|string|max:255',
        ];

        // Hizmet türüne göre ek validasyon
        $serviceType = $request->get('service_type');
        $serviceDataRules = [];

        switch ($serviceType) {
            case 'products':
                $serviceDataRules = [
                    'categories' => 'required|array|min:1',
                ];
                break;
            case 'food':
                $serviceDataRules = [
                    'cuisines' => 'required|array|min:1',
                    'seating_capacity' => 'nullable|integer|min:1',
                    'has_delivery' => 'nullable|boolean',
                ];
                break;
            case 'hotel':
                $serviceDataRules = [
                    'room_count' => 'required|integer|min:1',
                    'star_rating' => 'required|integer|min:1|max:5',
                    'amenities' => 'nullable|array',
                ];
                break;
            case 'transport':
                $serviceDataRules = [
                    'vehicle_count' => 'required|integer|min:1',
                    'vehicle_type' => 'required|string',
                    'service_region' => 'required|string',
                ];
                break;
            case 'services':
                $serviceDataRules = [
                    'skills' => 'required|array|min:1',
                    'experience_years' => 'nullable|integer|min:0',
                    'hourly_rate' => 'nullable|numeric|min:0',
                ];
                break;
            case 'career':
                $serviceDataRules = [
                    'sectors' => 'required|array|min:1',
                    'job_types' => 'nullable|array',
                ];
                break;
        }

        $data = $request->validate(array_merge($baseRules, $serviceDataRules));

        // service_data JSON alanını hazırla
        $serviceData = [];
        $serviceFields = ['categories', 'cuisines', 'seating_capacity', 'has_delivery', 
                         'room_count', 'star_rating', 'amenities', 'vehicle_count', 
                         'vehicle_type', 'service_region', 'skills', 'experience_years', 
                         'hourly_rate', 'sectors', 'job_types'];
        
        foreach ($serviceFields as $field) {
            if (isset($data[$field])) {
                $serviceData[$field] = $data[$field];
                unset($data[$field]);
            }
        }
        
        $data['service_data'] = $serviceData;

        $application = SellerApplication::create($data);

        // Send confirmation email
        try {
            \Mail::to($data['email'])->send(new \App\Mail\SellerApplicationReceived($application));
        } catch (\Exception $e) {
            \Log::error('Seller application email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Başvurunuz alındı. En kısa sürede geri dönüş yapılacaktır.',
            'application' => $application,
        ], 201);
    }

    /**
     * Admin - Bekleyen başvuru listesi
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $serviceType = $request->get('service_type');
        
        $query = SellerApplication::where('status', $status);
        
        // Hizmet türüne göre filtrele
        if ($serviceType && $serviceType !== 'all') {
            $query->where('service_type', $serviceType);
        }
        
        $applications = $query->with('approvedBy:id,name')
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json($applications);
    }

    /**
     * Admin - Hizmet türüne göre istatistikler
     */
    public function statsByServiceType(Request $request)
    {
        $serviceType = $request->get('service_type');
        
        $query = SellerApplication::query();
        
        if ($serviceType && $serviceType !== 'all') {
            $query->where('service_type', $serviceType);
        }
        
        $stats = [
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'approved' => (clone $query)->where('status', 'approved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
            'total' => $query->count(),
            'by_service_type' => SellerApplication::selectRaw('service_type, status, COUNT(*) as count')
                ->groupBy('service_type', 'status')
                ->get()
                ->groupBy('service_type'),
        ];

        return response()->json($stats);
    }

    /**
     * Admin - Başvuru detayları
     */
    public function show(SellerApplication $application)
    {
        $application->load('approvedBy:id,name');
        
        return response()->json($application);
    }

    /**
     * Admin - Başvuruyu onayla
     */
    public function approve(Request $request, SellerApplication $application)
    {
        if ($application->status !== 'pending') {
            return response()->json([
                'error' => 'Bu başvuru zaten işleme alınmış'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Create user account
            $user = User::create([
                'name' => $application->first_name . ' ' . $application->last_name,
                'email' => $application->email,
                'password' => Hash::make(\Str::random(16)), // Random password, will be reset via email
                'phone' => $application->phone,
                'role' => 'seller',
                'email_verified_at' => now(),
            ]);

            // Create vendor record
            $vendor = Vendor::create([
                'user_id' => $user->id,
                'name' => $application->company_name,
                'email' => $application->email,
                'phone' => $application->phone,
                'address' => $application->company_address,
                'tax_number' => $application->tax_number,
                'tax_office' => $application->tax_office,
                'bank_name' => $application->bank_name,
                'iban' => $application->iban,
                'account_holder' => $application->account_holder,
                'status' => 'active',
                'is_approved' => true,
                'approved_at' => now(),
            ]);

            // Update application status
            $application->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            // Send approval email with password reset link
            try {
                \Mail::to($application->email)->send(
                    new \App\Mail\SellerApplicationApproved($application, $user)
                );
            } catch (\Exception $e) {
                \Log::error('Seller approval email failed: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Başvuru onaylandı ve satıcı hesabı oluşturuldu',
                'user' => $user,
                'vendor' => $vendor,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'error' => 'Başvuru onaylanırken hata oluştu',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Admin - Başvuruyu reddet
     */
    public function reject(Request $request, SellerApplication $application)
    {
        if ($application->status !== 'pending') {
            return response()->json([
                'error' => 'Bu başvuru zaten işleme alınmış'
            ], 400);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $application->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_by' => auth()->id(),
        ]);

        // Send rejection email
        try {
            \Mail::to($application->email)->send(
                new \App\Mail\SellerApplicationRejected($application)
            );
        } catch (\Exception $e) {
            \Log::error('Seller rejection email failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Başvuru reddedildi',
        ]);
    }

    /**
     * İstatistikler
     */
    public function stats()
    {
        $stats = [
            'pending' => SellerApplication::where('status', 'pending')->count(),
            'approved' => SellerApplication::where('status', 'approved')->count(),
            'rejected' => SellerApplication::where('status', 'rejected')->count(),
            'total' => SellerApplication::count(),
        ];

        return response()->json($stats);
    }
}
