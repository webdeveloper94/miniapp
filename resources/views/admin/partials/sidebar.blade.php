<ul class="sidebar-links" id="simple-bar">
  <li class="sidebar-main-title"><div><h6>General</h6></div></li>
  <li class="sidebar-list">
    <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
      <svg class="stroke-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#stroke-home') }}"></use></svg>
      <svg class="fill-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#fill-home') }}"></use></svg>
      <span>Dashboard</span>
    </a>
  </li>
  <li class="sidebar-list">
    <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
      <svg class="stroke-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#stroke-user') }}"></use></svg>
      <svg class="fill-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#fill-user') }}"></use></svg>
      <span>Foydalanuvchilar</span>
    </a>
  </li>
  <li class="sidebar-list">
    <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
      <svg class="stroke-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#stroke-ecommerce') }}"></use></svg>
      <svg class="fill-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#fill-ecommerce') }}"></use></svg>
      <span>Buyurtmalar</span>
    </a>
  </li>
  <li class="sidebar-list">
    <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}">
      <svg class="stroke-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#stroke-ecommerce') }}"></use></svg>
      <svg class="fill-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#fill-ecommerce') }}"></use></svg>
      <span>To'lovlar</span>
    </a>
  </li>
  <li class="sidebar-list">
    <a class="sidebar-link sidebar-title link-nav {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">
      <svg class="stroke-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#stroke-settings') }}"></use></svg>
      <svg class="fill-icon"><use href="{{ asset('assets/admin/svg/icon-sprite.svg#fill-settings') }}"></use></svg>
      <span>Sozlash</span>
    </a>
  </li>
  <li class="sidebar-list">
    <form method="POST" action="{{ route('admin.logout') }}" class="px-3 mb-3">@csrf
      <button type="submit" class="btn btn-primary w-100">Chiqish</button>
    </form>
  </li>
</ul>


