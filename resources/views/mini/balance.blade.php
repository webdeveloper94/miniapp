@extends('layouts.mini')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('mini.profile') }}" class="btn btn-link p-0 me-3">
                    <i class="bi bi-arrow-left fs-4"></i>
                </a>
                <h4 class="mb-0">Balans</h4>
            </div>

            <!-- Balance Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-5">
                    <div class="mb-3">
                        <i class="bi bi-wallet2 text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h2 class="text-primary mb-2">{{ number_format($balance, 0, ',', ' ') }} so'm</h2>
                    <p class="text-muted mb-0">Joriy balans</p>
                </div>
            </div>

            <!-- Add Balance Form -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="bi bi-plus-circle text-success me-2"></i>
                        Balans to'ldirish
                    </h5>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('mini.balance.add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label">Summa (so'm)</label>
                            <input type="number" class="form-control form-control-lg" id="amount" name="amount" 
                                   placeholder="100000" min="1000" max="1000000" required>
                            <div class="form-text">Minimum: 1,000 so'm, Maksimum: 1,000,000 so'm</div>
                        </div>
                        
                        
                        <div class="mb-3">
                            <label for="receipt_image" class="form-label">To'lov cheki</label>
                            <input type="file" class="form-control" id="receipt_image" name="receipt_image" 
                                   accept="image/*" required>
                            <div class="form-text">To'lov chekini rasm ko'rinishida yuklang</div>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-plus-circle me-2"></i>
                            Balans to'ldirish so'rovi yuborish
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Amount Buttons -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">Tezkor summa</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100" onclick="setAmount(50000)">50,000</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100" onclick="setAmount(100000)">100,000</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100" onclick="setAmount(200000)">200,000</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100" onclick="setAmount(500000)">500,000</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Card Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-credit-card text-primary me-2"></i>
                        Admin karta raqami
                    </h6>
                    <div class="bg-light p-3 rounded">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong>{{ \App\Models\AdminSetting::where('key', 'admin_card_number')->first()->value ?? 'Karta raqami kiritilmagan' }}</strong>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" onclick="copyCardNumber()">
                                <i class="bi bi-copy me-1"></i> Nusxa olish
                            </button>
                        </div>
                        <small class="text-muted">Bu karta raqamiga to'lov qiling</small>
                    </div>
                </div>
            </div>

            <!-- Balance Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-info-circle text-info me-2"></i>
                        Balans haqida
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Balansingizdan buyurtmalar uchun to'lov qilishingiz mumkin
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Buyurtma bekor qilinganda pul balansingizga qaytariladi
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Balansingiz xavfsiz saqlanadi
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setAmount(amount) {
    document.getElementById('amount').value = amount;
}

function copyCardNumber() {
    const cardNumber = '{{ \App\Models\AdminSetting::where("key", "admin_card_number")->first()->value ?? "" }}';
    if (cardNumber) {
        navigator.clipboard.writeText(cardNumber).then(function() {
            alert('Karta raqami nusxa olindi!');
        });
    }
}
</script>
@endsection
