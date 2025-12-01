namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    // 📦 Tüm siparişleri listele (admin paneli)
    public function index()
    {
        return Order::with(['products', 'buyer', 'seller'])->latest()->get();
    }

    // 🛒 Sepet üzerinden çoklu sipariş
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer'
        ]);

        $total = collect($validated['items'])->sum(fn($i) => $i['price'] * $i['quantity']);

        $order = Order::create([
            'buyer_id' => auth()->id(),
            'status' => 'beklemede',
            'total' => $total
        ]);

        foreach ($validated['items'] as $item) {
            $order->products()->attach($item['id'], ['quantity' => $item['quantity']]);
        }

        return response()->json([
            'message' => 'Sepet siparişi oluşturuldu',
            'order_id' => $order->id
        ]);
    }

    // 🛍️ Tek ürün siparişi
    public function storeSingle(Request $request)
    {
        $validated = $request->validate([
            'seller_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'price' => 'required|numeric'
        ]);

        $order = Order::create([
            'buyer_id' => auth()->id(),
            'seller_id' => $validated['seller_id'],
            'product_id' => $validated['product_id'],
            'price' => $validated['price'],
            'status' => 'beklemede'
        ]);

        return response()->json([
            'message' => 'Tek ürün siparişi oluşturuldu',
            'order' => $order
        ]);
    }

    // ✅ Siparişi tamamla ve rozet kontrolü
    public function complete(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status' => 'tamamlandı',
            'completed_at' => now()
        ]);

        if ($order->seller_id) {
            $seller = User::find($order->seller_id);
            $badges = $seller->badges ?? [];

            if (!in_array('first_order', $badges)) {
                $seller->badges = array_merge($badges, ['first_order']);
                $seller->save();
            }
        }

        return response()->json([
            'message' => 'Sipariş tamamlandı ve rozet kontrol edildi'
        ]);
    }

    // 📊 Satıcı sipariş istatistikleri
    public function sellerStats($sellerId)
    {
        $total = Order::where('seller_id', $sellerId)->count();
        $completed = Order::where('seller_id', $sellerId)->where('status', 'tamamlandı')->count();

        return response()->json([
            'totalOrders' => $total,
            'completedOrders' => $completed,
            'completionRate' => $total ? round($completed / $total * 100, 2) : 0
        ]);
    }

    // 📄 Sipariş detay ekranı
    public function show($id)
    {
        $order = Order::with(['products', 'buyer', 'seller'])->findOrFail($id);

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'price' => $order->price,
            'tracking_code' => $order->tracking_code,
            'paid_at' => $order->paid_at,
            'products' => $order->products,
            'buyer' => $order->buyer,
            'seller' => $order->seller
        ]);
    }
}