@extends('layouts.mini')

@section('title', __('messages.cart'))

@section('content')
<div class="page">
  @if (session('status'))
    <div class="alert alert-info py-2 px-3 mb-3">{{ session('status') }}</div>
  @endif

  @if(count($items) > 0)
    @foreach($items as $key => $item)
    <div class="card mini-card p-3 mb-3">
      <div class="d-flex align-items-start" onclick="viewProduct('{{ $key }}')" style="cursor: pointer;">
        @if($item['image_url'])
          <img src="{{ $item['image_url'] }}" class="me-3" style="width:80px;height:80px;object-fit:cover;border-radius:8px" referrerpolicy="no-referrer" crossorigin="anonymous">
        @else
          <div class="me-3 d-flex align-items-center justify-content-center" style="width:80px;height:80px;background:#f0f0f0;border-radius:8px">
            <i class="bi bi-image text-muted"></i>
          </div>
        @endif
        
        <div class="flex-grow-1">
          <h6 class="mb-1">{{ $item['title'] }}</h6>
          <div class="text-secondary small mb-2">
            @if($item['selected_variants'])
              @php $variants = json_decode($item['selected_variants'], true) @endphp
              @foreach($variants as $variant => $value)
                <span class="chip me-1">{{ $variant }}: {{ $value }}</span>
              @endforeach
            @endif
          </div>
          
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <span class="fw-semibold">{{ number_format($item['price'], 0, '', ' ') }} so'm</span>
              <small class="text-secondary d-block">{{ __('messages.qty') }}: {{ $item['quantity'] }}</small>
            </div>
            
            <div class="d-flex align-items-center gap-2">
              
              
              <form method="POST" action="{{ route('mini.cart.remove') }}" class="d-inline">
                @csrf
                <input type="hidden" name="product_key" value="{{ $key }}">
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('{{ __('messages.remove') }}?')">
                  <i class="bi bi-trash"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach

    <div class="card mini-card p-3 mb-3">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="fw-semibold">{{ __('messages.total_label') }}:</span>
        <span class="fw-bold text-primary">
          @php
            $total = 0;
            foreach($items as $item) {
              $total += $item['price'] * $item['quantity'];
            }
          @endphp
          {{ number_format($total, 0, '', ' ') }} so'm
        </span>
      </div>
      
      <div class="d-grid gap-2">
        <!-- <button class="btn btn-mini"><i class="bi bi-lightning-charge"></i> {{ __('messages.checkout') }}</button> -->
        <a href="{{ route('mini.home') }}" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> {{ __('messages.continue') }}</a>
      </div>
    </div>
  @else
    <div class="card mini-card p-4 text-center">
      <i class="bi bi-cart-x fs-1 text-muted mb-3"></i>
      <h6 class="mb-2">{{ __('messages.cart_empty') }}</h6>
      <p class="text-secondary mb-3">&nbsp;</p>
      <a href="{{ route('mini.home') }}" class="btn btn-mini"><i class="bi bi-house-door"></i> {{ __('messages.go_home') }}</a>
    </div>
  @endif
</div>

<script>
function viewProduct(productKey) {
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = '{{ route("mini.cart.view-product") }}';
  
  const token = document.createElement('input');
  token.type = 'hidden';
  token.name = '_token';
  token.value = '{{ csrf_token() }}';
  
  const productKeyInput = document.createElement('input');
  productKeyInput.type = 'hidden';
  productKeyInput.name = 'product_key';
  productKeyInput.value = productKey;
  
  form.appendChild(token);
  form.appendChild(productKeyInput);
  document.body.appendChild(form);
  form.submit();
}
</script>
@endsection


