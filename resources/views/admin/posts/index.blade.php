@extends('layouts.admin')
@section('title', 'Berita')
@section('content')
<div class="topline">
  <div><h1>Berita</h1><p>{{ $posts->total() }} berita tersimpan.</p></div>
  <a href="{{ route('admin.posts.create') }}" class="btn btn-red" data-testid="new-post-btn"><i data-lucide="plus"></i> Tulis Berita</a>
</div>
<form class="filters" method="get" data-testid="posts-filter-form">
  <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul…" style="flex:1" data-testid="posts-search-input">
  <select name="region_id" data-testid="posts-filter-region"><option value="">Semua wilayah</option>@foreach($regions as $r)<option value="{{ $r->id }}" @selected(request('region_id') == $r->id)>{{ $r->full_name }}</option>@endforeach</select>
  <select name="category_id" data-testid="posts-filter-category"><option value="">Semua kategori</option>@foreach($categories as $c)<option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>@endforeach</select>
  <select name="status" data-testid="posts-filter-status"><option value="">Semua status</option><option value="published" @selected(request('status') === 'published')>Terbit</option><option value="draft" @selected(request('status') === 'draft')>Draft</option></select>
  <select name="year" data-testid="posts-filter-year"><option value="">Semua tahun</option>@foreach($years as $y)<option value="{{ $y }}" @selected((int) request('year') === $y)>{{ $y }}</option>@endforeach</select>
  <button class="btn" data-testid="posts-filter-submit"><i data-lucide="filter"></i> Filter</button>
</form>
<div class="panel" style="padding:6px 8px">
  <table data-testid="posts-table">
    <thead><tr><th></th><th>Judul</th><th>Wilayah / Kategori / Tag</th><th>Penanda</th><th>Iklan</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @forelse($posts as $p)
      <tr data-testid="post-row-{{ $p->id }}">
        <td style="width:84px"><img class="thumb" src="{{ $p->image_url }}" alt=""></td>
        <td><div class="ttl">{{ $p->title }}</div><div class="sub">{{ tgl($p->published_at, 'd M Y H:i') }} · {{ number_format($p->shown_views, 0, ',', '.') }} dibaca @if($p->views_manual !== null)<span class="tag gold" title="Angka manual">manual · asli {{ number_format($p->views, 0, ',', '.') }}</span>@endif · {{ $p->author_name }}</div></td>
        <td>@if($p->region)<span class="tag red">{{ $p->region->name }}</span>@endif @foreach($p->categories as $c)<span class="tag">{{ $c->name }}</span>@endforeach @foreach($p->tags->take(3) as $t)<span class="tag off">#{{ $t->name }}</span>@endforeach</td>
        <td>@if($p->is_headline)<span class="tag gold">Utama</span>@endif @if($p->is_editor_choice)<span class="tag gold">Pilihan</span>@endif @if($p->is_breaking)<span class="tag gold">Terkini</span>@endif</td>
        <td class="sub">{{ $p->ads_count }} iklan</td>
        <td><span class="tag {{ $p->status === 'published' ? 'ok' : 'off' }}">{{ $p->status === 'published' ? 'Terbit' : 'Draft' }}</span></td>
        <td><div class="actions">
          @if($p->status === 'published')<a href="{{ $p->url }}" target="_blank" class="btn btn-sm btn-ghost" title="Lihat"><i data-lucide="eye"></i></a>@endif
          <a href="{{ route('admin.posts.edit', $p) }}" class="btn btn-sm btn-ghost" data-testid="edit-post-{{ $p->id }}"><i data-lucide="pencil"></i></a>
          <form method="post" action="{{ route('admin.posts.destroy', $p) }}" data-confirm="Hapus berita ini?">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" data-testid="delete-post-{{ $p->id }}"><i data-lucide="trash-2"></i></button></form>
        </div></td>
      </tr>
    @empty
      <tr><td colspan="7" class="empty-row">Tidak ada berita.</td></tr>
    @endforelse
    </tbody>
  </table>
</div>
{{ $posts->links() }}
@endsection
