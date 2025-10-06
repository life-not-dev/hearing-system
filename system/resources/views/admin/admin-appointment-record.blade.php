@extends('layouts.app')

@section('title', 'Appointment Record | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
    <div style="margin-top:-30px; margin-bottom:18px;">
        <h3 style="font-weight:bold;">Appointment Record</h3>
        <p class="text-muted mb-3" style="font-size:0.9rem;">Summary of all appointments</p>
    </div>

    <div style="margin-bottom:18px;" class="d-flex justify-content-between flex-wrap gap-3 align-items-start align-items-md-center">
        <div id="paginationControls"></div>
        <div class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center">
            <div class="d-flex align-items-center gap-1 p-1 border rounded" style="background-color: #f8f9fa;">
                <span style="font-size:0.8rem; color:#6b7280;">Show</span>
                <select id="entriesPerPage" class="form-select form-select-sm border-0" style="width: 80px; padding-right: 25px;">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="300">300</option>
                    <option value="400">400</option>
                    <option value="500">500</option>
                </select>
                <span style="font-size:0.8rem; color:#6b7280;">entries</span>
            </div>
            <select class="form-select" style="width:180px;">
                <option>Branch</option>
                <option>Davao</option>
                <option>Butuan</option>
                <option>CDO</option>
            </select>
            <div class="position-relative" style="width:260px;">
                <span class="position-absolute" style="left:8px; top:50%; transform:translateY(-50%); color:#475569;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" class="form-control" placeholder="Search" style="padding-left:32px;">
            </div>
        </div>
    </div>

    <div class="card shadow-sm" style="border:1px solid #e2e8f0;">
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
                            <th style="width:110px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $appt)
                        @php
                            $patient = $appt->patient;
                            $serviceName = optional($appt->serviceRef)->service_name ?? '';
                            $branchName = optional($appt->branchRef)->branch_name ?? '—';
                            $fullName = $patient ? trim(($patient->patient_firstname ?? '').' '.($patient->patient_surname ?? '')) : '';
                        @endphp
                        <tr data-id="{{ $appt->id }}"
                            data-first_name="{{ optional($patient)->patient_firstname ?? ($appt->first_name ?? '') }}"
                            data-surname="{{ optional($patient)->patient_surname ?? ($appt->surname ?? '') }}"
                            data-middlename="{{ optional($patient)->patient_middlename ?? ($appt->middlename ?? '') }}"
                            data-gender="{{ optional($patient)->patient_gender ?? ($appt->gender ?? '') }}"
                            data-birthdate="{{ optional($patient)->patient_birthdate ?? ($appt->birthdate ?? '') }}"
                            data-address="{{ optional($patient)->patient_address ?? ($appt->address ?? '') }}"
                            data-contact="{{ optional($patient)->patient_contact_number ?? ($appt->contact ?? '') }}"
                            data-email="{{ optional($patient)->patient_email ?? '' }}"
                            data-service="{{ $serviceName }}"
                            data-branch="{{ $branchName }}"
                            data-patient_type="{{ $appt->patient_type ?? '' }}"
                            data-referred_by="{{ $appt->referred_by ?? '' }}"
                            data-purpose="{{ $appt->purpose ?? '' }}"
                            data-medical_history="{{ $appt->medical_history ?? '' }}"
                            data-date="{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}"
                            data-time="{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}"
                        >
                            <td data-label="#">{{ sprintf('%02d', $loop->iteration) }}</td>
                            <td data-label="Patient">{{ $fullName }}</td>
                            <td data-label="Type">
                                @php $pt = $appt->patient_type ?? 'Regular'; @endphp
                                <span class="badge bg-secondary-subtle text-secondary-emphasis" style="font-size:.65rem;">{{ strtoupper($pt) }}</span>
                            </td>
                            <td data-label="Service">{{ $serviceName }}</td>
                            <td data-label="Branch">{{ $branchName }}</td>
                            <td data-label="Time">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</td>
                            <td data-label="Date">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</td>
                            <td data-label="Action">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-primary btn-view" title="View">
                                        View
                                    </button>
                                    <form method="POST" action="{{ route('admin.appointment.delete', $appt) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this appointment?');">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No appointments found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end mt-3">
        <div id="paginationInfo" class="text-muted" style="font-size:0.85rem;"></div>
    </div>
</div>

<!-- View Info Modal -->
<div id="viewInfoModal" class="appt-modal hidden">
    <div class="modal-card">
        <button type="button" class="modal-close" onclick="closeViewInfo()">&times;</button>
        <h5 class="fw-bold mb-3">Appointment Details</h5>
        <div class="row g-3 small">
            <div class="col-12 col-sm-6">
                <ul class="list-unstyled mb-0 info-list">
                    <li><strong>Name:</strong> —</li>
                    <li><strong>Surname:</strong> —</li>
                    <li><strong>Middle:</strong> —</li>
                    <li><strong>Age:</strong> —</li>
                    <li><strong>Birthdate:</strong> —</li>
                    <li><strong>Address:</strong> —</li>
                    <li><strong>Contact:</strong> —</li>
                    <li><strong>Email:</strong> —</li>
                </ul>
            </div>
            <div class="col-12 col-sm-6">
                <ul class="list-unstyled mb-0 info-list">
                    <li><strong>Gender:</strong> —</li>
                    <li><strong>Service:</strong> —</li>
                    <li><strong>Branch:</strong> —</li>
                    <li><strong>Patient Type:</strong> —</li>
                    <li><strong>Referred by:</strong> —</li>
                    <li><strong>Purpose:</strong> —</li>
                    <li><strong>Medical history:</strong> —</li>
                    <li><strong>Date:</strong> —</li>
                    <li><strong>Time:</strong> —</li>
                </ul>
            </div>
        </div>
        <div class="text-end mt-4">
            <button class="btn btn-secondary" onclick="closeViewInfo()">Close</button>
        </div>
    </div>
</div>

@push('styles')
<style>
    @import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
    .appointment-table thead th { background:#f1f5f9; font-weight:700; font-size:.75rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
    .appointment-table tbody td { font-size:.85rem; }
    .appointment-table tbody tr:hover { background:#f8fafc; }
    .badge { letter-spacing:.4px; }
    .btn-icon { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; padding:0; }
    .btn-icon + .btn-icon { margin-left:4px; }
    /* Action buttons styling - match staff version */
    .btn-danger { background-color:#dc2626; border-color:#dc2626; }
    .btn-danger:hover { background-color:#b91c1c; border-color:#b91c1c; }
    /* Make action buttons same size */
    .btn-view, .btn-danger { width: 60px; font-size: 0.75rem; padding: 0.25rem 0.5rem; }
    .appt-modal { position:fixed; inset:0; background:rgba(0,0,0,.25); display:flex; align-items:center; justify-content:center; z-index:1050; padding:20px; }
    .appt-modal.hidden { display:none; }
    .modal-card { background:#fff; width:100%; max-width:620px; border-radius:14px; box-shadow:0 4px 24px -4px rgba(0,0,0,.15); padding:28px 26px 26px; position:relative; }
    .modal-close { position:absolute; top:10px; right:14px; background:none; border:none; font-size:24px; line-height:1; cursor:pointer; color:#475569; }
    .info-list li { padding:2px 0; font-size:.8rem; }
    @media (max-width: 768px){
        .appointment-table thead { display:none; }
        .appointment-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
        .appointment-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
        .appointment-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
        .btn-icon { width:34px; height:34px; }
    }
</style>
@endpush

@push('scripts')
<script>
    function closeViewInfo(){
        document.getElementById('viewInfoModal').classList.add('hidden');
    }
    function deleteAppointment(id){
        if(!confirm('Delete this appointment?')) return;
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if(row) row.remove();
    }
    
    
    document.addEventListener('DOMContentLoaded', function(){
        function computeAge(dateStr){
            if(!dateStr) return '';
            var d = new Date(dateStr);
            if(isNaN(d)) return '';
            var diff = Date.now() - d.getTime();
            var age = new Date(diff).getUTCFullYear() - 1970;
            return age >= 0 ? String(age) : '';
        }
        
        // Pagination variables
        let currentPage = 1;
        let entriesPerPage = 10;
        let allAppointments = [];
        let filteredAppointments = [];
        
        // Initialize data
        function initializeData() {
            const rows = document.querySelectorAll('.appointment-table tbody tr');
            allAppointments = Array.from(rows).filter(row => row.querySelector('td[data-label="Patient"]'));
            filteredAppointments = [...allAppointments];
            updateDisplay();
        }
        
        // Filter appointments
        function filterAppointments() {
            const searchTerm = document.querySelector('input[placeholder="Search"]').value.toLowerCase();
            const branchFilter = document.querySelector('select').value.toLowerCase();
            
            filteredAppointments = allAppointments.filter(row => {
                const patientName = row.querySelector('td[data-label="Patient"]').textContent.toLowerCase();
                const branchName = row.querySelector('td[data-label="Branch"]').textContent.toLowerCase();
                
                let showRow = true;
                
                // Search filter
                if (searchTerm && !patientName.includes(searchTerm)) {
                    showRow = false;
                }
                
                // Branch filter
                if (branchFilter && branchFilter !== 'branch' && !branchName.includes(branchFilter)) {
                    showRow = false;
                }
                
                return showRow;
            });
            
            currentPage = 1;
            updateDisplay();
        }
        
        // Update display
        function updateDisplay() {
            const startIndex = (currentPage - 1) * entriesPerPage;
            const endIndex = startIndex + entriesPerPage;
            const currentAppointments = filteredAppointments.slice(startIndex, endIndex);
            
            // Hide all rows
            allAppointments.forEach(row => row.style.display = 'none');
            
            // Show current page rows
            currentAppointments.forEach(row => row.style.display = '');
            
            updatePaginationInfo();
            updatePaginationControls();
        }
        
        // Update pagination info
        function updatePaginationInfo() {
            const total = filteredAppointments.length;
            const start = total === 0 ? 0 : (currentPage - 1) * entriesPerPage + 1;
            const end = Math.min(currentPage * entriesPerPage, total);
            
            document.getElementById('paginationInfo').textContent = 
                `Showing ${start} to ${end} of ${total} entries`;
        }
        
        // Update pagination controls
        function updatePaginationControls() {
            const totalPages = Math.ceil(filteredAppointments.length / entriesPerPage);
            const controlsContainer = document.getElementById('paginationControls');
            
            if (totalPages <= 1) {
                controlsContainer.innerHTML = '';
                return;
            }
            
            let controlsHTML = '<nav><ul class="pagination pagination-sm mb-0">';
            
            // Previous button
            controlsHTML += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage - 1}); return false;">Previous</a>
            </li>`;
            
            // Page numbers
            const startPage = Math.max(1, currentPage - 2);
            const endPage = Math.min(totalPages, currentPage + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                controlsHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
                </li>`;
            }
            
            // Next button
            controlsHTML += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="changePage(${currentPage + 1}); return false;">Next</a>
            </li>`;
            
            controlsHTML += '</ul></nav>';
            controlsContainer.innerHTML = controlsHTML;
        }
        
        // Change page function (global)
        window.changePage = function(page) {
            const totalPages = Math.ceil(filteredAppointments.length / entriesPerPage);
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                updateDisplay();
            }
        };
        
        // Initialize data
        initializeData();
        
        // Add event listeners for search and filter
        const searchInput = document.querySelector('input[placeholder="Search"]');
        const branchSelect = document.querySelector('select');
        const entriesSelect = document.getElementById('entriesPerPage');
        
        if (searchInput) {
            searchInput.addEventListener('input', filterAppointments);
        }
        
        if (branchSelect) {
            branchSelect.addEventListener('change', filterAppointments);
        }
        
        if (entriesSelect) {
            entriesSelect.addEventListener('change', function() {
                entriesPerPage = parseInt(this.value);
                currentPage = 1;
                updateDisplay();
            });
        }
        
        document.querySelectorAll('.btn-view').forEach(function(btn){
            btn.addEventListener('click', function(){
                var row = btn.closest('tr');
                if(!row) return;
                // Map helper
                var get = function(attr){ return (row.getAttribute('data-'+attr) || '').trim(); };
                // Fill modal values
                var map = {
                    Name: get('first_name') || get('fname'),
                    Surname: get('surname'),
                    Middle: get('middlename'),
                    Age: computeAge(get('birthdate')),
                    Birthdate: get('birthdate') ? new Date(get('birthdate')).toLocaleDateString() : '',
                    Address: get('address'),
                    Contact: get('contact'),
                    Email: get('email'),
                    Gender: get('gender'),
                    Service: get('service'),
                    Branch: get('branch'),
                    'Patient Type': (get('patient_type') || '').toUpperCase(),
                    'Referred by': get('referred_by'),
                    Purpose: get('purpose'),
                    'Medical history': get('medical_history'),
                    Date: get('date'),
                    Time: get('time')
                };
                var lis = document.querySelectorAll('#viewInfoModal .info-list li');
                lis.forEach(function(li){
                    var strong = li.querySelector('strong');
                    if(!strong) return; var key = strong.textContent.replace(':','').trim();
                    if(map[key] !== undefined){
                        strong.nextSibling && (strong.nextSibling.textContent = ' ' + (map[key] || '—'));
                    }
                });
                document.getElementById('viewInfoModal').classList.remove('hidden');
            });
        });
    });
</script>
@endpush
@endsection
