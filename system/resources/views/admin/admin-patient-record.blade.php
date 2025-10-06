@extends('layouts.app')

@section('title', 'Patient Record | Kamatage Hearing Aid')

@section('page-title', 'Patient Record')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
    <div style="margin-top:-30px; margin-bottom:18px;">
        <h3 style="font-weight:bold;">Patient Record</h3>
        <p class="text-muted mb-3" style="font-size:0.9rem;">Complete list of registered patients with their records and history.</p>
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
            <div class="input-group" style="width: 200px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" id="nameFilter" class="form-control border-start-0" placeholder="Search by name..." style="border-left: none;">
            </div>
            <div class="input-group" style="width: 180px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-calendar text-muted"></i>
                </span>
                <input type="date" id="dateFilter" class="form-control border-start-0" placeholder="Filter by date" style="border-left: none;">
            </div>
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
                            <th style="width:60px;">No.</th>
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
    
    <div class="d-flex justify-content-end mt-3">
        <div id="paginationInfoBottom" class="text-muted" style="font-size:0.85rem;"></div>
    </div>

</div>

@endsection

@push('styles')
<style>
  .patients-table thead th { background:#f1f5f9; font-weight:600; font-size:.8rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
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
  
  
  @media (max-width: 768px){
    .patients-table thead { display:none; }
    .patients-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
    .patients-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
    .patients-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
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


// Patient Details functionality with Pagination and Filtering
document.addEventListener('DOMContentLoaded', function(){
  // Pagination variables
  let currentPage = 1;
  let entriesPerPage = 10;
  let allPatients = [];
  let filteredPatients = [];
  
  // Initialize data
  function initializeData() {
    const rows = document.querySelectorAll('.patients-table tbody tr');
    allPatients = Array.from(rows).filter(row => row.querySelector('td[data-label="Full Name"]'));
    filteredPatients = [...allPatients];
    updateDisplay();
  }
  
  // Filter patients
  function filterPatients() {
    const nameFilter = (document.getElementById('nameFilter').value || '').toLowerCase().trim();
    const dateFilter = document.getElementById('dateFilter').value;
    
    filteredPatients = allPatients.filter(function(row) {
      let showRow = true;
      
      // Name filter
      if (nameFilter) {
        const nameCell = row.querySelector('td[data-label="Full Name"]');
        const fullName = (nameCell ? nameCell.textContent : '').toLowerCase();
        if (!fullName.includes(nameFilter)) {
          showRow = false;
        }
      }
      
      // Date filter
      if (dateFilter) {
        const dateCell = row.querySelector('td[data-label="Date Registered"]');
        if (dateCell) {
          const registeredDate = dateCell.textContent.trim();
          try {
            // Parse the date (format: "October 4, 2025")
            const parsedDate = new Date(registeredDate);
            const registeredDateStr = parsedDate.toISOString().split('T')[0];
            
            if (registeredDateStr !== dateFilter) {
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
      
      return showRow;
    });
    
    currentPage = 1;
    updateDisplay();
  }
  
  // Update display
  function updateDisplay() {
    const startIndex = (currentPage - 1) * entriesPerPage;
    const endIndex = startIndex + entriesPerPage;
    const currentPatients = filteredPatients.slice(startIndex, endIndex);
    
    // Hide all rows
    allPatients.forEach(row => row.style.display = 'none');
    
    // Show current page rows
    currentPatients.forEach(row => row.style.display = '');
    
    updatePaginationInfo();
    updatePaginationControls();
  }
  
  // Update pagination info
  function updatePaginationInfo() {
    const total = filteredPatients.length;
    const start = total === 0 ? 0 : (currentPage - 1) * entriesPerPage + 1;
    const end = Math.min(currentPage * entriesPerPage, total);
    
    const infoText = `Showing ${start} to ${end} of ${total} entries`;
    document.getElementById('paginationInfoBottom').textContent = infoText;
  }
  
  // Update pagination controls
  function updatePaginationControls() {
    const totalPages = Math.ceil(filteredPatients.length / entriesPerPage);
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
    const totalPages = Math.ceil(filteredPatients.length / entriesPerPage);
    if (page >= 1 && page <= totalPages) {
      currentPage = page;
      updateDisplay();
    }
  };
  
  // Initialize data
  initializeData();
  
  // Event listeners
  const nameFilter = document.getElementById('nameFilter');
  const dateFilter = document.getElementById('dateFilter');
  const entriesSelect = document.getElementById('entriesPerPage');
  
  if (nameFilter) {
    nameFilter.addEventListener('input', filterPatients);
  }
  
  if (dateFilter) {
    dateFilter.addEventListener('change', filterPatients);
  }
  
  if (entriesSelect) {
    entriesSelect.addEventListener('change', function() {
      entriesPerPage = parseInt(this.value);
      currentPage = 1;
      updateDisplay();
    });
  }
  
  // Handle row clicks - redirect to patient details page
  document.querySelectorAll('.patient-row-clickable').forEach(function(row){
    row.addEventListener('click', function(){
      const patientId = row.getAttribute('data-patient-id');
      
      // Redirect to patient details page
      window.location.href = `/admin/patient-record/details/${patientId}`;
    });
  });
  
});
</script>
@endpush
