<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <!-- Cuba assets (your paths) -->
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('assets/admin/images/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/animation.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/font/fonts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/icon/style.css') }}">
    @stack('styles')
</head>
<body onload="window.startTime && startTime()">
    <div id="wrapper">
      <div id="page" class="">
        <div class="layout-wrap">
          <!-- Left menu (Remos) -->
          <div class="section-menu-left">
            <div class="box-logo">
              <a href="{{ route('admin.dashboard') }}" id="site-logo-inner">
                <img id="logo_header" alt="Remos" src="{{ asset('assets/admin/images/logo/logo.png') }}" data-light="{{ asset('assets/admin/images/logo/logo.png') }}" data-dark="{{ asset('assets/admin/images/logo/logo-dark.png') }}">
              </a>
              <div class="button-show-hide"><i class="icon-menu-left"></i></div>
            </div>
            <div class="center">
              <div class="center-item">
                <div class="center-heading">General</div>
                <ul class="menu-list">
                  <li class="menu-item">
                    <a href="{{ route('admin.dashboard') }}" class="menu-item-button">
                      <div class="icon"><i class="icon-grid"></i></div>
                      <div class="text">Dashboard</div>
                    </a>
                  </li>
                  <li class="menu-item">
                    <a href="{{ route('admin.users.index') }}" class="menu-item-button">
                      <div class="icon"><i class="icon-users"></i></div>
                      <div class="text">Foydalanuvchilar</div>
                    </a>
                  </li>
                  <li class="menu-item">
                    <a href="{{ route('admin.orders.index') }}" class="menu-item-button">
                      <div class="icon"><i class="icon-file"></i></div>
                      <div class="text">Buyurtmalar</div>
                    </a>
                  </li>
                  <li class="menu-item">
                    <a href="{{ route('admin.payments.index') }}" class="menu-item-button">
                      <div class="icon"><i class="icon-dollar-sign"></i></div>
                      <div class="text">To'lovlar</div>
                    </a>
                  </li>
                  <li class="menu-item">
                    <a href="{{ route('admin.settings') }}" class="menu-item-button">
                      <div class="icon"><i class="icon-settings"></i></div>
                      <div class="text">Sozlash</div>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            <div class="bot text-center" style="padding:12px">
              <form method="POST" action="{{ route('admin.logout') }}">@csrf
                <button class="tf-button w-full" type="submit">Logout</button>
              </form>
            </div>
          </div>
          <!-- Right content -->
          <div class="section-content-right">
            <div class="header-dashboard">
              <div class="wrap">
                <div class="header-left">
                  <a href="{{ route('admin.dashboard') }}">
                    <img id="logo_header_mobile" alt="Remos" src="{{ asset('assets/admin/images/logo/logo.png') }}">
                  </a>
                  <div class="button-show-hide"><i class="icon-menu-left"></i></div>
                  <form class="form-search" action="{{ route('admin.orders.index') }}" method="GET">
                    <fieldset class="name">
                      <input type="text" placeholder="Qidirish..." class="show-search" name="q" value="{{ request('q') }}">
                    </fieldset>
                    <div class="button-submit"><button type="submit"><i class="icon-search"></i></button></div>
                  </form>
                </div>
                <div class="header-grid">
                  <div class="header-item country">
                    <select class="image-select no-text" onchange="location.href='{{ url('admin?lang=') }}'+this.value">
                      <option data-thumbnail="{{ asset('assets/admin/images/country/1.png') }}" value="en">EN</option>
                      <option data-thumbnail="{{ asset('assets/admin/images/country/9.png') }}" value="uz">UZ</option>
                      <option data-thumbnail="{{ asset('assets/admin/images/country/7.png') }}" value="ru">RU</option>
                    </select>
                  </div>
                  <div class="header-item button-dark-light" onclick="document.body.classList.toggle('dark'); localStorage.setItem('remos-dark', document.body.classList.contains('dark') ? '1' : '0');" title="Dark/Light">
                    <i class="icon-moon"></i>
                  </div>
                  <div class="popup-wrap noti type-header">
                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="header-item"><span class="text-tiny">{{ isset($stats['orders_pending']) ? $stats['orders_pending'] : (isset($pendingOrders) ? $pendingOrders->count() : 0) }}</span><i class="icon-bell"></i></span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end has-content">
                        <li><h6>Yangi bildirishnomalar</h6></li>
                        @forelse(($pendingOrders ?? []) as $po)
                          <li>
                            <div class="message-item item-1">
                              <div class="image"><i class="icon-noti-1"></i></div>
                              <div>
                                <div class="body-title-2">Yangi buyurtma #{{ $po->id }}</div>
                                <div class="text-tiny">Foydalanuvchi: {{ $po->user->name ?? 'Noma’lum' }} | Holat: {{ $po->status }}</div>
                              </div>
                            </div>
                          </li>
                        @empty
                          <li><div class="px-3 py-2 text-muted">Hozircha bildirishnoma yo‘q</div></li>
                        @endforelse
                        <li><a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="tf-button w-full">Barchasini ko‘rish</a></li>
                      </ul>
                    </div>
                  </div>
                  <div class="popup-wrap user type-header">
                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="header-user wg-user">
                          <span class="image"><img src="{{ asset('assets/admin/images/avatar/user-1.png') }}" alt=""></span>
                          <span class="flex flex-column"><span class="body-title mb-2">{{ auth()->user()->name ?? 'Admin' }}</span><span class="text-tiny">Admin</span></span>
                        </span>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end has-content">
                        <li>
                          <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="dropdown-item" type="submit">Logout</button></form>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="main-content">
              <div class="main-content-inner">
                <div class="main-content-wrap">
                  @if (session('status'))
                    <div class="alert alert-info">{{ session('status') }}</div>
                  @endif
                  @if ($errors->any())
                    <div class="alert alert-danger">
                      <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @endif
                  @yield('content')
                </div>
              </div>
              <div class="bottom-page text-center" style="padding:16px">
                <div class="body-text">© {{ date('Y') }} Remos</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="{{ asset('assets/admin/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/zoom.js') }}"></script>
    <script src="{{ asset('assets/admin/js/apexcharts/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/admin/js/theme-settings.js') }}"></script>
    <script src="{{ asset('assets/admin/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>


