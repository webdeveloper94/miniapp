@extends('layouts.admin')

@section('title', 'Sozlash')

@section('content')
<div class="wg-box">
  <h5 class="mb-4 d-flex align-items-center gap-2"><i class="bi bi-gear"></i> Sozlash</h5>
  
  <!-- Asosiy sozlamalar -->
  <div class="card mb-4">
    <div class="card-header">
      <h6 class="mb-0"><i class="bi bi-globe"></i> Asosiy sozlamalar</h6>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.settings.update') }}" class="row g-3">
        @csrf
        <div class="col-12 col-md-6">
          <label class="form-label">Til</label>
          <select class="form-select" name="language" required>
            @php($languages = ['uz' => 'O\'zbekcha','ru' => 'Русский','en' => 'English'])
            @foreach($languages as $key => $label)
              <option value="{{ $key }}" @selected(($setting->language ?? 'uz') === $key)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Valyuta kursi (1 CNY → UZS)</label>
          <div class="input-group">
            <input class="form-control" type="number" step="0.01" min="0" name="cny_to_uzs" value="{{ old('cny_to_uzs', $setting->cny_to_uzs ?? '') }}" placeholder="Masalan: 1700.00">
            <span class="input-group-text">UZS</span>
          </div>
          <div class="form-text">Mahsulot narxlari ushbu kurs bo‘yicha so‘mda ko‘rsatiladi.</div>
        </div>
        <div class="col-12">
          <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Saqlash</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Xizmat haqi boshqaruvi -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="bi bi-percent"></i> Xizmat haqi qoidalari</h6>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addServiceFeeModal">
        <i class="bi bi-plus me-1"></i> Qo'shish
      </button>
    </div>
    <div class="card-body">
      @if($serviceFees->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Summa oralig'i</th>
                <th>Xizmat haqi (%)</th>
                <th>Holat</th>
                <th>Harakat</th>
              </tr>
            </thead>
            <tbody>
              @foreach($serviceFees as $index => $fee)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                  <strong>{{ number_format($fee->min_amount, 0, ',', ' ') }} - {{ number_format($fee->max_amount, 0, ',', ' ') }} so'm</strong>
                </td>
                <td>
                  <span class="badge bg-primary">{{ $fee->fee_percentage }}%</span>
                </td>
                <td>
                  <span class="badge {{ $fee->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $fee->is_active ? 'Faol' : 'Nofaol' }}
                  </span>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editServiceFeeModal"
                            data-fee-id="{{ $fee->id }}"
                            data-min-amount="{{ $fee->min_amount }}"
                            data-max-amount="{{ $fee->max_amount }}"
                            data-fee-percentage="{{ $fee->fee_percentage }}"
                            data-is-active="{{ $fee->is_active ? '1' : '0' }}"
                            title="Tahrirlash">
                      <i class="bi bi-pencil me-1"></i>Tahrirlash
                    </button>
                    <form action="{{ route('admin.settings.service-fees.destroy', $fee) }}" 
                          method="POST" 
                          style="display:inline"
                          onsubmit="return confirm('Bu xizmat haqi qoidasini o\'chirishni xohlaysizmi?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-danger btn-sm" title="O'chirish">
                        <i class="bi bi-trash me-1"></i>O'chirish
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center text-muted py-4">
          <i class="bi bi-percent fs-1"></i>
          <p class="mt-2">Hozircha xizmat haqi qoidalari mavjud emas</p>
          <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addServiceFeeModal">
            <i class="bi bi-plus me-2"></i> Birinchi qoidani qo'shing
          </button>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Xizmat haqi qo'shish modal -->
<div class="modal fade" id="addServiceFeeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('admin.settings.service-fees.store') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-plus"></i> Yangi xizmat haqi qoidasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Boshlang'ich summa <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" class="form-control" name="min_amount" step="0.01" min="0" required>
                <span class="input-group-text">so'm</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Yakuniy summa <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" class="form-control" name="max_amount" step="0.01" min="0" required>
                <span class="input-group-text">so'm</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Xizmat haqi foizi <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" class="form-control" name="fee_percentage" step="0.01" min="0" max="100" required>
                <span class="input-group-text">%</span>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                <label class="form-check-label">Faol</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
          <button type="submit" class="btn btn-primary">Qo'shish</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Xizmat haqi tahrirlash modal -->
<div class="modal fade" id="editServiceFeeModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" id="editServiceFeeForm">
        @csrf @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title"><i class="bi bi-pencil"></i> Xizmat haqi qoidasini tahrirlash</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Boshlang'ich summa <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" class="form-control" name="min_amount" step="0.01" min="0" required>
                <span class="input-group-text">so'm</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Yakuniy summa <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" class="form-control" name="max_amount" step="0.01" min="0" required>
                <span class="input-group-text">so'm</span>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Xizmat haqi foizi <span class="text-danger">*</span></label>
              <div class="input-group">
                <input type="number" class="form-control" name="fee_percentage" step="0.01" min="0" max="100" required>
                <span class="input-group-text">%</span>
              </div>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1">
                <label class="form-check-label">Faol</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
          <button type="submit" class="btn btn-primary">Saqlash</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit modal event listener
    document.getElementById('editServiceFeeModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const feeId = button.getAttribute('data-fee-id');
        const minAmount = button.getAttribute('data-min-amount');
        const maxAmount = button.getAttribute('data-max-amount');
        const feePercentage = button.getAttribute('data-fee-percentage');
        const isActive = button.getAttribute('data-is-active');
        
        const modal = this;
        const form = modal.querySelector('#editServiceFeeForm');
        form.action = `/admin/settings/service-fees/${feeId}`;
        
        form.querySelector('input[name="min_amount"]').value = minAmount;
        form.querySelector('input[name="max_amount"]').value = maxAmount;
        form.querySelector('input[name="fee_percentage"]').value = feePercentage;
        form.querySelector('input[name="is_active"]').checked = isActive === '1';
    });
});
</script>
@endpush

