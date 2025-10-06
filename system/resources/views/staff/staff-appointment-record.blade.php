@extends('layouts.staff')

@section('content')

<style>
    @import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
    .record-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .record-title { font-size:1.1rem; font-weight:700; }
    
    /* Search and Filter Styling */
    .input-group-text { 
        border-color: #d1d5db; 
        background-color: #f8fafc; 
    }
    .input-group .form-control { 
        border-color: #d1d5db; 
        font-size: 0.875rem;
    }
    .input-group .form-control:focus { 
        border-color: #0d6efd; 
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); 
    }
    .input-group .form-control:focus + .input-group-text,
    .input-group .form-control:focus ~ .input-group-text { 
        border-color: #0d6efd; 
    }
    
    /* Responsive adjustments for search filters */
    @media (max-width: 1200px) {
        .d-flex.gap-2 {
            flex-wrap: wrap;
            gap: 0.5rem !important;
        }
        .input-group {
            width: 160px !important;
        }
    }
    
    @media (max-width: 992px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        .d-flex.gap-2 {
            width: 100%;
            justify-content: space-between;
        }
        .input-group {
            width: 140px !important;
        }
    }
    
    @media (max-width: 768px) {
        .d-flex.gap-2 {
            flex-direction: column;
            width: 100%;
            gap: 0.75rem !important;
        }
        .input-group {
            width: 100% !important;
        }
        .btn {
            width: 100%;
        }
    }
    /* Admin-like table styling */
    .appointment-table thead th { background:#f1f5f9; font-weight:700; font-size:.75rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
    .appointment-table tbody td { font-size:.85rem; }
    .appointment-table tbody tr:hover { background:#f8fafc; }
    .badge { letter-spacing:.4px; }
    .btn-icon { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; padding:0; }
    .btn-icon + .btn-icon { margin-left:4px; }
    .btn-outline-primary { --bs-btn-color:#1d4ed8; --bs-btn-border-color:#1d4ed8; }
    .btn-outline-primary:hover { background:#1d4ed8; color:#fff; }
    .btn-outline-danger { --bs-btn-color:#dc2626; --bs-btn-border-color:#dc2626; }
    .btn-outline-danger:hover { background:#dc2626; color:#fff; }
    .btn-outline-success { --bs-btn-color:#059669; --bs-btn-border-color:#059669; }
    .btn-outline-success:hover { background:#059669; color:#fff; }
    .appointment-table td.actions .action-buttons { display:flex; align-items:center; gap:6px; }
    .appointment-table td.actions .btn-icon { flex:0 0 34px; }
    @media (max-width: 992px){
        .appointment-table td.actions .action-buttons { justify-content:flex-start; }
    }
    /* Admin modal styles copied */
    .appt-modal { position:fixed; inset:0; width:100vw; height:100vh; background:rgba(0,0,0,.25); display:flex; align-items:center; justify-content:center; z-index:5000; padding:20px; }
    .appt-modal.hidden { display:none; }
    .modal-card { background:#fff; width:100%; max-width:620px; border-radius:14px; box-shadow:0 4px 24px -4px rgba(0,0,0,.15); padding:28px 26px 26px; position:relative; margin:auto; }
    .modal-close { position:absolute; top:10px; right:14px; background:none; border:none; font-size:24px; line-height:1; cursor:pointer; color:#475569; }
    .info-list li { padding:2px 0; font-size:.8rem; }
    @media (max-width: 768px){
        .appointment-table thead { display:none; }
        .appointment-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
        .appointment-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
        .appointment-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
        .btn-icon { width:34px; height:34px; }
    }
    /* Center headers inside the View modal */
    #viewInfoModal .modal-title { width:100%; text-align:center; }
    #viewInfoModal h6 { text-align:center; }
    
    /* Pagination styling */
    .pagination { margin: 0; }
    .pagination .page-link { padding: 0.375rem 0.75rem; font-size: 0.875rem; }
    .pagination .page-item.disabled .page-link { color: #6c757d; }
    .pagination .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; }
</style>

<div class="main-content">
    <div class="record-panel">
        <div class="d-flex justify-content-between flex-wrap gap-3 align-items-start align-items-md-start" style="margin-top:-25px; margin-bottom:3px;">
            <div>
                <h4 class="fw-bold mb-0" style="margin-top: -50px;">Appointment Record</h4>
                <p class="text-muted mb-0" style="margin-top: -4px;">Comprehensive record of patient visits and diagnostic outcomes.</p>
            </div>
            
            <div class="d-flex gap-2 align-items-center">
                <!-- Search and Filter Controls -->
                <div class="d-flex gap-2 align-items-center">
                    <!-- Name Search -->
                    <div class="input-group" style="width: 200px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" id="nameSearch" class="form-control border-start-0" placeholder="Search by name..." style="border-left: none;">
                    </div>
                    
                    <!-- Single Date Filter -->
                    <div class="input-group" style="width: 180px;">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-calendar text-muted"></i>
                        </span>
                        <input type="date" id="appointmentDate" class="form-control border-start-0" placeholder="Filter by date" style="border-left: none;">
                    </div>
                    
                    <!-- Clear Filter Button -->
                    <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm" title="Clear all filters">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="card shadow-sm" style="border:1px solid #e2e8f0; margin-top: 20px;">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 appointment-table align-middle">
                        <thead>
                            <tr>
                                <th style="width:70px;">no.</th>
                                <th>Patient Name</th>
                                <th style="width:140px;">Patient Type</th>
                                <th>Service</th>
                                <th style="width:120px;">Branch</th>
                                <th style="width:160px;">Time</th>
                                <th style="width:160px;">Date</th>
                                <th style="width:110px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($appointments as $appt)
                            @php
                                $patient = $appt->patient;
                                $serviceName = optional($appt->serviceRef)->service_name ?? '';
                                $branchName = optional($appt->branchRef)->branch_name ?? '—';
                                $fullName = $patient ? trim(($patient->patient_firstname ?? '').' '.($patient->patient_surname ?? '')) : '';
                                $emailVal = $patient->patient_email ?? '';
                                $contactVal = $patient->patient_contact_number ?? ($appt->contact ?? '');
                                $addressVal = $patient->patient_address ?? ($appt->address ?? '');
                                $genderVal = $patient->patient_gender ?? ($appt->gender ?? '');
                                $birthdateVal = $patient->patient_birthdate ?? ($appt->birthdate ?? '');
                                $firstNameVal = $patient->patient_firstname ?? ($appt->first_name ?? '');
                                $surnameVal = $patient->patient_surname ?? ($appt->surname ?? '');
                                $middlenameVal = $patient->patient_middlename ?? ($appt->middlename ?? '');
                                $testsData = [];
                                $loaded = (method_exists($appt, 'relationLoaded') && $appt->relationLoaded('tests') && $appt->tests && $appt->tests->count());
                                $tests = $loaded ? $appt->tests : (\Illuminate\Support\Facades\Schema::hasTable('tbl_test')
                                    ? \App\Models\Test::where('patient_id', (int)($appt->patient_id ?? 0))
                                        ->orderByDesc('test_date')->get()
                                    : collect());
                                if ($tests && $tests->count()) {
                                    $testsData = $tests->map(function($t){
                                        return [
                                            'type' => (string)($t->test_type ?? ''),
                                            'result' => (string)($t->test_result ?? ''),
                                            'date' => (string)($t->test_date ? \Carbon\Carbon::parse($t->test_date)->format('M d, Y') : ''),
                                        ];
                                    })->values()->toArray();
                                }
                                $svcRecordsRow = $svcRecordsPerAppt[$appt->id] ?? [];
                                $billDefaults = [
                                    'items' => [],
                                    'regularTotal' => 0,
                                    'discountTotal' => 0,
                                    'finalTotal' => 0,
                                    'patient_type' => ($appt->patient_type ?? ''),
                                ];
                                $billingRow = array_merge($billDefaults, ($billingPerAppt[$appt->id] ?? []));
                            @endphp
                            <tr data-id="{{ $appt->id }}"
                                data-first_name="{{ $firstNameVal }}"
                                data-surname="{{ $surnameVal }}"
                                data-middlename="{{ $middlenameVal }}"
                                data-gender="{{ $genderVal }}"
                                data-birthdate="{{ $birthdateVal }}"
                                data-address="{{ $addressVal }}"
                                data-contact="{{ $contactVal }}"
                                data-email="{{ $emailVal }}"
                                data-service="{{ $serviceName }}"
                                data-branch="{{ $branchName }}"
                                data-patient_type="{{ $appt->patient_type ?? '' }}"
                                data-referred_by="{{ $appt->referred_by ?? '' }}"
                                data-purpose="{{ $appt->purpose ?? '' }}"
                                data-medical_history="{{ $appt->medical_history ?? '' }}"
                                data-date="{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}"
                                data-time="{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}"
                                data-tests='@json($testsData)'
                                data-svc-records='@json($svcRecordsRow)'
                                data-billing='@json($billingRow)'
                            >
                                <td data-label="#">{{ sprintf('%02d', ($appointments->currentPage() - 1) * $appointments->perPage() + $loop->iteration) }}</td>
                                <td data-label="Patient">{{ $fullName }}</td>
                                <td data-label="Type">
                                    @php 
                                        $pt = $appt->patient_type ?? 'Regular';
                                        $statusText = strtoupper($pt);
                                    @endphp
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis" style="font-size:.65rem;">{{ $statusText }}</span>
                                </td>
                                <td data-label="Service">{{ $serviceName }}</td>
                                <td data-label="Branch">{{ $branchName }}</td>
                                <td data-label="Time">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</td>
                                <td data-label="Date">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</td>
                                <td data-label="Action" class="actions">
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-primary btn-view" type="button">View</button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No confirmed appointments found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Pagination Info and Controls -->
        <div class="d-flex justify-content-between align-items-center mt-3 px-3 py-2 bg-light border-top">
            <div class="text-muted small">
                Showing {{ $appointments->firstItem() ?? 0 }} to {{ $appointments->lastItem() ?? 0 }} of {{ $appointments->total() }} entries
            </div>
            <div>
                {{ $appointments->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>

<!-- View Info Modal (Bootstrap, centered like New Patient modal) -->
<div class="modal fade" id="viewInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border:none; box-shadow:0 6px 18px rgba(0,0,0,0.12);">
            <div class="modal-header">
                <h5 class="modal-title fw-bold mb-0">Appointment Details</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="fw-bold mb-2">Patient Information</h6>
                    <div class="row g-2 small">
                        <div class="col-md-4"><label class="form-label mb-0">Name</label><input class="form-control form-control-sm" id="pf_name" readonly></div>
                        <div class="col-md-4"><label class="form-label mb-0">Surname</label><input class="form-control form-control-sm" id="pf_surname" readonly></div>
                        <div class="col-md-4"><label class="form-label mb-0">Middle</label><input class="form-control form-control-sm" id="pf_middle" readonly></div>
                        <div class="col-md-4"><label class="form-label mb-0">Birthdate</label><input class="form-control form-control-sm" id="pf_birthdate" readonly></div>
                        <div class="col-md-2"><label class="form-label mb-0">Age</label><input class="form-control form-control-sm" id="pf_age" readonly></div>
                        <div class="col-md-2"><label class="form-label mb-0">Gender</label><input class="form-control form-control-sm" id="pf_gender" readonly></div>
                        <div class="col-md-4"><label class="form-label mb-0">Contact</label><input class="form-control form-control-sm" id="pf_contact" readonly></div>
                        <div class="col-md-6"><label class="form-label mb-0">Email</label><input class="form-control form-control-sm" id="pf_email" readonly></div>
                        <div class="col-md-6"><label class="form-label mb-0">Address</label><input class="form-control form-control-sm" id="pf_address" readonly></div>
                    </div>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold mb-2">Appointment Information</h6>
                    <div class="row g-2 small">
                        <div class="col-md-6"><label class="form-label mb-0">Service</label><input class="form-control form-control-sm" id="pf_service" readonly></div>
                        <div class="col-md-6"><label class="form-label mb-0">Branch</label><input class="form-control form-control-sm" id="pf_branch" readonly></div>
                        <div class="col-md-6"><label class="form-label mb-0">Purpose</label><input class="form-control form-control-sm" id="pf_purpose" readonly></div>
                        <div class="col-md-6"><label class="form-label mb-0">Referred By</label><input class="form-control form-control-sm" id="pf_referred_by" readonly></div>
                        <div class="col-12"><label class="form-label mb-0">Medical History</label><textarea class="form-control form-control-sm" id="pf_medical_history" readonly rows="2"></textarea></div>
                    </div>
                </div>
                <div class="mb-3">
                    <h6 class="fw-bold mb-2">Test Results</h6>
                    <div id="apptTestsContainer" class="small"></div>
                </div>
                <div class="mb-1">
                    <h6 class="fw-bold mb-2">Billing</h6>
                    <div id="apptBillingContainer" class="small"></div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary me-2" id="addPatientFromAppointmentBtn" data-appointment-id="">
                    <i class="bi bi-plus-circle me-1"></i>Add Patient
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        var modalEl = document.getElementById('viewInfoModal');
        var modal = modalEl ? new bootstrap.Modal(modalEl) : null;
        function computeAge(dateStr){
            if(!dateStr) return '';
            var d = new Date(dateStr);
            if(isNaN(d)) return '';
            var diff = Date.now() - d.getTime();
            var age = new Date(diff).getUTCFullYear() - 1970;
            return age >= 0 ? String(age) : '';
        }
        document.querySelectorAll('.btn-view').forEach(function(btn){
            btn.addEventListener('click', function(){
                var row = btn.closest('tr');
                if(!row) return;
                
                // Set appointment ID for the Add Patient button
                var appointmentId = row.getAttribute('data-id');
                var addPatientBtn = document.getElementById('addPatientFromAppointmentBtn');
                if (addPatientBtn) {
                    addPatientBtn.setAttribute('data-appointment-id', appointmentId);
                }
                
                // Map helper
                var get = function(attr){ return (row.getAttribute('data-'+attr) || '').trim(); };
                // Fill readonly patient fields
                var byId = function(id){ return document.getElementById(id); };
                var ageVal = computeAge(get('birthdate')) || '';
                if (byId('pf_name')) byId('pf_name').value = get('first_name') || get('fname') || '';
                if (byId('pf_surname')) byId('pf_surname').value = get('surname') || '';
                if (byId('pf_middle')) byId('pf_middle').value = get('middlename') || '';
                if (byId('pf_birthdate')) byId('pf_birthdate').value = (get('birthdate') ? new Date(get('birthdate')).toLocaleDateString() : '');
                if (byId('pf_age')) byId('pf_age').value = ageVal || '';
                if (byId('pf_gender')) byId('pf_gender').value = get('gender') || '';
                if (byId('pf_contact')) byId('pf_contact').value = get('contact') || '';
                if (byId('pf_email')) byId('pf_email').value = get('email') || '';
                if (byId('pf_address')) byId('pf_address').value = get('address') || '';
                // Fill appointment information fields
                if (byId('pf_service')) byId('pf_service').value = get('service') || '';
                if (byId('pf_branch')) byId('pf_branch').value = get('branch') || '';
                if (byId('pf_purpose')) byId('pf_purpose').value = get('purpose') || '';
                if (byId('pf_referred_by')) byId('pf_referred_by').value = get('referred_by') || '';
                if (byId('pf_medical_history')) byId('pf_medical_history').value = get('medical_history') || '';
                // Render tests
                var testsContainer = document.getElementById('apptTestsContainer');
                if (testsContainer) {
                    testsContainer.innerHTML = '';
                    // Prefer server-rendered partials by reconstructing svcRecords
                    var svcRaw = row.getAttribute('data-svc-records') || '{}';
                    var svc = {};
                    try { svc = JSON.parse(svcRaw); } catch(e) { svc = {}; }
                    // Debug: log to see what data is being received
                    console.log('SVC Records for appointment:', svc);
                    // Build a lightweight renderer per service when present
                    var any = false;
                    var renderTable = function(html){ var div=document.createElement('div'); div.innerHTML = html; testsContainer.appendChild(div); };
                    // Because we cannot include blade partials client-side, show compact tables from JSON
                    var simple = function(title, headers, rows){
                        var h = '<div class="card shadow-sm mb-2"><div class="card-body">';
                        h += '<div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">'+title+'</h6></div>';
                        h += '<div class="table-responsive"><table class="table table-bordered align-middle small"><thead class="table-light"><tr>';
                        headers.forEach(function(th){ h += '<th>'+th+'</th>'; });
                        h += '</tr></thead><tbody>';
                        rows.forEach(function(r){ h += '<tr>' + r.map(function(c){ return '<td>'+ (c ?? '') +'</td>'; }).join('') + '</tr>'; });
                        h += '</tbody></table></div></div></div>';
                        renderTable(h);
                    };
                    if (svc.oae && svc.oae.length){ any = true; svc.oae.forEach(function(r){ simple('OAE', ['Ear','Pass','Refer','Not tested','Remarks'], [
                        ['Left', r.oae_left_pass?'✓':'', r.oae_left_refer?'✓':'', r.oae_left_not_tested?'✓':'', r.oae_left_remarks||''],
                        ['Right', r.oae_right_pass?'✓':'', r.oae_right_refer?'✓':'', r.oae_right_not_tested?'✓':'', r.oae_right_remarks||''],
                    ]); }); }
                    if (svc.tym && svc.tym.length){ any = true; svc.tym.forEach(function(r){ simple('Tympanometry', ['Ear','Type'], [
                        ['Right', r.tym_right_type||''], ['Left', r.tym_left_type||'']
                    ]); }); }
                    if (svc.speech && svc.speech.length){ any = true; svc.speech.forEach(function(r){ simple('Speech Audiometry', ['Ear','SRT','SDS'], [
                        ['Right', r.speech_right_srt||'', r.speech_right_sds||''], ['Left', r.speech_left_srt||'', r.speech_left_sds||'']
                    ]); }); }
                    if (svc.play && svc.play.length){ any = true; svc.play.forEach(function(r){ simple('Play Audiometry', ['Ear','SRT','SDS'], [
                        ['Right', r.speech_right_srt||'', r.speech_right_sds||''], ['Left', r.speech_left_srt||'', r.speech_left_sds||'']
                    ]); }); }
                    if (svc.pta && svc.pta.length){ any = true; svc.pta.forEach(function(r){
                        var freqs = [250,500,1000,1500,2000,3000,4000,6000,8000];
                        var mk = function(arr){ return (arr||[]).map(function(v){ return v ?? ''; }); };
                        var rows = [];
                        rows.push(['RIGHT AC'].concat(mk(r.pta_right_ac)));
                        rows.push(['MASKED'].concat(mk(r.pta_right_ac_masked)));
                        rows.push(['RIGHT BC'].concat(mk(r.pta_right_bc)));
                        rows.push(['MASKED'].concat(mk(r.pta_right_bc_masked)));
                        rows.push(['LEFT AC'].concat(mk(r.pta_left_ac)));
                        rows.push(['MASKED'].concat(mk(r.pta_left_ac_masked)));
                        rows.push(['LEFT BC'].concat(mk(r.pta_left_bc)));
                        rows.push(['MASKED'].concat(mk(r.pta_left_bc_masked)));
                        simple('Pure Tone Audiometry', [''].concat(freqs.map(function(hz){ return hz+' Hz'; })), rows);
                    }); }
                    if (svc.abr && svc.abr.length){ any = true; svc.abr.forEach(function(r){
                        var rn=r.abr_rn||[], ri=r.abr_ri||[], r3=r.abr_r3||[], rv=r.abr_rv||[], r13=r.abr_r13||[], r35=r.abr_r35||[], r15=r.abr_r15||[], rvv=r.abr_rvv||[];
                        var rowsR = [];
                        var maxR = Math.max(rn.length,3);
                        for(var i=0;i<maxR;i++){ rowsR.push([rn[i]||'', ri[i]||'', r3[i]||'', rv[i]||'', r13[i]||'', r35[i]||'', r15[i]||'', rvv[i]||'']); }
                        simple('ABR (Right)', ['N','I','III','V','I–III','III–V','I–V','V–V(a)'], rowsR);
                        var ln=r.abr_ln||[], li=r.abr_li||[], l3=r.abr_l3||[], lv=r.abr_lv||[], l13=r.abr_l13||[], l35=r.abr_l35||[], l15=r.abr_l15||[], lvv=r.abr_lvv||[];
                        var rowsL = []; var maxL = Math.max(ln.length,3);
                        for(var j=0;j<maxL;j++){ rowsL.push([ln[j]||'', li[j]||'', l3[j]||'', lv[j]||'', l13[j]||'', l35[j]||'', l15[j]||'', lvv[j]||'']); }
                        simple('ABR (Left)', ['N','I','III','V','I–III','III–V','I–V','V–V(a)'], rowsL);
                    }); }
                    if (svc.assr && svc.assr.length){ any = true; svc.assr.forEach(function(r){
                        var rows = [ ['RIGHT', r.assr_r_500||'', r.assr_r_1000||'', r.assr_r_2000||'', r.assr_r_4000||''], ['LEFT', r.assr_l_500||'', r.assr_l_1000||'', r.assr_l_2000||'', r.assr_l_4000||''] ];
                        simple('ASSR', ['EAR','500 Hz','1000 Hz','2000 Hz','4000 Hz'], rows);
                    }); }
                    if (svc.hearing && svc.hearing.length){ any = true; 
                        // Show only one table for hearing aid fitting, regardless of number of records
                        var hearingRows = [];
                        svc.hearing.forEach(function(r){ 
                            hearingRows.push([r.brand||'', r.model||'', r.ear_side||'', r.date_issued||'']);
                        });
                        simple('Hearing Aid Fitting', ['Brand','Model','Ear Side','Date Issued'], hearingRows);
                    }
                    if (!any) {
                        testsContainer.innerHTML = '<div class="text-muted">No test results recorded.</div>';
                    }
                }
                // Render billing
                var billEl = document.getElementById('apptBillingContainer');
                if (billEl) {
                    billEl.innerHTML = '';
                    var raw = row.getAttribute('data-billing') || '{}';
                    var b = {}; try { b = JSON.parse(raw); } catch(e) { b = {}; }
                    var items = Array.isArray(b.items) ? b.items : [];
                    if (!items.length) { billEl.innerHTML = '<div class="text-muted">No billing yet.</div>'; }
                    else {
                        var h = '<div class="table-responsive"><table class="table table-bordered align-middle small"><thead class="table-light"><tr><th>Service</th><th class="text-end">Regular</th><th class="text-end">Discount</th><th class="text-end">Final</th></tr></thead><tbody>';
                        items.forEach(function(it){ h += '<tr><td>'+it.label+'</td><td class="text-end">'+Number(it.regular).toLocaleString()+'</td><td class="text-end">'+Number(it.discount).toLocaleString()+'</td><td class="text-end">'+Number(it.final).toLocaleString()+'</td></tr>'; });
                        h += '<tr class="fw-bold"><td class="text-end">Totals</td><td class="text-end">'+Number(b.regularTotal||0).toLocaleString()+'</td><td class="text-end">'+Number(b.discountTotal||0).toLocaleString()+'</td><td class="text-end">'+Number(b.finalTotal||0).toLocaleString()+'</td></tr>';
                        h += '</tbody></table></div>';
                        billEl.innerHTML = h;
                    }
                }
                if(modal){ modal.show(); }
            });
        });

        // Defensive cleanup to avoid lingering backdrops (mirrors New Patient modal)
        if(modalEl){
            modalEl.addEventListener('hidden.bs.modal', function(){
                document.querySelectorAll('.modal-backdrop').forEach(function(b){ b.remove(); });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }

        // Add Patient button functionality
        document.getElementById('addPatientFromAppointmentBtn').addEventListener('click', function(){
            var appointmentId = this.getAttribute('data-appointment-id');
            if (!appointmentId) {
                alert('No appointment selected');
                return;
            }
            
            // Show loading state
            var originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Adding...';
            this.disabled = true;
            
            // Create form and submit
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("staff.appointment.add.patient.record", ":id") }}'.replace(':id', appointmentId);
            
            // Add CSRF token
            var csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Submit form
            document.body.appendChild(form);
            form.submit();
        });
    });

    // Search and Filter Functionality
    document.addEventListener('DOMContentLoaded', function(){
        const nameSearch = document.getElementById('nameSearch');
        const appointmentDate = document.getElementById('appointmentDate');
        const clearFilters = document.getElementById('clearFilters');
        const appointmentsTable = document.querySelector('.appointment-table');
        
        if (!appointmentsTable) return;
        
        const originalRows = Array.from(appointmentsTable.querySelectorAll('tbody tr'));
        
        function filterAppointments() {
            const nameFilter = (nameSearch.value || '').toLowerCase().trim();
            const selectedDate = appointmentDate.value;
            
            originalRows.forEach(function(row) {
                let showRow = true;
                
                // Name filter
                if (nameFilter) {
                    const nameCell = row.querySelector('td[data-label="Patient"]');
                    const fullName = (nameCell ? nameCell.textContent : '').toLowerCase();
                    if (!fullName.includes(nameFilter)) {
                        showRow = false;
                    }
                }
                
                // Single date filter
                if (selectedDate) {
                    const dateCell = row.querySelector('td[data-label="Date"]');
                    if (dateCell) {
                        const appointmentDate = dateCell.textContent.trim();
                        try {
                            // Parse the date (format: "Oct 04, 2025")
                            const parsedDate = new Date(appointmentDate);
                            const appointmentDateStr = parsedDate.toISOString().split('T')[0];
                            
                            if (appointmentDateStr !== selectedDate) {
                                showRow = false;
                            }
                        } catch (e) {
                            // If date parsing fails, hide the row
                            showRow = false;
                        }
                    } else {
                        showRow = false;
                    }
                }
                
                // Show/hide row
                row.style.display = showRow ? '' : 'none';
            });
            
            // Check if any rows are visible
            const visibleRows = originalRows.filter(row => row.style.display !== 'none');
            const emptyRow = appointmentsTable.querySelector('tbody tr:last-child');
            
            if (visibleRows.length === 0) {
                if (!emptyRow || !emptyRow.querySelector('td[colspan]')) {
                    const newEmptyRow = document.createElement('tr');
                    newEmptyRow.innerHTML = '<td colspan="8" class="text-center text-muted py-4">No appointments found matching your criteria.</td>';
                    appointmentsTable.querySelector('tbody').appendChild(newEmptyRow);
                }
            } else {
                // Remove empty row if it exists
                if (emptyRow && emptyRow.querySelector('td[colspan]')) {
                    emptyRow.remove();
                }
            }
        }
        
        function clearAllFilters() {
            nameSearch.value = '';
            appointmentDate.value = '';
            originalRows.forEach(function(row) {
                row.style.display = '';
            });
            
            // Remove empty row if it exists
            const emptyRow = appointmentsTable.querySelector('tbody tr:last-child');
            if (emptyRow && emptyRow.querySelector('td[colspan]')) {
                emptyRow.remove();
            }
        }
        
        // Event listeners
        if (nameSearch) {
            nameSearch.addEventListener('input', filterAppointments);
        }
        
        if (appointmentDate) {
            appointmentDate.addEventListener('change', filterAppointments);
        }
        
        if (clearFilters) {
            clearFilters.addEventListener('click', clearAllFilters);
        }
    });
</script>
@endpush
