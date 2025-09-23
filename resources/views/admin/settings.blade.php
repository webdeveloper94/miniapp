@extends('layouts.admin')

@section('title', 'Sozlash')

@section('content')
<div class="wg-box">
  <h5 class="mb-3 d-flex align-items-center gap-2"><i class="bi bi-gear"></i> Sozlash</h5>
  <form method="POST" action="{{ route('admin.settings.update') }}" class="row g-3">
    @csrf
    <div class="col-12 col-md-6">
      <label class="form-label">Til</label>
      <select class="form-select" name="language" required>
        @php($languages = ['uz' => 'O‘zbekcha','ru' => 'Русский','en' => 'English'])
        @foreach($languages as $key => $label)
          <option value="{{ $key }}" @selected(($setting->language ?? 'uz') === $key)>{{ $label }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">Xizmat haqi (%)</label>
      <div class="input-group">
        <input class="form-control" type="number" step="0.01" min="0" max="100" name="service_fee_percent" value="{{ old('service_fee_percent', $setting->service_fee_percent ?? 0) }}" required>
        <span class="input-group-text">%</span>
      </div>
      <div class="form-text">Masalan, 5% bo‘lsa, 1 000 000 so‘mga 50 000 so‘m xizmat haqi qo‘shiladi.</div>
    </div>
    <div class="col-12">
      <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Saqlash</button>
    </div>
  </form>
</div>
@endsection


