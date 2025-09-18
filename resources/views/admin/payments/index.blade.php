@extends('layouts.admin')

@section('title', 'To\'lovlar')

@section('content')
<div class="card p-3">
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0 d-flex align-items-center gap-2"><i class="bi bi-credit-card"></i> To'lovlar</h5>
  <form class="d-flex gap-2" method="GET">
    <input class="form-control form-control-sm" type="text" name="q" value="{{ request('q') }}" placeholder="ID, summa yoki foydalanuvchi...">
    <select class="form-select form-select-sm" name="status">
      <option value="">Barcha holatlar</option>
      @foreach(['pending'=>'Kutilmoqda','approved'=>'Tasdiqlangan','rejected'=>'Rad etilgan'] as $key=>$label)
        <option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-search"></i></button>
    <a class="btn btn-sm btn-outline-light" href="{{ route('admin.payments.index') }}"><i class="bi bi-x"></i></a>
  </form>
</div>
<div class="table-responsive">
<table class="table table-sm align-middle">
    <thead>
        <tr>
            <th>ID</th>
            <th>Foydalanuvchi</th>
            <th>Buyurtma</th>
            <th>Summasi</th>
            <th>Status</th>
            <th>Chek</th>
            <th>Harakat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payments as $payment)
            <tr>
                <td>#{{ $payment->id }}</td>
                <td>{{ $payment->user->name }}</td>
                <td>{{ optional($payment->order)->id ? ('#'.$payment->order->id) : '-' }}</td>
                <td>{{ $payment->amount }} {{ $payment->currency }}</td>
                <td>{{ $payment->status }}</td>
                <td>
                    @if($payment->receipt_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::url($payment->receipt_path) }}" target="_blank">Ko'rish</a>
                    @else - @endif
                </td>
                <td>
                    @if($payment->status === 'pending')
                        <form action="{{ route('admin.payments.approve', $payment) }}" method="POST" style="display:inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check2-circle"></i></button>
                        </form>
                        <form action="{{ route('admin.payments.reject', $payment) }}" method="POST" style="display:inline" class="d-inline-flex gap-1 align-items-center">
                            @csrf
                            @method('PUT')
                            <input class="form-control form-control-sm" type="text" name="note" placeholder="Sabab (ixtiyoriy)" />
                            <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-x-circle"></i></button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $payments->links() }}
</div>
</div>
@endsection


