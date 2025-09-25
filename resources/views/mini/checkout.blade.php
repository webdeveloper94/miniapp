@extends('layouts.mini')

@section('title', __('messages.checkout'))

@section('content')
<div class="page">
  @if (session('status'))
    <div class="alert alert-info py-2 px-3 mb-3">{{ session('status') }}</div>
  @endif

  @php
    $raw = session('mini_product');
    $p = $raw['data'] ?? $raw ?? [];
    $unitRaw = $p['price'] ?? ($p['minPrice'] ?? ($p['discountPrice'] ?? ($p['referencePrice'] ?? ($p['promotionPrice'] ?? null))));
    if ($unitRaw === null) {
        if (!empty($p['productSaleInfo']['priceRangeList'][0]['price'])) {
            $unitRaw = $p['productSaleInfo']['priceRangeList'][0]['price'];
        } elseif (!empty($p['productSaleInfo']['priceRangeList'][0]['value'])) {
            $unitRaw = $p['productSaleInfo']['priceRangeList'][0]['value'];
        } elseif (!empty($p['priceRange'][0]['price'])) {
            $unitRaw = $p['priceRange'][0]['price'];
        } elseif (!empty($p['priceRanges'][0]['price'])) {
            $unitRaw = $p['priceRanges'][0]['price'];
        }
    }
    // sanitize numeric
    $unit = 0;
    if ($unitRaw !== null) {
        $unit = (float) filter_var(is_string($unitRaw) ? preg_replace('/[^\d.]/', '', $unitRaw) : $unitRaw, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    $rate = optional(\App\Models\AdminSetting::first())->cny_to_uzs ?? 0;
    $unitUzs = $rate > 0 ? ($unit * $rate) : $unit;
    $serviceRule = \App\Models\ServiceFee::getFeeForAmount($unitUzs);
    $servicePercent = $serviceRule?->fee_percentage ?? 0;
  @endphp

  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-3">{{ __('messages.product_info') }}</h6>
    
    <div class="d-flex align-items-start">
      @if(isset($product['data']['productImage']['images'][0]))
        <img src="{{ $product['data']['productImage']['images'][0] }}" class="me-3" style="width:80px;height:80px;object-fit:cover;border-radius:8px" referrerpolicy="no-referrer" crossorigin="anonymous">
      @elseif(isset($product['productImage']['images'][0]))
        <img src="{{ $product['productImage']['images'][0] }}" class="me-3" style="width:80px;height:80px;object-fit:cover;border-radius:8px" referrerpolicy="no-referrer" crossorigin="anonymous">
      @else
        <div class="me-3 d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:#f0f0f0;border-radius:8px">
          <i class="bi bi-image text-muted"></i>
        </div>
      @endif
      
      <div class="flex-grow-1">
        <h6 class="mb-1">{{ $product['data']['subject'] ?? $product['subject'] ?? 'Mahsulot' }}</h6>
        <div class="text-secondary small mb-2">
          <span class="fw-semibold">{{ number_format($unitUzs, 0, '', ' ') }} {{ $rate>0 ? "so'm" : 'yuan' }}</span>
        </div>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('mini.order.create') }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $offerId ?? ($product['data']['itemId'] ?? $product['itemId'] ?? '') }}">
    <input type="hidden" name="title" value="{{ $product['data']['subject'] ?? $product['subject'] ?? 'Mahsulot' }}">
    <input type="hidden" name="price" value="{{ (float) ($rate>0 ? $unit*$rate : $unit) }}">
    <input type="hidden" name="selected_variants" value="{{ json_encode([]) }}">

  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-3">{{ __('messages.user_info') }}</h6>
      
      <div class="row g-2 mb-3">
        <div class="col-6">
          <label class="form-label">{{ __('messages.first_name') }}</label>
          <input type="text" name="first_name" class="form-control mini-input" value="{{ session('telegram_user.first_name') }}" required>
        </div>
        <div class="col-6">
          <label class="form-label">{{ __('messages.last_name') }}</label>
          <input type="text" name="last_name" class="form-control mini-input" value="{{ session('telegram_user.last_name') }}" required>
        </div>
      </div>
      
      <div class="mb-3">
        <label class="form-label">{{ __('messages.phone') }}</label>
        <input type="tel" name="phone" class="form-control mini-input" placeholder="+998901234567" required>
      </div>
      
      <div class="mb-3">
        <label class="form-label">{{ __('messages.address') }}</label>
        <textarea name="address" class="form-control mini-input" rows="3" placeholder="Shahar, tuman, ko'cha, uy raqami..." required></textarea>
      </div>
    </div>

  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-3">{{ __('messages.order_info') }}</h6>
      
      <div class="mb-3">
        <label class="form-label">Miqdor</label>
        <input type="number" name="quantity" class="form-control mini-input" value="1" min="1" max="99" required>
      </div>
      
      <div class="mb-3">
        <label class="form-label">{{ __('messages.note') }} (ixtiyoriy)</label>
        <textarea name="notes" class="form-control mini-input" rows="3" placeholder="Buyurtma haqida qo'shimcha ma'lumot..."></textarea>
      </div>
    </div>

  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-3">{{ __('messages.payment_info') }}</h6>
      
      <div class="alert alert-warning py-2 px-3 mb-3">
        <small>
          <i class="bi bi-info-circle me-1"></i>
          {{ __('messages.payment_note') }}
        </small>
      </div>
      
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span>{{ __('messages.price') }}:</span>
        <span id="unit-price">{{ number_format($unitUzs, 0, '', ' ') }} {{ $rate>0 ? "so'm" : 'yuan' }}</span>
      </div>
      
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span>{{ __('messages.quantity') }}:</span>
        <span id="quantity-display">1 {{ __('messages.unit_piece') ?? 'dona' }}</span>
      </div>
      
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span>{{ __('messages.service_fee_label') }} ({{ (float) $servicePercent }}%):</span>
        <span id="service-fee">0 so'm</span>
      </div>
      <hr>
      <div class="d-flex justify-content-between align-items-center">
        <span class="fw-semibold">{{ __('messages.total_label') }}:</span>
        <span class="fw-bold text-primary" id="total-price">{{ number_format($unit, 0, '', ' ') }} so'm</span>
      </div>
    </div>

    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-mini">
        <i class="bi bi-lightning-charge"></i> {{ __('messages.checkout') }}
      </button>
      <a href="{{ route('mini.product') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
      </a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const quantityInput = document.querySelector('input[name="quantity"]');
  const quantityDisplay = document.getElementById('quantity-display');
  const totalPrice = document.getElementById('total-price');
  const unitPrice = {{ (float) $unitUzs }};
  const servicePercent = {{ (float) $servicePercent }};
  const serviceFeeEl = document.getElementById('service-fee');

  function updateTotal() {
    const quantity = parseInt(quantityInput.value) || 1;
    const base = unitPrice * quantity;
    const feeAmount = Math.round(base * servicePercent / 100);
    const total = base + feeAmount;
    
    quantityDisplay.textContent = quantity + ' dona';
    totalPrice.textContent = new Intl.NumberFormat('uz-UZ').format(total) + ' so\'m';
    serviceFeeEl.textContent = new Intl.NumberFormat('uz-UZ').format(feeAmount) + ' so\'m';
  }

  quantityInput.addEventListener('input', updateTotal);
  updateTotal();
});
</script>
@endsection
