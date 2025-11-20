namespace App\Exports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VendorExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $segments = Vendor::select('segment')->distinct()->pluck('segment');

        $sheets = [];

        foreach ($segments as $segment) {
            $sheets[] = new SegmentSheetExport($segment);
        }

        return $sheets;
    }
}