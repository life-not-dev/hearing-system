@extends('layouts.app')

@section('title', 'Billing Report | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding: 0 24px;">
    <!-- Header Section - Moved Up -->
    <div style="margin-top: 5px; margin-bottom: 5px;">
        <h3 style="font-weight: bold;">Billing Report</h3>
        <p class="text-muted mb-0" style="margin-top: -4px;">Monitor and review all charges, discounts, and final totals.</p>
    </div>
    
    <!-- Search and Filter Controls - Kept in Original Position -->
    <div class="d-flex justify-content-end flex-wrap gap-3 align-items-center" style="margin-bottom: 18px;">
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
                    <input type="date" id="billingDate" class="form-control border-start-0" placeholder="Filter by date" style="border-left: none;">
                </div>
                
                <!-- Clear Filter Button -->
                <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm" title="Clear all filters">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
            
            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button class="btn btn-danger"><i class="bi bi-file-earmark-pdf me-1"></i>Download PDF</button>
            </div>
        </div>
    </div>
    <div class="card shadow-sm" style="border:1px solid #e2e8f0;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 report-table align-middle billing-report-table">
                    <thead>
                        <tr>
                            <th style="width:2%;">No.</th>
                            <th style="width:8.33%;">Patient Name</th>
                            <th style="width:8.33%;">Patient Type</th>
                            <th style="width:8.33%;">Services</th>
                            <th style="width:8.33%;">Hearing Aid</th>
                            <th style="width:8.33%;">Billing Date</th>
                            <th style="width:8.33%;" class="text-end">Original Bill</th>
                            <th style="width:8.33%;" class="text-end">Discount</th>
                            <th style="width:8.33%;" class="text-end">Total Bill</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($rows) && count($rows) > 0)
                            @foreach($rows as $index => $row)
                                <tr>
                                    <td data-label="No." class="fw-semibold">{{ $index + 1 }}</td>
                                    <td data-label="Patient Name">{{ $row['patient_name'] }}</td>
                                    <td data-label="Patient Type">
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis" style="font-size:.65rem;">
                                            {{ strtoupper($row['patient_type']) }}
                                        </span>
                                    </td>
                                    <td data-label="Services">{{ $row['services'] ?: '—' }}</td>
                                    <td data-label="Hearing Aid">{{ $row['hearing_aid'] ?: '—' }}</td>
                                    <td data-label="Billing Date">
                                        {{ !empty($row['date']) ? \Carbon\Carbon::parse($row['date'])->format('M d, Y') : '—' }}
                                    </td>
                                    <td data-label="Original Bill" class="text-end">₱{{ number_format($row['bill'], 2) }}</td>
                                    <td data-label="Discount" class="text-end">₱{{ number_format($row['discount'], 2) }}</td>
                                    <td data-label="Total Bill" class="text-end fw-semibold">₱{{ number_format($row['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="empty-state">
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-receipt text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                                        <h5 class="text-muted mb-2">No billing report found</h5>
                                        <p class="text-muted mb-0">There are no billing records to display at the moment.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
    .billing-report-table thead th { background:#f1f5f9; font-weight:600; font-size:.7rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; white-space: nowrap; }
    .billing-report-table tbody td { font-size:.8rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .billing-report-table .text-end { text-align: right !important; }
    .billing-report-table .text-center { text-align: center !important; }
    .billing-report-table .text-start { text-align: left !important; }
    
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
    .billing-report-table tbody tr:hover { background:#f8fafc; }
    .billing-report-table td { vertical-align:top; }
    .btn-outline-secondary { --bs-btn-color:#475569; --bs-btn-border-color:#cbd5e1; }
    .btn-outline-secondary:hover { background:#475569; color:#fff; }
    .empty-state td { border: none !important; }
    .empty-state:hover { background: transparent !important; }
    @media (max-width: 768px){
        .billing-report-table thead { display:none; }
        .billing-report-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
        .billing-report-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
        .billing-report-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; padding-right:12px; }
        .billing-report-table tbody td[style*="max-width"] { max-width:100%!important; }
    }
    @media print { .btn, .card, .sidebar, .topbar { display:none !important; } .container-fluid { padding:0; } }
</style>
@endpush

@push('scripts')
<script>
// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function(){
    const nameSearch = document.getElementById('nameSearch');
    const billingDate = document.getElementById('billingDate');
    const clearFilters = document.getElementById('clearFilters');
    const billingTable = document.querySelector('.billing-report-table');
    
    if (!billingTable) return;
    
    const originalRows = Array.from(billingTable.querySelectorAll('tbody tr'));
    
    function filterBilling() {
        const nameFilter = (nameSearch.value || '').toLowerCase().trim();
        const selectedDate = billingDate.value;
        
        originalRows.forEach(function(row) {
            let showRow = true;
            
            // Skip empty state row
            if (row.classList.contains('empty-state')) {
                return;
            }
            
            // Name filter
            if (nameFilter) {
                const nameCell = row.querySelector('td[data-label="Patient Name"]');
                const fullName = (nameCell ? nameCell.textContent : '').toLowerCase();
                if (!fullName.includes(nameFilter)) {
                    showRow = false;
                }
            }
            
            // Date filter
            if (selectedDate) {
                const dateCell = row.querySelector('td[data-label="Billing Date"]');
                if (dateCell) {
                    const billingDate = dateCell.textContent.trim();
                    try {
                        // Parse the date (format: "Sep 30, 2025")
                        const parsedDate = new Date(billingDate);
                        const billingDateStr = parsedDate.toISOString().split('T')[0];
                        
                        if (billingDateStr !== selectedDate) {
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
        const visibleRows = originalRows.filter(row => 
            !row.classList.contains('empty-state') && row.style.display !== 'none'
        );
        const emptyStateRow = billingTable.querySelector('tbody tr.empty-state');
        
        if (visibleRows.length === 0) {
            if (emptyStateRow) {
                emptyStateRow.style.display = '';
            } else {
                // Create empty state row if it doesn't exist
                const newEmptyRow = document.createElement('tr');
                newEmptyRow.className = 'empty-state';
                newEmptyRow.innerHTML = '<td colspan="9" class="text-center py-5"><div class="d-flex flex-column align-items-center"><i class="bi bi-receipt text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i><h5 class="text-muted mb-2">No billing records found</h5><p class="text-muted mb-0">No billing records match your search criteria.</p></div></td>';
                billingTable.querySelector('tbody').appendChild(newEmptyRow);
            }
        } else {
            // Hide empty state row if it exists
            if (emptyStateRow) {
                emptyStateRow.style.display = 'none';
            }
        }
    }
    
    function clearAllFilters() {
        nameSearch.value = '';
        billingDate.value = '';
        originalRows.forEach(function(row) {
            row.style.display = '';
        });
        
        // Hide empty state row
        const emptyStateRow = billingTable.querySelector('tbody tr.empty-state');
        if (emptyStateRow) {
            emptyStateRow.style.display = 'none';
        }
    }
    
    // Event listeners
    if (nameSearch) {
        nameSearch.addEventListener('input', filterBilling);
    }
    
    if (billingDate) {
        billingDate.addEventListener('change', filterBilling);
    }
    
    if (clearFilters) {
        clearFilters.addEventListener('click', clearAllFilters);
    }
});

// Example: Add JS for PDF download if needed
// document.querySelector('.btn-danger').addEventListener('click', function() {
//     // Implement PDF download logic here
// });
</script>
@endpush
@endsection
