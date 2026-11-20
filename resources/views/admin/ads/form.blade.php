@extends('layouts.admin')
@section('title', $ad->exists ? 'Edit Iklan' : 'Tambah Iklan')
@section('content')
<div class="topline"><div><h1>{{ $ad->exists ? 'Edit Iklan' : 'Tambah Iklan' }}</h1></div><a href="{{ route('admin.ads.index') }}" class="btn btn-ghost"><i data-lucide="arrow-left"></i> Kembali</a></div>
<form method="post" enctype="multipart/form-data" action="{{ $ad->exists ? route('admin.ads.update', $ad) : route('admin.ads.store') }}" data-testid="ad-form">
  @csrf @if($ad->exists) @method('PUT') @endif
  <div class="form-grid">
    <div class="panel">
      <div class="field"><label>Nama iklan</label><input type="text" name="name" value="{{ old('name', $ad->name) }}" required data-testid="ad-name-input"></div>
      <div class="field"><label>Tautan tujuan <span class="hint">(opsional, mis. https://www.bpdbali.co.id)</span></label><input type="url" name="link_url" value="{{ old('link_url', $ad->link_url) }}" data-testid="ad-link-input"></div>
      <div class="field"><label>Format</label>
        @foreach(\App\Models\Ad::FORMATS as $k => $label)
          <label class="check"><input type="radio" name="format" value="{{ $k }}" @checked(old('format', $ad->format) === $k) data-testid="ad-format-{{ $k }}"><span>{{ $label }}</span></label>
        @endforeach
      </div>
      <div class="row">
        <div class="field"><label>Mulai tayang <span class="hint">(opsional)</span></label><input type="datetime-local" name="starts_at" value="{{ old('starts_at', $ad->starts_at?->format('Y-m-d\TH:i')) }}" data-testid="ad-start-input"></div>
        <div class="field"><label>Selesai tayang <span class="hint">(opsional)</span></label><input type="datetime-local" name="ends_at" value="{{ old('ends_at', $ad->ends_at?->format('Y-m-d\TH:i')) }}" data-testid="ad-end-input"></div>
      </div>
      <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $ad->is_active)) data-testid="ad-active-check"><span>Aktif</span></label>
      <button class="btn btn-red" style="margin-top:8px" data-testid="ad-save-btn"><i data-lucide="save"></i> Simpan Iklan</button>
    </div>
    <div class="panel">
      <label>Gambar iklan <span class="hint">(maks 5MB)</span></label>
      <div class="preview" id="adPreview">@if($ad->image_url)<img src="{{ $ad->image_url }}" alt="" style="object-fit:contain">@else Belum ada gambar @endif</div>
      <input type="file" name="image" accept="image/*" data-preview="#adPreview" style="margin-top:10px" data-testid="ad-image-input">
      @if($ad->exists)<p class="sub" style="margin-top:14px">Total klik: <b>{{ number_format($ad->clicks, 0, ',', '.') }}</b></p>@endif
    </div>
  </div>
</form>
@endsection
