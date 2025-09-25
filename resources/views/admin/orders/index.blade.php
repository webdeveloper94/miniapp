@extends('layouts.admin')

@section('title', 'Buyurtmalar')

@section('content')
<div class="wg-box">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
    <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bi bi-bag"></i> Buyurtmalar</h5>
    <form class="d-flex gap-2 flex-wrap" method="GET">
      <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="ID, tracking yoki foydalanuvchi..." style="min-width: 200px;">
      <select class="form-select" name="status" style="min-width: 150px;">
        <option value="">Barcha holatlar</option>
        @php($statuses=['pending'=>'Kutilmoqda','accepted'=>'Qabul qilindi','rejected'=>'Rad etildi','shipping'=>'Yetakazilmoqda','delivered'=>'Yetakazib berildi','cancelled'=>'Bekor qilindi'])
        @foreach($statuses as $key=>$label)
          <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
        @endforeach
      </select>
      <button class="btn btn-primary" type="submit" title="Qidirish"><i class="bi bi-search me-1"></i>Qidirish</button>
      <a class="btn btn-outline-secondary" href="{{ route('admin.orders.index') }}" title="Tozalash"><i class="bi bi-x me-1"></i>Tozalash</a>
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
            <div class="body-text"><a class="btn btn-outline-primary" href="{{ route('admin.orders.show', $order) }}"><i class="bi bi-eye me-1"></i>Ko'rish</a></div>
          </div>
        </li>
      @endforeach
    </ul>
  </div>
  <div class="mt-3">{{ $orders->links() }}</div>
</div>
@push('styles')
<style>
  .wg-box{padding:20px}
  .btn{padding: 0.5rem 1rem; font-size: 0.875rem; border-radius: 0.375rem; font-weight: 500; white-space: nowrap;}
  .btn-primary{background-color: #0d6efd; border-color: #0d6efd; color: #fff;}
  .btn-primary:hover{background-color: #0b5ed7; border-color: #0a58ca;}
  .btn-outline-primary{color: #0d6efd; border-color: #0d6efd;}
  .btn-outline-primary:hover{background-color: #0d6efd; border-color: #0d6efd; color: #fff;}
  .btn-outline-secondary{color: #6c757d; border-color: #6c757d;}
  .btn-outline-secondary:hover{background-color: #6c757d; border-color: #6c757d; color: #fff;}
  .d-flex.gap-2 > * + *{margin-left: 0.5rem;}
  
  /* Responsive tugmalar */
  @media (max-width: 768px) {
    .d-flex.flex-wrap{flex-direction: column; align-items: stretch !important;}
    .d-flex.flex-wrap form{flex-direction: column; gap: 0.5rem;}
    .d-flex.flex-wrap form > *{width: 100% !important; min-width: auto !important;}
  }
</style>
@endpush
@endsection


