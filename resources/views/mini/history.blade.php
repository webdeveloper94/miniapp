@extends('layouts.mini')

@section('title','Tarix')

@section('content')
<div class="page">
  <h6 class="mb-3">Tarix</h6>
  @foreach($orders as $o)
    <div class="card mini-card p-3 mb-2">
      <div class="d-flex justify-content-between">
        <div>#{{ $o->id }} — {{ $o->status }}</div>
        <div>{{ number_format($o->total_price,0,'',' ') }} so‘m</div>
      </div>
    </div>
  @endforeach
  {{ $orders->links() }}
</div>
@endsection



