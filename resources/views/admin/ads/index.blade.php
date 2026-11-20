@extends('layouts.admin')
@section('title', 'Master Iklan')
@section('content')
<div class="topline">
  <div><h1>Master Iklan</h1><p>Kelola materi iklan. Atur penempatan di <a href="{{ route('admin.landing-ads.edit') }}" style="color:var(--red)">Iklan Landing</a> atau saat input berita.</p></div>
  <a href="{{ route('admin.ads.create') }}" class="btn btn-red" data-testid="new-ad-btn"><i data-lucide="plus"></i> Tambah Iklan</a>
</div>
<div class="panel" style="padding:6px 8px">
  <table data-testid="ads-table">
    <thead><tr><th></th><th>Nama</th><th>Format</th><th>Periode</th><th>Dipakai</th><th>Klik</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse($ads as $ad)
      <tr data-testid="ad-row-{{ $ad->id }}">
        <td style="width:130px"><img class="thumb" style="width:120px;height:56px" src="{{ $ad->image_url }}" alt=""></td>
        <td><div class="ttl">{{ $ad->name }}</div><div class="sub">{{ \Illuminate\Support\Str::limit($ad->link_url, 40) ?: 'Tanpa tautan' }}</div></td>
        <td><span class="tag">{{ $ad->format === 'kotak' ? 'Kotak' : 'Banner' }}</span></td>
        <td class="sub">{{ $ad->starts_at ? tgl($ad->starts_at) : 'Sekarang' }} – {{ $ad->ends_at ? tgl($ad->ends_at) : 'Seterusnya' }}</td>
        <td class="sub">{{ $ad->posts_count }} berita</td>
        <td>{{ number_format($ad->clicks, 0, ',', '.') }}</td>
        <td>{!! $ad->is_live ? '<span class="tag ok">Tayang</span>' : '<span class="tag off">Tidak tayang</span>' !!}</td>
        <td><div class="actions">
          <a href="{{ route('admin.ads.edit', $ad) }}" class="btn btn-sm btn-ghost" data-testid="edit-ad-{{ $ad->id }}"><i data-lucide="pencil"></i></a>
          <form method="post" action="{{ route('admin.ads.destroy', $ad) }}" data-confirm="Hapus iklan ini? Iklan juga akan dilepas dari landing dan berita.">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" data-testid="delete-ad-{{ $ad->id }}"><i data-lucide="trash-2"></i></button></form>
        </div></td>
      </tr>
    @empty
      <tr><td colspan="8" class="empty-row">Belum ada iklan.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
@endsection
