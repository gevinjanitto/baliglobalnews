@extends('layouts.site')

@section('title', strip_tags($title).' – '.setting('site_name'))

@section('content')
<div class="wrap">
  <header class="list-head stagger">
    <span class="eyebrow">{{ $kicker }}</span>
    <h1 data-testid="list-title">{{ $title }}</h1>
    @if($description)<p>{{ $description }}</p>@endif
    @if(!empty($subRegions) && $subRegions->isNotEmpty())
      <div class="chips" style="margin-top:20px">
        @foreach($subRegions as $s)<a class="chip" href="{{ route('region.show', $s->slug) }}">{{ $s->name }}</a>@endforeach
      </div>
    @endif
  </header>

  @if($posts->isEmpty())
    <div class="empty" data-testid="empty-state"><i data-lucide="newspaper"></i>Belum ada berita di sini.</div>
  @else
  <div class="list-grid" data-testid="list-grid">
    @foreach($posts as $p)
    <a href="{{ $p->url }}" class="card reveal">
      <div class="media r43"><img src="{{ $p->image_url }}" alt="" loading="lazy"></div>
      <div class="meta">@if($p->category)<b class="cat">{{ $p->category->name }}</b><span>•</span>@endif @if($p->region)<span class="reg">{{ $p->region->name }}</span><span class="reg">•</span>@endif {{ tgl($p->published_at, 'd M Y') }}</div>
      <h3>{{ $p->title }}</h3>
      <p>{{ \Illuminate\Support\Str::limit($p->summary, 130) }}</p>
    </a>
    @endforeach
  </div>
  {{ $posts->links() }}
  @endif
</div>
@endsection
