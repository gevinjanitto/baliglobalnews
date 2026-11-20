@extends('layouts.admin')
@section('title', 'Kategori')
@section('content')
<div class="topline">
  <div><h1>Kategori</h1><p>Master kategori berita. Centang “Tampil di landing” untuk membuat blok khusus di beranda.</p></div>
  <a href="{{ route('admin.categories.create') }}" class="btn btn-red" data-testid="new-category-btn"><i data-lucide="plus"></i> Tambah Kategori</a>
</div>
<div class="panel" style="padding:6px 8px">
  <table data-testid="categories-table">
    <thead><tr><th>#</th><th>Nama</th><th>Berita</th><th>Menu</th><th>Landing</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @foreach($categories as $c)
      <tr data-testid="category-row-{{ $c->id }}">
        <td class="sub">{{ $c->sort_order }}</td>
        <td><div class="ttl">{{ $c->name }}</div><div class="sub">/kategori/{{ $c->slug }}</div></td>
        <td>{{ $c->posts_count }}</td>
        <td>{!! $c->show_in_menu ? '<span class="tag ok">Ya</span>' : '<span class="tag off">Tidak</span>' !!}</td>
        <td>{!! $c->show_on_home ? '<span class="tag gold">Tampil</span>' : '<span class="tag off">—</span>' !!}</td>
        <td>{!! $c->is_active ? '<span class="tag ok">Aktif</span>' : '<span class="tag off">Nonaktif</span>' !!}</td>
        <td><div class="actions">
          <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-sm btn-ghost" data-testid="edit-category-{{ $c->id }}"><i data-lucide="pencil"></i></a>
          <form method="post" action="{{ route('admin.categories.destroy', $c) }}" data-confirm="Hapus kategori ini?">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" data-testid="delete-category-{{ $c->id }}"><i data-lucide="trash-2"></i></button></form>
        </div></td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endsection
