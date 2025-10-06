@extends('layouts.staff')

@section('title', 'Auditory Steady State Response | Kamatage Hearing Aid')

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
      <h4 style="font-weight:bold; margin-bottom:4px;">Auditory Steady State Response</h4>
      <div class="text-muted" style="font-size:.95rem;">Frequency thresholds and measurement grid.</div>
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
            <a href="{{ route('staff.patient.record.details.assr', ['id' => $id]) }}" class="list-group-item d-flex align-items-center active text-decoration-none">
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

    <!-- Right panel: ASSR form -->
    <div class="col-lg-8">
      <div class="card shadow-sm" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <form id="assrForm" method="POST" action="{{ route('staff.patient.service.session.save', ['id' => $id, 'service' => 'assr']) }}" novalidate>
            @csrf

            <!-- Frequency thresholds table -->
            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle assr-table text-center">
                <thead class="table-light">
                  <tr>
                    <th style="width:120px;">EAR</th>
                    <th>500 Hz</th>
                    <th>1000 Hz</th>
                    <th>2000 Hz</th>
                    <th>4000 Hz</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="fw-semibold">RIGHT</td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_r_500"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_r_1000"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_r_2000"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_r_4000"></td>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Left</td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_l_500"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_l_1000"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_l_2000"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_l_4000"></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Measurement grid table -->
            <div class="table-responsive mb-2">
              <table class="table table-bordered align-middle assr-table assr-grid text-center">
                <thead class="table-light">
                  <tr>
                    <th class="assr-col-n">N</th>
                    <th>Electr.</th>
                    <th>LFF, Hz</th>
                    <th>HFF, Hz</th>
                    <th>50 Hz</th>
                    <th>Rejection µV</th>
                    <th>Aver.</th>
                    <th>Reject.</th>
                    <th>Transducer</th>
                    <th>Stimulus</th>
                    <th>Noise</th>
                    <th>Emn</th>
                    <th>RN, nV</th>
                  </tr>
                </thead>
                <tbody>
                  @for($r=0; $r<6; $r++)
                  <tr>
                    <td class="assr-col-n"><input type="text" class="form-control form-control-sm text-center" name="assr_n[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_electr[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_lff[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_hff[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_50hz[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject_uv[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_aver[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_transducer[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_stimulus[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_noise[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_emn[]"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="assr_rn_nv[]"></td>
                  </tr>
                  @endfor
                </tbody>
              </table>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="assr_date_taken" class="form-label fw-semibold">Date Taken</label>
                <input type="date" id="assr_date_taken" name="date_taken" class="form-control">
              </div>
            </div>

            <!-- Notes section -->
            <div class="mb-3">
              <label for="assr_notes" class="form-label fw-semibold">Notes</label>
              <textarea id="assr_notes" name="assr_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations..."></textarea>
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
  /* Table header size and tight spacing */
  .assr-table thead th { font-size: 0.9rem; padding-top: .45rem; padding-bottom: .45rem; }
  .assr-table tbody td { padding-top: .4rem; padding-bottom: .4rem; }
  /* Centered text cells */
  .assr-table th, .assr-table td { text-align: center; vertical-align: middle; }
  /* Smaller font for RIGHT/Left labels in first column */
  .assr-table tbody td.fw-semibold { font-size: 0.85rem; }
  .assr-col-n { width: 100px; min-width: 100px; }

  /* Expanded measurement grid for cleaner spacing */
  .assr-grid thead th { white-space: nowrap; }
  .assr-grid thead th, .assr-grid tbody td { padding-top: .65rem; padding-bottom: .65rem; }
  .assr-grid input.form-control { height: 2.1rem; padding: .275rem .5rem; font-size: .95rem; }
  /* Column min-widths (match header order) */
  .assr-grid th:nth-child(1), .assr-grid td:nth-child(1) { min-width: 90px; width: 90px; }     /* N */
  .assr-grid th:nth-child(2), .assr-grid td:nth-child(2) { min-width: 140px; }                 /* Electr. */
  .assr-grid th:nth-child(3), .assr-grid td:nth-child(3) { min-width: 120px; }                 /* LFF, Hz */
  .assr-grid th:nth-child(4), .assr-grid td:nth-child(4) { min-width: 120px; }                 /* HFF, Hz */
  .assr-grid th:nth-child(5), .assr-grid td:nth-child(5) { min-width: 110px; }                 /* 50 Hz */
  .assr-grid th:nth-child(6), .assr-grid td:nth-child(6) { min-width: 170px; }                 /* Rejection µV */
  .assr-grid th:nth-child(7), .assr-grid td:nth-child(7) { min-width: 120px; }                 /* Aver. */
  .assr-grid th:nth-child(8), .assr-grid td:nth-child(8) { min-width: 120px; }                 /* Reject. */
  .assr-grid th:nth-child(9), .assr-grid td:nth-child(9) { min-width: 150px; }                 /* Transducer */
  .assr-grid th:nth-child(10), .assr-grid td:nth-child(10) { min-width: 150px; }               /* Stimulus */
  .assr-grid th:nth-child(11), .assr-grid td:nth-child(11) { min-width: 130px; }               /* Noise */
  .assr-grid th:nth-child(12), .assr-grid td:nth-child(12) { min-width: 120px; }               /* Emn */
  .assr-grid th:nth-child(13), .assr-grid td:nth-child(13) { min-width: 130px; }               /* RN, nV */
</style>
@endpush