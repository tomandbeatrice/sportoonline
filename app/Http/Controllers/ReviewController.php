<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Create a new review
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'comment' => 'required|string',
            'pros' => 'nullable|string',
            'cons' => 'nullable|string',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|max:5120' // 5MB per image
        ]);

        // Check if user already reviewed this product
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingReview) {
            return response()->json(['error' => 'Bu ürünü zaten değerlendirdiniz'], 400);
        }

        // Check if user purchased this product
        $hasPurchased = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $request->product_id)
            ->where('orders.user_id', auth()->id())
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->exists();

        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('review-photos', 'public');
                $photos[] = [
                    'url' => Storage::url($path),
                    'path' => $path
                ];
            }
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'pros' => $request->pros,
            'cons' => $request->cons,
            'photos' => json_encode($photos),
            'verified_purchase' => $hasPurchased,
            'helpful_count' => 0,
            'not_helpful_count' => 0
        ]);

        // Update product average rating
        $this->updateProductRating($request->product_id);

        $review->load('user');
        $review->user_name = $review->user->name ?? 'Anonim';

        return response()->json(['review' => $review], 201);
    }

    /**
     * List all reviews
     */
    public function index(Request $request)
    {
        $query = Review::with('user');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $reviews = $query->orderByDesc('created_at')->get();
        
        $reviews->transform(function($review) {
            $review->user_name = $review->user->name ?? 'Anonim';
            $review->photos = json_decode($review->photos, true) ?? [];
            return $review;
        });

        return response()->json($reviews);
    }

    /**
     * Get reviews for a specific product
     */
    public function productReviews($productId, Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $query = Review::where('product_id', $productId)
            ->with(['user', 'sellerResponse']);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter photos only
        if ($request->boolean('with_photos')) {
            $query->whereNotNull('photos')
                ->where('photos', '!=', '[]');
        }

        // Sorting
        switch ($request->get('sort', 'recent')) {
            case 'helpful':
                $query->orderByDesc('helpful_count');
                break;
            case 'highest':
                $query->orderByDesc('rating');
                break;
            case 'lowest':
                $query->orderBy('rating');
                break;
            default: // recent
                $query->orderByDesc('created_at');
        }

        $reviews = $query->paginate($perPage);

        $reviews->getCollection()->transform(function($review) {
            $review->user_name = $review->user->name ?? 'Anonim';
            $review->photos = json_decode($review->photos, true) ?? [];
            
            // Check if current user found this helpful
            if (auth()->check()) {
                $review->user_found_helpful = DB::table('review_helpful')
                    ->where('review_id', $review->id)
                    ->where('user_id', auth()->id())
                    ->where('is_helpful', true)
                    ->exists();
                    
                $review->user_found_not_helpful = DB::table('review_helpful')
                    ->where('review_id', $review->id)
                    ->where('user_id', auth()->id())
                    ->where('is_helpful', false)
                    ->exists();
            }
            
            return $review;
        });

        return response()->json([
            'reviews' => $reviews->items(),
            'has_more' => $reviews->hasMorePages(),
            'total' => $reviews->total()
        ]);
    }

    /**
     * Get reviews for a vendor
     */
    public function vendorReviews($vendorId)
    {
        $reviews = Review::whereHas('product', function($query) use ($vendorId) {
                $query->where('seller_id', $vendorId);
            })
            ->with('user', 'product')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        $reviews->transform(function($review) {
            $review->user_name = $review->user->name ?? 'Anonim';
            $review->photos = json_decode($review->photos, true) ?? [];
            return $review;
        });

        return response()->json(['reviews' => $reviews]);
    }

    /**
     * Update review
     */
    public function update(Request $request, $id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'title' => 'sometimes|string|max:255',
            'comment' => 'sometimes|string',
            'pros' => 'nullable|string',
            'cons' => 'nullable|string'
        ]);

        $review->update($request->only(['rating', 'title', 'comment', 'pros', 'cons']));

        // Update product rating
        $this->updateProductRating($review->product_id);

        return response()->json(['review' => $review]);
    }

    /**
     * Delete review
     */
    public function destroy($id)
    {
        $review = Review::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $productId = $review->product_id;
        
        // Delete photos
        if ($review->photos) {
            $photos = json_decode($review->photos, true);
            foreach ($photos as $photo) {
                if (isset($photo['path'])) {
                    Storage::disk('public')->delete($photo['path']);
                }
            }
        }

        $review->delete();

        // Update product rating
        $this->updateProductRating($productId);

        return response()->json(['message' => 'Yorum silindi']);
    }

    /**
     * Mark review as helpful
     */
    public function markHelpful($id)
    {
        $review = Review::findOrFail($id);

        // Check if already marked
        $existing = DB::table('review_helpful')
            ->where('review_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing && $existing->is_helpful) {
            // Remove helpful mark
            DB::table('review_helpful')
                ->where('review_id', $id)
                ->where('user_id', auth()->id())
                ->delete();
            
            $review->decrement('helpful_count');
        } else {
            // Add or update helpful mark
            DB::table('review_helpful')->updateOrInsert(
                ['review_id' => $id, 'user_id' => auth()->id()],
                ['is_helpful' => true, 'created_at' => now(), 'updated_at' => now()]
            );
            
            $review->increment('helpful_count');
            
            // If was marked as not helpful, decrement that
            if ($existing && !$existing->is_helpful) {
                $review->decrement('not_helpful_count');
            }
        }

        return response()->json(['message' => 'İşlem başarılı']);
    }

    /**
     * Mark review as not helpful
     */
    public function markNotHelpful($id)
    {
        $review = Review::findOrFail($id);

        // Check if already marked
        $existing = DB::table('review_helpful')
            ->where('review_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing && !$existing->is_helpful) {
            // Remove not helpful mark
            DB::table('review_helpful')
                ->where('review_id', $id)
                ->where('user_id', auth()->id())
                ->delete();
            
            $review->decrement('not_helpful_count');
        } else {
            // Add or update not helpful mark
            DB::table('review_helpful')->updateOrInsert(
                ['review_id' => $id, 'user_id' => auth()->id()],
                ['is_helpful' => false, 'created_at' => now(), 'updated_at' => now()]
            );
            
            $review->increment('not_helpful_count');
            
            // If was marked as helpful, decrement that
            if ($existing && $existing->is_helpful) {
                $review->decrement('helpful_count');
            }
        }

        return response()->json(['message' => 'İşlem başarılı']);
    }

    /**
     * Report review
     */
    public function report(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        DB::table('review_reports')->insert([
            'review_id' => $id,
            'user_id' => auth()->id(),
            'reason' => $request->reason,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json(['message' => 'Şikayet alındı, inceleme yapılacak']);
    }

    /**
     * Update product average rating
     */
    private function updateProductRating($productId)
    {
        $avgRating = Review::where('product_id', $productId)->avg('rating');
        $reviewCount = Review::where('product_id', $productId)->count();

        Product::where('id', $productId)->update([
            'avg_rating' => $avgRating ?? 0,
            'review_count' => $reviewCount
        ]);
    }
}
