@extends('layouts.staff')

@section('title', 'Pure Tone Audiometry | Kamatage Hearing Aid')

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
      <h4 style="font-weight:bold; margin-bottom:4px;">Pure Tone Audiometry</h4>
      <div class="text-muted" style="font-size:.95rem;">Enter thresholds for AC (masked/unmasked) per ear.</div>
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
            <a href="{{ route('staff.patient.record.details.hearing', ['id' => $id]) }}" class="list-group-item d-flex align-items-center text-decoration-none">
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
            <a href="{{ route('staff.patient.record.details.pta', ['id' => $id]) }}" class="list-group-item d-flex align-items-center active text-decoration-none">
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

    <!-- Right panel: PTA form -->
    <div class="col-lg-8">
      <div class="card shadow-sm" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <form id="ptaForm" method="POST" action="{{ route('staff.patient.service.session.save', ['id' => $id, 'service' => 'pta']) }}" novalidate>
            @csrf

            <!-- PTA grid -->
            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle pta-table text-center">
                <thead class="table-light">
                  <tr>
                    <th style="width:200px;">&nbsp;</th>
                    <th>250<br><span class="text-muted">Hz</span></th>
                    <th>500<br><span class="text-muted">Hz</span></th>
                    <th>1000<br><span class="text-muted">Hz</span></th>
                    <th>1500<br><span class="text-muted">Hz</span></th>
                    <th>2000<br><span class="text-muted">Hz</span></th>
                    <th>3000<br><span class="text-muted">Hz</span></th>
                    <th>4000<br><span class="text-muted">Hz</span></th>
                    <th>6000<br><span class="text-muted">Hz</span></th>
                    <th>8000<br><span class="text-muted">Hz</span></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th>RIGHT AC</th>
                    @for($i=0;$i<9;$i++)
                      <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                    @endfor
                  </tr>
                  <tr>
                    <th>MASKED</th>
                    @for($i=0;$i<9;$i++)
                      <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                    @endfor
                  </tr>
                  <tr>
                    <th>RIGHT BC</th>
                    @for($i=0;$i<9;$i++)
                      <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                    @endfor
                  </tr>
                  <tr>
                    <th>MASKED</th>
                    @for($i=0;$i<9;$i++)
                      <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                    @endfor
                  </tr>
                  <tr>
                    <th>LEFT AC</th>
                    @for($i=0;$i<9;$i++)
                      <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                    @endfor
                  </tr>
                  <tr>
                    <th>MASKED</th>
                    @for($i=0;$i<9;$i++)
                      <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac_masked[]"></td>
                    @endfor
                  </tr>
                  <tr>
                    <th>LEFT BC</th>
                    @for($i=0;$i<9;$i++)
                      <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                    @endfor
                  </tr>
                  <tr>
                    <th>MASKED</th>
                    @for($i=0;$i<9;$i++)
                      <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                    @endfor
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Upload box -->
            <div class="text-center mb-3">
              <button type="button" class="btn btn-outline-secondary pta-upload">
                <i class="bi bi-cloud-upload"></i>
                Upload file puretone test result
              </button>
            </div>

            <!-- Date + Notes -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="pta_date_taken" class="form-label fw-semibold">Date Taken</label>
                <input type="date" id="pta_date_taken" name="date_taken" class="form-control">
              </div>
            </div>
            <!-- Notes -->
            <div class="mb-3">
              <label for="pta_notes" class="form-label fw-semibold">Notes</label>
              <textarea id="pta_notes" name="pta_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations..."></textarea>
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
  /* PTA table styling */
  /* Match OAE's smaller header font and compact style */
  .pta-table thead th { font-size: 0.85rem; padding-top:.4rem; padding-bottom:.4rem; white-space: nowrap; }
  .pta-table th, .pta-table td { text-align: center; vertical-align: middle; }
  /* Wider first column for row labels */
  .pta-table thead th:first-child,
  .pta-table tbody th:first-child { width: 150px; min-width: 150px; }
  /* Smaller font for row labels (RIGHT/LEFT AC/BC, MASKED) */
  .pta-table tbody th { font-size: 0.85rem; font-weight: 600; }
  /* Slightly expand row height */
  .pta-table tbody td { padding-top:.45rem; padding-bottom:.45rem; }
  .pta-table input.form-control { height: 2.1rem; padding: .275rem .5rem; font-size: .95rem; }
  /* Slightly widen frequency columns */
  .pta-table thead th:nth-child(n+2),
  .pta-table tbody td:nth-child(n+2) { min-width: 86px; }
  .pta-upload { width: 70%; padding: .9rem 1.25rem; }
</style>
@endpush
