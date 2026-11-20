@extends('layouts.admin')
@section('title', 'Kategori Wilayah')
@section('content')
<div class="topline">
  <div><h1>Kategori Wilayah</h1><p>Master wilayah berjenjang (Provinsi › Kabupaten › Instansi). Dipilih saat input berita.</p></div>
  <a href="{{ route('admin.regions.create') }}" class="btn btn-red" data-testid="new-region-btn"><i data-lucide="plus"></i> Tambah Wilayah</a>
</div>
<div class="panel" style="padding:6px 8px">
  <table data-testid="regions-table">
    <thead><tr><th>Nama</th><th>Slug</th><th>Berita</th><th>Menu</th><th>Status</th><th></th></tr></thead>
    <tbody>
    @foreach($regions as $r)
      <tr data-testid="region-row-{{ $r->id }}">
        <td><div class="ttl">{{ $r->name }}</div><div class="sub">{{ $r->full_name }}</div></td>
        <td class="sub">/wilayah/{{ $r->slug }}</td>
        <td>{{ $r->posts_count }}</td>
        <td>{!! $r->show_in_menu ? '<span class="tag ok">Ya</span>' : '<span class="tag off">Tidak</span>' !!}</td>
        <td>{!! $r->is_active ? '<span class="tag ok">Aktif</span>' : '<span class="tag off">Nonaktif</span>' !!}</td>
        <td><div class="actions">
          <a href="{{ route('admin.regions.edit', $r) }}" class="btn btn-sm btn-ghost" data-testid="edit-region-{{ $r->id }}"><i data-lucide="pencil"></i></a>
          <form method="post" action="{{ route('admin.regions.destroy', $r) }}" data-confirm="Hapus wilayah ini? Sub-wilayah akan menjadi wilayah utama dan berita tidak lagi berwilayah.">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" data-testid="delete-region-{{ $r->id }}"><i data-lucide="trash-2"></i></button></form>
        </div></td>
      </tr>
    @endforeach
    </tbody>
  </table>
</div>
@endsection
