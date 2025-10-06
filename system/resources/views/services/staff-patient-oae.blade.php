@extends('layouts.staff')

@section('title', 'OAE | Kamatage Hearing Aid')

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
      <h4 style="font-weight:bold; margin-bottom:4px;">Oto Acoustic with Emession</h4>
      <div class="text-muted" style="font-size:.95rem;">Record OAE results for each ear.</div>
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
            <a href="{{ route('staff.patient.record.details.hearing', ['id' => $id]) }}" class="list-group-item d-flex align-items-center text-decoration-none">
              <i class="bi bi-ear me-2"></i> Patient Hearing Aid
              <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
            <a href="{{ route('staff.patient.record.details.oae', ['id' => $id]) }}" class="list-group-item d-flex align-items-center active text-decoration-none">
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

    <div class="col-lg-8">
      <div class="card shadow-sm" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <form id="oaeForm" method="POST" action="{{ route('staff.patient.service.session.save', ['id' => $id, 'service' => 'oae']) }}" novalidate>
            @csrf
            <div class="table-responsive">
              <table class="table table-bordered align-middle oae-table">
                <thead class="table-light">
                  <tr>
                    <th style="width:140px;">Ear</th>
                    <th class="text-center" style="width:120px;">Pass (✓)</th>
                    <th class="text-center" style="width:120px;">Refer (✓)</th>
                    <th class="text-center" style="width:140px;">Not tested (✓)</th>
                    <th>Remarks</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Left Ear</td>
                    <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_left_pass"></td>
                    <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_left_refer"></td>
                    <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_left_not_tested"></td>
                    <td><input type="text" class="form-control" name="oae_left_remarks"></td>
                  </tr>
                  <tr>
                    <td>Right Ear</td>
                    <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_right_pass"></td>
                    <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_right_refer"></td>
                    <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_right_not_tested"></td>
                    <td><input type="text" class="form-control" name="oae_right_remarks"></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="oae_date_taken" class="form-label fw-semibold">Date Taken</label>
                <input type="date" id="oae_date_taken" name="date_taken" class="form-control">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Notes</label>
              <textarea id="oae_notes" name="oae_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations..."></textarea>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i>Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .list-group-item.active{ background:#7a8699; border-color:#7a8699; }
  .container-main { padding-top: 45px; }
  .patient-menu .list-group-item{ cursor:pointer; display:flex; align-items:center; }
  .patient-menu .list-group-item:not(.active):hover{ background:#f8fafc; }
  .patient-menu .list-group-item i.bi-chevron-right{ color:#94a3b8; }
  /* Smaller header font for OAE table (match ABR) */
  .oae-table thead th { font-size: 0.85rem; padding-top: .4rem; padding-bottom: .4rem; }
  /* Center the 'Ear' column */
  .oae-table thead th:first-child,
  .oae-table tbody td:first-child { text-align: center; vertical-align: middle; }
  /* Tighter row spacing for OAE table */
  //.oae-table tbody td { padding-top: .35rem; padding-bottom: .35rem; }
  //.oae-table .form-control { padding: .25rem .5rem; height: 32px; }
  //.oae-table .form-check-input { width: 1rem; height: 1rem; margin: 0; }
</style>
@endpush