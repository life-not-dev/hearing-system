@extends('layouts.staff')

@section('content')

<style>
    /* Page-specific styles (kept here so layout remains shared) */
    /* slimmer greeting banner */
    /* changed to neutral gray background with black text per request */
    .greeting-banner { position: relative; background: linear-gradient(90deg, #b8b6b6ff 0%, #c3c3c3ff 100%); border-radius: 12px; box-shadow: 0 px 6px rgba(0,0,0,0.04); padding: 0; display: flex; align-items: center; margin-bottom: 18px; margin-top: 10px; min-height: 110px; overflow: hidden; }
    .greeting-banner img { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); max-width: 180px; height: auto; border-radius: 0 10px 10px 0; object-fit: cover; }
    /* stack greeting and subtext vertically so subtext is always below main greeting */
    .greeting-text { font-size: 1.25em; font-weight: 700; color: #000; padding: 16px 220px 16px 16px; min-width: 200px; display: flex; flex-direction: column; justify-content: center; height: 110px; }
    /* Dynamic greeting styles - main greeting stays visible (static) */
    .greeting-line { display:inline-block; opacity:1; transform: none; transition: none; }
    /* motivational slanted subtext */
    #greetingMotivation { font-style: italic; color: #000; font-size: 0.88rem; margin-top: 8px; opacity: 0; transform: translateY(6px); transition: opacity 420ms ease, transform 420ms ease; }
    #greetingMotivation.show { opacity: 1; transform: translateY(0); }
    .card-appointments { background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #ddd; margin-bottom: 24px; }
    .card-appointments-header { background: #00c853; color: #fff; border-radius: 8px 8px 0 0; padding: 12px 16px; font-weight: bold; font-size: 1.1em; }
    .card-appointments-body { padding: 0; }
        .appointment-item { background:#fff; padding:12px 20px; }
        .appointment-item + .appointment-item { border-top: 1px solid #e0e0e0; }
        .appt-row { display:flex; justify-content:space-between; align-items:center; gap:16px; }
        .appt-number { font-weight:600; font-size: 0.9rem; color:#666; margin-right:8px; display:inline-block; }
        .appt-name { font-weight:600; font-size: 1rem; display:inline-block; }
        .appt-date { font-size:.95em; color:#666; margin-top:4px; }
        .btn-view-list { background:#1a33ff; border-color:#1a33ff; font-weight:700; color:#fff; padding:.35rem 1.1rem; }
        .btn-view-list:hover { background:#1427cc; border-color:#1427cc; color:#fff; }
    
    /* Pagination Styles */
    .card-appointments-footer { 
        padding: 12px 16px; 
        border-top: 1px solid #e0e0e0; 
        background: #f8f9fa; 
        border-radius: 0 0 8px 8px; 
    }
    .pagination { margin: 0; }
    .page-link { 
        color: #00c853; 
        border-color: #dee2e6; 
        font-size: 0.875rem; 
        padding: 0.375rem 0.75rem; 
    }
    .page-link:hover { 
        color: #fff; 
        background-color: #00c853; 
        border-color: #00c853; 
    }
    .page-item.disabled .page-link { 
        color: #6c757d; 
        background-color: #fff; 
        border-color: #dee2e6; 
    }

    /* Modal styles to mimic provided mock */
    .dash-view-modal .modal-content { border:none; border-radius:10px; box-shadow:0 8px 28px rgba(0,0,0,.15); }
    .dash-view-modal .modal-header { border:none; padding-bottom:0; position:relative; }
    .dash-view-modal .modal-title { font-weight:700; font-size:1.6rem; width:100%; text-align:center; }
    .dash-view-modal .modal-header .btn-close { position:absolute; right:12px; top:12px; margin:0; }
    .dash-view-modal .modal-body { padding-top:8px; }
    .dash-view-modal .divider { height:2px; background:#000; margin:8px 0 18px; }
    .dash-view-modal .info-list { list-style:none; padding:0; margin:0; }
    .dash-view-modal .info-list li { display:flex; gap:8px; align-items:baseline; padding:8px 0; font-size:1rem; }
    .dash-view-modal .info-list strong { width:140px; display:inline-block; font-weight:600; }
    .dash-view-modal .modal-footer { border:none; justify-content:center; gap:24px; }
    .dash-view-modal .btn-confirm { background:#19a642; border-color:#19a642; color:#fff; font-weight:700; min-width:140px; }
    .dash-view-modal .btn-cancel { background:#d11a1a; border-color:#d11a1a; color:#fff; font-weight:700; min-width:140px; }
    /* Cancel modal styles */
    .cancel-modal .modal-content { border:none; border-radius:10px; box-shadow:0 8px 28px rgba(0,0,0,.15); }
    .cancel-modal .modal-header { border:none; position:relative; }
    .cancel-modal .modal-title { width:100%; text-align:center; font-weight:700; font-size:1.6rem; }
    .cancel-modal .modal-header .btn-close { position:absolute; right:12px; top:12px; }
    .cancel-modal .divider { height:2px; background:#000; margin:8px 0 18px; }
    .cancel-modal .form-label { font-weight:700; }
    .cancel-modal textarea { width:100%; min-height:160px; resize:vertical; background:#e5e5e5; border:1px solid #d0d0d0; border-radius:4px; }
    .cancel-modal .modal-footer { border:none; justify-content:center; }
    .cancel-modal .btn-cancel-submit { background:#d11a1a; border-color:#d11a1a; color:#fff; font-weight:700; min-width:140px; }
    .schedule-box { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 28px; border: 1px solid #d0d0d0; }
        .schedule-calendar { border: none; border-radius: 12px; padding: 20px; margin-bottom: 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .calendar-frame { border: none; padding: 0; background: transparent; }
        .calendar-title { font-weight: 700; margin-bottom: 16px; font-size: 1.2em; color: #2c3e50; text-align: center; }
        .calendar-grid { display:flex; flex-direction:column; }
        .calendar-weekdays { display:grid; grid-template-columns:repeat(7,1fr); background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); padding: 12px 8px; gap: 4px; border-radius: 8px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(0,123,255,0.2); }
        .calendar-weekdays div { text-align:center; font-weight:700; color: #fff; font-size: 0.9em; }
        .calendar-days { display:grid; grid-template-columns:repeat(7,1fr); gap: 4px; }
        .calendar-cell { min-height: 40px; display:flex; align-items:center; justify-content:center; font-weight:600; border-radius: 6px; transition: all 0.2s ease; cursor: pointer; }
        .calendar-cell:hover { background: #e3f2fd; transform: scale(1.05); }
        .calendar-cell.empty { background:transparent; color:transparent; cursor: default; }
        .calendar-cell.empty:hover { background: transparent; transform: none; }
        .calendar-cell.today { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: #fff; box-shadow: 0 3px 10px rgba(40,167,69,0.3); font-weight: 700; }
        .calendar-cell.today:hover { background: linear-gradient(135deg, #218838 0%, #1ea085 100%); transform: scale(1.05); }

        .schedule-events { border-top: 1px solid #ddd; margin-top: 12px; padding-top: 12px; }
        .event-row { display:flex; gap:12px; align-items:flex-start; padding:14px 0; border-bottom:1px solid #eee; }
        .event-row:last-child { border-bottom: none; }
        .event-time { width:84px; font-weight:700; }
        .event-details { flex:1; text-align:center; }
        .event-title { font-weight:800; font-size:1.05em; }
        .event-person { color:#666; margin-top:6px; }
    @media (max-width: 1100px) {
        .greeting-banner { flex-direction: column; align-items: stretch; gap: 12px; min-height: 140px; }
        .greeting-banner img { position: static; transform: none; width: 100%; height: 120px; border-radius: 12px; }
        .greeting-text { padding: 12px 18px; font-size: 1.1em; }
        .schedule-box { margin-left: 0 !important; width: 100%; max-width: none; }
    }
</style>

<div class="container-fluid" style="margin-top: -50px;">
    <div class="d-flex flex-wrap align-items-start" style="gap:24px;">
        <div style="flex: 1 1 680px; min-width:300px; max-width:720px;">
            <div class="greeting-banner">
                <div class="greeting-text">
                    <span id="greetingText" class="greeting-line" style="background: url('/images/sun-cloud.png') left center no-repeat; padding-left: 60px;">Good day, User!</span>
                </div>
                <img src="/images/staff-greeting.png" alt="Greeting Illustration">
            </div>
            <div class="card-appointments mt-3">
                <div class="card-appointments-header">New Appointment</div>
                <div class="card-appointments-body" id="staffDashNewAppointments">
                   
                </div>
                <!-- Pagination -->
                <div class="card-appointments-footer" id="appointmentPagination" style="display: none;">
                    <nav aria-label="Appointment pagination">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li class="page-item" id="prevPageBtn">
                                <button class="page-link" type="button" id="prevBtn">Previous</button>
                            </li>
                            <li class="page-item" id="nextPageBtn">
                                <button class="page-link" type="button" id="nextBtn">Next</button>
                            </li>
                        </ul>
                    </nav>
                    <div class="text-center mt-2">
                        <small class="text-muted" id="pageInfo">Page 1 of 1</small>
                    </div>
                </div>
            </div>
        </div>
    <div style="width: 450px; min-width: 370px;">
            <div class="schedule-box">
                <h5 style="font-weight: bold;">Today's Schedule</h5>

                <div class="schedule-calendar">
                    <div class="calendar-frame">
                        <div class="calendar-title" id="calTitle"></div>
                        <div class="calendar-grid">
                            <div class="calendar-weekdays">
                                <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                            </div>
                            <div class="calendar-days" id="calDays"></div>
                        </div>
                    </div>
                </div>

                <div class="schedule-events" id="todayEvents"></div>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard View Modal -->
<div class="modal fade dash-view-modal" id="dashApptViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Appointment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="divider"></div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <ul class="info-list">
                            <li><strong>Name :</strong> <span id="dv_name">—</span></li>
                            <li><strong>Surname :</strong> <span id="dv_surname">—</span></li>
                            <li><strong>Middle :</strong> <span id="dv_middle">—</span></li>
                            <li><strong>Age :</strong> <span id="dv_age">—</span></li>
                            <li><strong>Birthdate :</strong> <span id="dv_birthdate">—</span></li>
                            <li><strong>Address :</strong> <span id="dv_address">—</span></li>
                            <li><strong>Contact :</strong> <span id="dv_contact">—</span></li>
                            <li><strong>Email :</strong> <span id="dv_email">—</span></li>
                        </ul>
                    </div>
                    <div class="col-12 col-md-6">
                        <ul class="info-list">
                            <li><strong>Gender :</strong> <span id="dv_gender">—</span></li>
                            <li><strong>Services :</strong> <span id="dv_services">—</span></li>
                            <li><strong>Branch :</strong> <span id="dv_branch">—</span></li>
                            <li><strong>Referred by :</strong> <span id="dv_referred">—</span></li>
                            <li><strong>Date :</strong> <span id="dv_date">—</span></li>
                            <li><strong>Time :</strong> <span id="dv_time">—</span></li>
                        </ul>
                    </div>
                </div>
            </div>
                        <div class="modal-footer">
                                <button type="button" class="btn btn-confirm" id="confirmAppointmentBtn">
                                    <i class="bi bi-envelope-check me-1"></i>Confirm Email
                                </button>
                                <button type="button" class="btn btn-cancel" id="cancelAppointmentBtn">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </button>
            </div>
        </div>
    </div>
    </div>

<!-- Confirm success modal -->
<div class="modal fade" id="confirmSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none; box-shadow:0 6px 18px rgba(0,0,0,0.12);">
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="bi bi-check-circle" style="font-size:48px; color:#19a642;"></i>
                </div>
                <h5 class="fw-bold">Appointment Confirmed Successfully</h5>
                <p class="text-muted mb-0">The patient has been confirmed by email and moved to New Appointments.</p>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade cancel-modal" id="dashCancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancel Appointment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="divider"></div>
                <div class="mb-3">
                    <label class="form-label" for="dc_reason">Reason:</label>
                    <textarea id="dc_reason" class="form-control" placeholder="Type reason here..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel-submit" id="submitCancelBtn">Cancel Appointment</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Staff dashboard: fetch and display recently booked appointments (pending/confirmed)
(function(){
    const box = document.getElementById('staffDashNewAppointments');
    const empty = document.getElementById('staffDashNewEmpty');
    const paginationEl = document.getElementById('appointmentPagination');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const pageInfo = document.getElementById('pageInfo');
    
    if(!box) return;
    
    // Pagination state
    let currentPage = 1;
    const itemsPerPage = 5;
    let allAppointments = [];
    let totalPages = 1;

    function fmtLongDate(val){
        if(!val) return '';
        try{
            const d = new Date(val);
            if(!isNaN(d)){
                return d.toLocaleDateString(undefined, { weekday:'long', month:'long', day:'2-digit', year:'numeric' });
            }
        }catch(e){/* ignore */}
        return String(val);
    }


    function fmtDateOnly(val){
        if(!val) return '';
        try{ const d=new Date(val); if(!isNaN(d)) return d.toLocaleDateString(); }catch(e){}
        return String(val||'');
    }

    function fmtMonthDayYear(val){
        if(!val) return '';
        try{ const d=new Date(val); if(!isNaN(d)) return d.toLocaleDateString(undefined,{ month:'long', day:'numeric', year:'numeric' }); }catch(e){}
        return String(val||'');
    }

    function computeAge(birth){
        if(!birth) return '';
        const d=new Date(birth); if(isNaN(d)) return '';
        const diff=Date.now()-d.getTime();
        const age=new Date(diff).getUTCFullYear()-1970; return age>=0?String(age):'';
    }

    const dashModalEl = document.getElementById('dashApptViewModal');
    const dashModal = dashModalEl ? new bootstrap.Modal(dashModalEl) : null;
    let currentAppointmentId = null;
    
    function fillDashModal(a){
        const pick = (k)=> (a && (a[k] ?? a[k.toLowerCase()])) ?? '';
        const val = (k, ...alts) => {
            for(const key of [k, ...alts]){ if(a && a[key] != null && String(a[key]).trim() !== '') return String(a[key]); }
            return '';
        };
        const first = val('first_name','fname','firstName');
        const sur = val('surname','last_name','lastName');
        const mid = val('middlename','middle_name','middleName');
        const bdate = val('birthdate','birthday','birth_date');
        const addr = val('address');
        const contact = val('contact','phone','mobile');
        const email = val('email');
        const gender = val('gender');
        const services = val('services','service');
        const branch = val('branch');
        const referred = val('referred_by','referrer','referredBy');
        const dateVal = val('appointment_date','date');
        const timeVal = val('appointment_time','time');

        const set = (id, v) => { const el=document.getElementById(id); if(el) el.textContent = (v && String(v).trim()) ? v : '—'; };
        set('dv_name', first || val('name'));
        set('dv_surname', sur);
        set('dv_middle', mid);
        set('dv_age', computeAge(bdate));
        set('dv_birthdate', fmtMonthDayYear(bdate));
        set('dv_address', addr);
        set('dv_contact', contact);
        set('dv_email', email);
        set('dv_gender', gender);
        set('dv_services', services);
        set('dv_branch', branch);
        set('dv_referred', referred);
        set('dv_date', fmtMonthDayYear(dateVal));
        set('dv_time', timeVal);
        
        // Store current appointment ID for confirm/cancel actions
        currentAppointmentId = a.id;
    }

    function makeItem(a, index){
        const div = document.createElement('div');
        div.className = 'appointment-item';
        const name = a.name && a.name.trim() ? a.name : '—';
        const date = fmtLongDate(a.date || '');
        div.innerHTML = `
            <div class="appt-row">
                <div style="flex:1;">
                    <div class="appt-number">${index + 1}.</div>
                    <div class="appt-name">${name}</div>
                    <div class="appt-date">${date}</div>
                </div>
                <div>
                    <button type="button" class="btn btn-view-list btn-sm">View</button>
                </div>
            </div>
        `;
        const btn = div.querySelector('.btn-view-list');
        if(btn && dashModal){ btn.addEventListener('click', function(){ fillDashModal(a); dashModal.show(); }); }
        return div;
    }

    // Wire Cancel button in view modal to show cancel modal
    const viewCancelBtn = dashModalEl ? dashModalEl.querySelector('.btn-cancel') : null;
    const cancelModalEl = document.getElementById('dashCancelModal');
    const cancelModal = cancelModalEl ? new bootstrap.Modal(cancelModalEl) : null;
    function openCancel(){ if(cancelModal){ const ta=document.getElementById('dc_reason'); if(ta) ta.value=''; cancelModal.show(); } }
    if(viewCancelBtn){
        viewCancelBtn.addEventListener('click', function(){
            if(dashModal){
                const afterHide = function(){ openCancel(); dashModalEl.removeEventListener('hidden.bs.modal', afterHide); };
                dashModalEl.addEventListener('hidden.bs.modal', afterHide);
                dashModal.hide();
            } else {
                openCancel();
            }
        });
    }

    // Handle Confirm button
    const confirmBtn = document.getElementById('confirmAppointmentBtn');
    if(confirmBtn){
        confirmBtn.addEventListener('click', async function(){
            if(!currentAppointmentId) return;
            
            try {
                const response = await fetch(`/staff/appointment/${currentAppointmentId}/confirm`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });
                
                if(response.ok){
                    if(dashModal) dashModal.hide();
                    refresh(); // Refresh the dashboard
                    // Show success modal briefly
                    try {
                        const modEl = document.getElementById('confirmSuccessModal');
                        if (modEl) {
                            const m = new bootstrap.Modal(modEl);
                            m.show();
                            setTimeout(() => { try { m.hide(); } catch(e){} }, 2200);
                        }
                    } catch(e) { /* ignore UI errors */ }
                } else {
                    throw new Error('Failed to confirm appointment');
                }
            } catch(error) {
                alert('Error confirming appointment: ' + error.message);
            }
        });
    }

    // Handle Cancel button (redirect to cancel modal)
    const cancelBtn = document.getElementById('cancelAppointmentBtn');
    if(cancelBtn){
        cancelBtn.addEventListener('click', function(){
            if(dashModal){
                const afterHide = function(){ openCancel(); dashModalEl.removeEventListener('hidden.bs.modal', afterHide); };
                dashModalEl.addEventListener('hidden.bs.modal', afterHide);
                dashModal.hide();
            } else {
                openCancel();
            }
        });
    }

    // Handle Cancel submission
    const submitCancelBtn = document.getElementById('submitCancelBtn');
    if(submitCancelBtn){
        submitCancelBtn.addEventListener('click', async function(){
            if(!currentAppointmentId) return;
            
            const reason = document.getElementById('dc_reason').value.trim();
            if(!reason){
                alert('Please provide a reason for cancellation.');
                return;
            }
            
            try {
                const response = await fetch(`/staff/appointment/${currentAppointmentId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ reason: reason })
                });
                
                if(response.ok){
                    if(cancelModal) cancelModal.hide();
                    refresh(); // Refresh the dashboard
                    // Show success message
                    const successDiv = document.createElement('div');
                    successDiv.className = 'alert alert-info alert-dismissible fade show';
                    successDiv.innerHTML = '<i class="bi bi-info-circle me-1"></i> Appointment cancelled successfully! <button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    document.querySelector('.container-fluid').insertBefore(successDiv, document.querySelector('.container-fluid').firstChild);
                    setTimeout(() => successDiv.remove(), 3000);
                } else {
                    throw new Error('Failed to cancel appointment');
                }
            } catch(error) {
                alert('Error cancelling appointment: ' + error.message);
            }
        });
    }

    // Pagination functions
    function updatePagination() {
        totalPages = Math.ceil(allAppointments.length / itemsPerPage);
        
        if (totalPages <= 1) {
            paginationEl.style.display = 'none';
            return;
        }
        
        paginationEl.style.display = 'block';
        
        // Update page info
        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
        
        // Update button states
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
        
        if (currentPage === 1) {
            prevBtn.parentElement.classList.add('disabled');
        } else {
            prevBtn.parentElement.classList.remove('disabled');
        }
        
        if (currentPage === totalPages) {
            nextBtn.parentElement.classList.add('disabled');
        } else {
            nextBtn.parentElement.classList.remove('disabled');
        }
    }
    
    function displayCurrentPage() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageData = allAppointments.slice(startIndex, endIndex);
        
        box.innerHTML = '';
        
        if (pageData.length === 0) {
            const d = document.createElement('div');
            d.className = 'text-muted text-center py-3';
            d.textContent = 'No appointments on this page';
            box.appendChild(d);
            return;
        }
        
        pageData.forEach((a, index) => {
            const globalIndex = startIndex + index;
            box.appendChild(makeItem(a, globalIndex));
        });
        
        updatePagination();
    }
    
    // Event listeners for pagination
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                displayCurrentPage();
            }
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                displayCurrentPage();
            }
        });
    }

    async function refresh(){
        try{
            const r = await fetch('{{ route('notifications.appointments.list') }}?status=pending');
            if(!r.ok) throw new Error('bad response');
            const js = await r.json();
            const data = (js && js.data) ? js.data : [];
            
            // Sort appointments by creation time (first come first serve)
            // Sort by created_at, updated_at, or id in ascending order (oldest first)
            const sortedData = data.sort((a, b) => {
                // Try different date fields to find the creation time
                const dateA = new Date(a.created_at || a.updated_at || a.id || 0);
                const dateB = new Date(b.created_at || b.updated_at || b.id || 0);
                return dateA - dateB; // Ascending order (oldest first)
            });
            
            allAppointments = sortedData;
            
            if(!data.length){
                box.innerHTML = '';
                const d = document.createElement('div');
                // Use Bootstrap utilities to center the message and match muted styling
                d.className = 'text-muted text-center py-3';
                d.textContent = 'No recent appointments';
                box.appendChild(d);
                paginationEl.style.display = 'none';
                currentPage = 1; // Reset to first page when no data
                return;
            }
            
            // Only reset to page 1 if current page is no longer valid
            const maxPages = Math.ceil(data.length / itemsPerPage);
            if (currentPage > maxPages) {
                currentPage = 1;
            }
            
            displayCurrentPage();
        }catch(e){
            box.innerHTML = '';
            const d = document.createElement('div');
            d.className = 'text-muted';
            d.style.padding = '8px 0';
            d.textContent = 'Failed to load.';
            box.appendChild(d);
            paginationEl.style.display = 'none';
        }
    }

    refresh();
    // Poll every 10s to catch status changes faster
    setInterval(refresh, 10000);
})();

// Staff dashboard: dynamic month calendar and today's confirmed appointments
(function(){
    const calTitle = document.getElementById('calTitle');
    const calDays = document.getElementById('calDays');
    const todayEvents = document.getElementById('todayEvents');
    if(!calTitle || !calDays || !todayEvents) return;

    function renderCalendar(date){
        const y = date.getFullYear();
        const m = date.getMonth(); // 0-based
        const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        calTitle.textContent = `${monthNames[m]} ${y}`;

        const first = new Date(y, m, 1);
        const startDay = first.getDay();
        const daysInMonth = new Date(y, m+1, 0).getDate();
        calDays.innerHTML = '';
        // leading blanks
        for(let i=0;i<startDay;i++){
            const d = document.createElement('div');
            d.className = 'calendar-cell empty';
            calDays.appendChild(d);
        }
        // days
        const today = new Date();
        for(let d=1; d<=daysInMonth; d++){
            const cell = document.createElement('div');
            cell.className = 'calendar-cell';
            if(d === today.getDate() && m === today.getMonth() && y === today.getFullYear()){
                cell.classList.add('today');
            }
            cell.textContent = String(d).padStart(2,'0');
            calDays.appendChild(cell);
        }
    }

    function makeEventRow(item){
        const row = document.createElement('div');
        row.className = 'event-row';
        row.innerHTML = `
            <div class="event-time">${item.time||''}</div>
            <div class="event-details">
                <div class="event-title">${item.service||'Appointment'}</div>
                <div class="event-person">${item.name||'—'}</div>
            </div>`;
        return row;
    }

    async function refreshToday(){
        try{
            const r = await fetch('{{ route('staff.schedule.today') }}?status=confirmed');
            if(!r.ok) throw new Error('bad response');
            const js = await r.json();
            const items = js.appointments || [];
            todayEvents.innerHTML = '';
            if(!items.length){
                const d = document.createElement('div');
                d.className = 'text-muted text-center py-3';
                d.textContent = "No confirmed appointments today.";
                todayEvents.appendChild(d);
                return;
            }
            items.forEach(it => todayEvents.appendChild(makeEventRow(it)));
        }catch(e){
            todayEvents.innerHTML = '';
            const d = document.createElement('div');
            d.className = 'text-muted';
            d.style.padding = '8px 0';
            d.textContent = 'Failed to load today\'s schedule.';
            todayEvents.appendChild(d);
        }
    }

    renderCalendar(new Date());
    refreshToday();
    setInterval(refreshToday, 20000);
})();

// Dynamic greeting: time-based message + username + animation
(function(){
    const greetEl = document.getElementById('greetingText');
    if(!greetEl) return;

    // Get user name from server-rendered blade or fallback
    const serverName = @json(optional(auth()->user())->name ?? optional(auth()->user())->first_name ?? null);
    const userName = (serverName && String(serverName).trim()) ? String(serverName).trim() : 'User';

    function timeGreeting(d){
        const h = d.getHours();
        if(h < 12) return 'Good Morning';
        if(h < 18) return 'Good Afternoon';
        return 'Good Evening';
    }

    function updateGreeting(){
        const now = new Date();
        const prefix = timeGreeting(now);
        const text = `${prefix}, ${userName}!`;
        // Set text (main greeting is static)
        if(greetEl.textContent.trim() !== text) greetEl.textContent = text;
    }

    // Initial set immediately
    updateGreeting();

    // Optional: refresh greeting once a minute to update when time boundary passes
    const intervalId = setInterval(updateGreeting, 60 * 1000);
    // If you ever need to clear: clearInterval(intervalId);
})();

// Motivational subtext: show after 1.5s with fade-in (randomized)
(function(){
    const greetEl = document.getElementById('greetingText');
    if(!greetEl) return;

    // Ensure we have a place to show the subtext; create one if missing
    let sub = document.getElementById('greetingMotivation');
    if(!sub){
        sub = document.createElement('div');
        sub.id = 'greetingMotivation';
        // insert after greetingText inside greeting-text
        const parent = greetEl.parentElement;
        if(parent){ parent.appendChild(sub); }
    }

    const messages = [
        '"Every day is a new chance to make a difference."',
        '"Keep shining, your hard work matters."',
        '"Every patient you help today hears a better tomorrow."',
        '"Caring for patients is caring from the heart."'
    ];

    function choose(){ return messages[Math.floor(Math.random()*messages.length)]; }

    // Show the motivational subtext 1.5s after greeting animation, then rotate every 6s
    function showAndRotate(){
        sub.textContent = choose();
        sub.classList.add('show');
    }
    setTimeout(() => {
        showAndRotate();
        setInterval(() => {
            // fade out, change text, fade in
            sub.classList.remove('show');
            setTimeout(() => { sub.textContent = choose(); sub.classList.add('show'); }, 420);
        }, 6000);
    }, 1500);
})();
</script>
@endpush
