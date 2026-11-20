@php $sum = max(1, $rows->sum('total')); @endphp
<div class="panel" data-testid="analytics-{{ $tid }}">
  <h3><i data-lucide="{{ $icon }}" style="width:18px;height:18px;color:var(--red)"></i> {{ $title }}</h3>
  @forelse($rows as $r)
  <div class="bd-row">
    <div class="bd-top"><span>{{ $r->label }}</span><b>{{ number_format($r->total, 0, ',', '.') }} <small>{{ round($r->total / $sum * 100) }}%</small></b></div>
    <div class="bar"><i style="--w:{{ round($r->total / $sum * 100) }}%"></i></div>
  </div>
  @empty
  <p class="sub">Belum ada data.</p>
  @endforelse
</div>
