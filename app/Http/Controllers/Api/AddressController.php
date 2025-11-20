<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Get all addresses for authenticated user
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->orderBy('is_default', 'desc')->get();

        return response()->json([
            'success' => true,
            'addresses' => $addresses,
        ]);
    }

    /**
     * Get single address
     */
    public function show($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);

        return response()->json([
            'success' => true,
            'address' => $address,
        ]);
    }

    /**
     * Create new address
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'address' => 'required|string',
            'zip_code' => 'nullable|string|max:10',
            'tax_office' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'type' => 'required|in:shipping,billing,both',
            'is_default' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();

        // If this is the first address, make it default
        if (Auth::user()->addresses()->count() === 0) {
            $validated['is_default'] = true;
        }

        $address = UserAddress::create($validated);

        // If marked as default, update others
        if ($address->is_default) {
            $address->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'address' => $address,
            'message' => 'Adres başarıyla eklendi',
        ], 201);
    }

    /**
     * Update address
     */
    public function update(Request $request, $id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'string|max:255',
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'phone' => 'string|max:20',
            'email' => 'nullable|email|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'string|max:100',
            'district' => 'string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'address' => 'string',
            'zip_code' => 'nullable|string|max:10',
            'tax_office' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'type' => 'in:shipping,billing,both',
            'is_default' => 'boolean',
        ]);

        $address->update($validated);

        // If marked as default, update others
        if (isset($validated['is_default']) && $validated['is_default']) {
            $address->setAsDefault();
        }

        return response()->json([
            'success' => true,
            'address' => $address->fresh(),
            'message' => 'Adres başarıyla güncellendi',
        ]);
    }

    /**
     * Delete address
     */
    public function destroy($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);

        // If this is the default address and there are others, make the first one default
        if ($address->is_default && Auth::user()->addresses()->count() > 1) {
            $nextAddress = Auth::user()->addresses()->where('id', '!=', $id)->first();
            if ($nextAddress) {
                $nextAddress->update(['is_default' => true]);
            }
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Adres başarıyla silindi',
        ]);
    }

    /**
     * Set address as default
     */
    public function setDefault($id)
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $address->setAsDefault();

        return response()->json([
            'success' => true,
            'message' => 'Varsayılan adres güncellendi',
        ]);
    }

    /**
     * Get cities (can be extended with real data)
     */
    public function getCities()
    {
        $cities = [
            'Adana', 'Adıyaman', 'Afyonkarahisar', 'Ağrı', 'Aksaray', 'Amasya', 'Ankara', 'Antalya',
            'Ardahan', 'Artvin', 'Aydın', 'Balıkesir', 'Bartın', 'Batman', 'Bayburt', 'Bilecik',
            'Bingöl', 'Bitlis', 'Bolu', 'Burdur', 'Bursa', 'Çanakkale', 'Çankırı', 'Çorum',
            'Denizli', 'Diyarbakır', 'Düzce', 'Edirne', 'Elazığ', 'Erzincan', 'Erzurum', 'Eskişehir',
            'Gaziantep', 'Giresun', 'Gümüşhane', 'Hakkari', 'Hatay', 'Iğdır', 'Isparta', 'İstanbul',
            'İzmir', 'Kahramanmaraş', 'Karabük', 'Karaman', 'Kars', 'Kastamonu', 'Kayseri', 'Kilis',
            'Kırıkkale', 'Kırklareli', 'Kırşehir', 'Kocaeli', 'Konya', 'Kütahya', 'Malatya', 'Manisa',
            'Mardin', 'Mersin', 'Muğla', 'Muş', 'Nevşehir', 'Niğde', 'Ordu', 'Osmaniye', 'Rize',
            'Sakarya', 'Samsun', 'Şanlıurfa', 'Siirt', 'Sinop', 'Şırnak', 'Sivas', 'Tekirdağ',
            'Tokat', 'Trabzon', 'Tunceli', 'Uşak', 'Van', 'Yalova', 'Yozgat', 'Zonguldak'
        ];

        return response()->json([
            'success' => true,
            'cities' => $cities,
        ]);
    }
}
