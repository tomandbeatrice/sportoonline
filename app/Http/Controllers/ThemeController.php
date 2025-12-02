namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ThemeController extends Controller
{
    public function store(Request $request) {
        Storage::disk('local')->put('theme.json', json_encode($request->all()));
        return response()->json(['message' => 'Tema kaydedildi']);
    }
}