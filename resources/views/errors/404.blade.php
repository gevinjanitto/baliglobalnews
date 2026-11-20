@extends('layouts.site')

@section('title', 'Halaman tidak ditemukan – '.setting('site_name'))

@section('content')
<div class="wrap">
  <header class="list-head err" data-testid="error-404">
    <div class="err-num">4<img src="{{ asset('brand/emblem.png') }}" alt="">4</div>
    <h1>Tak Ditemukan</h1>
    <p>Berita atau halaman yang Anda cari mungkin sudah dipindah atau dihapus.</p>
    <a href="{{ route('home') }}" class="btn-pill" data-testid="error-home-btn">Kembali ke Beranda <i data-lucide="arrow-right"></i></a>
  </header>
</div>
@endsection
