<?php

namespace App\Http\Controllers;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Region;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    private function listing()
    {
        return Post::published()->with(['region', 'category'])->latest('published_at');
    }

    public function show(string $slug)
    {
        $post = Post::published()->with(['region.parent', 'category', 'categories', 'tags', 'ads'])->where('slug', $slug)->firstOrFail();
        if (! \App\Models\Visit::isBot((string) request()->userAgent())) {
            $post->increment('views');
        }
        request()->attributes->set('visit_post_id', $post->id);

        return view('site.post', $this->articleData($post));
    }

    public function articleData(Post $post): array
    {
        $related = $this->listing()->where('id', '!=', $post->id)
            ->where(fn ($q) => $q->where('category_id', $post->category_id)->orWhere('region_id', $post->region_id))
            ->take(4)->get();
        $popular = Post::published()->popular()->take(5)->get();
        $latest = $this->listing()->where('id', '!=', $post->id)->take(5)->get();

        $paragraphs = preg_split('/(?<=<\/p>)/i', (string) $post->body, -1, PREG_SPLIT_NO_EMPTY);

        return compact('post', 'related', 'popular', 'latest', 'paragraphs');
    }

    public function tag(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $posts = $this->listing()->whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id))->paginate(12);

        return view('site.list', ['title' => '#'.$tag->name, 'kicker' => 'Tag', 'description' => $posts->total().' berita dengan tag ini', 'posts' => $posts]);
    }

    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $posts = $this->listing()->inCategory($category->id)->paginate(12);

        return view('site.list', ['title' => $category->name, 'kicker' => 'Kategori', 'description' => $category->description, 'posts' => $posts]);
    }

    public function region(string $slug)
    {
        $region = Region::where('slug', $slug)->where('is_active', true)->with('children')->firstOrFail();
        $posts = $this->listing()->whereIn('region_id', $region->descendantIds())->paginate(12);

        return view('site.list', [
            'title' => $region->name, 'kicker' => 'Wilayah', 'description' => $region->description,
            'posts' => $posts, 'subRegions' => $region->children,
        ]);
    }

    public function latest()
    {
        return view('site.list', ['title' => 'Indeks Berita', 'kicker' => 'Semua Berita', 'description' => 'Seluruh berita terbaru dari Bali Global News.', 'posts' => $this->listing()->paginate(12)]);
    }

    public function search(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $posts = $this->listing()
            ->when($q !== '', fn ($w) => $w->where(fn ($s) => $s->where('title', 'like', "%{$q}%")->orWhere('excerpt', 'like', "%{$q}%")->orWhere('body', 'like', "%{$q}%")))
            ->paginate(12)->withQueryString();

        return view('site.list', ['title' => $q !== '' ? '“'.$q.'”' : 'Pencarian', 'kicker' => 'Hasil Pencarian', 'description' => $posts->total().' berita ditemukan', 'posts' => $posts, 'q' => $q]);
    }

    public function page(string $slug)
    {
        return view('site.page', ['page' => Page::where('slug', $slug)->firstOrFail()]);
    }

    public function adClick(Ad $ad)
    {
        $ad->increment('clicks');

        return redirect()->away($ad->link_url ?: url('/'));
    }

    public function media(string $path)
    {
        $disk = Storage::disk(config('filesystems.default'));
        abort_unless(str_starts_with($path, 'uploads/') && $disk->exists($path), 404);

        return $disk->response($path, null, ['Cache-Control' => 'public, max-age=2592000']);
    }
}
