<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class InvoiceController extends Controller
{
    public function generate($orderId)
    {
        $order = Order::with(['products', 'buyer'])->findOrFail($orderId);

        $html = view('invoices.template', compact('order'))->render();

        return response($html);
    }
}