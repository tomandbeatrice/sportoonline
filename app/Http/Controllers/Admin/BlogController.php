<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Blog yazılarını listele
     */
    public function index(Request $request)
    {
        $query = BlogPost::with(['author', 'category']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('content', 'like', "%{$request->search}%");
            });
        }

        if ($request->has('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        $posts = $query->orderByDesc('created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($posts);
    }

    /**
     * İstatistikler
     */
    public function stats()
    {
        $stats = [
            'total_posts' => BlogPost::count(),
            'published_posts' => BlogPost::where('status', 'published')->count(),
            'draft_posts' => BlogPost::where('status', 'draft')->count(),
            'total_views' => BlogPost::sum('views'),
            'total_comments' => \DB::table('blog_comments')->count(),
            'posts_this_month' => BlogPost::whereMonth('created_at', now()->month)->count(),
            'popular_posts' => BlogPost::orderByDesc('views')->limit(5)->get(['id', 'title', 'views']),
            'categories' => BlogCategory::withCount('posts')->orderByDesc('posts_count')->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Blog yazısı detayı
     */
    public function show($id)
    {
        $post = BlogPost::with(['author', 'category', 'tags', 'comments.user'])
            ->findOrFail($id);

        return response()->json($post);
    }

    /**
     * Yeni blog yazısı
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:blog_categories,id',
            'featured_image' => 'nullable|image|max:4096',
            'tags' => 'nullable|array',
            'status' => 'in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('blog', 'public');
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['author_id'] = auth()->id();
        $validated['views'] = 0;

        // Auto-generate excerpt if not provided
        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['content']), 200);
        }

        // Set published_at for immediate publish
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = BlogPost::create($validated);

        // Attach tags
        if (!empty($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Blog yazısı oluşturuldu',
            'post' => $post->load(['category', 'tags'])
        ], 201);
    }

    /**
     * Blog yazısı güncelle
     */
    public function update(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'nullable|exists:blog_categories,id',
            'featured_image' => 'nullable|image|max:4096',
            'tags' => 'nullable|array',
            'status' => 'in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:70',
            'meta_description' => 'nullable|string|max:160',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) {
                Storage::disk('public')->delete($post->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('blog', 'public');
        }

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Set published_at when publishing
        if (isset($validated['status']) && $validated['status'] === 'published' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        $post->update($validated);

        // Update tags
        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Blog yazısı güncellendi',
            'post' => $post->fresh()->load(['category', 'tags'])
        ]);
    }

    /**
     * Blog yazısı sil
     */
    public function destroy($id)
    {
        $post = BlogPost::findOrFail($id);

        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog yazısı silindi'
        ]);
    }

    /**
     * Durumu değiştir
     */
    public function toggleStatus(Request $request, $id)
    {
        $post = BlogPost::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:draft,published'
        ]);

        if ($validated['status'] === 'published' && !$post->published_at) {
            $post->published_at = now();
        }

        $post->status = $validated['status'];
        $post->save();

        return response()->json([
            'success' => true,
            'message' => 'Durum güncellendi',
            'status' => $post->status
        ]);
    }

    /**
     * Öne çıkarma toggle
     */
    public function toggleFeatured($id)
    {
        $post = BlogPost::findOrFail($id);
        $post->update(['is_featured' => !$post->is_featured]);

        return response()->json([
            'success' => true,
            'message' => $post->is_featured ? 'Yazı öne çıkarıldı' : 'Öne çıkarma kaldırıldı'
        ]);
    }

    // ==========================================
    // KATEGORİ YÖNETİMİ
    // ==========================================

    /**
     * Kategorileri listele
     */
    public function getCategories()
    {
        $categories = BlogCategory::withCount('posts')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Kategori ekle
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category = BlogCategory::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori eklendi',
            'category' => $category
        ], 201);
    }

    /**
     * Kategori güncelle
     */
    public function updateCategory(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'description' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:20',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori güncellendi',
            'category' => $category->fresh()
        ]);
    }

    /**
     * Kategori sil
     */
    public function deleteCategory($id)
    {
        $category = BlogCategory::findOrFail($id);

        if ($category->posts()->count() > 0) {
            return response()->json([
                'error' => 'Bu kategoride yazılar var, önce yazıları taşıyın'
            ], 400);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori silindi'
        ]);
    }

    // ==========================================
    // YORUM YÖNETİMİ
    // ==========================================

    /**
     * Yorumları listele
     */
    public function getComments(Request $request)
    {
        $query = \DB::table('blog_comments')
            ->join('blog_posts', 'blog_comments.post_id', '=', 'blog_posts.id')
            ->leftJoin('users', 'blog_comments.user_id', '=', 'users.id')
            ->select(
                'blog_comments.*',
                'blog_posts.title as post_title',
                'users.name as user_name'
            );

        if ($request->has('status')) {
            $query->where('blog_comments.status', $request->status);
        }

        if ($request->has('post_id')) {
            $query->where('blog_comments.post_id', $request->post_id);
        }

        $comments = $query->orderByDesc('blog_comments.created_at')
            ->paginate($request->get('per_page', 20));

        return response()->json($comments);
    }

    /**
     * Yorum onayla
     */
    public function approveComment($id)
    {
        \DB::table('blog_comments')
            ->where('id', $id)
            ->update(['status' => 'approved', 'updated_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Yorum onaylandı'
        ]);
    }

    /**
     * Yorum reddet
     */
    public function rejectComment($id)
    {
        \DB::table('blog_comments')
            ->where('id', $id)
            ->update(['status' => 'rejected', 'updated_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'Yorum reddedildi'
        ]);
    }

    /**
     * Yorum sil
     */
    public function deleteComment($id)
    {
        \DB::table('blog_comments')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Yorum silindi'
        ]);
    }

    // ============================================
    // PUBLIC ENDPOINTS (No Auth Required)
    // ============================================

    /**
     * Public: Blog kategorilerini listele
     */
    public function publicCategories()
    {
        $categories = BlogCategory::where('is_active', true)
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published')
                    ->where('published_at', '<=', now());
            }])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Public: Yayınlanmış blog yazılarını listele
     */
    public function publicPosts(Request $request)
    {
        $query = BlogPost::with(['author:id,name', 'category:id,name,slug'])
            ->where('status', 'published')
            ->where('published_at', '<=', now());

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('excerpt', 'like', "%{$request->search}%");
            });
        }

        $posts = $query->orderByDesc('published_at')
            ->paginate($request->get('per_page', 10));

        return response()->json($posts);
    }

    /**
     * Public: Öne çıkan yazıları getir
     */
    public function featuredPosts(Request $request)
    {
        $limit = $request->get('limit', 5);

        $posts = BlogPost::with(['author:id,name', 'category:id,name,slug'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();

        return response()->json($posts);
    }

    /**
     * Public: Tek bir blog yazısı detayı (slug ile)
     */
    public function publicShowPost($slug)
    {
        $post = BlogPost::with(['author:id,name', 'category:id,name,slug'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        // View sayısını artır
        $post->increment('views');

        // İlgili yazılar
        $relatedPosts = BlogPost::where('id', '!=', $post->id)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->where(function ($query) use ($post) {
                $query->where('category_id', $post->category_id);
                if (!empty($post->tags)) {
                    foreach ($post->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->orderByDesc('published_at')
            ->limit(4)
            ->get(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at']);

        return response()->json([
            'post' => $post,
            'related' => $relatedPosts
        ]);
    }
}

