@extends('layouts.admin')
@section('title', 'Iklan Landing')
@section('content')
<div class="topline">
  <div><h1>Iklan Landing</h1><p>Pilih iklan mana yang masuk ke setiap posisi di halaman depan. Bisa lebih dari satu per posisi (ditampilkan berurutan).</p></div>
  <a href="{{ route('home') }}" target="_blank" class="btn btn-ghost"><i data-lucide="external-link"></i> Lihat Landing</a>
</div>
<form method="post" action="{{ route('admin.landing-ads.update') }}" class="panel" data-testid="landing-ads-form">
  @csrf @method('PUT')
  @if($ads->isEmpty())<p class="sub">Belum ada iklan. <a href="{{ route('admin.ads.create') }}" style="color:var(--red)">Tambah di Master Iklan</a>.</p>@endif
  @foreach(\App\Models\LandingAd::SLOTS as $slot => [$label, $desc])
  <div class="slot" data-testid="slot-{{ $slot }}">
    <div><h4>{{ $label }}</h4><p>{{ $desc }}</p><div class="code">{{ $slot === 'home_sidebar' ? 'Disarankan format Kotak' : 'Disarankan format Banner' }}</div></div>
    <div class="ad-pick">
      @foreach($ads as $ad)
      <label class="check" data-testid="slot-{{ $slot }}-ad-{{ $ad->id }}">
        <input type="checkbox" name="slots[{{ $slot }}][]" value="{{ $ad->id }}" @checked(in_array($ad->id, $selected[$slot] ?? []))>
        <img src="{{ $ad->image_url }}" alt="">
        <span>{{ $ad->name }}<small>{{ $ad->format === 'kotak' ? 'Kotak' : 'Banner' }} · {{ $ad->is_live ? 'Tayang' : 'Tidak tayang' }}</small></span>
      </label>
      @endforeach
    </div>
  </div>
  @endforeach
  <button class="btn btn-red" data-testid="landing-ads-save-btn"><i data-lucide="save"></i> Simpan Pengaturan</button>
</form>
@endsection
