@extends('layouts.mini')

@section('title','Profil')

@section('content')
<!-- <div class="page">
  <div class="card mini-card p-3 mb-3 d-flex align-items-center">
    <div class="avatar me-3"><i class="bi bi-person"></i></div>
    <div>
      <div class="fw-semibold">{{ session('telegram_user.first_name') }} {{ session('telegram_user.last_name') }}</div>
      <small class="text-secondary">@{{ session('telegram_user.username') }} • ID: {{ session('telegram_user.id') }}</small>
    </div>
  </div> -->

  <!-- Balance Card -->
  <div class="card mini-card p-3 mb-3">
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h6 class="mb-1">Balans</h6>
        <div class="text-primary fw-bold fs-5">
          {{ number_format(optional(\App\Models\User::find(session('telegram_user.id')))->balance ?? 0, 0, ',', ' ') }} so'm
        </div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('mini.payments') }}" class="btn btn-outline-primary btn-sm">
          <i class="bi bi-clock-history me-1"></i> Tarix
        </a>
        <a href="{{ route('mini.balance') }}" class="btn btn-primary btn-sm">
          <i class="bi bi-wallet2 me-1"></i> To'ldirish
        </a>
      </div>
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
        <option value="uz" {{ session('telegram_user.language_code') == 'uz' ? 'selected' : '' }}>O'zbekcha</option>
        <option value="ru" {{ session('telegram_user.language_code') == 'ru' ? 'selected' : '' }}>Русский</option>
        <option value="en" {{ session('telegram_user.language_code') == 'en' ? 'selected' : '' }}>English</option>
      </select>
      <button class="btn btn-mini" type="submit"><i class="bi bi-save"></i> Saqlash</button>
    </form>
  </div>

  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-2">Telegram username</h6>
    <form method="POST" action="{{ route('mini.profile.credentials') }}" class="row g-2">
      @csrf
      <div class="col-12">
        <label class="form-label">Username</label>
        <input class="form-control mini-input" type="text" name="username" value="{{ old('username', session('telegram_user.username')) }}" required>
      </div>
      <div class="col-12">
        <button class="btn btn-mini w-100" type="submit"><i class="bi bi-check2"></i> Yangilash</button>
      </div>
    </form>
  </div>

  <div class="card mini-card p-3 mb-3">
    <h6 class="mb-2">Parolni o'zgartirish</h6>
    <form method="POST" action="{{ route('mini.auth.changePassword') }}" class="row g-2">
      @csrf
      <div class="col-12">
        <label class="form-label">Eski parol</label>
        <input class="form-control mini-input" type="password" name="old_password" placeholder="Eski parol">
        <div class="form-text">Agar avval parol qo'ygan bo'lsangiz, eski parolni kiriting</div>
      </div>
      <div class="col-12">
        <label class="form-label">Yangi parol</label>
        <input class="form-control mini-input" type="password" name="new_password" required minlength="4" maxlength="50" placeholder="Yangi parol">
      </div>
      <div class="col-12">
        <label class="form-label">Yangi parol (tasdiqlash)</label>
        <input class="form-control mini-input" type="password" name="new_password_confirmation" required minlength="4" maxlength="50" placeholder="Yangi parolni tasdiqlang">
      </div>
      <div class="col-12">
        <button class="btn btn-mini w-100" type="submit"><i class="bi bi-shield-lock"></i> Parolni yangilash</button>
      </div>
    </form>
  </div>

  <div class="card mini-card p-3">
    <br>
</div>
@endsection


