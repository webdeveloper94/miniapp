@extends('layouts.mini')

@section('title','Buyurtmalar')

@section('content')
<div class="page">
  <h6 class="mb-3">Buyurtmalar</h6>
  @forelse($orders as $o)
    <div class="card mini-card p-3 mb-2">
      <div class="d-flex justify-content-between">
        <div>
          <div class="fw-semibold">Buyurtma #{{ $o->id }}</div>
          <small class="text-secondary">Status: {{ $o->status }}</small>
        </div>
        <div class="text-end">
          <div class="fw-semibold">{{ number_format($o->total_price,0,'',' ') }} so‘m</div>
          <a class="text-decoration-none" href="#">Tafsilot</a>
        </div>
      </div>
    </div>
  @empty
    <div class="text-center text-secondary">Hozircha buyurtmalar yo‘q</div>
  @endforelse
</div>
@endsection



