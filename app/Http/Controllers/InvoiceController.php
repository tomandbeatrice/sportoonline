namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\PlannedCampaign;

class InvoiceController extends Controller
{
    public function getSellerInvoice($sellerId)
    {
        $orders = Order::where('seller_id', $sellerId)
            ->where('status', 'tamamlandı')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json([
                'total' => 0,
                'commission_rate' => 0,
                'net_income' => 0
            ]);
        }

        $total = $orders->sum('amount');

        // Kampanya tag'leri farklı olabilir → en sık kullanılanı al
        $tag = $orders->groupBy('campaign_tag')->sortByDesc(fn($g) => $g->count())->keys()->first();
        $commission = PlannedCampaign::where('tag', $tag)->value('commission_drop') ?? 0;

        $net = $total * (1 - $commission / 100);

        return response()->json([
            'total' => round($total, 2),
            'commission_rate' => round($commission, 2),
            'net_income' => round($net, 2)
        ]);
    }
}