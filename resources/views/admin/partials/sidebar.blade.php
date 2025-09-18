<h5 class="text-white mb-3 d-flex align-items-center gap-2"><i class="bi bi-speedometer2"></i> Admin Panel</h5>
<nav class="d-flex flex-column gap-1">
  <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid"></i> Dashboard</a>
  <a class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people"></i> Foydalanuvchilar</a>
  <a class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="bi bi-bag"></i> Buyurtmalar</a>
  <a class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" href="{{ route('admin.payments.index') }}"><i class="bi bi-credit-card"></i> To'lovlar</a>
  <a class="{{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}"><i class="bi bi-gear"></i> Sozlash</a>
  <form class="mt-3" method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-sm btn-outline-light w-100"><i class="bi bi-box-arrow-right"></i> Chiqish</button>
  </form>
</nav>


