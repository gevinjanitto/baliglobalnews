@extends('layouts.site')

@section('title', $post->title.' – '.setting('site_name'))
@section('description', $post->summary)
@section('og_image', $post->image_url)

@push('top')<div class="progress" id="progress"></div>@if($isPreview ?? false)<div class="preview-bar" data-testid="preview-banner">PRATINJAU · BELUM DIPUBLIKASIKAN</div>@endif @endpush

@section('content')
<div class="wrap">
  <div class="crumbs" data-testid="breadcrumbs">
    <a href="{{ route('home') }}"><i data-lucide="home"></i></a>
    @if($post->region?->parent)<i data-lucide="chevron-right"></i><a href="{{ route('region.show', $post->region->parent->slug) }}">{{ $post->region->parent->name }}</a>@endif
    @if($post->region)<i data-lucide="chevron-right"></i><a href="{{ route('region.show', $post->region->slug) }}">{{ $post->region->name }}</a>@endif
    @if($post->category)<i data-lucide="chevron-right"></i><a href="{{ route('category.show', $post->category->slug) }}">{{ $post->category->name }}</a>@endif
  </div>

  <div class="art-grid">
    <article>
      <header class="art-head stagger">
        @include('partials.badges', ['p' => $post, 'red' => true])
        <h1 data-testid="post-title">{{ $post->title }}</h1>
        @if($post->excerpt)<p class="lead">{{ $post->excerpt }}</p>@endif
        <div class="byline">
          <div class="author">
            <span class="av">{{ strtoupper(substr($post->author_name, 0, 1)) }}</span>
            <div><b>{{ $post->author_name }}</b><span class="meta">{{ tgl($post->published_at, 'l, d F Y · H:i') }} WITA <span>•</span> {{ $post->read_minutes }} menit baca <span>•</span> {{ number_format($post->shown_views, 0, ',', '.') }} dibaca</span></div>
          </div>
          <div class="share" data-testid="share-buttons">
            <a class="icon-btn wa" target="_blank" rel="noopener" href="https://wa.me/?text={{ urlencode($post->title.' '.$post->url) }}" aria-label="WhatsApp" data-testid="share-whatsapp"><i data-lucide="message-circle"></i></a>
            <a class="icon-btn" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($post->url) }}" aria-label="Facebook" data-testid="share-facebook"><i data-lucide="facebook"></i></a>
            <a class="icon-btn" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode($post->url) }}" aria-label="X" data-testid="share-twitter"><i data-lucide="twitter"></i></a>
            <button class="icon-btn" data-copy="{{ $post->url }}" aria-label="Salin tautan" data-testid="copy-link-btn"><i data-lucide="link"></i></button>
            <span id="copyToast" class="toast">Tautan tersalin!</span>
          </div>
        </div>
      </header>

      @if($post->image_url)
      <figure class="art-figure reveal">
        <div class="media"><img src="{{ $post->image_url }}" alt="{{ $post->title }}"></div>
        @if($post->image_caption)<figcaption><i data-lucide="camera"></i> {{ $post->image_caption }}</figcaption>@endif
      </figure>
      @endif

      @include('partials.ad', ['ads' => $post->adsFor('atas'), 'format' => 'banner', 'tid' => 'ad-post-atas'])

      <div class="prose art-body" data-testid="post-body">
        @foreach($paragraphs as $i => $para)
          {!! $para !!}
          @if($i === 1)
            @include('partials.ad', ['ads' => $post->adsFor('tengah'), 'format' => 'banner', 'tid' => 'ad-post-tengah'])
          @endif
        @endforeach
        @if(count($paragraphs) < 2)
          @include('partials.ad', ['ads' => $post->adsFor('tengah'), 'format' => 'banner', 'tid' => 'ad-post-tengah'])
        @endif
      </div>

      <div class="tags-row">
        <span>Tag:</span>
        @if($post->region)<a class="chip on" href="{{ route('region.show', $post->region->slug) }}">{{ $post->region->name }}</a>@endif
        @foreach($post->categories as $c)<a class="chip" href="{{ route('category.show', $c->slug) }}" data-testid="post-category-chip">{{ $c->name }}</a>@endforeach
        @foreach($post->tags as $t)<a class="chip" href="{{ $t->slug ? route('tag.show', $t->slug) : '#' }}" data-testid="post-tag-chip">#{{ $t->name }}</a>@endforeach
      </div>

      @include('partials.ad', ['ads' => $post->adsFor('bawah'), 'format' => 'banner', 'tid' => 'ad-post-bawah'])
    </article>

    <aside>
      <div class="sticky">
        @include('partials.ad', ['ads' => $post->adsFor('sidebar'), 'format' => 'kotak', 'tid' => 'ad-post-sidebar'])
        <div class="pop-box" style="margin-top:24px">
          <div class="live-head"><h3>Terpopuler</h3><i data-lucide="flame" class="flame"></i></div>
          @foreach($popular as $i => $p)<a href="{{ $p->url }}" class="pop-item"><span class="num">{{ $i + 1 }}</span><div><h4>{{ $p->title }}</h4></div></a>@endforeach
        </div>
        <div class="live-box" style="margin-top:24px">
          <div class="live-head"><h3>Terbaru</h3><span class="live"><i></i>LIVE</span></div>
          <div class="live-list">
            @foreach($latest as $p)<a href="{{ $p->url }}" class="live-item"><div class="media"><img src="{{ $p->image_url }}" alt="" loading="lazy"></div><div><h4>{{ $p->title }}</h4><span class="meta">{{ tgl($p->published_at, 'd M Y') }}</span></div></a>@endforeach
          </div>
        </div>
      </div>
    </aside>
  </div>

  @if($related->isNotEmpty())
  <section class="section reveal" data-testid="related-section">
    <div class="sec-head"><div><span class="eyebrow">Masih hangat</span><h2 class="sec-title">Baca Juga</h2></div></div>
    <div class="grid4">
      @foreach($related as $p)
      <a href="{{ $p->url }}" class="card">
        <div class="media r43"><img src="{{ $p->image_url }}" alt="" loading="lazy"></div>
        <div class="meta">@if($p->category)<b class="cat">{{ $p->category->name }}</b><span>•</span>@endif {{ tgl($p->published_at, 'd M Y') }}</div>
        <h3>{{ $p->title }}</h3>
      </a>
      @endforeach
    </div>
  </section>
  @endif
</div>
@endsection
