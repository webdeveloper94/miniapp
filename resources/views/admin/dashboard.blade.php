@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="tf-section-4 mb-30">
  <div class="row g-3">
    <div class="col-12 col-md-4">
      <div class="wg-chart-default">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap14">
            <div class="image type-white"><i class="icon-file"></i></div>
            <div>
              <div class="body-text mb-2">Jami buyurtmalar</div>
              <h4>{{ $stats['orders_count'] ?? 0 }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="wg-chart-default">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap14">
            <div class="image type-white"><i class="icon-dollar-sign"></i></div>
            <div>
              <div class="body-text mb-2">Kutilayotgan to'lovlar</div>
              <h4>{{ $stats['payments_pending'] ?? 0 }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <div class="wg-chart-default">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap14">
            <div class="image type-white"><i class="icon-clock"></i></div>
            <div>
              <div class="body-text mb-2">Kutilayotgan buyurtmalar</div>
              <h4>{{ $stats['orders_pending'] ?? 0 }}</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-6">
    <div class="wg-box">
      <div class="flex items-center justify-between"><h5>Recent Order</h5></div>
      <div id="line-chart-orders" style="height:260px"></div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="wg-box">
      <div class="flex items-center justify-between"><h5>Top Products</h5></div>
      <div class="wg-table table-top-product">
        <ul class="table-title flex gap10 mb-14">
          <li><div class="body-title">Mahsulot</div></li>
          <li><div class="body-title">Soni</div></li>
        </ul>
        <ul class="flex flex-column gap18">
          @forelse(($recentOrders ?? [])->take(5) as $o)
            <li class="product-item gap14">
              <div class="flex items-center justify-between flex-grow gap10">
                <div class="name"><span class="body-text">Order #{{ $o->id }}</span></div>
                <div class="body-text">{{ optional($o->items->first())->quantity ?? 1 }}</div>
              </div>
            </li>
          @empty
            <li class="product-item"><div class="body-text">Hozircha ma'lumot yo'q</div></li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
  <div class="col-12 col-lg-6">
    <div class="wg-box">
      <div class="flex items-center justify-between"><h5>So'nggi to'lovlar</h5></div>
      <div class="wg-table table-orders">
        <ul class="table-title flex gap10 mb-14">
          <li><div class="body-title">ID</div></li>
          <li><div class="body-title">Foydalanuvchi</div></li>
          <li><div class="body-title">Summa</div></li>
          <li><div class="body-title">Status</div></li>
        </ul>
        <ul class="flex flex-column gap18">
          @forelse($recentPayments ?? [] as $p)
            <li class="product-item gap14">
              <div class="body-text">#{{ $p->id }}</div>
              <div class="flex items-center justify-between flex-grow gap10">
                <div class="name"><span class="body-text">{{ $p->user->name }}</span></div>
                <div class="body-text">{{ $p->amount }} {{ $p->currency }}</div>
                <div class="body-text"><span class="badge bg-{{ $p->status === 'approved' ? 'success' : ($p->status === 'rejected' ? 'danger' : 'warning') }}">{{ $p->status }}</span></div>
              </div>
            </li>
          @empty
            <li class="product-item"><div class="body-text">Hozircha ma'lumot yo'q</div></li>
          @endforelse
        </ul>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function(){
    if (!window.ApexCharts) return;
    var options = {
      chart: { type: 'line', height: 260, toolbar: { show:false } },
      series: [{ name: 'Orders', data: @json($chartSeries ?? []) }],
      xaxis: { categories: @json($chartLabels ?? []) },
      stroke: { curve: 'smooth', width: 3 },
      colors: ['#2377FC']
    };
    var el = document.querySelector('#line-chart-orders');
    if (el) new ApexCharts(el, options).render();
  })();
</script>
@endpush
@endsection


