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
      
      <div class="d-flex justify-content-between align-items-center mb-2">
        <div class="fw-bold text-primary">
          Jami: {{ number_format($order->total_price, 0, '', ' ') }} so'm
        </div>
        @if($order->tracking_number)
          <small class="text-secondary">
            <i class="bi bi-truck me-1"></i>
            {{ $order->tracking_number }}
          </small>
        @endif
      </div>
      
      @if($order->status === 'pending')
        @php
          $payment = $order->payment;
          $hasPayment = $payment && $payment->receipt_url;
        @endphp
        
        @if($hasPayment)
          <div class="d-grid gap-2">
            <button class="btn btn-warning" disabled>
              <i class="bi bi-clock me-1"></i> Tasdiqlanishi kutilmoqda
            </button>
          </div>
        @else
          <div class="d-grid gap-2">
            <button class="btn btn-mini" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $order->id }}">
              <i class="bi bi-credit-card me-1"></i> To'lov qilish
            </button>
          </div>
        @endif
      @elseif($order->status === 'accepted')
        <div class="d-grid gap-2">
          <button class="btn btn-success" disabled>
            <i class="bi bi-check-circle me-1"></i> Tasdiqlangan
          </button>
        </div>
      @elseif($order->status === 'rejected')
        <div class="d-grid gap-2">
          <button class="btn btn-danger" disabled>
            <i class="bi bi-x-circle me-1"></i> Rad etilgan
          </button>
          @if($order->payment && $order->payment->note)
            <small class="text-danger mt-1">
              <i class="bi bi-info-circle me-1"></i> Sabab: {{ $order->payment->note }}
            </small>
          @endif
        </div>
      @elseif($order->status === 'shipping')
        <div class="d-grid gap-2">
          <button class="btn btn-info" disabled>
            <i class="bi bi-truck me-1"></i> Yetkazilmoqda
          </button>
        </div>
      @elseif($order->status === 'delivered')
        <div class="d-grid gap-2">
          <button class="btn btn-success" disabled>
            <i class="bi bi-check-circle-fill me-1"></i> Yetkazib berildi
          </button>
        </div>
      @endif
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
      <nav aria-label="Buyurtmalar pagination">
        <ul class="pagination pagination-sm" style="--bs-pagination-color: var(--mini-primary); --bs-pagination-bg: white; --bs-pagination-border-color: #dee2e6; --bs-pagination-hover-color: var(--mini-primary); --bs-pagination-hover-bg: #e9ecef; --bs-pagination-hover-border-color: #dee2e6; --bs-pagination-focus-color: var(--mini-primary); --bs-pagination-focus-bg: #e9ecef; --bs-pagination-focus-box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); --bs-pagination-active-color: #fff; --bs-pagination-active-bg: var(--mini-primary); --bs-pagination-active-border-color: var(--mini-primary); --bs-pagination-disabled-color: #6c757d; --bs-pagination-disabled-bg: #fff; --bs-pagination-disabled-border-color: #dee2e6;">
          @if($orders->onFirstPage())
            <li class="page-item disabled">
              <span class="page-link">
                <i class="bi bi-chevron-left"></i>
              </span>
            </li>
          @else
            <li class="page-item">
              <a class="page-link" href="{{ $orders->previousPageUrl() }}">
                <i class="bi bi-chevron-left"></i>
              </a>
            </li>
          @endif

          @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
            @if($page == $orders->currentPage())
              <li class="page-item active">
                <span class="page-link">{{ $page }}</span>
              </li>
            @else
              <li class="page-item">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
              </li>
            @endif
          @endforeach

          @if($orders->hasMorePages())
            <li class="page-item">
              <a class="page-link" href="{{ $orders->nextPageUrl() }}">
                <i class="bi bi-chevron-right"></i>
              </a>
            </li>
          @else
            <li class="page-item disabled">
              <span class="page-link">
                <i class="bi bi-chevron-right"></i>
              </span>
            </li>
          @endif
        </ul>
      </nav>
    </div>
    
    <div class="text-center mt-2">
      <small class="text-muted">
        {{ $orders->firstItem() }} dan {{ $orders->lastItem() }} gacha, jami {{ $orders->total() }} ta
      </small>
    </div>
  @endif
</div>

<!-- Payment Modals -->
@foreach($orders as $order)
  @if($order->status === 'pending')
    <div class="modal fade" id="paymentModal{{ $order->id }}" tabindex="-1" aria-labelledby="paymentModalLabel{{ $order->id }}" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="paymentModalLabel{{ $order->id }}">To'lov qilish</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form method="POST" action="{{ route('mini.payment.submit') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            <div class="modal-body">
              <!-- Admin karta ma'lumotlari -->
              <div class="alert alert-info mb-3">
                <h6 class="mb-2"><i class="bi bi-credit-card me-1"></i> Admin karta ma'lumotlari:</h6>
                <div class="fw-bold">Karta raqami: {{ $adminSettings['admin_card_number'] ?? '8600 1234 5678 9012' }}</div>
                <div class="fw-bold">Karta egasi: {{ $adminSettings['admin_card_owner'] ?? 'Admin User' }}</div>
                <div class="fw-bold">Bank: {{ $adminSettings['admin_bank'] ?? 'Xalq Banki' }}</div>
              </div>
              
              <!-- Qisqa matn -->
              <div class="alert alert-warning mb-3">
                <small>
                  <i class="bi bi-info-circle me-1"></i>
                  <strong>Diqqat:</strong> To'lov admin tomonidan tasdiqlanadi, shu sababli biroz vaqt olish mumkin. 
                  To'lov chekini yuklab, admin bilan bog'laning.
                </small>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Sizning karta raqamingiz</label>
                <input type="text" name="card_number" class="form-control" placeholder="8600 1234 5678 9012" required>
                <small class="text-muted">Qaysi kartadan to'lov qilganingizni kiriting</small>
              </div>
              <div class="mb-3">
                <label class="form-label">To'lov summasi</label>
                <input type="number" name="amount" class="form-control" value="{{ $order->total_price }}" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label">To'lov cheki (rasm)</label>
                <input type="file" name="receipt_image" class="form-control" accept="image/*" required>
                <small class="text-muted">To'lov chekining rasmini yuklang</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Qo'shimcha izoh</label>
                <textarea name="note" class="form-control" rows="2" placeholder="To'lov haqida qo'shimcha ma'lumot..."></textarea>
              </div>
              
              <!-- Admin aloqa -->
              <div class="alert alert-light border mb-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-telegram me-2 text-primary"></i>
                  <div>
                    <small class="text-muted">Savollar uchun admin bilan bog'laning:</small>
                    <div class="fw-bold">{{ $adminSettings['admin_telegram'] ?? '@admin_username' }}</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
              <button type="submit" class="btn btn-mini">To'lovni yuborish</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
@endforeach
@endsection



