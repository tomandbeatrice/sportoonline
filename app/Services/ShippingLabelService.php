<?php

namespace App\Services;

use App\Models\ReturnRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Exception;

class ShippingLabelService
{
    /**
     * Generate shipping label PDF for return request
     */
    public function generateReturnLabel(ReturnRequest $returnRequest): string
    {
        try {
            // Load return request with relationships
            $returnRequest->load(['user', 'order', 'orderItem.product', 'vendor']);
            
            // Generate PDF
            $pdf = Pdf::loadView('pdfs.return-shipping-label', [
                'returnRequest' => $returnRequest,
                'returnNumber' => $returnRequest->return_number,
                'trackingNumber' => $returnRequest->tracking_number ?? 'N/A',
                'carrier' => $returnRequest->shipping_carrier ?? 'Kargo Şirketi',
                'customerName' => $returnRequest->user->name,
                'customerAddress' => $returnRequest->order->shipping_address ?? $returnRequest->order->address ?? 'Adres bilgisi eksik',
                'vendorName' => $returnRequest->vendor->name ?? 'SportOnline',
                'vendorAddress' => $returnRequest->vendor->address ?? 'SportOnline Merkez',
                'createdAt' => $returnRequest->created_at->format('d.m.Y H:i'),
            ]);
            
            // Set paper size to A4
            $pdf->setPaper('A4', 'portrait');
            
            // Generate unique filename
            $filename = 'return-labels/' . $returnRequest->return_number . '.pdf';
            
            // Ensure directory exists
            Storage::disk('public')->makeDirectory('return-labels');
            
            // Save PDF to storage
            Storage::disk('public')->put($filename, $pdf->output());
            
            // Return the URL
            return '/storage/' . $filename;
        } catch (Exception $e) {
            throw new Exception('PDF oluşturma hatası: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate standard shipping label for order
     */
    public function generateOrderLabel($order): string
    {
        try {
            $pdf = Pdf::loadView('pdfs.shipping-label', [
                'order' => $order,
                'orderNumber' => $order->order_number,
                'trackingNumber' => $order->tracking_number ?? 'N/A',
                'carrier' => $order->shipping_carrier ?? 'Kargo Şirketi',
                'customerName' => $order->user->name,
                'customerAddress' => $order->shipping_address ?? $order->address,
                'vendorName' => $order->vendor->name ?? 'SportOnline',
                'vendorAddress' => $order->vendor->address ?? 'SportOnline Merkez',
                'createdAt' => $order->created_at->format('d.m.Y H:i'),
            ]);
            
            $pdf->setPaper('A4', 'portrait');
            
            $filename = 'shipping-labels/' . $order->order_number . '.pdf';
            
            Storage::disk('public')->makeDirectory('shipping-labels');
            Storage::disk('public')->put($filename, $pdf->output());
            
            return '/storage/' . $filename;
        } catch (Exception $e) {
            throw new Exception('PDF oluşturma hatası: ' . $e->getMessage());
        }
    }
}
