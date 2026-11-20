@extends('layouts.admin')
@section('title', 'Pengaturan')
@section('content')
<div class="topline"><div><h1>Pengaturan</h1><p>Identitas situs, media sosial, dan akun admin.</p></div></div>
<div class="panel storage-panel" data-testid="storage-status-panel">
  <h3><i data-lucide="hard-drive"></i> Status Penyimpanan Gambar<small>Tempat gambar upload disimpan. Di Railway harus <b>s3</b> (Bucket) agar gambar tidak hilang saat redeploy.</small></h3>
  <div class="storage-row">
    <span class="tag {{ $storage['disk'] === 's3' ? 'ok' : 'gold' }}" data-testid="storage-disk">FILESYSTEM_DISK = {{ $storage['disk'] }}</span>
    @if($storage['ok'])
      <span class="tag ok" data-testid="storage-ok">Tes tulis berhasil — tersambung</span>
    @else
      <span class="tag red" data-testid="storage-fail">Tes tulis gagal</span>
    @endif
    @if($storage['disk'] !== 's3')<span class="sub">Gambar disimpan di dalam server/container (bukan Bucket).</span>@endif
  </div>
  @if($storage['details'])
  <div class="storage-grid">@foreach($storage['details'] as $k => $v)<div><span>{{ $k }}</span><b class="{{ $v === 'KOSONG' ? 'bad' : '' }}">{{ $v }}</b></div>@endforeach</div>
  @endif
  @if($storage['error'])<div class="errors" style="margin:14px 0 0" data-testid="storage-error"><b>Pesan error:</b> {{ $storage['error'] }}</div>@endif
</div>
<div class="grid2">
  <form method="post" action="{{ route('admin.settings.update') }}" class="panel" data-testid="settings-form">
    @csrf @method('PUT')
    <h3>Identitas Situs</h3>
    <div class="row">
      <div class="field"><label>Nama situs</label><input type="text" name="site_name" value="{{ setting('site_name') }}" data-testid="setting-site-name"></div>
      <div class="field"><label>Tagline</label><input type="text" name="tagline" value="{{ setting('tagline') }}"></div>
    </div>
    <div class="field"><label>Motto</label><input type="text" name="motto" value="{{ setting('motto') }}"></div>
    <div class="field"><label>Tentang (footer)</label><textarea name="about">{{ setting('about') }}</textarea></div>
    <div class="row3">
      <div class="field"><label>Email</label><input type="text" name="email" value="{{ setting('email') }}"></div>
      <div class="field"><label>Telepon / WA</label><input type="text" name="phone" value="{{ setting('phone') }}"></div>
      <div class="field"><label>Alamat</label><input type="text" name="address" value="{{ setting('address') }}"></div>
    </div>
    <h3 style="margin-top:10px">Media Sosial</h3>
    <div class="row">
      <div class="field"><label>Facebook</label><input type="text" name="facebook" value="{{ setting('facebook') }}"></div>
      <div class="field"><label>Twitter / X</label><input type="text" name="twitter" value="{{ setting('twitter') }}"></div>
      <div class="field"><label>Instagram</label><input type="text" name="instagram" value="{{ setting('instagram') }}"></div>
      <div class="field"><label>YouTube</label><input type="text" name="youtube" value="{{ setting('youtube') }}"></div>
    </div>
    <label class="check"><input type="checkbox" name="ticker_enabled" value="1" @checked(setting('ticker_enabled') === '1')><span>Tampilkan ticker “Terkini” di atas</span></label>
    <button class="btn btn-red" style="margin-top:8px" data-testid="settings-save-btn"><i data-lucide="save"></i> Simpan Pengaturan</button>
  </form>
  <form method="post" action="{{ route('admin.settings.password') }}" class="panel" data-testid="password-form">
    @csrf @method('PUT')
    <h3>Ganti Password<small>Minimal 8 karakter.</small></h3>
    <div class="field"><label>Password lama</label><input type="password" name="current_password" required data-testid="current-password-input"></div>
    <div class="field"><label>Password baru</label><input type="password" name="password" required data-testid="new-password-input"></div>
    <div class="field"><label>Ulangi password baru</label><input type="password" name="password_confirmation" required data-testid="confirm-password-input"></div>
    <button class="btn btn-red" data-testid="password-save-btn"><i data-lucide="key-round"></i> Ganti Password</button>
  </form>
</div>
@endsection
