<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#0A0A0A">
<title>@yield('title', setting('site_name').' – '.setting('tagline'))</title>
<meta name="description" content="@yield('description', setting('about'))">
<meta property="og:site_name" content="{{ setting('site_name') }}">
<meta property="og:title" content="@yield('title', setting('site_name'))">
<meta property="og:image" content="@yield('og_image', asset('brand/logo-full.png'))">
<link rel="icon" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Manrope:wght@400..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/site.css') }}?v=14">
</head>
<body>
<div class="preloader" id="preloader" data-testid="preloader"><img src="{{ asset('brand/logo-full.png') }}" alt=""><span></span></div>
@stack('top')
<div class="topbar" data-testid="topbar">
  <div class="wrap">
    <span class="date">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
    @if(setting('ticker_enabled') === '1' && $breaking->isNotEmpty())
    <div class="ticker" data-testid="breaking-ticker">
      <span class="ticker-label"><span class="dot"></span>TERKINI</span>
      <div class="ticker-mask"><div class="ticker-track">
        @foreach([1,2] as $_) @foreach($breaking as $b)<a href="{{ route('post.show', $b->slug) }}">{{ $b->title }}</a>@endforeach @endforeach
      </div></div>
    </div>
    @else <div style="flex:1"></div> @endif
    <div class="socials">
      @if(setting('facebook'))<a href="{{ setting('facebook') }}" target="_blank" rel="noopener" aria-label="Facebook"><i data-lucide="facebook"></i></a>@endif
      @if(setting('twitter'))<a href="{{ setting('twitter') }}" target="_blank" rel="noopener" aria-label="Twitter"><i data-lucide="twitter"></i></a>@endif
      @if(setting('instagram'))<a href="{{ setting('instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><i data-lucide="instagram"></i></a>@endif
      @if(setting('youtube'))<a href="{{ setting('youtube') }}" target="_blank" rel="noopener" aria-label="YouTube"><i data-lucide="youtube"></i></a>@endif
    </div>
  </div>
</div>

<header class="site-header" id="siteHeader">
  <div class="masthead">
    <div class="wrap">
      <div class="mh-left">
        <button class="icon-btn" data-open-drawer aria-label="Menu" data-testid="menu-btn"><i data-lucide="menu"></i></button>
        <a href="{{ route('latest') }}" class="text-link" data-testid="nav-indeks">Indeks Berita</a>
      </div>
      <a href="{{ route('home') }}" class="brand" data-testid="brand-logo">
        <img src="{{ asset('brand/logo-full.png') }}" alt="{{ setting('site_name') }}">
        <span class="brand-word"><b>Bali Global <em>News</em></b><small>{{ setting('motto') }}</small></span>
      </a>
      <div class="mh-right">
        <form action="{{ route('search') }}" method="get" class="search-pill" data-testid="header-search-form">
          <input name="q" placeholder="Cari berita…" value="{{ $q ?? '' }}" data-testid="header-search-input" autocomplete="off">
          <button aria-label="Cari" data-testid="header-search-btn"><i data-lucide="search"></i></button>
        </form>
        <button class="icon-btn mobile-search" data-open-search aria-label="Cari" data-testid="search-open-btn"><i data-lucide="search"></i></button>
      </div>
    </div>
  </div>
  <nav class="mainnav" data-testid="main-nav">
    <div class="wrap">
      <ul>
        <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" data-testid="nav-home">Beranda</a></li>
        @foreach($menuRegions as $reg)
          @if($reg->children->isNotEmpty())
          <li class="has-mega"><a href="{{ route('region.show', $reg->slug) }}" class="{{ request()->is('wilayah/'.$reg->slug) ? 'active' : '' }}">{{ $reg->name }} <i data-lucide="chevron-down"></i></a>
            <div class="mega">
              <div><h6>Kabupaten / Kota</h6><div class="grid">
                @foreach($reg->children as $ch)<a href="{{ route('region.show', $ch->slug) }}">{{ $ch->name }}</a>@endforeach
              </div></div>
              <div class="col"><h6>Lihat Juga</h6>
                <a href="{{ route('region.show', $reg->slug) }}">Semua {{ $reg->name }}</a>
                @foreach($menuRegions->where('id', '!=', $reg->id) as $o)<a href="{{ route('region.show', $o->slug) }}">{{ $o->name }}</a>@endforeach
              </div>
            </div>
          </li>
          @else
          <li><a href="{{ route('region.show', $reg->slug) }}" class="{{ request()->is('wilayah/'.$reg->slug) ? 'active' : '' }}">{{ $reg->name }}</a></li>
          @endif
        @endforeach
        @foreach($menuCategories as $cat)
          <li><a href="{{ route('category.show', $cat->slug) }}" class="{{ request()->is('kategori/'.$cat->slug) ? 'active' : '' }}">{{ $cat->name }}</a></li>
        @endforeach
      </ul>
    </div>
  </nav>
