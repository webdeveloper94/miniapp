@extends('layouts.mini')

@section('title','Profil')

@section('content')
<div class="page">
  <div class="card mini-card p-3 mb-3 d-flex align-items-center">
    <div class="avatar me-3"><i class="bi bi-person"></i></div>
    <div>
      <div class="fw-semibold">Foydalanuvchi</div>
      <small class="text-secondary">ID: 000000</small>
    </div>
  </div>

  <div class="card mini-card p-3 mb-3">
    @if (session('status'))
      <div class="alert alert-info py-2 px-3">{{ session('status') }}</div>
    @endif
    <h6 class="mb-2">Til</h6>
    <form method="POST" action="{{ route('mini.profile.language') }}" class="d-flex gap-2">
      @csrf
      <select class="form-select mini-input" name="language" style="max-width:200px">
        <option value="uz">O‘zbekcha</option>
        <option value="ru">Русский</option>
        <option value="en">English</option>
      </select>
      <button class="btn btn-mini" type="submit"><i class="bi bi-save"></i> Saqlash</button>
    </form>
  </div>

  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-2">Login va parol</h6>
    <form method="POST" action="{{ route('mini.profile.credentials') }}" class="row g-2">
      @csrf
      <div class="col-12">
        <label class="form-label">Login (username)</label>
        <input class="form-control mini-input" type="text" name="username" value="{{ old('username', auth()->user()->username ?? '') }}" required>
      </div>
      <div class="col-12">
        <label class="form-label">Yangi parol (ixtiyoriy)</label>
        <input class="form-control mini-input" type="password" name="password" placeholder="******">
      </div>
      <div class="col-12">
        <label class="form-label">Parol tasdiqi</label>
        <input class="form-control mini-input" type="password" name="password_confirmation" placeholder="******">
      </div>
      <div class="col-12">
        <button class="btn btn-mini w-100" type="submit"><i class="bi bi-check2"></i> Yangilash</button>
      </div>
    </form>
  </div>

  <div class="card mini-card p-3">
    <h6 class="mb-2">So‘nggi to‘lovlar</h6>
    @forelse($payments as $p)
      <div class="d-flex justify-content-between py-1">
        <div>#{{ $p->id }} — {{ $p->status }}</div>
        <div>{{ number_format($p->amount,0,'',' ') }} {{ $p->currency }}</div>
      </div>
    @empty
      <div class="text-secondary">Ma'lumot yo‘q</div>
    @endforelse
  </div>
</div>
@endsection


