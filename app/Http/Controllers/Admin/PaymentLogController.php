use App\Models\PaymentExportLog;

public function exportCsv(Request $request)
{
    $query = Payment::query();

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('method')) {
        $query->where('method', $request->method);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $payments = $query->orderByDesc('created_at')->get();

    $csvData = [];
    $csvData[] = ['ID', 'User ID', 'Order ID', 'Method', 'Amount', 'Status', 'Paid At', 'Created At'];

    foreach ($payments as $p) {
        $csvData[] = [
            $p->id,
            $p->user_id,
            $p->order_id,
            $p->method,
            number_format($p->amount, 2, '.', ''),
            $p->status,
            $p->paid_at,
            $p->created_at
        ];
    }

    $filename = 'payment_logs_' . now()->format('Ymd_His') . '.csv';

    // 🧠 Export işlemini logla
    PaymentExportLog::create([
        'filename' => $filename,
        'filters' => $request->all(),
        'admin_id' => auth()->id(),
        'exported_at' => now()
    ]);

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\""
    ];

    $callback = function () use ($csvData) {
        $handle = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    };

    return Response::stream($callback, 200, $headers);
}