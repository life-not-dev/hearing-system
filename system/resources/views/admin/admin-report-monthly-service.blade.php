@extends('layouts.app')

@section('title', 'Monthly Service Revenue | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding: 0 24px;">
    <!-- Header Section -->
    <div style="margin-top: 8px; margin-bottom: 8px;">
        <h3 style="font-weight: bold;">Monthly Service Revenue</h3>
        <p class="text-muted mb-0" style="margin-top: -4px;">Summary of all Services Revenue.</p>
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
                    <input type="text" id="nameSearch" class="form-control border-start-0" placeholder="Search service..." style="border-left: none;">
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
                <table class="table table-hover mb-0 report-table align-middle monthly-service-report-table">
                    <thead>
                        <tr> 
                            <th class="text-center" style="width:60px;">No</th>
                            <th>Service</th>
                            <th class="text-center">Quantity Availed</th>
                            <th class="text-end">Total Original Bill</th>
                            <th class="text-end">Total Discount</th>
                            <th class="text-end">Total Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="empty-state">
                            <td colspan="9" style="text-align:center; color:#888; font-style:italic; height: 60px;">No records found</td>
                        </tr>
                        <!-- Example row structure for when data is available:
                        <tr>
                            <td data-label="No" class="text-center">1</td>
                            <td data-label="Service">Service Name</td>
                            <td data-label="Quantity Availed" class="text-center">5</td>
                            <td data-label="Total Original Bill" class="text-end">₱10,000.00</td>
                            <td data-label="Total Discount" class="text-end">₱1,000.00</td>
                            <td data-label="Total Revenue" class="text-end">₱9,000.00</td>
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
    .monthly-service-report-table thead th { 
        background:#f1f5f9; 
        font-weight:bold; 
        font-size:.7rem; 
        text-transform:uppercase; 
        color:#475569; 
        border-bottom:1px solid #e2e8f0; 
        white-space: nowrap; 
    }
    .monthly-service-report-table tbody td { 
        font-size:.8rem; 
        white-space: nowrap; 
        overflow: hidden; 
        text-overflow: ellipsis; 
    }
    .monthly-service-report-table .text-end { text-align: right !important; }
    .monthly-service-report-table .text-center { text-align: center !important; }
    .monthly-service-report-table .text-start { text-align: left !important; }
    .monthly-service-report-table tbody tr:hover { background:#f8fafc; }
    .monthly-service-report-table td { vertical-align:top; }
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
        .monthly-service-report-table thead { display:none; }
        .monthly-service-report-table tbody tr { 
            display:block; 
            margin-bottom:12px; 
            background:#fff; 
            border:1px solid #e2e8f0; 
            border-radius:8px; 
            padding:10px 12px; 
        }
        .monthly-service-report-table tbody td { 
            display:flex; 
            justify-content:space-between; 
            padding:.35rem .25rem; 
        }
        .monthly-service-report-table tbody td:before { 
            content: attr(data-label); 
            font-weight:600; 
            color:#334155; 
            padding-right:12px; 
        }
        .monthly-service-report-table tbody td[style*="max-width"] { 
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
    const serviceTable = document.querySelector('.monthly-service-report-table');
    
    if (!serviceTable) return;
    
    const originalRows = Array.from(serviceTable.querySelectorAll('tbody tr'));
    
    function filterServices() {
        const nameFilter = (nameSearch.value || '').toLowerCase().trim();
        const dateFilterValue = dateFilter.value || '';
        
        originalRows.forEach(row => {
            const serviceCell = row.querySelector('td:nth-child(2)'); // Service column
            const dateCell = row.querySelector('td:nth-child(3)'); // Date column (if exists)
            
            if (!serviceCell) return;
            
            const serviceName = serviceCell.textContent.toLowerCase();
            const serviceDate = dateCell ? dateCell.textContent : '';
            
            let showRow = true;
            
            // Filter by service name
            if (nameFilter && !serviceName.includes(nameFilter)) {
                showRow = false;
            }
            
            // Filter by date (if date column exists)
            if (dateFilterValue && dateCell && !serviceDate.includes(dateFilterValue)) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    // Event listeners
    if (nameSearch) {
        nameSearch.addEventListener('input', filterServices);
    }
    
    if (dateFilter) {
        dateFilter.addEventListener('change', filterServices);
    }
    
    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            if (nameSearch) nameSearch.value = '';
            if (dateFilter) dateFilter.value = '';
            filterServices();
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
