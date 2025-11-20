namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Order;
use App\Models\DemoLog;
use App\Models\AutoPlanLog;
use App\Models\Product;

class AdminController extends Controller
{
    // 📊 Genel sistem istatistikleri
    public function stats() { /* ... */ }

    // 🎯 Kampanya bazlı kullanıcı analizi
    public function campaignStats() { /* ... */ }

    // 📈 Kampanya trend analizi (zaman aralıklı)
    public function campaignTrend(Request $request) { /* ... */ }

    // 📤 Kampanya trend verisini CSV olarak dışa aktar
    public function exportCampaignTrend(Request $request) { /* ... */ }

    // 📊 Otomatik planlama trendi (son 6 hafta)
    public function autoPlanTrend() { /* ... */ }

    // 🎯 Kampanya türü başarı analizi
    public function campaignEffectiveness() { /* ... */ }

    // 📈 Kampanya türü başarı eğrisi (zaman bazlı)
    public function campaignSuccessTrend(Request $request) { /* ... */ }

    // 📤 Kampanya başarı eğrisi verisini CSV olarak dışa aktar
    public function exportCampaignSuccessTrend(Request $request) { /* ... */ }

    // 🧠 Kampanya türü haftalık etki puanı
    public function campaignImpactScore(Request $request) { /* ... */ }

    // 📈 Kampanya türü strateji eğrisi (haftalık etki puanı trendi)
    public function campaignImpactTrend() { /* ... */ }

    // 📊 Katılım bazlı kampanya etki analizi
    public function campaignParticipationEffect() { /* ... */ }

    // 🔁 Dinamik ağırlık güncelleme (öneri motoru için)
    public function updateSuggestionWeights()
    {
        $data = $this->campaignParticipationEffect()->getData(true);
        $max = collect($data)->max('conversion_rate');

        foreach ($data as $row) {
            $type = match($row['tag']) {
                'early_access_100' => 'davet',
                'beta_invite' => 'trend',
                'organic' => 'organik'
            };

            $normalized = $max > 0 ? round($row['conversion_rate'] / $max, 2) : 0.33;

            DB::table('campaign_weights')->updateOrInsert(
                ['type' => $type],
                ['w1' => $normalized, 'w2' => round(1 - $normalized, 2), 'w3' => 0.1]
            );
        }

        return response()->json(['status' => 'updated']);
    }
}
public function dashboard()
{
    return response()->json([
        'users' => User::count(),
        'products' => Product::count(),
        'orders' => Order::count(),
        'segments' => Segment::count()
    ]);
}