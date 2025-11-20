namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VendorStatsExport implements FromView
{
    protected $vendors;

    public function __construct($vendors)
    {
        $this->vendors = $vendors;
    }

    public function view(): View
    {
        return view('admin.exports.vendor_stats_excel', [
            'vendors' => $this->vendors
        ]);
    }
}