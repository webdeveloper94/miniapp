@extends('layouts.admin')

@section('title', 'To\'lovlar')

@section('content')
<div class="wg-box">
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bi bi-credit-card"></i> To'lovlar</h5>
  <form class="d-flex gap-2" method="GET">
    <input class="form-control form-control-sm" type="text" name="q" value="{{ request('q') }}" placeholder="ID, summa yoki foydalanuvchi...">
    <select class="form-select form-select-sm" name="status">
      <option value="">Barcha holatlar</option>
      @foreach(['pending'=>'Kutilmoqda','approved'=>'Tasdiqlangan','rejected'=>'Rad etilgan'] as $key=>$label)
        <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i></button>
    <a class="btn btn-sm btn-outline-light" href="{{ route('admin.payments.index') }}"><i class="bi bi-x"></i></a>
  </form>
</div>
<div class="table-responsive">
<div class="wg-table table-orders">
  <ul class="table-title flex gap10 mb-14">
    <li><div class="body-title">ID</div></li>
    <li><div class="body-title">Foydalanuvchi</div></li>
    <li><div class="body-title">Buyurtma</div></li>
    <li><div class="body-title">Summasi</div></li>
    <li><div class="body-title">Status</div></li>
    <li><div class="body-title">Chek</div></li>
    <li><div class="body-title">Harakat</div></li>
  </ul>
  <ul class="flex flex-column gap18">
    @foreach ($payments as $payment)
      <li class="product-item gap14">
        <div class="body-text">#{{ $payment->id }}</div>
        <div class="flex items-center justify-between flex-grow gap10">
          <div class="name"><span class="body-text">{{ $payment->user->name }}</span></div>
          <div class="body-text">{{ optional($payment->order)->id ? ('#'.$payment->order->id) : '-' }}</div>
          <div class="body-text">{{ $payment->amount }} {{ $payment->currency }}</div>
          <div class="body-text"><span class="badge {{ $payment->status==='approved' ? 'bg-success' : ($payment->status==='rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $payment->status }}</span></div>
          <div class="body-text">
            @if($payment->receipt_path)
              <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}" target="_blank">Ko'rish</a>
            @else - @endif
          </div>
          <div class="body-text">
            @if($payment->status === 'pending')
              <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" style="display:inline">@csrf @method('PUT')
                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check2-circle"></i></button>
              </form>
              <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" style="display:inline" class="d-inline-flex gap-1 align-items-center">@csrf @method('PUT')
                <input class="form-control form-control-sm" type="text" name="note" placeholder="Sabab (ixtiyoriy)" />
                <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-x-circle"></i></button>
              </form>
            @endif
          </div>
        </div>
      </li>
    @endforeach
  </ul>
</div>
<div class="mt-3">{{ $payments->links() }}</div>
</div>
@push('styles')
<style>
  .remos-table thead th{font-weight:600}
  body.dark .remos-table{color:#e2e8f0}
  body.dark .remos-table thead th{color:#cbd5e1;border-color:rgba(255,255,255,.12)}
  body.dark .remos-table tbody td{color:#e2e8f0;border-color:rgba(255,255,255,.08)}
  .wg-box{padding:20px}
}</style>
@endpush
</div>
@endsection


