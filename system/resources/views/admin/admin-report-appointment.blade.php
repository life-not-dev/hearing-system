@extends('layouts.app')

@section('title', 'Appointment Report | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
    <!-- Header Section - Moved Up -->
    <div style="margin-top: 8px; margin-bottom: 8px;">
        <h3 style="font-weight: bold;">Appointment Report</h3>
        <p class="text-muted mb-0" style="margin-top: -4px;">Summary of all Appointments.</p>
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
                    <input type="date" id="appointmentDate" class="form-control border-start-0" placeholder="Filter by date" style="border-left: none;">
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
                <table class="table table-hover mb-0 report-table align-middle appointment-report-table">
                    <thead>
                        <tr>
                            <th style="width:65px;">No.</th>
                            <th>Patient Name</th>
                            <th>Service</th>
                            <th>Purpose</th>
                            <th>Referred By</th>
                            <th>Branch</th>
                            <th style="width:150px;">Date</th>
                            <th style="width:130px;">Time</th>
                            <th style="width:120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($appointments->count() > 0)
                            @foreach($appointments as $index => $appointment)
                                <tr>
                                    <td data-label="ID" class="fw-semibold">{{ $index + 1 }}</td>
                                    <td data-label="Patient">
                                        @if($appointment->patient)
                                            {{ $appointment->patient->patient_firstname }} {{ $appointment->patient->patient_surname }}
                                        @else
                                            <span class="text-muted">No patient data</span>
                                        @endif
                                    </td>
                                    <td data-label="Service">
                                        @if($appointment->serviceRef)
                                            {{ $appointment->serviceRef->service_name }}
                                        @else
                                            <span class="text-muted">No service data</span>
                                        @endif
                                    </td>
                                    <td data-label="Purpose" style="max-width:260px;">
                                        {{ $appointment->purpose ?? 'No purpose specified' }}
                                    </td>
                                    <td data-label="Referred">
                                        @if($appointment->patient && $appointment->patient->patient_referred_by)
                                            {{ $appointment->patient->patient_referred_by }}
                                        @else
                                            <span class="text-muted">Not specified</span>
                                        @endif
                                    </td>
                                    <td data-label="Branch">
                                        @if($appointment->branchRef)
                                            {{ $appointment->branchRef->branch_name }}
                                        @else
                                            <span class="text-muted">No branch data</span>
                                        @endif
                                    </td>
                                    <td data-label="Date" class="fw-semibold">
                                        {{ $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : 'No date' }}
                                    </td>
                                    <td data-label="Time">
                                        {{ $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('g:i A') : 'No time' }}
                                    </td>
                                    <td data-label="Status">
                                        @if($appointment->status)
                                            @php
                                                $statusClass = match($appointment->status) {
                                                    'confirmed' => 'bg-success',
                                                    'pending' => 'bg-warning',
                                                    'completed' => 'bg-info',
                                                    'canceled' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}" style="font-size:.65rem;">
                                                {{ strtoupper($appointment->status) }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary" style="font-size:.65rem;">UNKNOWN</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="empty-state">
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                                        <h5 class="text-muted mb-2">No appointment report found</h5>
                                        <p class="text-muted mb-0">There are no appointments to display at the moment.</p>
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
    .appointment-report-table thead th { background:#f1f5f9; font-weight:600; font-size:.7rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
    .appointment-report-table tbody td { font-size:.8rem; }
    .appointment-report-table tbody tr:hover { background:#f8fafc; }
    .appointment-report-table td { vertical-align:top; }
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
    @media (max-width: 768px){
        .appointment-report-table thead { display:none; }
        .appointment-report-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
        .appointment-report-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
        .appointment-report-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; padding-right:12px; }
        .appointment-report-table tbody td[style*="max-width"] { max-width:100%!important; }
    }
    @media print { .btn, .card, .sidebar, .topbar { display:none !important; } .container-fluid { padding:0; } }
</style>
@endpush

@push('scripts')
<script>
// Search and Filter Functionality
document.addEventListener('DOMContentLoaded', function(){
    const nameSearch = document.getElementById('nameSearch');
    const appointmentDate = document.getElementById('appointmentDate');
    const clearFilters = document.getElementById('clearFilters');
    const appointmentsTable = document.querySelector('.appointment-report-table');
    
    if (!appointmentsTable) return;
    
    const originalRows = Array.from(appointmentsTable.querySelectorAll('tbody tr'));
    
    function filterAppointments() {
        const nameFilter = (nameSearch.value || '').toLowerCase().trim();
        const selectedDate = appointmentDate.value;
        
        originalRows.forEach(function(row) {
            let showRow = true;
            
            // Skip empty state row
            if (row.classList.contains('empty-state')) {
                return;
            }
            
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
                        // Parse the date (format: "Jan 15, 2024")
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
        const visibleRows = originalRows.filter(row => 
            !row.classList.contains('empty-state') && row.style.display !== 'none'
        );
        const emptyStateRow = appointmentsTable.querySelector('tbody tr.empty-state');
        
        if (visibleRows.length === 0) {
            if (emptyStateRow) {
                emptyStateRow.style.display = '';
            } else {
                // Create empty state row if it doesn't exist
                const newEmptyRow = document.createElement('tr');
                newEmptyRow.className = 'empty-state';
                newEmptyRow.innerHTML = '<td colspan="9" class="text-center py-5"><div class="d-flex flex-column align-items-center"><i class="bi bi-calendar-x text-muted" style="font-size: 3rem; margin-bottom: 1rem;"></i><h5 class="text-muted mb-2">No appointments found</h5><p class="text-muted mb-0">No appointments match your search criteria.</p></div></td>';
                appointmentsTable.querySelector('tbody').appendChild(newEmptyRow);
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
        appointmentDate.value = '';
        originalRows.forEach(function(row) {
            row.style.display = '';
        });
        
        // Hide empty state row
        const emptyStateRow = appointmentsTable.querySelector('tbody tr.empty-state');
        if (emptyStateRow) {
            emptyStateRow.style.display = 'none';
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

// Placeholder for future PDF generation logic
</script>
@endpush
@endsection
