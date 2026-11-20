<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NewsController;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Post;
use App\Models\Region;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use StoresImages;

    public function index(Request $request)
    {
        $posts = Post::with(['region', 'category', 'categories', 'tags'])->withCount('ads')
            ->when($request->q, fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->when($request->region_id, fn ($q, $v) => $q->where('region_id', $v))
            ->when($request->category_id, fn ($q, $v) => $q->inCategory((int) $v))
            ->when($request->status, fn ($q, $v) => $q->where('status', $v))
            ->when((int) $request->year, fn ($q, $y) => $q->whereRaw('COALESCE(published_at, created_at) >= ? AND COALESCE(published_at, created_at) < ?', ["$y-01-01 00:00:00", ($y + 1).'-01-01 00:00:00']))
            ->latest()->paginate(15)->withQueryString();

        $span = Post::selectRaw('MIN(COALESCE(published_at, created_at)) as lo, MAX(COALESCE(published_at, created_at)) as hi')->first();
        $years = $span?->lo ? range((int) substr($span->hi, 0, 4), (int) substr($span->lo, 0, 4)) : [(int) date('Y')];

        return view('admin.posts.index', ['posts' => $posts, 'years' => $years, 'regions' => Region::flatOptions(), 'categories' => Category::orderBy('name')->get()]);
    }

    public function create()
    {
        return $this->form(new Post(['status' => 'published', 'author_name' => 'Bali Globalnews', 'published_at' => now()]));
    }

    public function edit(Post $post)
    {
        return $this->form($post->load('ads', 'categories', 'tags'));
    }

    private function form(Post $post)
    {
        return view('admin.posts.form', [
            'post' => $post,
            'regions' => Region::flatOptions(),
            'categories' => Category::where('is_active', true)->orderBy('sort_order')->get(),
            'ads' => Ad::orderByDesc('is_active')->orderBy('name')->get(),
            'selected' => $post->exists ? $post->ads->groupBy('pivot.placement')->map(fn ($g) => $g->pluck('id')->all())->all() : [],
            'allTags' => Tag::orderBy('name')->pluck('name'),
        ]);
    }

    public function store(Request $request)
    {
        $post = new Post;
        $this->save($request, $post);

        return redirect()->route('admin.posts.index')->with('ok', 'Berita berhasil disimpan.');
    }

    public function update(Request $request, Post $post)
    {
        $this->save($request, $post);

        return redirect()->route('admin.posts.edit', $post)->with('ok', 'Berita berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        $this->deleteImage($post->image);
        $post->delete();

        return back()->with('ok', 'Berita dihapus.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['file' => 'required|image|max:5120'], [], ['file' => 'gambar']);
        $path = $this->storeImage($request->file('file'), 'content');

        return response()->json(['location' => '/media/'.$path]);
    }

    public function preview(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255', 'image' => 'nullable|image|max:5120'], [], ['title' => 'judul']);
        $existing = $request->filled('post_id') ? Post::find($request->integer('post_id')) : null;

        $post = new Post([
            'title' => $request->input('title'),
            'slug' => $existing?->slug ?: (Str::slug((string) $request->input('title')) ?: 'pratinjau'),
            'excerpt' => $request->input('excerpt'),
            'body' => $this->cleanBody($request->input('body')),
            'image_caption' => $request->input('image_caption'),
            'author_name' => $request->input('author_name') ?: 'Bali Globalnews',
            'published_at' => $request->filled('published_at') ? Carbon::parse($request->input('published_at')) : now(),
            'views' => $existing?->views ?? 0,
            'views_manual' => $request->input('views_mode') === 'manual' ? $request->integer('views_manual') : null,
        ]);
        $file = $request->file('image');
        $post->image = $file ? 'data:'.$file->getMimeType().';base64,'.base64_encode($file->get()) : $existing?->image;
        $post->id = $existing?->id ?? 0;

        $region = $request->filled('region_id') ? Region::with('parent')->find($request->integer('region_id')) : null;
        $post->region_id = $region?->id;
        $categories = Category::whereIn('id', (array) $request->input('categories', []))->orderBy('sort_order')->get();
        $post->category_id = $categories->first()?->id;
        $tags = collect(explode(',', (string) $request->input('tags')))->map(fn ($n) => trim($n))->filter()
            ->unique()->map(fn ($n) => new Tag(['name' => $n, 'slug' => Str::slug($n)]))->values();

        $ads = Ad::whereIn('id', collect($request->input('ads', []))->flatten()->all())->get()->keyBy('id');
        $chosen = collect();
        foreach ((array) $request->input('ads', []) as $placement => $ids) {
            foreach ((array) $ids as $id) {
                if ($ad = $ads->get((int) $id)) {
                    $chosen->push((clone $ad)->setRelation('pivot', new Fluent(['placement' => $placement])));
                }
            }
        }
        $post->setRelation('region', $region)->setRelation('category', $categories->first())
            ->setRelation('categories', $categories)->setRelation('tags', $tags)->setRelation('ads', $chosen);

        return view('site.post', app(NewsController::class)->articleData($post) + ['isPreview' => true]);
    }

    private function cleanBody(?string $body): string
    {
        return clean_html($body);
    }

    private function save(Request $request, Post $post): void
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'nullable|string',
            'image' => ($post->image ? 'nullable' : 'required').'|image|max:5120',
            'image_caption' => 'nullable|string|max:255',
            'region_id' => 'nullable|exists:regions,id',
            'categories' => 'array',
            'categories.*' => 'integer|exists:categories,id',
            'tags' => 'nullable|string|max:2000',
            'author_name' => 'required|string|max:120',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
            'views_mode' => 'nullable|in:asli,manual',
            'views_manual' => 'nullable|required_if:views_mode,manual|integer|min:0|max:4000000000',
            'ads' => 'array',
            'ads.*' => 'array',
            'ads.*.*' => 'integer|exists:ads,id',
        ], [], ['image' => 'gambar utama', 'title' => 'judul', 'views_manual' => 'jumlah dibaca manual']);

        $data['views_manual'] = ($data['views_mode'] ?? 'asli') === 'manual' ? (int) $data['views_manual'] : null;
        $categoryIds = Category::whereIn('id', $data['categories'] ?? [])->orderBy('sort_order')->pluck('id')->all();
        $data['category_id'] = $categoryIds[0] ?? null;
        $tagIds = Tag::idsFrom($data['tags'] ?? null);

        $data['slug'] = Post::uniqueSlug(($data['slug'] ?? null) ?: $data['title'], $post->id);
        $data['body'] = $this->cleanBody($data['body'] ?? '');
        $data['image'] = $this->storeImage($request->file('image'), 'posts', $post->image);
        $data['published_at'] = $data['published_at'] ?? ($data['status'] === 'published' ? now() : null);
        foreach (['is_headline', 'is_editor_choice', 'is_breaking'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }
        unset($data['ads'], $data['categories'], $data['tags'], $data['views_mode']);
        $post->fill($data)->save();
        $post->categories()->sync($categoryIds);
        $post->tags()->sync($tagIds);

        $sync = [];
        foreach ($request->input('ads', []) as $placement => $ids) {
            if (! array_key_exists($placement, Post::PLACEMENTS)) {
                continue;
            }
            foreach ($ids as $id) {
                $sync[] = ['ad_id' => (int) $id, 'placement' => $placement];
            }
        }
        DB::transaction(function () use ($post, $sync) {
            $post->ads()->detach();
            foreach ($sync as $row) {
                $post->ads()->attach($row['ad_id'], ['placement' => $row['placement']]);
            }
        });
    }
}
