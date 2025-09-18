@extends('layouts.admin')

@section('title', 'Buyurtmalar')

@section('content')
<div class="card p-3">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bi bi-bag"></i> Buyurtmalar</h5>
    <form class="d-flex gap-2" method="GET">
      <input class="form-control form-control-sm" type="text" name="q" value="{{ request('q') }}" placeholder="ID, tracking yoki foydalanuvchi...">
      <select class="form-select form-select-sm" name="status">
        <option value="">Barcha holatlar</option>
        @php($statuses=['pending'=>'Kutilmoqda','accepted'=>'Qabul qilindi','rejected'=>'Rad etildi','shipping'=>'Yetakazilmoqda','delivered'=>'Yetakazib berildi','cancelled'=>'Bekor qilindi'])
        @foreach($statuses as $key=>$label)
          <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
        @endforeach
      </select>
      <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i></button>
      <a class="btn btn-sm btn-outline-light" href="{{ route('admin.orders.index') }}"><i class="bi bi-x"></i></a>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Foydalanuvchi</th>
                <th>Status</th>
                <th>Summasi</th>
                <th>Harakat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>#{{ $order->id }}</td>
                    <td>{{ $order->user->name }}</td>
                    <td><span class="badge bg-info">{{ $order->status }}</span></td>
                    <td>{{ $order->total_price }}</td>
                    <td>
                        <a class="btn btn-sm btn-outline-light" href="{{ route('admin.orders.show', $order) }}"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
  </div>
  {{ $orders->links() }}
</div>
@endsection


