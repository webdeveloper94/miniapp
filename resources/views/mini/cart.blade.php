@extends('layouts.mini')

@section('title','Savatcha')

@section('content')
<div class="page">
  <h6 class="mb-3">Savatcha</h6>
  @if(empty($items))
    <div class="text-center text-secondary">Savatcha bo‘sh</div>
  @else
    <div class="card mini-card p-3 mb-2">Item</div>
  @endif
  <button class="btn btn-mini w-100 mt-3">Buyurtma berish</button>
  <small class="d-block mt-2 text-secondary">To‘lov uchun sizga karta raqami ko‘rsatiladi va chek yuklaysiz.</small>
 </div>
@endsection


