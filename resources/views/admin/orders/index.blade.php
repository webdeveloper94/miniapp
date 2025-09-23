@extends('layouts.admin')

@section('title', 'Buyurtmalar')

@section('content')
<div class="wg-box">
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
  <div class="wg-table table-orders">
    <ul class="table-title flex gap10 mb-14">
      <li><div class="body-title">ID</div></li>
      <li><div class="body-title">Foydalanuvchi</div></li>
      <li><div class="body-title">Status</div></li>
      <li><div class="body-title">Summasi</div></li>
      <li><div class="body-title">Harakat</div></li>
    </ul>
    <ul class="flex flex-column gap18">
      @foreach ($orders as $order)
        <li class="product-item gap14">
          <div class="body-text">#{{ $order->id }}</div>
          <div class="flex items-center justify-between flex-grow gap10">
            <div class="name"><span class="body-text">{{ $order->user->name }}</span></div>
            <div class="body-text"><span class="badge {{ $order->status==='delivered' ? 'bg-success' : ($order->status==='rejected' ? 'bg-danger' : ($order->status==='pending' ? 'bg-warning text-dark' : 'bg-info')) }}">{{ $order->status }}</span></div>
            <div class="body-text">{{ $order->total_price }}</div>
            <div class="body-text"><a class="btn btn-sm btn-outline-light" href="{{ route('admin.orders.show', $order) }}"><i class="bi bi-eye"></i></a></div>
          </div>
        </li>
      @endforeach
    </ul>
  </div>
  <div class="mt-3">{{ $orders->links() }}</div>
</div>
@endsection


