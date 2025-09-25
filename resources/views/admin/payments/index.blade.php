@extends('layouts.admin')

@section('title', 'To\'lovlar')

@section('content')
<div class="wg-box">
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
  <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bi bi-credit-card"></i> To'lovlar</h5>
  <form class="d-flex gap-2 flex-wrap" method="GET">
    <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="ID, summa yoki foydalanuvchi..." style="min-width: 200px;">
    <select class="form-select" name="status" style="min-width: 150px;">
      <option value="">Barcha holatlar</option>
      @foreach(['pending'=>'Kutilmoqda','approved'=>'Tasdiqlangan','rejected'=>'Rad etilgan'] as $key=>$label)
        <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="btn btn-primary" type="submit" title="Qidirish"><i class="bi bi-search me-1"></i>Qidirish</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.payments.index') }}" title="Tozalash"><i class="bi bi-x me-1"></i>Tozalash</a>
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
        <div class="body-text">
          <a href="{{ route('admin.payments.show', $payment) }}" class="text-decoration-none fw-semibold">
            #{{ $payment->id }}
          </a>
        </div>
        <div class="flex items-center justify-between flex-grow gap10">
          <div class="name">
            <a href="{{ route('admin.payments.show', $payment) }}" class="text-decoration-none">
              <span class="body-text">{{ $payment->user->name }}</span>
            </a>
          </div>
          <div class="body-text">
            @if($payment->order)
              <a href="{{ route('admin.orders.show', $payment->order) }}" class="text-decoration-none">
                #{{ $payment->order->id }}
              </a>
            @else - @endif
          </div>
          <div class="body-text">{{ $payment->amount }} {{ $payment->currency }}</div>
          <div class="body-text"><span class="badge {{ $payment->status==='approved' ? 'bg-success' : ($payment->status==='rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ $payment->status }}</span></div>
          <div class="body-text">
            @if($payment->receipt_path)
              <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}" target="_blank">Ko'rish</a>
            @else - @endif
          </div>
          <div class="body-text">
            @if($payment->status === 'pending')
              <div class="d-flex gap-2">
                <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" style="display:inline">@csrf @method('PUT')
                  <button type="submit" class="btn btn-success" title="Tasdiqlash"><i class="bi bi-check2-circle me-1"></i>Tasdiqlash</button>
                </form>
                <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" style="display:inline" class="d-inline-flex gap-1 align-items-center">@csrf @method('PUT')
                  <input class="form-control form-control-sm" type="text" name="note" placeholder="Sabab (ixtiyoriy)" />
                  <button type="submit" class="btn btn-danger" title="Rad etish"><i class="bi bi-x-circle me-1"></i>Rad etish</button>
                </form>
              </div>
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
  
  /* Tugma stillari */
  .btn{padding: 0.5rem 1rem; font-size: 0.875rem; border-radius: 0.375rem; font-weight: 500; white-space: nowrap;}
  .btn-primary{background-color: #0d6efd; border-color: #0d6efd; color: #fff;}
  .btn-primary:hover{background-color: #0b5ed7; border-color: #0a58ca;}
  .btn-success{background-color: #198754; border-color: #198754; color: #fff;}
  .btn-success:hover{background-color: #157347; border-color: #146c43;}
  .btn-danger{background-color: #dc3545; border-color: #dc3545; color: #fff;}
  .btn-danger:hover{background-color: #bb2d3b; border-color: #b02a37;}
  .btn-outline-secondary{color: #6c757d; border-color: #6c757d;}
  .btn-outline-secondary:hover{background-color: #6c757d; border-color: #6c757d; color: #fff;}
  .d-flex.gap-2 > * + *{margin-left: 0.5rem;}
  
  /* Responsive tugmalar */
  @media (max-width: 768px) {
    .d-flex.flex-wrap{flex-direction: column; align-items: stretch !important;}
    .d-flex.flex-wrap form{flex-direction: column; gap: 0.5rem;}
    .d-flex.flex-wrap form > *{width: 100% !important; min-width: auto !important;}
  }
}</style>
@endpush
</div>
@endsection


