@if($order->logs->isEmpty())
    <div class="alert alert-warning">Bu sipariş için işlem kaydı bulunamadı.</div>
@else
    <div class="card border shadow-sm mb-4">
        <div class="card-header">
            <strong>📋 İşlem Geçmişi</strong>
        </div>
        <ul class="list-group list-group-flush">
            @foreach($order->logs as $log)
                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <span class="fw-bold">{{ $log->admin->name }}</span>
                        <small class="text-muted ms-2">
                            {{ $log->created_at->format('d.m.Y H:i') }}
                        </small>
                        <br>
                        <span>{{ $log->note }}</span>
                    </div>
                    <span class="badge 
                        @class([
                            'bg-warning' => $log->action_type === 'status_change',
                            'bg-info'    => $log->action_type === 'custom_note',
                            'bg-dark'    => !in_array($log->action_type, ['status_change', 'custom_note'])
                        ])">
                        {{ strtoupper(str_replace('_', ' ', $log->action_type)) }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
@endif