@extends('layouts.app')

@section('title', 'Billing | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
    <div style="margin-top:-30px; margin-bottom:18px;">
        <h3 style="font-weight:bold;">Billing</h3>
        <p class="text-muted mb-3" style="font-size:0.9rem;">Complete list of billing transactions for all patients.</p>
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

    <div class="card shadow-sm" style="border:1px solid #e2e8f0;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 billing-table align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:5%;">#</th>
                            <th style="width:12.5%;">Patient Name</th>
                            <th class="text-center" style="width:12.5%;">Patient Type</th>
                            <th style="width:12.5%;">Services</th>
                            <th class="text-center" style="width:12.5%;">Hearing Aid</th>
                            <th class="text-center" style="width:12.5%;">Date</th>
                            <th class="text-end" style="width:12.5%;">Bill</th>
                            <th class="text-end" style="width:12.5%;">Discount</th>
                            <th class="text-end" style="width:12.5%;">Total Bill</th>
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
    
    <div class="d-flex justify-content-end mt-3">
        <div id="paginationInfoBottom" class="text-muted" style="font-size:0.85rem;"></div>
    </div>
</div>

@push('styles')
<style>
    .billing-table thead th { background:#f1f5f9; font-weight:600; font-size:.75rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
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
// Billing Pagination and Filtering Functionality
document.addEventListener('DOMContentLoaded', function(){
    // Pagination variables
    let currentPage = 1;
    let entriesPerPage = 10;
    let allBills = [];
    let filteredBills = [];
    
    // Initialize data
    function initializeData() {
        const rows = document.querySelectorAll('.billing-table tbody tr');
        allBills = Array.from(rows).filter(row => row.querySelector('td[data-label="Patient Name"]'));
        filteredBills = [...allBills];
        updateDisplay();
    }
    
    // Filter bills
    function filterBills() {
        const nameFilter = (document.getElementById('nameFilter').value || '').toLowerCase().trim();
        const dateFilter = document.getElementById('dateFilter').value;
        
        filteredBills = allBills.filter(function(row) {
            let showRow = true;
            
            // Name filter
            if (nameFilter) {
                const nameCell = row.querySelector('td[data-label="Patient Name"]');
                const patientName = (nameCell ? nameCell.textContent : '').toLowerCase();
                if (!patientName.includes(nameFilter)) {
                    showRow = false;
                }
            }
            
            // Single date filter
            if (dateFilter) {
                const dateCell = row.querySelector('td[data-label="Date"]');
                if (dateCell) {
                    const billDate = dateCell.textContent.trim();
                    try {
                        // Parse the date (format: "Oct 4, 2025")
                        const parsedDate = new Date(billDate);
                        const billDateStr = parsedDate.toISOString().split('T')[0];
                        
                        if (billDateStr !== dateFilter) {
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
        const currentBills = filteredBills.slice(startIndex, endIndex);
        
        // Hide all rows
        allBills.forEach(row => row.style.display = 'none');
        
        // Show current page rows
        currentBills.forEach(row => row.style.display = '');
        
        updatePaginationInfo();
        updatePaginationControls();
    }
    
    // Update pagination info
    function updatePaginationInfo() {
        const total = filteredBills.length;
        const start = total === 0 ? 0 : (currentPage - 1) * entriesPerPage + 1;
        const end = Math.min(currentPage * entriesPerPage, total);
        
        const infoText = `Showing ${start} to ${end} of ${total} entries`;
        document.getElementById('paginationInfoBottom').textContent = infoText;
    }
    
    // Update pagination controls
    function updatePaginationControls() {
        const totalPages = Math.ceil(filteredBills.length / entriesPerPage);
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
        const totalPages = Math.ceil(filteredBills.length / entriesPerPage);
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
        nameFilter.addEventListener('input', filterBills);
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', filterBills);
    }
    
    if (entriesSelect) {
        entriesSelect.addEventListener('change', function() {
            entriesPerPage = parseInt(this.value);
            currentPage = 1;
            updateDisplay();
        });
    }
});
</script>
@endpush
@endsection
