<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use PDF;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update($request->all());
        return redirect()->route('admin.orders.index')->with('success', 'Sipariş güncellendi.');
    }

    public function addNote(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->note = $request->input('note');
        $order->save();
        return back()->with('success', 'Not eklendi.');
    }

    public function export()
    {
        $orders = Order::all();
        // CSV export logic buraya entegre edilir (isteğe bağlı)
        return response()->json($orders);
    }

    public function invoicePreview($id)
    {
        $order = Order::with(['vendor', 'user', 'product'])->findOrFail($id);
        return view('admin.orders.invoice-preview', compact('order'));
    }

    public function downloadInvoice($id)
    {
        $order = Order::with(['vendor', 'user', 'product'])->findOrFail($id);

        $pdf = PDF::loadView('admin.orders.invoice-preview', compact('order'))
                  ->setPaper('A4', 'portrait');

        $filename = 'invoice_' . $order->id . '.pdf';

        return $pdf->download($filename);
    }
}