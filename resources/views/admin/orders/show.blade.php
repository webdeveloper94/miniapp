@extends('layouts.admin')

@section('title', 'Buyurtma #'.$order->id)

@section('content')
<div class="wg-box mb-3">
<h5 class="d-flex align-items-center gap-2"><i class="bi bi-bag"></i> Buyurtma #{{ $order->id }}</h5>

<p class="mb-1"><strong>Foydalanuvchi:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
<p class="mb-1"><strong>Status:</strong> <span class="badge bg-info">{{ $order->status }}</span></p>
<p class="mb-1"><strong>Manba platforma:</strong> {{ $order->source_platform }}</p>
<p class="mb-0"><a class="link-light" href="{{ $order->product_url }}" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Mahsulot linki</a></p>
</div>

<div class="wg-box">
<h6 class="mb-2">Mahsulotlar</h6>
<div class="wg-table table-orders">
  <ul class="table-title flex gap10 mb-14">
    <li><div class="body-title">Nomi</div></li>
    <li><div class="body-title">Miqdori</div></li>
    <li><div class="body-title">Narx</div></li>
    <li><div class="body-title">Jami</div></li>
  </ul>
  <ul class="flex flex-column gap18">
    @foreach($order->items as $item)
      <li class="product-item gap14">
        <div class="body-text">{{ $item->title }}</div>
        <div class="flex items-center justify-between flex-grow gap10">
          <div class="body-text">{{ $item->quantity }}</div>
          <div class="body-text">{{ $item->unit_price }}</div>
          <div class="body-text">{{ $item->subtotal }}</div>
        </div>
      </li>
    @endforeach
  </ul>
</div>

<form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="row g-2 mt-2">
    @csrf
    @method('PUT')
    <div class="col-md-4">
      <label class="form-label">Holat</label>
      <select class="form-select" name="status" required>
          @php($statuses = ['pending' => 'Kutilmoqda','accepted' => 'Qabul qilindi','rejected' => 'Rad etildi','shipping' => 'Yetakazilmoqda','delivered' => 'Yetakazib berildi','cancelled' => 'Bekor qilindi'])
          @foreach($statuses as $key => $label)
              <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
          @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Tracking raqami</label>
      <input class="form-control" type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" />
    </div>
    <div class="col-md-4 d-flex align-items-end">
      <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Saqlash</button>
    </div>
  </form>
</div>
 </div>
@endsection


