<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Dashboard') · Admin {{ setting('site_name') }}</title>
<link rel="icon" href="{{ asset('favicon.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Instrument+Serif:ital@0;1&family=Manrope:wght@400..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=15">
@stack('head')
</head>
<body>
<div class="shell">
  <aside class="side" id="side" data-testid="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="brand"><img src="{{ asset('brand/logo-full.png') }}" alt=""><div><b>Bali Global <em>News</em></b><small>Ruang Redaksi</small></div></a>
    @php $is = fn ($p) => request()->routeIs($p) ? 'on' : ''; @endphp
    <nav>
      <a href="{{ route('admin.dashboard') }}" class="{{ $is('admin.dashboard') }}" data-testid="nav-dashboard"><i data-lucide="layout-dashboard"></i> Dashboard</a>
      <a href="{{ route('admin.analytics') }}" class="{{ $is('admin.analytics') }}" data-testid="nav-analytics"><i data-lucide="chart-line"></i> Analitik</a>
      <h6>Konten</h6>
      <a href="{{ route('admin.posts.index') }}" class="{{ $is('admin.posts.*') }}" data-testid="nav-posts"><i data-lucide="newspaper"></i> Berita</a>
      <a href="{{ route('admin.pages.index') }}" class="{{ $is('admin.pages.*') }}" data-testid="nav-pages"><i data-lucide="file-text"></i> Halaman</a>
      <h6>Master Data</h6>
      <a href="{{ route('admin.regions.index') }}" class="{{ $is('admin.regions.*') }}" data-testid="nav-regions"><i data-lucide="map-pin"></i> Kategori Wilayah</a>
      <a href="{{ route('admin.categories.index') }}" class="{{ $is('admin.categories.*') }}" data-testid="nav-categories"><i data-lucide="tags"></i> Kategori</a>
      <a href="{{ route('admin.ads.index') }}" class="{{ $is('admin.ads.*') }}" data-testid="nav-ads"><i data-lucide="megaphone"></i> Master Iklan</a>
      <h6>Tampilan</h6>
      <a href="{{ route('admin.landing-ads.edit') }}" class="{{ $is('admin.landing-ads.*') }}" data-testid="nav-landing-ads"><i data-lucide="layout-template"></i> Iklan Landing</a>
      <a href="{{ route('admin.settings.edit') }}" class="{{ $is('admin.settings.*') }}" data-testid="nav-settings"><i data-lucide="settings"></i> Pengaturan</a>
      <a href="{{ route('home') }}" target="_blank" data-testid="nav-view-site"><i data-lucide="external-link"></i> Lihat Website</a>
    </nav>
    <div class="foot">
      <div class="me"><span class="av">{{ strtoupper(substr(auth()->user()->email, 0, 1)) }}</span><div><small>Masuk sebagai</small><b>{{ auth()->user()->email }}</b></div></div>
      <form method="post" action="{{ route('admin.logout') }}">@csrf<button data-testid="logout-btn"><i data-lucide="log-out"></i> Keluar</button></form>
    </div>
  </aside>
  <div class="side-bg" id="sideBg"></div>
  <main class="main">
    <div class="appbar">
      <button class="ibtn menu-toggle" onclick="toggleSide()" aria-label="Menu" data-testid="admin-menu-btn"><i data-lucide="menu"></i></button>
      <a href="{{ route('admin.dashboard') }}" class="appbar-logo"><img src="{{ asset('brand/logo-full.png') }}" alt=""><b>Bali Global <em>News</em></b></a>
      <div class="crumb"><span>Admin</span><i data-lucide="chevron-right"></i><b>@yield('title', 'Dashboard')</b></div>
      <div class="appbar-right">
        <span class="clock" id="clock" data-testid="admin-clock"></span>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-red btn-sm hide-sm" data-testid="appbar-new-post-btn"><i data-lucide="plus"></i> Tulis</a>
        <a href="{{ route('home') }}" target="_blank" class="ibtn" aria-label="Lihat website" data-testid="appbar-view-site"><i data-lucide="globe"></i></a>
      </div>
    </div>
    <div class="content">
      @if(session('ok'))<div class="flash" data-testid="flash-message"><i data-lucide="check-circle"></i> {{ session('ok') }}</div>@endif
      @if($errors->any())
        <div class="errors" data-testid="form-errors"><b>Periksa kembali isian Anda:</b><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      @yield('content')
    </div>
  </main>
</div>
@php $is2 = fn ($p) => request()->routeIs($p) ? 'on' : ''; @endphp
<nav class="admin-bottom-nav" data-testid="admin-bottom-nav">
  <a href="{{ route('admin.dashboard') }}" class="{{ $is2('admin.dashboard') }}" data-testid="admin-bottom-dashboard"><i data-lucide="layout-dashboard"></i><span>Dashboard</span></a>
  <a href="{{ route('admin.posts.index') }}" class="{{ request()->routeIs('admin.posts.index') ? 'on' : '' }}" data-testid="admin-bottom-posts"><i data-lucide="newspaper"></i><span>Berita</span></a>
  <a href="{{ route('admin.posts.create') }}" class="bn-add" aria-label="Tulis berita" data-testid="admin-bottom-new-post"><i data-lucide="plus"></i></a>
  <a href="{{ route('admin.ads.index') }}" class="{{ $is2('admin.ads.*') }}" data-testid="admin-bottom-ads"><i data-lucide="megaphone"></i><span>Iklan</span></a>
  <button type="button" onclick="toggleSide()" data-testid="admin-bottom-menu"><i data-lucide="layout-grid"></i><span>Menu</span></button>
</nav>
<script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
<script>
lucide.createIcons();
function toggleSide() { document.getElementById('side').classList.toggle('open'); document.getElementById('sideBg').classList.toggle('open'); }
document.getElementById('sideBg').addEventListener('click', toggleSide);
const clock = document.getElementById('clock');
const tick = () => clock.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WITA';
tick(); setInterval(tick, 1000);
document.querySelectorAll('[data-count]').forEach(el => {
  const end = +el.dataset.count, t0 = performance.now(), d = 1200;
  const step = now => { const p = Math.min((now - t0) / d, 1), v = Math.round(end * (1 - Math.pow(1 - p, 3))); el.textContent = v.toLocaleString('id-ID'); if (p < 1) requestAnimationFrame(step); };
  requestAnimationFrame(step);
});
document.querySelectorAll('[data-confirm]').forEach(f => f.addEventListener('submit', e => { if (!confirm(f.dataset.confirm)) e.preventDefault(); }));
document.querySelectorAll('[data-preview]').forEach(inp => inp.addEventListener('change', () => {
  const box = document.querySelector(inp.dataset.preview), f = inp.files[0];
  if (box && f) box.innerHTML = '<img src="' + URL.createObjectURL(f) + '"><div>' + f.name + '</div>';
}));
</script>
@stack('scripts')
</body>
</html>
