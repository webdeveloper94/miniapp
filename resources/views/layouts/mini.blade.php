<!DOCTYPE html>
<html lang="uz">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>@yield('title','Vikup Mini')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    :root{
      --mini-bg:#f7fbff; /* light */
      --mini-card:#ffffff; /* white */
      --mini-accent:#1d72f2; /* primary blue */
      --mini-accent-2:#e6f0ff; /* pale blue */
      --mini-text:#0b132b; /* dark */
    }
    body{background:var(--mini-bg);color:var(--mini-text)}
    .mini-card{background:var(--mini-card);border:1px solid #e7eefb;color:var(--mini-text);box-shadow:0 6px 14px rgba(29,114,242,0.06)}
    .mini-input{background:#f2f7ff;border-color:#cfe1ff;color:#0b132b}
    .mini-input::placeholder{color:#7b94c2}
    .btn-mini{background:var(--mini-accent);border:none;color:#fff}
    .btn-mini:active,.btn-mini:hover{background:#135bd1}
    .bottom-nav{position:fixed;left:0;right:0;bottom:0;background:#ffffff;border-top:1px solid #e7eefb}
    .bottom-nav a{color:#4a5e8a;text-decoration:none;font-size:12px}
    .bottom-nav a.active{color:#1d72f2}
    .page{padding-bottom:88px;padding-top:12px}
    .avatar{width:40px;height:40px;border-radius:12px;background:#e6f0ff;display:flex;align-items:center;justify-content:center;color:#1d72f2}
    .chip{background:#e6f0ff;color:#1d72f2;border-radius:999px;padding:.2rem .6rem;font-size:.75rem}
  </style>
</head>
<body>
  <div class="container-sm" style="max-width:480px">
    @if(session('telegram_user'))
    <div class="d-flex align-items-center p-2 mb-2" style="background:var(--mini-accent-2);border-radius:8px">
      <div class="avatar me-2">
        <i class="bi bi-person"></i>
      </div>
      <div>
        <div class="fw-semibold">{{ session('telegram_user.first_name') }} {{ session('telegram_user.last_name') }}</div>
        <small class="text-muted">@{{ session('telegram_user.username') }}</small>
      </div>
    </div>
    @endif
    @yield('content')
  </div>

  <nav class="bottom-nav py-2">
    <div class="container-sm" style="max-width:480px">
      <div class="d-flex justify-content-between text-center">
        <a class="flex-fill {{ request()->routeIs('mini.home')?'active':'' }}" href="{{ route('mini.home') }}"><div><i class="bi bi-house-door fs-5"></i></div>Bosh sahifa</a>
        <a class="flex-fill {{ request()->routeIs('mini.orders')?'active':'' }}" href="{{ route('mini.orders') }}"><div><i class="bi bi-bag fs-5"></i></div>Buyurtmalar</a>
        <a class="flex-fill {{ request()->routeIs('mini.cart')?'active':'' }}" href="{{ route('mini.cart') }}"><div><i class="bi bi-cart fs-5"></i></div>Savatcha</a>
        <a class="flex-fill {{ request()->routeIs('mini.profile')?'active':'' }}" href="{{ route('mini.profile') }}"><div><i class="bi bi-person fs-5"></i></div>Profil</a>
      </div>
    </div>
  </nav>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @php
    $needsLoginPassword = false;
    if (session('telegram_user')) {
      $uid = session('telegram_user.id');
      try {
        $u = $uid ? \App\Models\User::find($uid) : null;
        $needsLoginPassword = $u && empty($u->login_password);
      } catch (\Throwable $e) {
        $needsLoginPassword = false;
      }
    }
  @endphp

  @if($needsLoginPassword)
  <div class="modal show" id="miniLoginPasswordModal" tabindex="-1" style="display:block; background: rgba(0,0,0,.6);">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Profil himoyasi</h5>
        </div>
        <div class="modal-body">
          <p class="mb-3">Iltimos, profilni tiklash uchun parol yarating yoki mavjud profilga kirish uchun parolingizni kiriting.</p>
          <div class="card mini-card p-3 mb-3">
            <h6 class="mb-2">Yangi parol o'rnatish</h6>
            <form method="POST" action="{{ route('mini.auth.setPassword') }}" class="d-grid gap-2">
              @csrf
              <input class="form-control mini-input" type="password" name="password" minlength="4" maxlength="50" placeholder="Yangi parol" required>
              <button class="btn btn-mini" type="submit">Parolni saqlash</button>
            </form>
          </div>
          <div class="card mini-card p-3">
            <h6 class="mb-2">Eski profilga kirish</h6>
            <form method="POST" action="{{ route('mini.auth.recover') }}" class="d-grid gap-2">
              @csrf
              <input class="form-control mini-input" type="text" name="username" minlength="3" maxlength="50" placeholder="@username" required>
              <input class="form-control mini-input" type="password" name="password" minlength="4" maxlength="50" placeholder="Parol" required>
              <button class="btn btn-outline-primary" type="submit">Profilni tiklash</button>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <small class="text-muted">Parol faqat profilni tiklash uchun ishlatiladi.</small>
        </div>
      </div>
    </div>
  </div>
  <script>
    // Block page scrolling when modal is forced
    document.body.style.overflow = 'hidden';
  </script>
  @endif
</body>
</html>



