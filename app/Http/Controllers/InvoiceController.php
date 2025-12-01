public function generate($orderId)
{
    $order = Order::with(['products', 'buyer'])->findOrFail($orderId);

    $html = view('invoices.template', compact('order'))->render();

    return response($html);
}