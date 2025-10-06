@extends('layouts.staff')

@section('title', 'Billing | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
    <div style="margin-bottom:18px;">
        <h3 style="font-weight:bold;">Billing</h3>
        <p class="text-muted mb-3" style="font-size:0.9rem;">Complete list of patient bills, payments, and discounts.</p>
        
        <!-- Search and Filter Controls with Show Entries -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div id="paginationInfo" class="text-muted small"></div>
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
    </div>

    <div class="card shadow-sm" style="border:1px solid #e2e8f0;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 billing-table align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:5%;">no.</th>
                            <th style="width:11.11%;">Patient Name</th>
                            <th class="text-center" style="width:11.11%;">Patient Type</th>
                            <th style="width:11.11%;">Services</th>
                            <th class="text-center" style="width:11.11%;">Hearing Aid</th>
                            <th class="text-center" style="width:11.11%;">Date</th>
                            <th class="text-end" style="width:11.11%;">Bill</th>
                            <th class="text-end" style="width:11.11%;">Discount</th>
                            <th class="text-end" style="width:11.11%;">Total Bill</th>
                            <th class="text-center" style="width:11.11%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($rows ?? []) as $r)
                            <tr>
                                <td data-label="#" class="text-center">{{ $r['seq'] }}</td>
                                <td data-label="Patient Name" class="patient-name-cell">{{ $r['patient_name'] }}</td>
                                <td data-label="Patient Type" class="text-center">{{ $r['patient_type'] }}</td>
                                <td data-label="Services">{{ $r['services'] }}</td>
                                <td data-label="Hearing Aid" class="ha-one-line text-center" title="{{ $r['hearing_aid'] }}">{{ $r['hearing_aid'] ?: '—' }}</td>
                                <td data-label="Date" class="text-center">{{ !empty($r['date']) ? \Carbon\Carbon::parse($r['date'])->format('M j, Y') : '—' }}</td>
                                <td data-label="Bill" class="text-end">₱ {{ number_format($r['bill'],2) }}</td>
                                <td data-label="Discount" class="text-end">₱ {{ number_format($r['discount'],2) }}</td>
                                <td data-label="Total Bill" class="text-end">₱ {{ number_format($r['total'],2) }}</td>
                                <td data-label="Actions" class="text-center">
                                    <form method="POST" action="{{ route('staff.billing.delete', ['patientId' => $r['patient_id']]) }}" onsubmit="return confirm('Delete billing record? This removes all session services & hearing aids for this patient.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="9" class="text-center text-muted py-3">No records found</td>
                            </tr>
                        @endforelse
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
</div>

@push('styles')
<style>
    .billing-table thead th { background:#f1f5f9; font-weight:700; font-size:.75rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
    .billing-table tbody td { font-size:.85rem; }
    .billing-table tbody tr:hover:not(.empty-row) { background:#f8fafc; }
    
    /* Patient name styling - straight line display */
    .billing-table .patient-name-cell {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
    }
    
    /* Force Hearing Aid column to a single line with ellipsis */
    .billing-table td.ha-one-line { 
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis;
    }
    
    /* Equal column widths */
    .billing-table {
        table-layout: fixed;
        width: 100%;
    }
    
    /* Search and Filter Styling */
    .input-group-text { background-color: #f8f9fa; border-color: #dee2e6; }
    .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); }
    
    /* Pagination Styling */
    .pagination-sm .page-link { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
    .pagination-sm .page-item:first-child .page-link { border-top-left-radius: 0.25rem; border-bottom-left-radius: 0.25rem; }
    .pagination-sm .page-item:last-child .page-link { border-top-right-radius: 0.25rem; border-bottom-right-radius: 0.25rem; }
    
    @media (max-width: 768px){
        .billing-table thead { display:none; }
        .billing-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
        .billing-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
        .billing-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
        .billing-table tbody .empty-row { display:block; border:none; background:transparent; padding:0; }
        .billing-table tbody .empty-row td { display:block; text-align:center; padding:28px 8px; }
        .billing-table tbody .empty-row td:before { content:''; }
    }
</style>
@endpush

@push('scripts')
<script>
// Pagination and Filtering functionality
document.addEventListener('DOMContentLoaded', function(){
  let allBills = [];
  let filteredBills = [];
  let currentPage = 1;
  let entriesPerPage = 10;
  
  // Initialize data
  function initializeData() {
    const rows = document.querySelectorAll('.billing-table tbody tr:not(.empty-row)');
    allBills = Array.from(rows).map(row => ({
      element: row,
      name: row.querySelector('.patient-name-cell')?.textContent.toLowerCase() || '',
      date: row.querySelector('td[data-label="Date"]')?.textContent || '',
      patientType: row.querySelector('td[data-label="Patient Type"]')?.textContent || '',
      services: row.querySelector('td[data-label="Services"]')?.textContent || '',
      hearingAid: row.querySelector('td[data-label="Hearing Aid"]')?.textContent || '',
      bill: row.querySelector('td[data-label="Bill"]')?.textContent || '',
      discount: row.querySelector('td[data-label="Discount"]')?.textContent || '',
      total: row.querySelector('td[data-label="Total Bill"]')?.textContent || ''
    }));
    filteredBills = [...allBills];
    updateDisplay();
  }
  
  // Filter bills based on search criteria
  function filterBills() {
    const nameFilter = document.getElementById('nameFilter').value.toLowerCase();
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    filteredBills = allBills.filter(bill => {
      // Name filter
      if (nameFilter && !bill.name.includes(nameFilter)) {
        return false;
      }
      
      // Date range filter
      if (dateFrom || dateTo) {
        const billDate = new Date(bill.date);
        if (dateFrom && billDate < new Date(dateFrom)) {
          return false;
        }
        if (dateTo && billDate > new Date(dateTo)) {
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
    const currentBills = filteredBills.slice(startIndex, endIndex);
    
    // Hide all rows first
    allBills.forEach(bill => {
      bill.element.style.display = 'none';
    });
    
    // Show current page rows
    currentBills.forEach(bill => {
      bill.element.style.display = '';
    });
    
    // Update pagination info
    updatePaginationInfo();
    updatePaginationControls();
  }
  
  // Update pagination information
  function updatePaginationInfo() {
    const totalRecords = filteredBills.length;
    const startRecord = totalRecords === 0 ? 0 : (currentPage - 1) * entriesPerPage + 1;
    const endRecord = Math.min(currentPage * entriesPerPage, totalRecords);
    
    document.getElementById('showingStart').textContent = startRecord;
    document.getElementById('showingEnd').textContent = endRecord;
    document.getElementById('totalRecords').textContent = totalRecords;
  }
  
  // Update pagination controls
  function updatePaginationControls() {
    const totalPages = Math.ceil(filteredBills.length / entriesPerPage);
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
  document.getElementById('nameFilter').addEventListener('input', filterBills);
  document.getElementById('dateFrom').addEventListener('change', filterBills);
  document.getElementById('dateTo').addEventListener('change', filterBills);
  
  document.getElementById('entriesPerPage').addEventListener('change', function() {
    entriesPerPage = parseInt(this.value);
    currentPage = 1;
    updateDisplay();
  });
  
  // Initialize everything
  initializeData();
});
</script>
@endpush
@endsection
