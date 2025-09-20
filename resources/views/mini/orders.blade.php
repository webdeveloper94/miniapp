@extends('layouts.mini')

@section('title','Buyurtmalar')

@section('content')
<div class="page">
  @if (session('status'))
    <div class="alert alert-info py-2 px-3 mb-3">{{ session('status') }}</div>
  @endif

  <h6 class="mb-3">Buyurtmalar</h6>
  
  @forelse($orders as $order)
    <div class="card mini-card p-3 mb-3">
      <div class="d-flex justify-content-between align-items-start mb-2">
        <div>
          <div class="fw-semibold">Buyurtma #{{ $order->id }}</div>
          <small class="text-secondary">{{ $order->created_at->format('d.m.Y H:i') }}</small>
        </div>
        <div class="text-end">
          @php
            $statusColors = [
              'pending' => 'warning',
              'accepted' => 'success', 
              'rejected' => 'danger',
              'shipping' => 'info',
              'delivered' => 'success'
            ];
            $statusTexts = [
              'pending' => 'Kutilmoqda',
              'accepted' => 'Qabul qilindi',
              'rejected' => 'Rad etildi',
              'shipping' => 'Yetkazilmoqda',
              'delivered' => 'Yetkazib berildi'
            ];
          @endphp
          <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
            {{ $statusTexts[$order->status] ?? $order->status }}
          </span>
        </div>
      </div>
      
      @if($order->orderItems->count() > 0)
        @foreach($order->orderItems as $item)
          <div class="d-flex align-items-start mb-2">
            @if($item->image_url)
              <img src="{{ $item->image_url }}" class="me-2" style="width:50px;height:50px;object-fit:cover;border-radius:6px" referrerpolicy="no-referrer" crossorigin="anonymous">
            @else
              <div class="me-2 d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:#f0f0f0;border-radius:6px">
                <i class="bi bi-image text-muted"></i>
              </div>
            @endif
            
            <div class="flex-grow-1">
              <div class="fw-semibold small">{{ $item->title }}</div>
              <div class="text-secondary small">
                {{ $item->quantity }} dona × {{ number_format($item->unit_price, 0, '', ' ') }} so'm
              </div>
              @if($item->product_params)
                @php $params = json_decode($item->product_params, true) @endphp
                @if($params && count($params) > 0)
                  <div class="small">
                    @foreach($params as $key => $value)
                      <span class="chip me-1">{{ $key }}: {{ $value }}</span>
                    @endforeach
                  </div>
                @endif
              @endif
            </div>
          </div>
        @endforeach
      @endif
      
      <div class="d-flex justify-content-between align-items-center">
        <div class="fw-bold text-primary">
          Jami: {{ number_format($order->total_amount, 0, '', ' ') }} so'm
        </div>
        @if($order->tracking_number)
          <small class="text-secondary">
            <i class="bi bi-truck me-1"></i>
            {{ $order->tracking_number }}
          </small>
        @endif
      </div>
    </div>
  @empty
    <div class="card mini-card p-4 text-center">
      <i class="bi bi-bag-x fs-1 text-muted mb-3"></i>
      <h6 class="mb-2">Buyurtmalar yo'q</h6>
      <p class="text-secondary mb-3">Hozircha buyurtma qilmagansiz</p>
      <a href="{{ route('mini.home') }}" class="btn btn-mini"><i class="bi bi-house-door"></i> Bosh sahifa</a>
    </div>
  @endforelse
  
  @if($orders->hasPages())
    <div class="d-flex justify-content-center mt-3">
      {{ $orders->links() }}
    </div>
  @endif
</div>
@endsection



