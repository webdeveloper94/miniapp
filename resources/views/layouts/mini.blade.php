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
</body>
</html>