@push('styles')
<style>
  .wg-box{padding:20px}
  .card{border: 1px solid #e9ecef; border-radius: 8px; background: #fff;}
  .card-header{background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; color: #333;}
  .card-body{color: #333;}
  .table th{font-weight: 600; border-top: none; color: #333;}
  .table td{color: #333;}
  .btn-group-sm .btn{padding: 0.25rem 0.5rem;}
  
  /* Harakat tugmalari uchun yangi stillar */
  .btn-sm{padding: 0.5rem 1rem; font-size: 0.875rem; border-radius: 0.375rem;}
  .btn-primary{background-color: #0d6efd; border-color: #0d6efd; color: #fff; font-weight: 500;}
  .btn-primary:hover{background-color: #0b5ed7; border-color: #0a58ca;}
  .btn-danger{background-color: #dc3545; border-color: #dc3545; color: #fff; font-weight: 500;}
  .btn-danger:hover{background-color: #bb2d3b; border-color: #b02a37;}
  .btn-success{background-color: #198754; border-color: #198754; color: #fff; font-weight: 500;}
  .btn-success:hover{background-color: #157347; border-color: #146c43;}
  
  /* Gap uchun */
  .d-flex.gap-2 > * + *{margin-left: 0.5rem;}
  
  /* Modal oynalar uchun ranglar */
  .modal-content{background: #fff; color: #333;}
  .modal-header{background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; color: #333;}
  .modal-title{color: #333; font-weight: 600;}
  .modal-body{color: #333;}
  .modal-footer{background-color: #f8f9fa; border-top: 1px solid #e9ecef;}
  
  /* Form elementlari */
  .form-label{color: #333; font-weight: 500;}
  .form-control{color: #333; background-color: #fff; border-color: #ced4da;}
  .form-control:focus{color: #333; background-color: #fff; border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);}
  .form-select{color: #333; background-color: #fff; border-color: #ced4da;}
  .form-select:focus{color: #333; background-color: #fff; border-color: #86b7fe; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);}
  
  /* Badge va button ranglar */
  .badge{color: #fff;}
  .btn-primary{background-color: #0d6efd; border-color: #0d6efd; color: #fff;}
  .btn-success{background-color: #198754; border-color: #198754; color: #fff;}
  .btn-danger{background-color: #dc3545; border-color: #dc3545; color: #fff;}
  .btn-secondary{background-color: #6c757d; border-color: #6c757d; color: #fff;}
  .btn-outline-primary{color: #0d6efd; border-color: #0d6efd;}
  .btn-outline-danger{color: #dc3545; border-color: #dc3545;}
  
  /* Text ranglar */
  .text-muted{color: #6c757d !important;}
  .text-danger{color: #dc3545 !important;}
  
  /* Dark mode uchun */
  body.dark .card{background: #2d3748; border-color: #4a5568; color: #e2e8f0;}
  body.dark .card-header{background-color: #4a5568; border-color: #4a5568; color: #e2e8f0;}
  body.dark .card-body{color: #e2e8f0;}
  body.dark .table th{color: #e2e8f0;}
  body.dark .table td{color: #e2e8f0;}
  body.dark .modal-content{background: #2d3748; color: #e2e8f0;}
  body.dark .modal-header{background-color: #4a5568; border-color: #4a5568; color: #e2e8f0;}
  body.dark .modal-body{color: #e2e8f0;}
  body.dark .modal-footer{background-color: #4a5568; border-color: #4a5568;}
  body.dark .form-label{color: #e2e8f0;}
  body.dark .form-control{color: #e2e8f0; background-color: #4a5568; border-color: #4a5568;}
  body.dark .form-control:focus{color: #e2e8f0; background-color: #4a5568; border-color: #86b7fe;}
  body.dark .form-select{color: #e2e8f0; background-color: #4a5568; border-color: #4a5568;}
  body.dark .form-select:focus{color: #e2e8f0; background-color: #4a5568; border-color: #86b7fe;}
  body.dark .text-muted{color: #a0aec0 !important;}
</style>
@endpush
@endsection


