@extends('layouts.staff')

@section('title', 'Patient Record | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
  <div style="margin-bottom:18px;">
    <h4 style="font-weight:bold;">Patient Record</h4>
    <p class="text-muted mb-3" style="font-size:0.9rem;">Complete list of registered patients with their records and history.</p>
    
    <!-- Search and Filter Controls with Show Entries -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="d-flex align-items-center gap-3">
        <button type="button" class="btn btn-primary" style="font-weight:600;" data-bs-toggle="modal" data-bs-target="#addPatientModal">+ Add Patient</button>
      </div>
      <div class="d-flex align-items-center gap-3">
        <div class="input-group" style="width: 250px;">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" id="nameFilter" class="form-control" placeholder="Search by patient name...">
        </div>
        <input type="date" id="dateFrom" class="form-control" placeholder="From date" style="width: 140px;">
        <div class="d-flex align-items-center gap-1 p-1 border rounded" style="background-color: #f8f9fa;">
          <span class="text-muted small">Show</span>
          <select id="entriesPerPage" class="form-select form-select-sm" style="width: 80px; padding-right: 25px;">
            <option value="5">5</option>
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="300">300</option>
            <option value="400">400</option>
            <option value="500">500</option>
          </select>
          <span class="text-muted small">entries</span>
        </div>
      </div>
    </div>
    
    <!-- Pagination Info -->
    <div class="mb-3">
      <div id="paginationInfo" class="text-muted small"></div>
    </div>
  </div>

  {{-- Flash messages from redirects (e.g., after confirming) --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show small" role="alert">
      <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show small" role="alert">
      <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show small" role="alert">
      <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show small" role="alert">
      <strong class="me-1">Please fix the following:</strong>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card shadow-sm" style="border:1px solid #e2e8f0;">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 patients-table align-middle">
          <thead>
            <tr>
              <th style="width:60px;">no.</th>
              <th style="width:200px;">Full Name</th>
              <th style="width:80px;">Gender</th>
              <th style="width:140px;">Birthdate</th>
              <th style="width:140px;">Date Registered</th>
            </tr>
          </thead>
          <tbody>
            @php($any = false)
            @php($displayCounter = 0)
            @php(
              $formatDate = function($val){
                try {
                  if(!$val) return '';
                  if($val instanceof \Carbon\Carbon) return $val->format('F j, Y');
                  return \Carbon\Carbon::parse($val)->format('F j, Y');
                } catch (Exception $e){
                  return '';
                }
              }
            )
            @foreach($appointments as $a)
              @php($p = $a->patient)
              @php($fileId = $fileIds[$a->id] ?? null)
              @if($p && $fileId) {{-- Only show if explicitly added to patient record --}}
                @php($any = true)
                @php($displayCounter++)
                @php($first = trim($p->patient_firstname ?? ''))
                @php($middle = trim($p->patient_middlename ?? ''))
                @php($last = trim($p->patient_surname ?? ''))
                @php($full = trim($first . ($middle ? ' ' . $middle : '') . ($last ? ' ' . $last : '')))
                @php($gender = $p->patient_gender ?? '')
                @php($bday = $formatDate($p->patient_birthdate ?? null))
                @php($dateReg = $formatDate($a->appointment_date ?? null))
                {{-- $fileId already resolved above; guaranteed not null here --}}
                <tr data-patient-id="{{ $fileId }}" data-full-name="{{ $full }}" data-gender="{{ $gender }}" data-birthdate="{{ $bday }}" data-date-registered="{{ $dateReg }}" class="patient-row-clickable">
                  <td data-label="#">{{ $displayCounter }}</td>
                  <td data-label="Full Name">{{ $full }}</td>
                  <td data-label="Gender">{{ $gender }}</td>
                  <td data-label="Birthdate">{{ $bday }}</td>
                  <td data-label="Date Registered">{{ $dateReg }}</td>
                </tr>
              @endif
            @endforeach
              @if(!$any)
                <tr>
                  <td colspan="5" class="text-center text-muted">No patient records found.</td>
                </tr>
              @endif
          </tbody>
        </table>
      </div>
      
    </div>
  </div>
  
  <!-- Pagination Controls -->
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div id="paginationControls">
      <!-- Pagination will be inserted here by JavaScript -->
    </div>
    <div class="text-muted small">
      Showing <span id="showingStart">0</span> to <span id="showingEnd">0</span> of <span id="totalRecords">0</span> entries
    </div>
  </div>



  <!-- Add Patient Modal -->
  <div class="modal fade" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addPatientModalLabel">Individual Patient Information</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-4">This form is for individual patient information per system.</p>
          <form id="addPatientForm" method="POST" action="{{ route('staff.patient.record.store') }}" novalidate>
            @csrf
            <div class="row">
              <div class="col-12 mb-3">
                <div class="inline-field">
                  <label class="form-label mb-0">First Name <span class="text-danger">*</span></label>
                  <input type="text" name="first_name" class="form-control" placeholder="Enter (Required) First Name here..." required>
                </div>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-12 mb-3">
                <div class="inline-field">
                  <label class="form-label mb-0">Middle Name</label>
                  <input type="text" name="middle_name" class="form-control" placeholder="Enter Middle Name here...">
                </div>
                <div class="invalid-feedback"></div>
              </div>
            </div>
            <div class="row">
              <div class="col-12 mb-3">
                <div class="inline-field">
                  <label class="form-label mb-0">Last Name <span class="text-danger">*</span></label>
                  <input type="text" name="last_name" class="form-control" placeholder="Enter (Required) Last Name here..." required>
                </div>
                <div class="invalid-feedback"></div>
              </div>
            <div class="row">
              <div class="col-12 mb-3">
                <div class="inline-field">
                  <label class="form-label mb-0">Birthday <span class="text-danger">*</span></label>
                  <input type="date" name="birthday" class="form-control" required>
                </div>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-12 mb-3">
                <div class="inline-field">
                  <label class="form-label mb-0">Gender <span class="text-danger">*</span></label>
                  <select name="gender" class="form-select" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                  </select>
                </div>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-12 mb-3">
                <div class="inline-field">
                  <label class="form-label mb-0">Patient Type</label>
                  <select name="patient_type" class="form-select">
                    <option value="">Select Patient Type</option>
                    <option value="PWD">PWD</option>
                    <option value="Senior Citizen">Senior Citizen</option>
                    <option value="Regular">Regular</option>
                  </select>
                </div>
                <div class="invalid-feedback"></div>
              </div>
              <div class="col-12 mb-3">
                <div class="inline-field">
                  <label class="form-label mb-0">Date Registered <span class="text-danger">*</span></label>
                  <input type="date" name="date_registered" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer d-flex justify-content-start gap-2">
          <button type="button" class="btn btn-reset-dark" onclick="resetPatientForm()"><i class="bi bi-arrow-clockwise"></i> Reset</button>
          <button type="submit" class="btn btn-submit"><i class="bi bi-check-lg"></i> Submit Form</button>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
  .patients-table thead th { background:#f1f5f9; font-weight:700; font-size:.8rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
  .patients-table tbody td { font-size:.9rem; }
  .patients-table tbody tr:hover { background:#f8fafc; }
  .badge.status-badge { font-size:.65rem; letter-spacing:.5px; padding:.45em .55em; }
  .icon-btn { line-height:1; display:inline-flex; align-items:center; justify-content:center; gap:0; padding:.25rem .45rem; }
  .card { border-radius:10px; }
  .pagination { --bs-pagination-active-bg:#0d6efd; --bs-pagination-active-border-color:#0d6efd; }
  .pagination .page-link { color:#0d6efd; }
  .pagination .page-item.active .page-link { color:#fff; font-weight:600; }
  .container-fluid .d-flex h4 { margin: 0; }
  .patients-table tbody tr:hover { background-color: #f8fafc; }
  .patient-row-clickable { cursor: pointer; }
  .patient-row-clickable:hover { background-color: #e3f2fd !important; }
  
  /* Modal Styling */
  .modal-content { border-radius:12px; border:none; box-shadow:0 8px 32px rgba(0,0,0,0.12); }
  .modal-header { border-bottom:1px solid #e5e7eb; padding:1.25rem 1.5rem; }
  .modal-title { font-weight:600; color:#111827; font-size:1.25rem; }
  .modal-body { padding:1.5rem; }
  .modal-footer { border-top:1px solid #e5e7eb; padding:1rem 1.5rem; }
  
  /* Form Styling */
  .form-label { font-weight:600; color:#374151; font-size:0.875rem; margin-bottom:0.5rem; }
  .form-control, .form-select { border:1px solid #d1d5db; border-radius:6px; padding:0.625rem 0.75rem; font-size:0.875rem; }
  .form-control:focus, .form-select:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,0.1); }
  .inline-field { display:flex; gap:12px; align-items:center; }
  .inline-field > label { width: 180px; flex: 0 0 auto; }
  .inline-field > .form-control, .inline-field > .form-select { flex: 1 1 auto; }
  
  /* Button Styling */
  .btn-reset { 
    background:#F48600; 
    color:#fff; 
    border:none; 
    padding:0.5rem 1rem; 
    border-radius:6px; 
    font-weight:600; 
    font-size:0.875rem;
    transition:background 0.15s;
  }
  .btn-reset:hover { background:#d97400; color:#fff; }
  
  .btn-reset-dark { 
    background:#212529; 
    color:#fff; 
    border:none; 
    padding:0.5rem 1rem; 
    border-radius:6px; 
    font-weight:600; 
    font-size:0.875rem;
    transition:background 0.15s;
  }
  .btn-reset-dark:hover { background:#1a1e21; color:#fff; }
  .btn-submit { 
    background:#F48600; 
    color:#fff; 
    border:none; 
    padding:0.5rem 1rem; 
    border-radius:6px; 
    font-weight:600; 
    font-size:0.875rem;
    transition:background 0.15s;
  }
  .btn-submit:hover { background:#d97400; color:#fff; }
  
  /* Search and Filter Styling */
  .input-group-text { background-color: #f8f9fa; border-color: #dee2e6; }
  .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); }
  
  /* Pagination Styling */
  .pagination-sm .page-link { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
  .pagination-sm .page-item:first-child .page-link { border-top-left-radius: 0.25rem; border-bottom-left-radius: 0.25rem; }
  .pagination-sm .page-item:last-child .page-link { border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem; }
  
  @media (max-width: 768px){
    .patients-table thead { display:none; }
    .patients-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
    .patients-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
    .patients-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
    .modal-dialog { margin:0.5rem; }
  }
  
  /* Print Styles */
  @media print {
    .btn, .modal { display: none !important; }
    .container-fluid { padding: 0 !important; }
    .card { border: 1px solid #000 !important; box-shadow: none !important; }
    .patients-table tbody tr:hover { background-color: transparent !important; }
  }
</style>
@endpush

@push('scripts')
<script>
// Auto-dismiss flash alerts only
document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.alert').forEach(function(el){
    setTimeout(function(){
      try { bootstrap.Alert.getOrCreateInstance(el).close(); } catch(e) { el.remove(); }
    }, 2500);
  });
});

function resetPatientForm(){
  const form = document.getElementById('addPatientForm');
  if(form){ form.reset(); }
}

// Pagination and Filtering functionality
document.addEventListener('DOMContentLoaded', function(){
  let allPatients = [];
  let filteredPatients = [];
  let currentPage = 1;
  let entriesPerPage = 10;
  
  // Initialize data
  function initializeData() {
    const rows = document.querySelectorAll('.patient-row-clickable');
    allPatients = Array.from(rows).map(row => ({
      element: row,
      name: row.getAttribute('data-full-name').toLowerCase(),
      gender: row.getAttribute('data-gender'),
      birthdate: row.getAttribute('data-birthdate'),
      dateRegistered: row.getAttribute('data-date-registered'),
      patientId: row.getAttribute('data-patient-id')
    }));
    filteredPatients = [...allPatients];
    updateDisplay();
  }
  
  // Filter patients based on search criteria
  function filterPatients() {
    const nameFilter = document.getElementById('nameFilter').value.toLowerCase();
    const dateFrom = document.getElementById('dateFrom').value;
    
    filteredPatients = allPatients.filter(patient => {
      // Name filter
      if (nameFilter && !patient.name.includes(nameFilter)) {
        return false;
      }
      
      // Date filter (using date registered)
      if (dateFrom) {
        const patientDate = new Date(patient.dateRegistered);
        if (patientDate < new Date(dateFrom)) {
          return false;
        }
      }
      
      return true;
    });
    
    currentPage = 1; // Reset to first page when filtering
    updateDisplay();
  }
  
  // Update display based on current page and entries per page
  function updateDisplay() {
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = startIndex + entriesPerPage;
    const currentPatients = filteredPatients.slice(startIndex, endIndex);
    
    // Hide all rows first
    allPatients.forEach(patient => {
      patient.element.style.display = 'none';
    });
    
    // Show current page rows
    currentPatients.forEach(patient => {
      patient.element.style.display = '';
    });
    
    // Update pagination info
    updatePaginationInfo();
    updatePaginationControls();
  }
  
  // Update pagination information
  function updatePaginationInfo() {
    const totalRecords = filteredPatients.length;
    const startRecord = totalRecords === 0 ? 0 : (currentPage - 1) * entriesPerPage + 1;
    const endRecord = Math.min(currentPage * entriesPerPage, totalRecords);
    
    document.getElementById('showingStart').textContent = startRecord;
    document.getElementById('showingEnd').textContent = endRecord;
    document.getElementById('totalRecords').textContent = totalRecords;
  }
  
  // Update pagination controls
  function updatePaginationControls() {
    const totalPages = Math.ceil(filteredPatients.length / entriesPerPage);
    const paginationControls = document.getElementById('paginationControls');
    
    if (totalPages <= 1) {
      paginationControls.innerHTML = '';
      return;
    }
    
    let paginationHTML = '<nav><ul class="pagination pagination-sm mb-0">';
    
    // Previous button
    paginationHTML += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
    </li>`;
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
      paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
      if (startPage > 2) {
        paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
      }
    }
    
    for (let i = startPage; i <= endPage; i++) {
      paginationHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>`;
    }
    
    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
      }
      paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
    }
    
    // Next button
    paginationHTML += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
    </li>`;
    
    paginationHTML += '</ul></nav>';
    paginationControls.innerHTML = paginationHTML;
    
    // Add event listeners to pagination links
    paginationControls.querySelectorAll('.page-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const page = parseInt(this.getAttribute('data-page'));
        if (page >= 1 && page <= totalPages && page !== currentPage) {
          currentPage = page;
          updateDisplay();
        }
      });
    });
  }
  
  // Event listeners
  document.getElementById('nameFilter').addEventListener('input', filterPatients);
  document.getElementById('dateFrom').addEventListener('change', filterPatients);
  
  document.getElementById('entriesPerPage').addEventListener('change', function() {
    entriesPerPage = parseInt(this.value);
    currentPage = 1;
    updateDisplay();
  });
  
  // Patient Details functionality
  document.querySelectorAll('.patient-row-clickable').forEach(function(row){
    row.addEventListener('click', function(){
      const patientId = row.getAttribute('data-patient-id');
      
      // Redirect to patient details page
      window.location.href = `/staff/patient-record/details/${patientId}`;
    });
  });
  
  // Initialize everything
  initializeData();
});

</script>
@endpush
