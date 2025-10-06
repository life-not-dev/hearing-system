@extends('layouts.staff')

@section('title', 'Speech Audiometry | Kamatage Hearing Aid')

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
      <h4 style="font-weight:bold; margin-bottom:4px;">Speech Audiometry</h4>
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
            <a href="{{ route('staff.patient.record.details.speech', ['id' => $id]) }}" class="list-group-item d-flex align-items-center active text-decoration-none">
              <i class="bi bi-chat-left-quote me-2"></i> Speech Audiometry
              <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
          </ul>
        </div>
      </div>
    </div>

    <!-- Right panel: Speech Audiometry form -->
    <div class="col-lg-8">
      <div class="card shadow-sm" style="border:1px solid #e2e8f0; border-radius:10px;">
        <div class="card-body">
          <form id="speechForm" method="POST" action="{{ route('staff.patient.service.session.save', ['id' => $id, 'service' => 'speech']) }}" novalidate>
            @csrf

            <div class="table-responsive mb-3">
              <table class="table table-bordered align-middle speech-table text-center">
                <thead class="table-light">
                  <tr>
                    <th style="width:140px;"></th>
                    <th>SRT</th>
                    <th>SDS</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th class="text-center speech-ear">Right</th>
                    <td><input type="text" class="form-control form-control-sm text-center" name="speech_right_srt"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="speech_right_sds"></td>
                  </tr>
                  <tr>
                    <th class="text-center speech-ear">Left</th>
                    <td><input type="text" class="form-control form-control-sm text-center" name="speech_left_srt"></td>
                    <td><input type="text" class="form-control form-control-sm text-center" name="speech_left_sds"></td>
                  </tr>
                  
                </tbody>
              </table>
            </div>

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="speech_date_taken" class="form-label fw-semibold">Date Taken</label>
                <input type="date" id="speech_date_taken" name="date_taken" class="form-control">
              </div>
            </div>
            <div class="mb-3">
              <label for="speech_notes" class="form-label fw-semibold">Notes</label>
              <textarea id="speech_notes" name="speech_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations.."></textarea>
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
  .patient-menu .list-group-item{ cursor:pointer; display:flex; align-items:center; }
  .patient-menu .list-group-item:not(.active):hover{ background:#f8fafc; }
  .patient-menu .list-group-item i.bi-chevron-right{ color:#94a3b8; }

  /* Small header fonts and compact rows */
  /* Match SRT/SDS header size to ear label size */
  .speech-table thead th { font-size: 0.85rem; padding-top:.45rem; padding-bottom:.45rem; }
  .speech-table tbody td, .speech-table tbody th { padding-top:.4rem; padding-bottom:.4rem; }
  .speech-table th, .speech-table td { vertical-align: middle; text-align:center; }

  /* Smaller ear labels */
  .speech-ear { font-size: 0.85rem; }
</style>
@endpush
