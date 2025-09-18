@extends('layouts.admin')

@section('title', 'Foydalanuvchilar')

@section('content')
<div class="card p-3">
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
  <div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ism</th>
                <th>Username</th>
                <th>Telefon</th>
                <th>Email</th>
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge bg-secondary">{{ $user->role }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>
  </div>
  {{ $users->links() }}
</div>
@endsection


