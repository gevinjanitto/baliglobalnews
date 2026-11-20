<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Masuk · Admin {{ setting('site_name') }}</title>
<link rel="icon" href="{{ asset('favicon.png') }}">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400..800&family=Instrument+Serif:ital@0;1&family=Manrope:wght@400..800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=11">
</head>
<body class="login-body">
<div class="login-wrap">
  <section class="login-art">
    <div class="aurora"><i></i><i></i><i></i></div>
    <div class="grid-bg"></div>
    <div class="art-top"><span class="kicker"><span class="dot"></span> Ruang Redaksi</span><span class="kicker plain">{{ setting('motto') }}</span></div>
    <div class="art-center">
      <img src="{{ asset('brand/logo-full.png') }}" alt="{{ setting('site_name') }}" class="logo-outline art-logo" data-testid="login-logo">
      <h2>Kabar Bali,<br><em>dari meja</em> Anda<span>.</span></h2>
      <p>Kelola berita, kategori wilayah, kategori, dan iklan {{ setting('site_name') }} dari satu tempat.</p>
    </div>
    <div class="live-stack" data-testid="login-live-list">
      @foreach($live as $p)
      <a href="{{ route('post.show', $p->slug) }}" target="_blank" class="live-row" style="animation-delay:{{ 0.4 + $loop->index * 0.15 }}s"><span class="live-pill">LIVE</span><span>{{ \Illuminate\Support\Str::limit($p->title, 70) }}</span></a>
      @endforeach
    </div>
  </section>
  <section class="login-form">
    <form method="post" action="{{ route('admin.login.submit') }}" data-testid="login-form" id="loginForm">
      @csrf
      <img src="{{ asset('brand/logo-full.png') }}" alt="" class="form-logo">
      <span class="pill-tag"><i data-lucide="sparkles"></i> Selamat datang kembali</span>
      <h1>Masuk Admin</h1>
      <p>Gunakan akun redaksi Anda untuk melanjutkan.</p>
      @if($errors->any())<div class="errors shake" data-testid="login-error"><i data-lucide="alert-circle"></i> {{ $errors->first() }}</div>@endif
      <label for="email">Email</label>
      <div class="ifield"><i data-lucide="mail"></i><input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@baliglobalnews.com" data-testid="login-email-input"></div>
      <label for="password">Password</label>
      <div class="ifield"><i data-lucide="lock"></i><input type="password" id="password" name="password" required placeholder="••••••••" data-testid="login-password-input">
        <button type="button" class="eye" id="eye" aria-label="Tampilkan password" data-testid="login-toggle-password"><i data-lucide="eye"></i></button></div>
      <label class="check remember"><input type="checkbox" name="remember" value="1" data-testid="login-remember-check"> Ingat saya di perangkat ini</label>
      <button class="btn btn-red btn-block" id="loginBtn" data-testid="login-submit-btn"><span>Masuk ke Dashboard</span><i data-lucide="arrow-right"></i></button>
      <a href="{{ route('home') }}" class="back" data-testid="login-back-link"><i data-lucide="arrow-left"></i> Kembali ke website</a>
    </form>
  </section>
</div>
<script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js"></script>
<script>
lucide.createIcons();
const pw = document.getElementById('password');
document.getElementById('eye').addEventListener('click', e => { pw.type = pw.type === 'password' ? 'text' : 'password'; e.currentTarget.classList.toggle('on'); });
document.getElementById('loginForm').addEventListener('submit', () => document.getElementById('loginBtn').classList.add('loading'));
document.querySelector('.login-art').addEventListener('mousemove', e => {
  const r = e.currentTarget.getBoundingClientRect(), x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
  document.querySelector('.art-logo').style.transform = `perspective(600px) rotateY(${x * 16}deg) rotateX(${-y * 16}deg)`;
});
</script>
</body>
</html>
