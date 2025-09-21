@extends('layouts.mini')

@section('title','Bosh sahifa')

@section('content')
<div class="page">
  <div class="d-flex align-items-center gap-2 mb-3">
    <div class="avatar"><i class="bi bi-person"></i></div>
    <div>
      <div class="fw-semibold">Xush kelibsiz!</div>
      <small class="text-secondary">Mahsulot linkini kiriting va narx taklifi oling</small>
    </div>
  </div>

  <div class="card mini-card p-3 mb-3">
    <form method="POST" action="{{ route('mini.find') }}">
      @csrf
      <label class="form-label">Mahsulot linkini kiriting:</label>
      <div class="input-group">
        <input class="form-control mini-input" name="link" type="url" placeholder="https://item.taobao.com/... yoki https://detail.1688.com/offer/..." required />
        <button class="btn btn-mini" type="submit"><i class="bi bi-send"></i> Yuborish</button>
      </div>
      <div class="alert alert-info mt-2 mb-0">
        <i class="bi bi-info-circle me-1"></i>
        <strong>Qo'llab-quvvatlanadigan saytlar:</strong> 1688.com va Taobao.com linklari
      </div>
    </form>
    @if ($errors->any())
      <div class="alert alert-danger mt-2 mb-0">
        @foreach ($errors->all() as $e)
          <div>{{ $e }}</div>
        @endforeach
      </div>
    @endif
  </div>

  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-2">Qanday ishlaydi?</h6>
    <ul class="mb-0">
      <li>1688.com yoki Taobao.com linkini yuboring</li>
      <li>Biz narx va tafsilotlarni tekshiramiz</li>
      <li>Buyurtma berish va to'lov</li>
      <li>Yetkazib berish</li>
    </ul>
  </div>

  <div class="row g-2">
    <div class="col-6">
      <div class="card mini-card p-3 text-center">
        <div class="fs-3"><i class="bi bi-qr-code"></i></div>
        <div>QR Skaner</div>
      </div>
    </div>
    <div class="col-6">
      <div class="card mini-card p-3 text-center">
        <div class="fs-3"><i class="bi bi-clock-history"></i></div>
        <div>Tarix</div>
      </div>
    </div>
  </div>
</div>
@endsection


