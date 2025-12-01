namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvitationCode;

class InvitationController extends Controller
{
    public function getAnalytics()
    {
        $logs = InvitationCode::with(['inviter', 'usedBy'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($log) {
                return [
                    'code'       => $log->code,
                    'inviter'    => $log->inviter->name ?? '—',
                    'used_by'    => $log->usedBy->name ?? '—',
                    'used_at'    => $log->usedAtFormatted(),
                    'is_used'    => $log->isUsed(),
                    'created_at' => $log->created_at->format('d.m.Y H:i')
                ];
            });

        return response()->json($logs);
    }
}