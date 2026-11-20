@extends('layouts.site')

@section('content')
@php
  $slides = $top->take(4); $spot = $top->slice(4, 4);
@endphp

<div class="wrap">
  @if($slides->isNotEmpty())
  <section class="hero" data-testid="top-news-grid">
    <div class="hero-slider" id="heroSlider" data-testid="hero-slider">
      @foreach($slides as $i => $p)
      <a href="{{ $p->url }}" class="hs-slide {{ $i === 0 ? 'on' : '' }}" data-testid="{{ $i === 0 ? 'top-news-main' : 'hero-slide-'.$i }}">
        <img src="{{ $p->image_url }}" alt="{{ $p->title }}">
        <div class="hs-cap">
          @include('partials.badges', ['p' => $p, 'red' => true])
          <h2>{{ $p->title }}</h2>
          <p>{{ \Illuminate\Support\Str::limit($p->summary, 160) }}</p>
          <div class="meta light">{{ tgl($p->published_at, 'd M Y · H:i') }} <span>•</span> {{ number_format($p->shown_views, 0, ',', '.') }} dibaca</div>
        </div>
      </a>
      @endforeach
      <div class="hs-nav">
        <button class="glass-btn" data-hs="prev" aria-label="Sebelumnya" data-testid="hero-slider-prev"><i data-lucide="chevron-left"></i></button>
        <button class="glass-btn" data-hs="next" aria-label="Berikutnya" data-testid="hero-slider-next"><i data-lucide="chevron-right"></i></button>
      </div>
      <div class="hs-bars">@foreach($slides as $i => $p)<button class="{{ $i === 0 ? 'on' : '' }}" data-hs-go="{{ $i }}" aria-label="Slide {{ $i + 1 }}" data-testid="hero-dot-{{ $i }}"><i></i></button>@endforeach</div>
    </div>
    <aside class="live-box" data-testid="live-latest">
      <div class="live-head"><h3>Berita Terkini</h3><span class="live"><i></i>LIVE</span></div>
      <div class="live-list">
        @foreach($latest as $p)
        <a href="{{ $p->url }}" class="live-item">
          <div class="media"><img src="{{ $p->image_url }}" alt="" loading="lazy"></div>
          <div><h4>{{ $p->title }}</h4><div class="meta">@if($p->category)<b class="cat">{{ $p->category->name }}</b><span>•</span>@endif {{ tgl($p->published_at, 'd M Y') }}</div></div>
        </a>
        @endforeach
      </div>
      <a href="{{ route('latest') }}" class="live-more" data-testid="live-view-more">Lihat semua berita <i data-lucide="arrow-right"></i></a>
    </aside>
  </section>

  @if($spot->isNotEmpty())
  <section class="grid4 reveal-group" data-testid="spot-grid">
    @foreach($spot as $p)
    <a href="{{ $p->url }}" class="card reveal">
      <div class="media r43"><img src="{{ $p->image_url }}" alt="" loading="lazy"></div>
      <div class="meta">@if($p->category)<b class="cat">{{ $p->category->name }}</b><span>•</span>@endif @if($p->region)<span class="reg">{{ $p->region->name }}</span><span class="reg">•</span>@endif {{ tgl($p->published_at, 'd M Y') }}</div>
      <h3>{{ $p->title }}</h3>
    </a>
    @endforeach
  </section>
  @endif
  @else
    <div class="empty" data-testid="empty-state">Belum ada berita yang diterbitkan.</div>
  @endif

  @include('partials.ad', ['ads' => $ads['home_top'], 'format' => 'banner', 'tid' => 'ad-home-top'])

  @if($editor->isNotEmpty())
  <section class="section reveal" data-testid="editor-choice-section">
    <div class="sec-head">
      <div><span class="eyebrow">Dikurasi redaksi</span><h2 class="sec-title">Pilihan Redaksi</h2></div>
      <div class="ec-tools">
        <div class="chips">
          <button class="chip on" data-ec-filter="all" data-testid="ec-filter-all">Semua</button>
          @foreach($editor->pluck('category')->filter()->unique('id') as $c)
            <button class="chip" data-ec-filter="{{ $c->slug }}" data-testid="ec-filter-{{ $c->slug }}">{{ $c->name }}</button>
          @endforeach
        </div>
        <div class="arrows">
          <button class="icon-btn" data-ec="prev" aria-label="Sebelumnya" data-testid="ec-prev"><i data-lucide="arrow-left"></i></button>
          <button class="icon-btn" data-ec="next" aria-label="Berikutnya" data-testid="ec-next"><i data-lucide="arrow-right"></i></button>
        </div>
      </div>
    </div>
    <div class="ec-track" id="ecTrack">
      @foreach($editor as $p)
      <a href="{{ $p->url }}" class="ec-item" data-cat="{{ $p->category?->slug }}">
        <img src="{{ $p->image_url }}" alt="" loading="lazy">
        <div class="ec-cap">
          @include('partials.badges', ['p' => $p])
          <h3>{{ $p->title }}</h3>
          <span class="ec-go"><i data-lucide="arrow-up-right"></i></span>
        </div>
      </a>
      @endforeach
    </div>
  </section>
  @endif

  <section class="section" data-testid="latest-section">
    <div class="split">
      <div class="reveal">
        <div class="sec-head">
          <div><span class="eyebrow">Update setiap saat</span><h2 class="sec-title">Berita Terbaru</h2></div>
          <a href="{{ route('latest') }}" class="btn-pill ghost" data-testid="latest-view-more">Lihat Semua <i data-lucide="arrow-right"></i></a>
        </div>
        @php $lf = $latest->first(); @endphp
        @if($lf)
        <a href="{{ $lf->url }}" class="feature-row">
          <div class="media"><img src="{{ $lf->image_url }}" alt="" loading="lazy"></div>
          <div>
            @include('partials.badges', ['p' => $lf, 'red' => true])
            <h3>{{ $lf->title }}</h3>
            <p>{{ $lf->summary }}</p>
            <div class="meta">{{ tgl($lf->published_at, 'd M Y · H:i') }}</div>
          </div>
        </a>
        @endif
        <div class="list-rows">
          @foreach($latest->slice(1) as $p)
          <a href="{{ $p->url }}" class="list-row">
            <div><div class="meta">@if($p->category)<b class="cat">{{ $p->category->name }}</b><span>•</span>@endif {{ tgl($p->published_at, 'd M Y · H:i') }}</div><h4>{{ $p->title }}</h4></div>
            <div class="media"><img src="{{ $p->image_url }}" alt="" loading="lazy"></div>
          </a>
          @endforeach
        </div>
      </div>
      <aside class="reveal" data-testid="popular-section">
        <div class="pop-box">
          <div class="live-head"><h3>Terpopuler</h3><i data-lucide="flame" class="flame"></i></div>
          @foreach($popular as $i => $p)
          <a href="{{ $p->url }}" class="pop-item">
            <span class="num">{{ $i + 1 }}</span>
            <div><h4>{{ $p->title }}</h4><div class="meta">{{ number_format($p->shown_views, 0, ',', '.') }} dibaca</div></div>
          </a>
          @endforeach
        </div>
        @include('partials.ad', ['ads' => $ads['home_sidebar'], 'format' => 'kotak', 'tid' => 'ad-home-sidebar'])
        <div class="cat-cloud" data-testid="popular-categories">
          <h6>Kategori Populer</h6>
          <div class="chips">
            @foreach($popularCategories as $c)
              <a class="chip" href="{{ route('category.show', $c->slug) }}">{{ $c->name }} <span>{{ $c->posts_count }}</span></a>
            @endforeach
          </div>
        </div>
      </aside>
    </div>
  </section>

  @include('partials.ad', ['ads' => $ads['home_middle'], 'format' => 'banner', 'tid' => 'ad-home-middle'])

  @if($bali && $baliPosts->isNotEmpty())
  <section class="section region-band reveal" data-testid="region-section">
    <div class="region-wrap">
      <div class="region-intro">
        <span class="eyebrow light">Wilayah</span>
        <h2>Bali</h2>
        <p>Kabar aktual dari sembilan kabupaten/kota di Pulau Dewata, setiap hari.</p>
        <a href="{{ route('region.show', $bali->slug) }}" class="btn-pill" data-testid="region-view-more">Lihat Semua <i data-lucide="arrow-right"></i></a>
      </div>
      <div>
        <div class="chips region-tabs">
          <button class="chip on" data-region-tab="all" data-testid="region-tab-all">Semua</button>
          @foreach($regionTabs as $t)
            <button class="chip" data-region-tab="{{ $t['region']->slug }}" data-testid="region-tab-{{ $t['region']->slug }}">{{ $t['region']->name }}</button>
          @endforeach
        </div>
        <div class="row-list" data-region-list="all">
          @foreach($baliPosts->take(6) as $p)
            <a href="{{ $p->url }}" class="row-item"><span class="d">{{ tgl($p->published_at, 'd.m') }}</span><span class="t">{{ $p->title }}</span><span class="arr"><span>{{ $p->region?->name }}</span><i data-lucide="arrow-right"></i></span></a>
          @endforeach
        </div>
        @foreach($regionTabs as $t)
        <div class="row-list hide" data-region-list="{{ $t['region']->slug }}">
          @foreach($t['posts'] as $p)
            <a href="{{ $p->url }}" class="row-item"><span class="d">{{ tgl($p->published_at, 'd.m') }}</span><span class="t">{{ $p->title }}</span><span class="arr"><span>{{ $p->region?->name }}</span><i data-lucide="arrow-right"></i></span></a>
          @endforeach
        </div>
        @endforeach
      </div>
    </div>
    <div class="trio">
      @foreach($baliPosts->slice(6, 3) as $p)
      <a href="{{ $p->url }}" class="card dark">
        <div class="media r43"><img src="{{ $p->image_url }}" alt="" loading="lazy"></div>
        <div class="meta light">@if($p->region){{ $p->region->name }}<span>•</span>@endif {{ tgl($p->published_at, 'd M Y') }}</div>
        <h3>{{ $p->title }}</h3>
      </a>
      @endforeach
    </div>
  </section>
  @endif

  @foreach($homeCategories as $sec)
  @php $c = $sec['category']; $ps = $sec['posts']; $mid = $ps->get(0); @endphp
  <section class="section reveal" data-testid="home-category-{{ $c->slug }}">
    <div class="sec-head">
      <div><span class="eyebrow">Kategori</span><h2 class="sec-title">{{ $c->name }}</h2></div>
      <a href="{{ route('category.show', $c->slug) }}" class="text-link arrow">Semua Berita <i data-lucide="arrow-right"></i></a>
    </div>
    <div class="feat3">
      @if($mid)
      <a href="{{ $mid->url }}" class="feat-main">
        <img src="{{ $mid->image_url }}" alt="" loading="lazy">
        <div class="hs-cap">
          @include('partials.badges', ['p' => $mid, 'red' => true])
          <h3>{{ $mid->title }}</h3>
          <div class="meta light">{{ tgl($mid->published_at, 'd M Y') }}</div>
        </div>
      </a>
      @endif
      @foreach([$ps->get(1), $ps->get(2)] as $p)
        @if($p)
        <a href="{{ $p->url }}" class="card">
          <div class="media r43"><img src="{{ $p->image_url }}" alt="" loading="lazy"></div>
          <div class="meta">@if($p->region)<b class="cat">{{ $p->region->name }}</b><span>•</span>@endif {{ tgl($p->published_at, 'd M Y') }}</div>
          <h3>{{ $p->title }}</h3>
        </a>
        @endif
      @endforeach
    </div>
  </section>
  @endforeach

  <section class="social-band reveal" data-testid="social-band">
    <div class="sb-intro"><span class="eyebrow">Stay with us</span><h3>Ikuti kabar Bali, langsung di genggaman.</h3><p>Viral · Update · Terpercaya</p></div>
    @if(setting('facebook'))
    <a href="{{ setting('facebook') }}" target="_blank" rel="noopener" class="sb-card sb-fb" data-testid="social-facebook"><i data-lucide="facebook"></i><div><b>Facebook</b><span>Sukai halaman kami</span></div></a>
    @endif
    @if(setting('twitter'))
    <a href="{{ setting('twitter') }}" target="_blank" rel="noopener" class="sb-card sb-tw" data-testid="social-twitter"><i data-lucide="twitter"></i><div><b>Twitter / X</b><span>Ikuti kami</span></div></a>
    @endif
    @if(setting('instagram'))
    <a href="{{ setting('instagram') }}" target="_blank" rel="noopener" class="sb-card sb-ig" data-testid="social-instagram"><i data-lucide="instagram"></i><div><b>Instagram</b><span>Ikuti kami</span></div></a>
    @endif
  </section>

  @include('partials.ad', ['ads' => $ads['home_bottom'], 'format' => 'banner', 'tid' => 'ad-home-bottom'])
</div>
@endsection
