namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\VendorLogExport;
use App\Models\VendorLog;
use PDF;
use Excel;

class VendorLogExportController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->input('format', 'xlsx');
        $fileName = 'vendor_logs_'.now()->format('Ymd_His').'.'.$format;

        if ($format === 'pdf') {
            $data = VendorLog::all();
            $pdf = PDF::loadView('exports.vendor_logs_pdf', compact('data'));
            return $pdf->download($fileName);
        }

        return Excel::download(new VendorLogExport, $fileName);
    }
}