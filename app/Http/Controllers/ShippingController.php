public function assignTracking(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|exists:orders,id',
        'carrier' => 'required|string'
    ]);

    $trackingCode = strtoupper($validated['carrier']) . '-' . rand(100000, 999999);

    $order = Order::find($validated['order_id']);
    $order->tracking_code = $trackingCode;
    $order->shipping_status = 'hazırlanıyor';
    $order->save();

    return response()->json(['tracking_code' => $trackingCode]);
}