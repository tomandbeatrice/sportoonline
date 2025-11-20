<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\Support\Facades\Response;
use PDF;

class VendorSettingsController extends Controller
{
    /**
     * Vendor ayarlarını kaydeder.
     */
    public function save(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'branding' => 'array',
            'branding.logo' => 'nullable|image|max:2048',
            'branding.colors' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        $vendor->settings = $validated['settings'] ?? [];
        $vendor->branding = $validated['branding'] ?? [];
        $vendor->save();

        return response()->json(['status' => 'success']);
    }

    /**
     * Branding test PDF döner (admin içi).
     */
    public function test(Request $request, Vendor $vendor)
    {
        $columns = $request->input('columns', ['name', 'price', 'category']);
        $sampleData = $this->getSampleData();

        $pdf = PDF::loadView('admin.vendors.test_pdf', [
            'vendor' => $vendor,
            'columns' => $columns,
            'data' => $sampleData,
            'branding' => $vendor->branding,
        ]);

        return Response::make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="test.pdf"',
        ]);
    }

    /**
     * Branding test PDF – dış erişimli, tokenlı.
     */
    public function externalPdfPreview(string $token)
    {
        $vendor = Vendor::where('external_pdf_token', $token)->firstOrFail();
        $columns = $vendor->branding['columns'] ?? ['name', 'price', 'category'];
        $sampleData = $this->getSampleData();

        $pdf = PDF::loadView('admin.vendors.test_pdf', [
            'vendor' => $vendor,
            'columns' => $columns,
            'data' => $sampleData,
            'branding' => $vendor->branding,
        ]);

        return Response::make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="demo_vendor_test.pdf"',
        ]);
    }

    /**
     * Örnek ürün verisi.
     */
    protected function getSampleData(): \Illuminate\Support\Collection
    {
        return collect([
            ['name' => 'Ürün A', 'price' => '₺99.99', 'category' => 'Kategori 1'],
            ['name' => 'Ürün B', 'price' => '₺149.99', 'category' => 'Kategori 2'],
        ]);
    }
}