@extends('layouts.admin')
@section('title', $category->exists ? 'Edit Kategori' : 'Tambah Kategori')
@section('content')
<div class="topline"><div><h1>{{ $category->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</h1></div><a href="{{ route('admin.categories.index') }}" class="btn btn-ghost"><i data-lucide="arrow-left"></i> Kembali</a></div>
<form method="post" action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}" class="panel" style="max-width:720px" data-testid="category-form">
  @csrf @if($category->exists) @method('PUT') @endif
  <div class="row">
    <div class="field"><label>Nama kategori</label><input type="text" name="name" value="{{ old('name', $category->name) }}" required data-testid="category-name-input"></div>
    <div class="field"><label>Slug <span class="hint">(otomatis bila kosong)</span></label><input type="text" name="slug" value="{{ old('slug', $category->slug) }}" data-testid="category-slug-input"></div>
  </div>
  <div class="field"><label>Urutan</label><input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" style="max-width:160px"></div>
  <div class="field"><label>Deskripsi</label><textarea name="description">{{ old('description', $category->description) }}</textarea></div>
  <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active)) data-testid="category-active-check"><span>Aktif</span></label>
  <label class="check"><input type="checkbox" name="show_in_menu" value="1" @checked(old('show_in_menu', $category->show_in_menu)) data-testid="category-menu-check"><span>Tampil di menu atas</span></label>
  <label class="check"><input type="checkbox" name="show_on_home" value="1" @checked(old('show_on_home', $category->show_on_home)) data-testid="category-home-check"><span>Tampil di landing<small>Membuat blok kategori khusus di beranda</small></span></label>
  <button class="btn btn-red" style="margin-top:8px" data-testid="category-save-btn"><i data-lucide="save"></i> Simpan</button>
</form>
@endsection
