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
      --mini-bg:#fff8f1; /* warm light */
      --mini-card:#ffffff; /* white */
      --mini-accent:#f97316; /* primary orange */
      --mini-accent-2:#ffe8d6; /* pale orange */
      --mini-text:#0b132b; /* dark */
      --mini-border:#f3d8c2;
    }
    body{background:var(--mini-bg);color:var(--mini-text)}
    .mini-card{background:var(--mini-card);border:1px solid var(--mini-border);color:var(--mini-text);box-shadow:0 6px 14px rgba(249,115,22,0.06)}
    .mini-input{background:#fff4e8;border-color:#ffd3b6;color:#0b132b}
    .mini-input::placeholder{color:#b77749}
    .btn-mini{background:var(--mini-accent);border:none;color:#fff}
    .btn-mini:active,.btn-mini:hover{background:#ea580c}
    .bottom-nav{position:fixed;left:0;right:0;bottom:0;background:#ffffff;border-top:1px solid var(--mini-border)}
    .bottom-nav a{color:#6b7280;text-decoration:none;font-size:12px}
    .bottom-nav a.active{color:var(--mini-accent)}
    .page{padding-bottom:88px;padding-top:12px}
    .avatar{width:40px;height:40px;border-radius:12px;background:var(--mini-accent-2);display:flex;align-items:center;justify-content:center;color:var(--mini-accent)}
    .chip{background:var(--mini-accent-2);color:var(--mini-accent);border-radius:999px;padding:.2rem .6rem;font-size:.75rem}
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
  
  <!-- Reusable Mini Toast / Confirm Components -->
  <style>
    .mini-toast-wrap{position:fixed;left:0;right:0;bottom:84px;display:flex;justify-content:center;pointer-events:none;z-index:1080}
    .mini-toast{max-width:86%;background:#0b132b;color:#fff;border-radius:14px;padding:10px 14px;box-shadow:0 10px 24px rgba(0,0,0,.25);transform:translateY(20px);opacity:0;transition:all .25s ease;pointer-events:auto}
    .mini-toast.show{transform:translateY(0);opacity:1}
    .mini-toast.success{background:#198754}
    .mini-toast.warning{background:#f59e0b}
    .mini-toast.danger{background:#dc3545}
    .mini-toast .close{margin-left:10px;cursor:pointer;opacity:.9}
    
    /* Nicer bootstrap modal visuals */
    .modal-content{border:0;border-radius:18px;box-shadow:0 24px 64px rgba(29,114,242,.2)}
    .modal-header{border:0;border-bottom:1px solid rgba(0,0,0,.06);padding:14px 18px}
    .modal-footer{border-top:1px solid rgba(0,0,0,.06);padding:12px 18px}
  </style>
  <div class="mini-toast-wrap" id="miniToastWrap" style="display:none">
    <div class="mini-toast" id="miniToast">
      <span id="miniToastMsg">Saved</span>
      <span class="close" onclick="hideMiniToast()">&times;</span>
    </div>
  </div>
  <script>
    let miniToastTimer;
    function showMiniToast(message, type){
      const wrap = document.getElementById('miniToastWrap');
      const t = document.getElementById('miniToast');
      document.getElementById('miniToastMsg').textContent = message;
      t.className = 'mini-toast ' + (type||'');
      wrap.style.display = 'flex';
      requestAnimationFrame(()=> t.classList.add('show'));
      clearTimeout(miniToastTimer);
      miniToastTimer = setTimeout(hideMiniToast, 2500);
    }
    function hideMiniToast(){
      const wrap = document.getElementById('miniToastWrap');
      const t = document.getElementById('miniToast');
      t.classList.remove('show');
      setTimeout(()=> wrap.style.display='none', 220);
    }
    // Confirm helper using Bootstrap modal
    function showMiniConfirm(message, onConfirm){
      const id = 'miniConfirmModal';
      let el = document.getElementById(id);
      if (!el){
        el = document.createElement('div');
        el.className='modal fade';
        el.id=id; el.tabIndex=-1;
        el.innerHTML = '<div class="modal-dialog"><div class="modal-content">\
          <div class="modal-header"><h6 class="modal-title">Tasdiqlaysizmi?</h6>\
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>\
          <div class="modal-body"><div id="miniConfirmBody"></div></div>\
          <div class="modal-footer">\
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>\
            <button type="button" class="btn btn-primary" id="miniConfirmOk">OK</button></div></div></div>';
        document.body.appendChild(el);
      }
      el.querySelector('#miniConfirmBody').textContent = message;
      const okBtn = el.querySelector('#miniConfirmOk');
      okBtn.onclick = function(){ if (onConfirm) onConfirm(); m.hide(); };
      const m = new bootstrap.Modal(el); m.show();
    }
  </script>
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



