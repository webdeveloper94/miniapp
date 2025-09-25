@extends('layouts.admin')

@section('title', 'To\'lov tafsilotlari')

@section('content')
<div class="wg-box">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h5 class="mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-credit-card"></i> 
        To'lov #{{ $payment->id }} tafsilotlari
      </h5>
      <small class="text-muted">Yaratilgan: {{ $payment->created_at->format('d.m.Y H:i') }}</small>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Orqaga
      </a>
      @if($payment->status === 'pending')
        <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" style="display:inline">
          @csrf @method('PUT')
          <button type="submit" class="btn btn-success">
            <i class="bi bi-check2-circle"></i> Tasdiqlash
          </button>
        </form>
        <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" style="display:inline">
          @csrf @method('PUT')
          <div class="d-flex gap-2">
            <input class="form-control" type="text" name="note" placeholder="Rad etish sababi (ixtiyoriy)" />
            <button type="submit" class="btn btn-danger">
              <i class="bi bi-x-circle"></i> Rad etish
            </button>
          </div>
        </form>
      @endif
    </div>
  </div>

  <div class="row">
    <!-- To'lov ma'lumotlari -->
    <div class="col-md-6 mb-4">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0"><i class="bi bi-info-circle"></i> To'lov ma'lumotlari</h6>
        </div>
        <div class="card-body">
          <table class="table table-borderless">
            <tr>
              <td><strong>ID:</strong></td>
              <td>#{{ $payment->id }}</td>
            </tr>
            <tr>
              <td><strong>Summa:</strong></td>
              <td>{{ number_format($payment->amount, 0, ',', ' ') }} {{ $payment->currency }}</td>
            </tr>
            <tr>
              <td><strong>Holat:</strong></td>
              <td>
                <span class="badge {{ $payment->status==='approved' ? 'bg-success' : ($payment->status==='rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                  @if($payment->status === 'approved') Tasdiqlangan
                  @elseif($payment->status === 'rejected') Rad etilgan
                  @else Kutilmoqda
                  @endif
                </span>
              </td>
            </tr>
            <tr>
              <td><strong>To'lov usuli:</strong></td>
              <td>{{ $payment->payment_method ?? 'Karta' }}</td>
            </tr>
            <tr>
              <td><strong>Transaction ID:</strong></td>
              <td>{{ $payment->transaction_id ?? '-' }}</td>
            </tr>
            <tr>
              <td><strong>Karta raqami:</strong></td>
              <td>{{ $payment->card_number ?? '-' }}</td>
            </tr>
            @if($payment->note)
            <tr>
              <td><strong>Izoh:</strong></td>
              <td>{{ $payment->note }}</td>
            </tr>
            @endif
            <tr>
              <td><strong>Yaratilgan:</strong></td>
              <td>{{ $payment->created_at->format('d.m.Y H:i') }}</td>
            </tr>
            <tr>
              <td><strong>Yangilangan:</strong></td>
              <td>{{ $payment->updated_at->format('d.m.Y H:i') }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>

    <!-- Foydalanuvchi ma'lumotlari -->
    <div class="col-md-6 mb-4">
      <div class="card">
        <div class="card-header">
          <h6 class="mb-0"><i class="bi bi-person"></i> Foydalanuvchi ma'lumotlari</h6>
        </div>
        <div class="card-body">
          <table class="table table-borderless">
            <tr>
              <td><strong>ID:</strong></td>
              <td>#{{ $payment->user->id }}</td>
            </tr>
            <tr>
              <td><strong>Ism:</strong></td>
              <td>{{ $payment->user->name }}</td>
            </tr>
            <tr>
              <td><strong>Email:</strong></td>
              <td>{{ $payment->user->email }}</td>
            </tr>
            <tr>
              <td><strong>Telefon:</strong></td>
              <td>{{ $payment->user->phone ?? '-' }}</td>
            </tr>
            <tr>
              <td><strong>Username:</strong></td>
              <td>{{ $payment->user->username ?? '-' }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Buyurtma ma'lumotlari -->
  @if($payment->order)
  <div class="card mb-4">
    <div class="card-header">
      <h6 class="mb-0"><i class="bi bi-bag"></i> Buyurtma ma'lumotlari</h6>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <table class="table table-borderless">
            <tr>
              <td><strong>Buyurtma ID:</strong></td>
              <td><a href="{{ route('admin.orders.show', $payment->order) }}">#{{ $payment->order->id }}</a></td>
            </tr>
            <tr>
              <td><strong>Holat:</strong></td>
              <td>
                <span class="badge {{ $payment->order->status==='completed' ? 'bg-success' : ($payment->order->status==='cancelled' ? 'bg-danger' : 'bg-warning text-dark') }}">
                  {{ $payment->order->status }}
                </span>
              </td>
            </tr>
            <tr>
              <td><strong>Platforma:</strong></td>
              <td>{{ $payment->order->source_platform }}</td>
            </tr>
            <tr>
              <td><strong>Mahsulot linki:</strong></td>
              <td>
                @if($payment->order->product_url)
                  <a href="{{ $payment->order->product_url }}" target="_blank" class="text-decoration-none">
                    <i class="bi bi-box-arrow-up-right"></i> Ko'rish
                  </a>
                @else - @endif
              </td>
            </tr>
            <tr>
              <td><strong>Tracking raqami:</strong></td>
              <td>{{ $payment->order->tracking_number ?? '-' }}</td>
            </tr>
          </table>
        </div>
        <div class="col-md-6">
          <table class="table table-borderless">
            <tr>
              <td><strong>Ism:</strong></td>
              <td>{{ $payment->order->first_name }} {{ $payment->order->last_name }}</td>
            </tr>
            <tr>
              <td><strong>Telefon:</strong></td>
              <td>{{ $payment->order->phone }}</td>
            </tr>
            <tr>
              <td><strong>Manzil:</strong></td>
              <td>{{ $payment->order->address }}</td>
            </tr>
            <tr>
              <td><strong>Jami summa:</strong></td>
              <td><strong>{{ number_format($payment->order->total_price, 0, ',', ' ') }} UZS</strong></td>
            </tr>
          </table>
        </div>
      </div>

      <!-- Buyurtma mahsulotlari -->
      @if($payment->order->orderItems->count() > 0)
      <div class="mt-4">
        <h6 class="mb-3">Buyurtma mahsulotlari:</h6>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Mahsulot</th>
                <th>Miqdor</th>
                <th>Narx</th>
                <th>Jami</th>
              </tr>
            </thead>
            <tbody>
              @foreach($payment->order->orderItems as $item)
              <tr>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    @if($item->image_url)
                      <img src="{{ $item->image_url }}" alt="{{ $item->title }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                    @endif
                    <div>
                      <div class="fw-semibold">{{ $item->title }}</div>
                      @if($item->product_params)
                        <small class="text-muted">{{ $item->product_params }}</small>
                      @endif
                    </div>
                  </div>
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 0, ',', ' ') }} UZS</td>
                <td><strong>{{ number_format($item->subtotal, 0, ',', ' ') }} UZS</strong></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
      </div>
    </div>
  </div>
  @endif

  <!-- To'lov cheki -->
  @if($payment->receipt_path || $payment->receipt_url)
  <div class="card">
    <div class="card-header">
      <h6 class="mb-0"><i class="bi bi-receipt"></i> To'lov cheki</h6>
    </div>
    <div class="card-body text-center">
      @if($payment->receipt_path)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}" 
             alt="To'lov cheki" 
             class="img-fluid" 
             style="max-height: 500px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <div class="mt-3">
          <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}" 
             target="_blank" 
             class="btn btn-outline-primary">
            <i class="bi bi-box-arrow-up-right"></i> To'liq ko'rish
          </a>
        </div>
      @elseif($payment->receipt_url)
        <img src="{{ $payment->receipt_url }}" 
             alt="To'lov cheki" 
             class="img-fluid" 
             style="max-height: 500px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
        <div class="mt-3">
          <a href="{{ $payment->receipt_url }}" 
             target="_blank" 
             class="btn btn-outline-primary">
            <i class="bi bi-box-arrow-up-right"></i> To'liq ko'rish
          </a>
        </div>
      @endif
    </div>
  </div>
  @else
  <div class="card">
    <div class="card-body text-center text-muted">
      <i class="bi bi-receipt fs-1"></i>
      <p class="mt-2">To'lov cheki yuklanmagan</p>
    </div>
  </div>
  @endif
</div>

@push('styles')
<style>
  .wg-box{padding:20px}
  .card{border: 1px solid #e9ecef; border-radius: 8px;}
  .card-header{background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;}
  .table-borderless td{padding: 0.5rem 0;}
  .table-borderless td:first-child{width: 40%;}
</style>
@endpush
@endsection
