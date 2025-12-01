namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class BrandingImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $path = $request->file('file')->store('temp');

        $rows = Excel::toCollection(null, storage_path('app/' . $path))->first();

        foreach ($rows as $row) {
            $vendor = Vendor::find($row['vendor_id']);
            if ($vendor) {
                $vendor->branding_color = $row['color'] ?? $vendor->branding_color;
                $vendor->branding_font = $row['font'] ?? $vendor->branding_font;

                if (!empty($row['logo_url'])) {
                    // İsteğe bağlı: logo URL'den dosya çekip storage'a kaydet
                    $logoContents = file_get_contents($row['logo_url']);
                    $filename = 'logos/' . uniqid() . '.png';
                    Storage::put($filename, $logoContents);
                    $vendor->branding_logo = $filename;
                }

                $vendor->save();
            }
        }

        return back()->with('success', 'Branding bilgileri başarıyla güncellendi.');
    }
}