</header>

@if($headerAds->isNotEmpty())
<div class="wrap">@include('partials.ad', ['ads' => $headerAds, 'format' => 'banner header', 'tid' => 'ad-header'])</div>
@endif

<main>@yield('content')</main>

<footer class="footer" data-testid="footer">
  <div class="wrap">
    <div class="foot-cta reveal">
      <div><span class="eyebrow light">Bali Global News</span><h3>Kabar Bali &amp; Nusantara, <em>setiap saat.</em></h3></div>
      <a href="{{ route('latest') }}" class="btn-pill light" data-testid="footer-index-btn">Indeks Berita <i data-lucide="arrow-up-right"></i></a>
    </div>
    <div class="foot-grid">
      <div>
        <div class="foot-brand"><img src="{{ asset('brand/logo-full.png') }}" alt=""><div><b>{{ setting('site_name') }}</b><small>{{ setting('motto') }}</small></div></div>
        <p>{{ setting('about') }}</p>
        <div class="smsi"><img src="{{ asset('seed/smsi.jpg') }}" alt="SMSI"><span>Anggota Serikat<br>Media Siber Indonesia</span></div>
      </div>
      <div><h6>Wilayah</h6><ul>
        @foreach($menuRegions as $reg)<li><a href="{{ route('region.show', $reg->slug) }}">{{ $reg->name }}</a></li>
          @foreach($reg->children->take(6) as $ch)<li><a href="{{ route('region.show', $ch->slug) }}">{{ $ch->name }}</a></li>@endforeach
        @endforeach
      </ul></div>
      <div><h6>Kategori</h6><ul>
        @foreach($menuCategories as $cat)<li><a href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a></li>@endforeach
      </ul></div>
      <div><h6>Redaksi</h6><ul>
        @foreach($footerPages as $pg)<li><a href="{{ route('page.show', $pg->slug) }}">{{ $pg->title }}</a></li>@endforeach
        <li><a href="{{ route('latest') }}">Indeks Berita</a></li>
        @if(setting('email'))<li><a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a></li>@endif
        @if(setting('phone'))<li>{{ setting('phone') }}</li>@endif
        @if(setting('address'))<li class="dim">{{ setting('address') }}</li>@endif
      </ul></div>
    </div>
    <div class="foot-bottom">
      <span>© {{ date('Y') }} {{ setting('site_name') }} — {{ setting('motto') }}</span>
      <div class="foot-social">
        @if(setting('facebook'))<a href="{{ setting('facebook') }}" target="_blank" rel="noopener" aria-label="Facebook"><i data-lucide="facebook"></i></a>@endif
        @if(setting('twitter'))<a href="{{ setting('twitter') }}" target="_blank" rel="noopener" aria-label="Twitter"><i data-lucide="twitter"></i></a>@endif
        @if(setting('instagram'))<a href="{{ setting('instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><i data-lucide="instagram"></i></a>@endif
        <a href="{{ route('admin.login') }}" class="foot-admin" data-testid="footer-admin-link"><i data-lucide="lock"></i> Admin</a>
      </div>
      <div class="foot-credit" data-testid="footer-credit">Design &amp; Develop by<a href="https://maiharta.com/" target="_blank" rel="noopener" data-testid="footer-maiharta-link">MaiHarta</a></div>
    </div>
  </div>
