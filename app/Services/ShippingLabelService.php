<?php

namespace App\Services;

use App\Models\ReturnRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;

class ShippingLabelService
{
    public function generateLabel(ReturnRequest $returnRequest): string
    {
        try {
            $pdf = Pdf::loadView('pdfs.shipping-label', [
                'returnRequest' => $returnRequest,
                'order' => $returnRequest->order,
                'user' => $returnRequest->user,
                'barcode' => $this->generateBarcode($returnRequest->return_number),
            ]);
            
            $labelDir = storage_path('app/public/return-labels');
            if (!file_exists($labelDir)) {
                mkdir($labelDir, 0755, true);
            }
            
            $filename = 'return-label-' . $returnRequest->return_number . '.pdf';
            $filepath = $labelDir . '/' . $filename;
            $pdf->save($filepath);
            
            $labelUrl = '/storage/return-labels/' . $filename;
            $returnRequest->update(['shipping_label_url' => $labelUrl]);
            
            Log::info('Shipping label generated', [
                'return_id' => $returnRequest->id,
                'label_url' => $labelUrl
            ]);
            
            return $labelUrl;
            
        } catch (Exception $e) {
            Log::error('Failed to generate shipping label: ' . $e->getMessage(), [
                'return_id' => $returnRequest->id
            ]);
            throw $e;
        }
    }
    
    protected function generateBarcode(string $returnNumber): string
    {
        $escapedNumber = htmlspecialchars($returnNumber, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="60"><rect width="200" height="60" fill="white"/><text x="100" y="40" font-family="monospace" font-size="14" text-anchor="middle">' . $escapedNumber . '</text></svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
