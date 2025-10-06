@extends('layouts.app')

@section('title', 'Monthly Hearing Aid Revenue | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding: 0 24px;">
    <!-- Header Section -->
    <div style="margin-top: 8px; margin-bottom: 8px;">
        <h3 style="font-weight: bold;">Monthly Hearing Aid Revenue</h3>
        <p class="text-muted mb-0" style="margin-top: -4px;">Summary of all Hearing Aid Revenue.</p>
    </div>
    
    <!-- Search and Filter Controls -->
    <div class="d-flex justify-content-end flex-wrap gap-3 align-items-center" style="margin-bottom: 18px;">
        <div class="d-flex gap-2 align-items-center">
            <!-- Search and Filter Controls -->
            <div class="d-flex gap-2 align-items-center">
                <!-- Name Search -->
                <div class="input-group" style="width: 200px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="nameSearch" class="form-control border-start-0" placeholder="Search brand/model..." style="border-left: none;">
                </div>
                
                <!-- Date Filter -->
                <div class="input-group" style="width: 180px;">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-calendar text-muted"></i>
                    </span>
                    <input type="date" id="dateFilter" class="form-control border-start-0" placeholder="Filter by date" style="border-left: none;">
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
                <table class="table table-hover mb-0 report-table align-middle hearing-aid-report-table">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:60px;">No</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th class="text-center">Quantity Availed</th>
                            <th class="text-end">Total Original Bill</th>
                            <th class="text-end">Total Discount</th>
                            <th class="text-end">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="empty-state">
                            <td colspan="7" style="text-align:center; color:#888; font-style:italic; height: 60px;">No records found</td>
                        </tr>
                        <!-- Example row structure for when data is available:
                        <tr>
                            <td data-label="No" class="text-center">1</td>
                            <td data-label="Brand">Phonak</td>
                            <td data-label="Model">TMAXX600 Chargable</td>
                            <td data-label="Quantity Availed" class="text-center">3</td>
                            <td data-label="Total Original Bill" class="text-end">₱315,000.00</td>
                            <td data-label="Total Discount" class="text-end">₱63,000.00</td>
                            <td data-label="Total Revenue" class="text-end">₱252,000.00</td>
                        </tr>
                        -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
    .hearing-aid-report-table thead th { 
        background:#f1f5f9; 
        font-weight:bold; 
        font-size:.7rem; 
        text-transform:uppercase; 
        color:#475569; 
        border-bottom:1px solid #e2e8f0; 
        white-space: nowrap; 
    }
    .hearing-aid-report-table tbody td { 
        font-size:.8rem; 
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis; 
    }
    .hearing-aid-report-table .text-end { text-align: right !important; }
    .hearing-aid-report-table .text-center { text-align: center !important; }
    .hearing-aid-report-table .text-start { text-align: left !important; }
    .hearing-aid-report-table tbody tr:hover { background:#f8fafc; }
    .hearing-aid-report-table td { vertical-align:top; }
    .btn-outline-secondary { --bs-btn-color:#475569; --bs-btn-border-color:#cbd5e1; }
    .btn-outline-secondary:hover { background:#475569; color:#fff; }
    .empty-state td { border: none !important; }
    .empty-state:hover { background: transparent !important; }
    
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
    
    /* Responsive table adjustments */
    @media (max-width: 768px){
        .hearing-aid-report-table thead { display:none; }
        .hearing-aid-report-table tbody tr { 
            display:block; 
            margin-bottom:12px; 
            background:#fff; 
            border:1px solid #e2e8f0; 
            border-radius:8px; 
            padding:10px 12px; 
        }
        .hearing-aid-report-table tbody td { 
            display:flex; 
            justify-content:space-between; 
            padding:.35rem .25rem; 
        }
        .hearing-aid-report-table tbody td:before { 
            content: attr(data-label); 
            font-weight:600; 
            color:#334155; 
            padding-right:12px; 
        }
        .hearing-aid-report-table tbody td[style*="max-width"] { 
            max-width:100%!important; 
        }
    }
    
    @media print { 
        .btn, .card, .sidebar, .topbar { display:none !important; } 
        .container-fluid { padding:0; } 
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameSearch = document.getElementById('nameSearch');
    const dateFilter = document.getElementById('dateFilter');
    const clearFilters = document.getElementById('clearFilters');
    const hearingAidTable = document.querySelector('.hearing-aid-report-table');
    
    if (!hearingAidTable) return;
    
    const originalRows = Array.from(hearingAidTable.querySelectorAll('tbody tr'));
    
    function filterHearingAids() {
        const nameFilter = (nameSearch.value || '').toLowerCase().trim();
        const dateFilterValue = dateFilter.value || '';
        
        originalRows.forEach(row => {
            const brandCell = row.querySelector('td:nth-child(2)'); // Brand column
            const modelCell = row.querySelector('td:nth-child(3)'); // Model column
            const dateCell = row.querySelector('td:nth-child(4)'); // Date column (if exists)
            
            if (!brandCell || !modelCell) return;
            
            const brandName = brandCell.textContent.toLowerCase();
            const modelName = modelCell.textContent.toLowerCase();
            const hearingAidDate = dateCell ? dateCell.textContent : '';
            
            let showRow = true;
            
            // Filter by brand/model name
            if (nameFilter && !brandName.includes(nameFilter) && !modelName.includes(nameFilter)) {
                showRow = false;
            }
            
            // Filter by date (if date column exists)
            if (dateFilterValue && dateCell && !hearingAidDate.includes(dateFilterValue)) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    // Event listeners
    if (nameSearch) {
        nameSearch.addEventListener('input', filterHearingAids);
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', filterHearingAids);
    }
    
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            if (nameSearch) nameSearch.value = '';
            if (dateFilter) dateFilter.value = '';
            filterHearingAids();
        });
    }
    
    // PDF Download functionality
    const downloadPdfBtn = document.querySelector('.btn-danger');
    if (downloadPdfBtn) {
        downloadPdfBtn.addEventListener('click', function() {
            // Simple print functionality for now
            window.print();
        });
    }
});
</script>
@endpush
@endsection
