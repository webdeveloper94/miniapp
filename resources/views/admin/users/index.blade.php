@extends('layouts.admin')

@section('title', 'Foydalanuvchilar')

@section('content')
<div class="wg-box">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bi bi-people"></i> Foydalanuvchilar</h5>
    <form class="d-flex gap-2" method="GET">
      <input class="form-control form-control-sm" type="text" name="q" value="{{ request('q') }}" placeholder="Qidirish...">
      <select class="form-select form-select-sm" name="role">
        <option value="">Barcha rollar</option>
        <option value="admin" @selected(request('role')==='admin')>Admin</option>
        <option value="user" @selected(request('role')==='user')>User</option>
      </select>
      <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i></button>
      <a class="btn btn-sm btn-outline-light" href="{{ route('admin.users.index') }}"><i class="bi bi-x"></i></a>
    </form>
  </div>
  <div class="wg-table table-orders">
    <ul class="table-title flex gap10 mb-14">
      <li><div class="body-title">ID</div></li>
      <li><div class="body-title">Ism</div></li>
      <li><div class="body-title">Username</div></li>
      <li><div class="body-title">Telefon</div></li>
      <li><div class="body-title">Email</div></li>
      <li><div class="body-title">Rol</div></li>
    </ul>
    <ul class="flex flex-column gap18">
      @foreach ($users as $user)
        <li class="product-item gap14">
          <div class="body-text">#{{ $user->id }}</div>
          <div class="flex items-center justify-between flex-grow gap10">
            <div class="name"><span class="body-text">{{ $user->name }}</span></div>
            <div class="body-text">{{ $user->username }}</div>
            <div class="body-text">{{ $user->phone }}</div>
            <div class="body-text">{{ $user->email }}</div>
            <div class="body-text"><span class="badge {{ $user->role==='admin' ? 'bg-primary' : 'bg-secondary' }}">{{ $user->role }}</span></div>
          </div>
        </li>
      @endforeach
    </ul>
  </div>
  <div class="mt-3">{{ $users->links() }}</div>
</div>
@endsection


