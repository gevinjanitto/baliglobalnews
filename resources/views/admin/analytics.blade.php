@extends('layouts.admin')
@section('title', 'Analitik')
@php
  $fmt = fn ($n) => number_format($n, 0, ',', '.');
  $devIcon = ['Mobile' => 'smartphone', 'Tablet' => 'tablet', 'Desktop' => 'monitor'];
@endphp
@section('content')
<div class="topline">
  <div><h1>Analitik Pengunjung</h1><p>Siapa yang mengunjungi website dan berita apa yang paling banyak dibaca.</p></div>
  <div class="range-tabs" data-testid="analytics-range">
    @foreach(\App\Http\Controllers\Admin\AnalyticsController::RANGES as $k => $label)
      <a href="{{ route('admin.analytics', ['range' => $k]) }}" class="{{ $range == $k ? 'on' : '' }}" data-testid="analytics-range-{{ $k }}">{{ $label }}</a>
    @endforeach
  </div>
</div>

@php $icons = ['eye', 'users', 'newspaper', 'radio']; @endphp
<div class="stats stats4" data-testid="analytics-summary">
  @foreach($summary as $label => $val)
  <div class="stat" style="animation-delay:{{ $loop->index * 70 }}ms" data-testid="analytics-stat-{{ $loop->index }}"><span class="si"><i data-lucide="{{ $icons[$loop->index] }}"></i></span><span class="sl">{{ $label }}</span><b data-count="{{ $val }}">{{ $fmt($val) }}</b></div>
  @endforeach
</div>

<div class="panel" data-testid="analytics-chart-panel">
  <h3>Tren Kunjungan<small>Tayangan halaman & pengunjung unik per hari</small></h3>
  <div style="height:280px"><canvas id="visitChart"></canvas></div>
</div>

<div class="grid2">
  <div class="panel" data-testid="analytics-top-posts">
    <h3>Berita Paling Banyak Dilihat <i data-lucide="flame" class="flame"></i><small>Berdasarkan kunjungan nyata pada periode ini</small></h3>
    @php $max = max(1, $topPosts->max('total') ?? 1); @endphp
    <div class="rank">
      @forelse($topPosts->values() as $i => $r)
      <a href="{{ $r['post']->url }}" target="_blank" class="rank-item" data-testid="analytics-top-post-{{ $i }}"><span class="rn">{{ $i + 1 }}</span><div><b>{{ \Illuminate\Support\Str::limit($r['post']->title, 80) }}</b><div class="bar"><i style="--w:{{ round($r['total'] / $max * 100) }}%"></i></div></div><span class="sub" style="text-align:right;white-space:nowrap">{{ $fmt($r['total']) }}× dilihat<br>{{ $fmt($r['visitors']) }} orang</span></a>
      @empty
      <p class="sub empty-row">Belum ada berita yang dibaca pada periode ini.</p>
      @endforelse
    </div>
  </div>
  <div>
    @foreach([['Negara', 'globe', $countries, 'countries'], ['Kota', 'map-pin', $cities, 'cities']] as [$title, $icon, $rows, $tid])
      @include('admin.partials.breakdown', compact('title', 'icon', 'rows', 'tid'))
    @endforeach
  </div>
</div>

<div class="grid4a">
  @foreach([['Perangkat', 'monitor-smartphone', $devices, 'devices'], ['Browser', 'compass', $browsers, 'browsers'], ['Sistem Operasi', 'cpu', $oses, 'os'], ['Sumber Kunjungan', 'link', $referrers, 'referrers']] as [$title, $icon, $rows, $tid])
    @include('admin.partials.breakdown', compact('title', 'icon', 'rows', 'tid'))
  @endforeach
</div>

<div class="panel" data-testid="analytics-visitors">
  <h3>Daftar Pengunjung<small>Setiap pengunjung dikenali per perangkat/browser. Lokasi diperkirakan dari alamat IP.</small></h3>
  <table>
    <thead><tr><th>Pengunjung</th><th>Lokasi</th><th>Perangkat</th><th>Halaman</th><th>Terakhir dibuka</th><th>Terakhir aktif</th></tr></thead>
    <tbody>
    @forelse($visitors as $v)
      @php $last = $lastVisits->get($v->last_id); @endphp
      <tr data-testid="visitor-row-{{ $loop->index }}">
        <td><div class="ttl">#{{ strtoupper(substr($v->visitor_id, 0, 6)) }}</div><div class="sub">{{ $last?->ip }}</div></td>
        <td>@if($last?->country_code)<span class="flag">{{ $last->country_code }}</span>@endif {{ $last?->location }}</td>
        <td><span class="devcell"><i data-lucide="{{ $devIcon[$last?->device] ?? 'monitor' }}"></i> {{ $last?->device }}</span><div class="sub">{{ $last?->browser }} · {{ $last?->os }}</div></td>
        <td><b>{{ $fmt($v->pages) }}</b> <span class="sub">hlm</span></td>
        <td class="sub" style="max-width:280px">{{ $last?->post ? \Illuminate\Support\Str::limit($last->post->title, 60) : $last?->path }}</td>
        <td class="sub" style="white-space:nowrap">{{ \Illuminate\Support\Carbon::parse($v->last_seen)->locale('id')->diffForHumans() }}</td>
      </tr>
    @empty
      <tr><td colspan="6" class="empty-row">Belum ada pengunjung pada periode ini.</td></tr>
    @endforelse
    </tbody>
  </table>
  {{ $visitors->links() }}
</div>

<div class="panel" data-testid="analytics-recent">
  <h3>Aktivitas Terbaru <span class="live-dot"></span><small>30 halaman terakhir yang dibuka pengunjung</small></h3>
  <table><tbody>
    @forelse($recent as $v)
    <tr>
      <td class="sub" style="white-space:nowrap;width:120px">{{ tgl($v->created_at, 'd M H:i') }}</td>
      <td><div class="ttl" style="font-weight:600">{{ $v->post ? \Illuminate\Support\Str::limit($v->post->title, 80) : $v->path }}</div><div class="sub">#{{ strtoupper(substr($v->visitor_id, 0, 6)) }} · {{ $v->location }} · {{ $v->device }} · {{ $v->browser }}@if($v->referrer) · dari {{ $v->referrer }}@endif</div></td>
    </tr>
    @empty
    <tr><td class="empty-row">Belum ada aktivitas.</td></tr>
    @endforelse
  </tbody></table>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
const days = @json($days);
new Chart(document.getElementById('visitChart'), {
  type: 'line',
  data: { labels: days.map(d => d.label), datasets: [
    { label: 'Tayangan', data: days.map(d => d.views), borderColor: '#C8161D', backgroundColor: 'rgba(200,22,29,.12)', fill: true, tension: .35, pointRadius: 3 },
    { label: 'Pengunjung unik', data: days.map(d => d.visitors), borderColor: '#D4AF37', backgroundColor: 'transparent', tension: .35, pointRadius: 3 },
  ] },
  options: { maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { position: 'bottom' } } },
});
</script>
@endpush