</footer>

<nav class="bottom-nav" data-testid="mobile-bottom-nav">
  <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'on' : '' }}" data-testid="bottom-nav-home"><i data-lucide="home"></i><span>Beranda</span></a>
  <a href="{{ route('latest') }}" class="{{ request()->routeIs('latest') ? 'on' : '' }}" data-testid="bottom-nav-latest"><i data-lucide="newspaper"></i><span>Indeks</span></a>
  <button type="button" class="bn-search" data-open-search data-testid="bottom-nav-search"><i data-lucide="search"></i></button>
  @php $firstRegion = $menuRegions->first(); @endphp
  @if($firstRegion)<a href="{{ route('region.show', $firstRegion->slug) }}" class="{{ request()->is('wilayah/'.$firstRegion->slug) ? 'on' : '' }}" data-testid="bottom-nav-region"><i data-lucide="map-pin"></i><span>Bali</span></a>@endif
  <button type="button" data-open-drawer data-testid="bottom-nav-menu"><i data-lucide="layout-grid"></i><span>Menu</span></button>
</nav>

<button class="to-top" id="toTop" aria-label="Kembali ke atas" data-testid="back-to-top-btn"><i data-lucide="arrow-up"></i></button>

<div class="overlay" id="searchOverlay" data-testid="search-overlay">
  <div class="search-box">
    <form action="{{ route('search') }}" method="get">
      <i data-lucide="search"></i>
      <input name="q" placeholder="Cari berita, wilayah, tokoh…" value="{{ $q ?? '' }}" data-testid="search-input" autocomplete="off">
      <button class="btn-pill" data-testid="search-submit">Cari</button>
    </form>
    <p>Tekan <b>Esc</b> untuk menutup · tekan <b>/</b> di mana saja untuk mencari</p>
  </div>
</div>
<div class="drawer-bg" id="drawerBg"></div>
<aside class="drawer" id="drawer" data-testid="mobile-drawer">
  <div class="drawer-head">
    <a href="{{ route('home') }}" class="brand"><img src="{{ asset('brand/logo-full.png') }}" alt=""><span class="brand-word"><b>Bali Global <em>News</em></b><small>{{ setting('motto') }}</small></span></a>
    <button class="icon-btn" data-open-drawer aria-label="Tutup" data-testid="drawer-close-btn"><i data-lucide="x"></i></button>
  </div>
  <form action="{{ route('search') }}" method="get" class="search-pill drawer-search"><input name="q" placeholder="Cari berita…" data-testid="drawer-search-input"><button aria-label="Cari"><i data-lucide="search"></i></button></form>
  <div class="drawer-links">
    <a href="{{ route('home') }}" class="lead-link">Beranda <i data-lucide="arrow-up-right"></i></a>
    <a href="{{ route('latest') }}" class="lead-link">Indeks Berita <i data-lucide="arrow-up-right"></i></a>
    @foreach($menuRegions as $reg)
      <h6>{{ $reg->name }}</h6>
      <div class="drawer-chips">
        <a href="{{ route('region.show', $reg->slug) }}">Semua</a>
        @foreach($reg->children as $ch)<a href="{{ route('region.show', $ch->slug) }}">{{ $ch->name }}</a>@endforeach
      </div>
    @endforeach
    <h6>Kategori</h6>
    <div class="drawer-chips">@foreach($menuCategories as $cat)<a href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a>@endforeach</div>
    <h6>Redaksi</h6>
    @foreach($footerPages as $pg)<a href="{{ route('page.show', $pg->slug) }}" class="lead-link sm">{{ $pg->title }}</a>@endforeach
  </div>
</aside>

<script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
<script src="{{ asset('js/site.js') }}?v=12"></script>
</body>
</html>
