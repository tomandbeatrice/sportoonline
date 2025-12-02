namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return Product::latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category' => 'nullable|string',
            'image' => 'nullable|image'
        ]);

        if ($request->hasFile('image')) {
            $validated['imageUrl'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            ...$validated,
            'vendor_id' => auth()->user()->vendor_id ?? null
        ]);

        return response()->json(['message' => 'Ürün eklendi', 'product' => $product]);
    }

    public function show($id)
    {
        return Product::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json(['message' => 'Güncellendi', 'product' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->imageUrl) {
            Storage::disk('public')->delete($product->imageUrl);
        }
        $product->delete();
        return response()->json(['message' => 'Silindi']);
    }
}