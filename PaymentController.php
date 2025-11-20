public function pay(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'card_number' => 'required',
        'expiry' => 'required',
        'cvv' => 'required'
    ]);

    // Sahte POS işlemi
    $order = Order::find($validated['order_id']);
    $order->status = 'ödendi';
    $order->paid_at = now();
    $order->save();

    return response()->json(['message' => 'Ödeme başarılı']);
}