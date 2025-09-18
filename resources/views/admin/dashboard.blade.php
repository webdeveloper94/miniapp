@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row g-3">
  <div class="col-12 col-md-4">
    <div class="card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="text-uppercase mb-1">Jami buyurtmalar</h6>
          <h3 class="mb-0">{{ $stats['orders_count'] ?? 0 }}</h3>
        </div>
        <i class="bi bi-bag fs-1 text-info"></i>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="text-uppercase mb-1">Kutilayotgan to'lovlar</h6>
          <h3 class="mb-0">{{ $stats['payments_pending'] ?? 0 }}</h3>
        </div>
        <i class="bi bi-cash-coin fs-1 text-warning"></i>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-4">
    <div class="card p-3">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h6 class="text-uppercase mb-1">Kutilayotgan buyurtmalar</h6>
          <h3 class="mb-0">{{ $stats['orders_pending'] ?? 0 }}</h3>
        </div>
        <i class="bi bi-hourglass-split fs-1 text-primary"></i>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card p-3">
      <h5 class="mb-3 d-flex align-items-center gap-2"><i class="bi bi-bag"></i> So'nggi buyurtmalar</h5>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead><tr><th>ID</th><th>Foydalanuvchi</th><th>Status</th><th></th></tr></thead>
          <tbody>
            @forelse($recentOrders ?? [] as $o)
              <tr>
                <td>#{{ $o->id }}</td>
                <td>{{ $o->user->name }}</td>
                <td><span class="badge bg-info">{{ $o->status }}</span></td>
                <td class="text-end"><a class="btn btn-sm btn-outline-light" href="{{ route('admin.orders.show', $o) }}"><i class="bi bi-eye"></i></a></td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center">Hozircha ma'lumot yo'q</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card p-3">
      <h5 class="mb-3 d-flex align-items-center gap-2"><i class="bi bi-credit-card"></i> So'nggi to'lovlar</h5>
      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead><tr><th>ID</th><th>Foydalanuvchi</th><th>Summa</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($recentPayments ?? [] as $p)
              <tr>
                <td>#{{ $p->id }}</td>
                <td>{{ $p->user->name }}</td>
                <td>{{ $p->amount }} {{ $p->currency }}</td>
                <td><span class="badge bg-{{ $p->status === 'approved' ? 'success' : ($p->status === 'rejected' ? 'danger' : 'warning') }}">{{ $p->status }}</span></td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center">Hozircha ma'lumot yo'q</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection


