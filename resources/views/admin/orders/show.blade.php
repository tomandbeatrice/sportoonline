@extends('admin.layouts.app')

@section('title', 'Sipariş Detayı')

@section('content')
<div class="container">
    <h1>Sipariş #{{ $order->id }}</h1>

    <div class="mb-3">
        <strong>Kullanıcı:</strong> {{ optional($order->user)->name }}<br>
        <strong>Durum:</strong> {{ $order->status }}<br>
        <strong>Toplam Tutar:</strong> ₺{{ number_format($order->total_price, 2) }}<br>
        <strong>Tarih:</strong> {{ $order->created_at->format('d.m.Y H:i') }}
    </div>

    <div class="mb-4">
        <a href="{{ route('admin.orders.invoice.pdf', $order->id) }}" class="btn btn-outline-dark">
            <i class="bi bi-file-earmark-pdf"></i> PDF İndir
        </a>
        <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary">
            <i class="bi bi-pencil"></i> Düzenle
        </a>
        @if(isset($vendor))
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#pdfPreviewModal">
            <i class="bi bi-eye"></i> Vendor PDF Önizleme
        </button>
        @endif
    </div>

    <h4>Ürünler</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ürün</th>
                <th>Adet</th>
                <th>Fiyat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->title }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>₺{{ number_format($item->price, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Loglar</h4>
    <ul>
        @foreach ($order->logs as $log)
            <li>
                <strong>{{ $log->created_at->format('d.m.Y H:i') }}</strong> -
                {{ $log->note }} ({{ $log->action_type }})
                @if($log->admin)
                    - <em>{{ $log->admin->name }}</em>
                @endif
            </li>
        @endforeach
    </ul>
</div>
@endsection

<a href="{{ route('admin.external-pdf.exportZip', [
    'vendor' => $vendor->id,
    'token' => config('app.vendor_pdf_token')
]) }}" class="btn btn-outline-success">
    <i class="bi bi-file-earmark-zip"></i> Vendor PDF Zip İndir
</a>


@if(isset($vendor))
@php
    $timestamp = now()->timestamp;
@endphp

<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">PDF Önizleme</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <iframe
          src="{{ route('admin.external-pdf.view', [
              'file' => 'vendor1-report.pdf',
              'token' => config('app.vendor_pdf_token'),
              'vendor' => $vendor->id,
              'ts' => $timestamp
          ]) }}"
          width="100%"
          height="600px"
          style="border:none;"
        ></iframe>
      </div>
    </div>
  </div>
</div>
@endif