@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<section class="welcome" data-testid="dashboard-welcome">
  <div>
    <span class="pill-tag light"><i data-lucide="sparkles"></i> {{ now()->locale('id')->translatedFormat('l, d F Y') }}</span>
    <h1>Selamat datang,<br><em>Redaksi</em><span>.</span></h1>
    <p>Ringkasan portal hari ini. Siap menerbitkan kabar terbaru?</p>
    <div class="welcome-actions">
      <a href="{{ route('admin.posts.create') }}" class="btn btn-light" data-testid="dashboard-new-post-btn"><i data-lucide="plus"></i> Tulis Berita</a>
      <a href="{{ route('admin.landing-ads.edit') }}" class="btn btn-outline-light" data-testid="dashboard-landing-ads-btn"><i data-lucide="layout-template"></i> Atur Iklan Landing</a>
    </div>
  </div>
  <div class="welcome-right">
    <img src="{{ asset('brand/logo-full.png') }}" alt="" class="welcome-logo" data-testid="dashboard-logo">
    <div class="live-stack">
      @foreach($recent->where('status', 'published')->take(2) as $p)
      <a href="{{ $p->url }}" target="_blank" class="live-row" style="animation-delay:{{ 0.3 + $loop->index * 0.15 }}s"><span class="live-pill">LIVE</span><span>{{ \Illuminate\Support\Str::limit($p->title, 60) }}</span></a>
      @endforeach
    </div>
  </div>
</section>
@php $icons = ['newspaper', 'pencil-line', 'eye', 'megaphone', 'map-pin', 'tags', 'file-text', 'activity']; @endphp
<div class="stats" data-testid="dashboard-stats">
  @foreach($stats as $label => $val)
  <div class="stat" style="animation-delay:{{ $loop->index * 70 }}ms"><span class="si"><i data-lucide="{{ $icons[$loop->index] ?? 'activity' }}"></i></span><span class="sl">{{ $label }}</span><b data-count="{{ $val }}">{{ number_format($val, 0, ',', '.') }}</b></div>
  @endforeach
</div>
<div class="grid2">
  <div class="panel">
    <h3>Berita terakhir diinput <a href="{{ route('admin.posts.index') }}" class="see">Semua <i data-lucide="arrow-right"></i></a></h3>
    <table><tbody>
      @forelse($recent as $p)
      <tr>
        <td style="width:84px"><img class="thumb" src="{{ $p->image_url }}" alt=""></td>
        <td><a href="{{ route('admin.posts.edit', $p) }}" class="ttl">{{ $p->title }}</a><div class="sub">{{ $p->region?->name }} · {{ $p->category?->name }} · {{ tgl($p->created_at, 'd M Y H:i') }}</div></td>
        <td><span class="tag {{ $p->status === 'published' ? 'ok' : 'off' }}">{{ $p->status === 'published' ? 'Terbit' : 'Draft' }}</span></td>
      </tr>
      @empty <tr><td class="empty-row">Belum ada berita.</td></tr> @endforelse
    </tbody></table>
  </div>
  <div>
    <div class="panel">
      <h3>Terpopuler <i data-lucide="flame" class="flame"></i></h3>
      @php $max = max(1, $popular->max('shown_views')); @endphp
      <div class="rank">
        @foreach($popular as $i => $p)
        <a href="{{ $p->url }}" target="_blank" class="rank-item"><span class="rn">{{ $i + 1 }}</span><div><b>{{ \Illuminate\Support\Str::limit($p->title, 70) }}</b><div class="bar"><i style="--w:{{ round($p->shown_views / $max * 100) }}%"></i></div></div><span class="sub">{{ number_format($p->shown_views, 0, ',', '.') }}×</span></a>
        @endforeach
      </div>
    </div>
    <div class="panel">
      <h3>Performa Iklan<small>Jumlah klik per iklan</small></h3>
      <table><tbody>
        @forelse($topAds as $ad)
        <tr><td style="width:84px"><img class="thumb" src="{{ $ad->image_url }}" alt=""></td><td class="ttl" style="font-weight:600">{{ $ad->name }}</td><td class="sub" style="white-space:nowrap">{{ number_format($ad->clicks, 0, ',', '.') }} klik</td></tr>
        @empty <tr><td class="empty-row">Belum ada iklan.</td></tr> @endforelse
      </tbody></table>
    </div>
  </div>
</div>
@endsection
