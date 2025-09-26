@extends('layouts.mini')

@section('title','To\'lovlar tarixi')

@section('content')
<div class="page">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h6 class="mb-0">To'lovlar tarixi</h6>
    <a class="btn btn-mini" href="{{ route('mini.balance') }}"><i class="bi bi-wallet2 me-1"></i> Balans</a>
  </div>

  @forelse($payments as $payment)
    <div class="card mini-card p-3 mb-2">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="fw-semibold">#{{ $payment->id }} • {{ $payment->type === 'balance_topup' ? 'Balans to\'ldirish' : 'Buyurtma to\'lovi' }}</div>
          <small class="text-secondary">{{ $payment->created_at->format('d.m.Y H:i') }}</small>
        </div>
        <div class="text-end">
          <div class="fw-bold">{{ number_format($payment->amount, 0, '', ' ') }} so'm</div>
          <span class="badge bg-{{ $payment->status === 'approved' ? 'success' : ($payment->status === 'rejected' ? 'danger' : 'warning') }}">
            {{ $payment->status }}
          </span>
        </div>
      </div>
      @if($payment->receipt_url)
        <div class="mt-2">
          <a class="small" href="{{ asset('storage/'.$payment->receipt_url) }}" target="_blank">
            <i class="bi bi-receipt me-1"></i> Chekni ko'rish
          </a>
        </div>
      @endif
    </div>
  @empty
    <div class="card mini-card p-4 text-center">
      <i class="bi bi-credit-card fs-1 text-muted mb-3"></i>
      <h6 class="mb-2">To'lovlar yo'q</h6>
      <p class="text-secondary mb-3">Hali hech qanday to'lov yubormagansiz</p>
      <a href="{{ route('mini.balance') }}" class="btn btn-mini"><i class="bi bi-wallet2"></i> Balansni to'ldirish</a>
    </div>
  @endforelse

  @if($payments->hasPages())
    <div class="d-flex justify-content-center mt-3">
      {{ $payments->links() }}
    </div>
  @endif
</div>
@endsection
