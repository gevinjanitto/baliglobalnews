@extends('layouts.admin')
@section('title', $region->exists ? 'Edit Wilayah' : 'Tambah Wilayah')
@section('content')
<div class="topline"><div><h1>{{ $region->exists ? 'Edit Wilayah' : 'Tambah Wilayah' }}</h1></div><a href="{{ route('admin.regions.index') }}" class="btn btn-ghost"><i data-lucide="arrow-left"></i> Kembali</a></div>
<form method="post" action="{{ $region->exists ? route('admin.regions.update', $region) : route('admin.regions.store') }}" class="panel" style="max-width:720px" data-testid="region-form">
  @csrf @if($region->exists) @method('PUT') @endif
  <div class="row">
    <div class="field"><label>Nama wilayah</label><input type="text" name="name" value="{{ old('name', $region->name) }}" required data-testid="region-name-input"></div>
    <div class="field"><label>Slug <span class="hint">(otomatis bila kosong)</span></label><input type="text" name="slug" value="{{ old('slug', $region->slug) }}" data-testid="region-slug-input"></div>
  </div>
  <div class="row">
    <div class="field"><label>Induk wilayah</label>
      <select name="parent_id" data-testid="region-parent-select"><option value="">— Wilayah utama —</option>
        @foreach($parents as $p)<option value="{{ $p->id }}" @selected(old('parent_id', $region->parent_id) == $p->id)>{{ $p->full_name }}</option>@endforeach
      </select></div>
    <div class="field"><label>Urutan</label><input type="number" name="sort_order" value="{{ old('sort_order', $region->sort_order ?? 0) }}"></div>
  </div>
  <div class="field"><label>Deskripsi</label><textarea name="description">{{ old('description', $region->description) }}</textarea></div>
  <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $region->is_active)) data-testid="region-active-check"><span>Aktif</span></label>
  <label class="check"><input type="checkbox" name="show_in_menu" value="1" @checked(old('show_in_menu', $region->show_in_menu)) data-testid="region-menu-check"><span>Tampil di menu<small>Wilayah utama tampil di menu atas, sub-wilayah tampil di dropdown</small></span></label>
  <button class="btn btn-red" style="margin-top:8px" data-testid="region-save-btn"><i data-lucide="save"></i> Simpan</button>
</form>
@endsection
