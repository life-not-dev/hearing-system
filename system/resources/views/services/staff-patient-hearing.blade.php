@extends('layouts.staff')

@section('title', 'Patient Hearing Aid | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
  @php
    $rec = $record ?? null;
    $fn = $rec['first_name'] ?? '';
    $mn = $rec['middle_name'] ?? '';
    $ln = $rec['last_name'] ?? '';
    $full = trim($fn . ' ' . ($mn ? $mn . ' ' : '') . $ln);
    $parts = preg_split('/\s+/', $full, -1, PREG_SPLIT_NO_EMPTY);
    $initials = '--';
    if ($full) {
        $init = '';
        foreach ($parts as $p) { $init .= strtoupper(substr($p, 0, 1)); }
        $initials = substr($init, 0, 2);
    }
    $dateReg = $rec['date_registered'] ?? null;
    $dateRegPretty = $dateReg ? date('F j, Y', strtotime($dateReg)) : '';
  @endphp

  <div style="margin-top:8px; margin-bottom:18px;" class="d-flex justify-content-between flex-wrap align-items-center">
    <div>
      <h4 style="font-weight:bold; margin-bottom:4px;">Patient Hearing Aid</h4>
    </div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
      <a href="{{ route('staff.patient.record.details', ['id' => $id]) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Back to Patient Info
      </a>
      <button type="button" class="btn btn-dark" >
        <i class="bi bi-printer"></i> Print Patient Record
      </button>
    </div>
  </div>

  <div class="row g-3">
    <!-- Left panel: Patient summary + menu -->
    <div class="col-lg-4">
      <div class="card shadow-sm" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <div class="d-flex align-items-center mb-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#f97316;color:#fff;font-weight:700;" id="pt-initials">{{ $initials }}</div>
            <div class="ms-3">
              <div id="pt-fullname" style="font-weight:700; font-size:1.05rem;">{{ $full ?: 'Not found' }}</div>
              <div id="pt-registered" class="text-muted" style="font-size:.85rem;">{{ $dateRegPretty ? ('Registered on ' . $dateRegPretty) : '' }}</div>
            </div>
          </div>
          <ul class="list-group patient-menu">
            <a href="{{ route('staff.patient.record.details', ['id' => $id]) }}" class="list-group-item d-flex align-items-center text-decoration-none">
              <i class="bi bi-person-lines-fill me-2"></i> Patient Information
              <i class="bi bi-chevron-right ms-auto"></i>
            </a>
            <a href="{{ route('staff.patient.record.details.hearing', ['id' => $id]) }}" class="list-group-item d-flex align-items-center active text-decoration-none">
              <i class="bi bi-ear me-2"></i> Patient Hearing Aid
              <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
            <a href="{{ route('staff.patient.record.details.oae', ['id' => $id]) }}" class="list-group-item d-flex align-items-center text-decoration-none">
              <i class="bi bi-soundwave me-2"></i> Oto Acoustic with Emession
              <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
            <a href="{{ route('staff.patient.record.details.abr', ['id' => $id]) }}" class="list-group-item d-flex align-items-center text-decoration-none">
              <i class="bi bi-activity me-2"></i> Auditory Brain Response
              <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
            <a href="{{ route('staff.patient.record.details.assr', ['id' => $id]) }}" class="list-group-item d-flex align-items-center text-decoration-none">
              <i class="bi bi-broadcast me-2"></i> Auditory Steady State Response
              <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
            <a href="{{ route('staff.patient.record.details.pta', ['id' => $id]) }}" class="list-group-item d-flex align-items-center text-decoration-none">
              <i class="bi bi-sliders me-2"></i> Pure Tone Audiometry
              <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
            <a href="{{ route('staff.patient.record.details.tym', ['id' => $id]) }}" class="list-group-item d-flex align-items-center text-decoration-none">
              <i class="bi bi-diagram-3 me-2"></i> Tympanometry
              <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
            <a href="{{ route('staff.patient.record.details.speech', ['id' => $id]) }}" class="list-group-item d-flex align-items-center text-decoration-none">
              <i class="bi bi-chat-left-quote me-2"></i> Speech Audiometry
              <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
          </ul>
        </div>
      </div>
    </div>

    <!-- Right panel: Patient Hearing Aid form -->
    <div class="col-lg-8">
      <div class="card shadow-sm" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif
          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong class="me-1">Please fix the following:</strong>
              <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          <h5 style="font-weight:700; margin-bottom:0.5rem;">Hearing Aid Information</h5>
          <p class="text-muted mb-4" style="font-size:.9rem;">Enter hearing aid details for this patient.</p>

          <form id="hearingAidForm" method="POST" action="{{ route('staff.patient.hearing.session.save', ['id' => $id]) }}" novalidate>
            @csrf

            <div class="row g-4 mb-2">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Patient name <span class="text-danger">*</span></label>
                <input type="text" class="form-control ha-input" value="{{ $full }}" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Date Issued <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="date_issued" required>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="row g-4 mb-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Hearing Aid Brand <span class="text-danger">*</span></label>
                <select class="form-select ha-input" name="brand" id="ha-brand" required>
                  <option value="">Select Brand</option>
                  <option value="Unitron" selected>Unitron</option>
                </select>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Hearing Aid Model <span class="text-danger">*</span></label>
                <select class="form-select ha-input" name="model" id="ha-model" required>
                  <option value="">Select Model</option>
                  <option value="TMAXX600 Chargable">TMAXX600 Chargable</option>
                  <option value="TMAXX600 Battery">TMAXX600 Battery</option>
                  <option value="StrideP500 Chargable">StrideP500 Chargable</option>
                  <option value="StrideP500 Battery">StrideP500 Battery</option>
                </select>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Ear Side <span class="text-danger">*</span></label>
                <select class="form-select ha-input" name="ear_side" required>
                  <option value="">Select Ear Side</option>
                  <option value="Left">Left</option>
                  <option value="Right">Right</option>
                  <option value="Both">Both</option>
                </select>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="d-flex gap-2">
              <button type="reset" class="btn btn-outline-secondary" id="resetHearingAidBtn"><i class="bi bi-arrow-clockwise"></i> Reset</button>
              <button type="submit" class="btn btn-success" id="saveHearingAidBtn"><i class="bi bi-check-lg"></i> Submit Form</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Records table removed: records are shown only on Patient Details page -->

    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .list-group-item.active{ background:#7a8699; border-color:#7a8699; }
  .patient-menu .list-group-item{ cursor:pointer; display:flex; align-items:center; }
  .patient-menu .list-group-item:not(.active):hover{ background:#f8fafc; }
  .patient-menu .list-group-item i.bi-chevron-right{ color:#94a3b8; }

  /* Compact header fonts */
  .card h4 { font-size: 1.05rem; }
  .form-label { font-size: .95rem; }

  /* Gray input style to match screenshot */
  #hearingAidForm .form-control,
  #hearingAidForm .form-select { border: 1px solid #cbd5e1; box-shadow: none; }
  #hearingAidForm .form-control:hover,
  #hearingAidForm .form-select:hover { border-color: #0d6efd; box-shadow: 0 0 0 .12rem rgba(13,110,253,.15); }
  .ha-input { background: #e6e6e6; border-color: #cbd5e1; }
  .ha-input:focus { background: #e6e6e6; border-color: #0d6efd; box-shadow: 0 0 0 .12rem rgba(13,110,253,.15); }
  .card { border-radius:10px; }

  /* Hearing Aid Results table responsive styles */
  .ha-results-table thead th { background:#f1f5f9; font-weight:600; font-size:.8rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
  .ha-results-table tbody td { font-size:.9rem; }
  .ha-results-table tbody tr:hover { background:#f8fafc; }
  .icon-btn { line-height:1; display:inline-flex; align-items:center; justify-content:center; gap:0; padding:.25rem .45rem; }

  @media (max-width: 768px){
    .ha-results-table thead { display:none; }
    .ha-results-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
    .ha-results-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
    .ha-results-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
  }
</style>
@endpush

@push('scripts')
<script>
(function(){
  const patientId = {!! json_encode($id) !!};
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

  function escapeHtml(str){
    return String(str)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  function formatDatePretty(iso){
    if(!iso) return '';
    const m = String(iso).match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if(!m) return iso;
    const year = parseInt(m[1],10);
    const month = parseInt(m[2],10)-1;
    const day = parseInt(m[3],10);
    const d = new Date(year, month, day);
    const monthName = d.toLocaleString('en-US', { month: 'long' });
    return `${monthName} ${day}, ${year}`;
  }

  function clearFormErrors() {
    const form = document.getElementById('hearingAidForm');
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
  }

  function showFieldError(fieldName, message) {
    const input = document.querySelector(`[name="${fieldName}"]`);
    const feedback = input?.closest('.col-md-4, .col-md-6')?.querySelector('.invalid-feedback');
    if (input) input.classList.add('is-invalid');
    if (feedback) feedback.textContent = message;
  }

  function resetForm() {
    const form = document.getElementById('hearingAidForm');
    form.reset();
    clearFormErrors();
    // Set today's date as default
    const dateField = form.querySelector('input[name="date_issued"]');
    if (dateField) {
      dateField.value = new Date().toISOString().split('T')[0];
    }
    // Keep Unitron as the only brand and reset model list to Unitron models
    const brandSel = document.getElementById('ha-brand');
    const modelSel = document.getElementById('ha-model');
    if (brandSel) brandSel.value = 'Unitron';
    if (modelSel) {
      modelSel.innerHTML = ''+
        '<option value="">Select Model</option>'+
        '<option value="TMAXX600 Chargable">TMAXX600 Chargable</option>'+
        '<option value="TMAXX600 Battery">TMAXX600 Battery</option>'+
        '<option value="StrideP500 Chargable">StrideP500 Chargable</option>'+
        '<option value="StrideP500 Battery">StrideP500 Battery</option>';
      modelSel.disabled = false;
    }
  }

  // Removed AJAX save; using normal form POST to server which redirects to details page

  // Event listeners
  document.addEventListener('DOMContentLoaded', function() {
    // Set today's date as default
  resetForm();

    // Submit handled by the form (standard POST)

    const resetBtn = document.getElementById('resetHearingAidBtn');
    if (resetBtn) {
      resetBtn.addEventListener('click', resetForm);
    }

    // Auto-dismiss any existing flash alerts
    document.querySelectorAll('.alert').forEach(function(el){
      setTimeout(function(){
        try { 
          bootstrap.Alert.getOrCreateInstance(el).close(); 
        } catch(e) { 
          el.remove(); 
        }
      }, 3000);
    });

    // No brand->model dependency; brand is fixed to Unitron and models are static per provided list
  });
})();
</script>
@endpush
