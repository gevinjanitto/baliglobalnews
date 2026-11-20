@extends('layouts.admin')
@section('title', $page->exists ? 'Edit Halaman' : 'Tambah Halaman')
@push('head')<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">@endpush
@section('content')
<div class="topline"><div><h1>{{ $page->exists ? 'Edit Halaman' : 'Tambah Halaman' }}</h1></div><a href="{{ route('admin.pages.index') }}" class="btn btn-ghost"><i data-lucide="arrow-left"></i> Kembali</a></div>
<form method="post" id="pageForm" action="{{ $page->exists ? route('admin.pages.update', $page) : route('admin.pages.store') }}" class="panel" data-testid="page-form">
  @csrf @if($page->exists) @method('PUT') @endif
  <div class="row3">
    <div class="field"><label>Judul</label><input type="text" name="title" value="{{ old('title', $page->title) }}" required data-testid="page-title-input"></div>
    <div class="field"><label>Slug</label><input type="text" name="slug" value="{{ old('slug', $page->slug) }}" data-testid="page-slug-input"></div>
    <div class="field"><label>Urutan</label><input type="number" name="sort_order" value="{{ old('sort_order', $page->sort_order ?? 0) }}"></div>
  </div>
  <div class="field"><label>Isi halaman</label><div id="editor">{!! old('body', $page->body) !!}</div><input type="hidden" name="body" id="bodyInput"></div>
  <label class="check"><input type="checkbox" name="show_in_footer" value="1" @checked(old('show_in_footer', $page->show_in_footer))><span>Tampilkan di footer & menu</span></label>
  <button class="btn btn-red" style="margin-top:8px" data-testid="page-save-btn"><i data-lucide="save"></i> Simpan</button>
</form>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
const quill = new Quill('#editor', { theme: 'snow', modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline'], ['blockquote', { list: 'ordered' }, { list: 'bullet' }], ['link'], ['clean']] } });
document.getElementById('pageForm').addEventListener('submit', () => { document.getElementById('bodyInput').value = quill.getSemanticHTML(); });
</script>
@endpush
