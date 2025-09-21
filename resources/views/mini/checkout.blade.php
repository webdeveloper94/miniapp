@extends('layouts.mini')

@section('title','Buyurtma berish')

@section('content')
<div class="page">
  @if (session('status'))
    <div class="alert alert-info py-2 px-3 mb-3">{{ session('status') }}</div>
  @endif

  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-3">Mahsulot ma'lumotlari</h6>
    
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
          @php
            $price = null;
            if (isset($product['data']['productSaleInfo']['priceRangeList'][0]['price'])) {
              $price = $product['data']['productSaleInfo']['priceRangeList'][0]['price'];
            } elseif (isset($product['productSaleInfo']['priceRangeList'][0]['price'])) {
              $price = $product['productSaleInfo']['priceRangeList'][0]['price'];
            }
          @endphp
          @if($price)
            <span class="fw-semibold">{{ number_format($price, 0, '', ' ') }} so'm</span>
          @else
            <span class="fw-semibold">Narx ma'lum emas</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <form method="POST" action="{{ route('mini.order.create') }}">
    @csrf
    <input type="hidden" name="product_id" value="{{ $offerId }}">
    <input type="hidden" name="title" value="{{ $product['data']['subject'] ?? $product['subject'] ?? 'Mahsulot' }}">
    <input type="hidden" name="price" value="{{ $product['data']['productSaleInfo']['priceRangeList'][0]['price'] ?? $product['productSaleInfo']['priceRangeList'][0]['price'] ?? 0 }}">
    <input type="hidden" name="selected_variants" value="{{ json_encode([]) }}">

    <div class="card mini-card p-3 mb-3">
      <h6 class="mb-3">Foydalanuvchi ma'lumotlari</h6>
      
      <div class="row g-2 mb-3">
        <div class="col-6">
          <label class="form-label">Ism</label>
          <input type="text" name="first_name" class="form-control mini-input" value="{{ session('telegram_user.first_name') }}" required>
        </div>
        <div class="col-6">
          <label class="form-label">Familiya</label>
          <input type="text" name="last_name" class="form-control mini-input" value="{{ session('telegram_user.last_name') }}" required>
        </div>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Telefon raqami</label>
        <input type="tel" name="phone" class="form-control mini-input" placeholder="+998901234567" required>
      </div>
      
      <div class="mb-3">
        <label class="form-label">To'liq manzil</label>
        <textarea name="address" class="form-control mini-input" rows="3" placeholder="Shahar, tuman, ko'cha, uy raqami..." required></textarea>
      </div>
    </div>

    <div class="card mini-card p-3 mb-3">
      <h6 class="mb-3">Buyurtma ma'lumotlari</h6>
      
      <div class="mb-3">
        <label class="form-label">Miqdor</label>
        <input type="number" name="quantity" class="form-control mini-input" value="1" min="1" max="99" required>
      </div>
      
      <div class="mb-3">
        <label class="form-label">Qo'shimcha izoh (ixtiyoriy)</label>
        <textarea name="notes" class="form-control mini-input" rows="3" placeholder="Buyurtma haqida qo'shimcha ma'lumot..."></textarea>
      </div>
    </div>

    <div class="card mini-card p-3 mb-3">
      <h6 class="mb-3">To'lov ma'lumotlari</h6>
      
      <div class="alert alert-warning py-2 px-3 mb-3">
        <small>
          <i class="bi bi-info-circle me-1"></i>
          Buyurtma berilgandan so'ng sizga to'lov karta raqami ko'rsatiladi. 
          To'lovni amalga oshirib, chekni yuklang.
        </small>
      </div>
      
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span>Mahsulot narxi:</span>
        <span id="unit-price">{{ number_format(session('mini_product.data.price') ?? session('mini_product.data.minPrice'), 0, '', ' ') }} so'm</span>
      </div>
      
      <div class="d-flex justify-content-between align-items-center mb-2">
        <span>Miqdor:</span>
        <span id="quantity-display">1 dona</span>
      </div>
      
      <hr>
      
      <div class="d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Jami:</span>
        <span class="fw-bold text-primary" id="total-price">{{ number_format(session('mini_product.data.price') ?? session('mini_product.data.minPrice'), 0, '', ' ') }} so'm</span>
      </div>
    </div>

    <div class="d-grid gap-2">
      <button type="submit" class="btn btn-mini">
        <i class="bi bi-lightning-charge"></i> Buyurtma berish
      </button>
      <a href="{{ route('mini.product') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Orqaga
      </a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const quantityInput = document.querySelector('input[name="quantity"]');
  const quantityDisplay = document.getElementById('quantity-display');
  const totalPrice = document.getElementById('total-price');
  const unitPrice = {{ isset($product['data']['productSaleInfo']['priceRangeList'][0]['price']) ? $product['data']['productSaleInfo']['priceRangeList'][0]['price'] : (isset($product['productSaleInfo']['priceRangeList'][0]['price']) ? $product['productSaleInfo']['priceRangeList'][0]['price'] : 0) }};

  function updateTotal() {
    const quantity = parseInt(quantityInput.value) || 1;
    const total = unitPrice * quantity;
    
    quantityDisplay.textContent = quantity + ' dona';
    totalPrice.textContent = new Intl.NumberFormat('uz-UZ').format(total) + ' so\'m';
  }

  quantityInput.addEventListener('input', updateTotal);
  updateTotal();
});
</script>
@endsection
