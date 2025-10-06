@extends('layouts.staff')

@section('content')
<style>
    .main-content h4 { margin: 0; }
    /* Match Services table look */
    /* Thin/subtle table styling (match staff-appointment-record) */
    /* Card wrapper similar to Appointment Record page */
    .appointments-card { border:1px solid #e9ecef; box-shadow: 0 2px 8px rgba(0,0,0,0.03); border-radius:6px; background:#fff; }
    .appointments-card .card-body { padding:0; }
    .appointments-table thead th { background:#f1f5f9; font-weight:600; font-size:.8rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; padding: 8px 12px; }
    /* Slightly reduced padding to make the table look thinner */
    .appointments-table tbody td { font-size:.9rem; padding: 8px 12px; border-top: 1px solid #e6e6e6; }
    .appointments-table tbody tr:hover { background:#f8fafc; }
    .appointment-item { margin-bottom: 10px; }
    /* Improve icon sizing and alignment on desktop */
    #appointmentsTable .btn .bi { vertical-align: -0.125em; font-size: 1rem; }
    .actions-cell { white-space: nowrap; }
    /* Modal custom styles to mirror screenshot */
    .appt-modal { border: 1px solid #2f2f2f; box-shadow: 0 4px 22px rgba(0,0,0,0.18); }
    .appt-panel { border: 1px solid #2f2f2f; background: #fff; }
    .appt-panel .form-control, .appt-panel .form-select, .appt-panel .input-group-text { border: 1px solid #2f2f2f; border-radius: 0; }
    /* 1px table borders inside the panel */
    .appt-panel .table-bordered, .appt-panel .table-bordered > :not(caption) > * { border-width: 1px; }
    .appt-panel .table-bordered th, .appt-panel .table-bordered td { border: 1px solid #2f2f2f; }
    /* Service table inputs keep 1px and compact look */
    .appt-panel .abr-table .form-control,
    .appt-panel .assr-table .form-control,
    .appt-panel .speech-table .form-control,
    .appt-panel .tym-table .form-control,
    .appt-panel .oae-table .form-control { border: 1px solid #2f2f2f; border-radius: 3px; }
    /* Responsive tweaks for form controls inside service tables */
    @media (max-width: 992px) {
        .appt-panel .abr-table th, .appt-panel .abr-table td,
        .appt-panel .assr-table th, .appt-panel .assr-table td,
        .appt-panel .speech-table th, .appt-panel .speech-table td,
        .appt-panel .tym-table th, .appt-panel .tym-table td,
        .appt-panel .oae-table th, .appt-panel .oae-table td { padding: .25rem; font-size: .9rem; }
        .appt-panel .abr-table .form-control,
        .appt-panel .assr-table .form-control,
        .appt-panel .speech-table .form-control,
        .appt-panel .tym-table .form-control,
        .appt-panel .oae-table .form-control { min-width: 72px; padding: .25rem .4rem; font-size: .9rem; }
    }
    @media (max-width: 576px) {
        .appt-panel .abr-table .form-control,
        .appt-panel .assr-table .form-control,
        .appt-panel .speech-table .form-control,
        .appt-panel .tym-table .form-control,
        .appt-panel .oae-table .form-control { min-width: 56px; font-size: .85rem; }
    }
    /* Focus (Select) state similar to screenshot: blue ring */
    .appt-panel .form-control:focus, .appt-panel .form-select:focus {
        border-color: #0d6efd; /* Bootstrap primary */
        box-shadow: 0 0 0 .2rem rgba(13,110,253,.25);
    }
    .btn-confirm { background: #27ae60; color: #fff; font-weight: 600; padding: .45rem 1.25rem; border: 1px solid #2f2f2f; border-radius: 0; }
    .btn-confirm:hover { background: #219150; color: #fff; }
    .modal-backdrop.show { opacity: .2; }

    /* Hover effects for fields (subtle blue ring, 1px border retained) */
    .appt-panel .form-control,
    .appt-panel .form-select {
        transition: box-shadow .12s ease, border-color .12s ease, background-color .12s ease;
    }
    .appt-panel .form-control:hover,
    .appt-panel .form-select:hover {
        border-color: #0d6efd;
        box-shadow: 0 0 0 .15rem rgba(13,110,253,.25);
        background-color: #fff;
    }
    .appt-panel .input-group-text { transition: box-shadow .12s ease, border-color .12s ease; }
    .appt-panel .input-group-text:hover { border-color: #0d6efd; box-shadow: 0 0 0 .12rem rgba(13,110,253,.2); }

    /* Birthdate group responsiveness */
    .bd-group { display: flex; gap: .5rem; flex-wrap: wrap; }
    .bd-group .form-control { flex: 1 1 100px; min-width: 80px; }

    @media (max-width: 768px) {
        .appointments-table thead { display: none; }
        .appointments-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
        .appointments-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
        .appointments-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
        /* Make action icons responsive and aligned */
        .appointments-table td.actions-cell { align-items: center; gap: 10px; }
        .appointments-table td.actions-cell .btn { display: inline-flex; align-items: center; justify-content: center; padding: .5rem .65rem; }
        .appointments-table td.actions-cell .bi { font-size: 1.15rem; }
    }

    /* Header spacing tweaks */
    .header-bar { padding-top: 6px; padding-bottom: 6px; }
    .header-bar .btn { padding: .45rem .9rem; }
    
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
        .header-bar .d-flex.gap-2 {
            flex-wrap: wrap;
            gap: 0.5rem !important;
        }
        .header-bar .input-group {
            width: 160px !important;
        }
    }
    
    @media (max-width: 992px) {
        .header-bar {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        .header-bar .d-flex.gap-2 {
            width: 100%;
            justify-content: space-between;
        }
        .header-bar .input-group {
            width: 140px !important;
        }
    }
    
    @media (max-width: 768px) {
        .header-bar .d-flex.gap-2 {
            flex-direction: column;
            width: 100%;
            gap: 0.75rem !important;
        }
        .header-bar .input-group {
            width: 100% !important;
        }
        .header-bar .btn {
            width: 100%;
        }
    }
    /* Service tables (compact, match services pages) */
    .oae-table thead th { font-size: 0.85rem; padding-top: .4rem; padding-bottom: .4rem; }
    .oae-table thead th:first-child,
    .oae-table tbody td:first-child { text-align: center; vertical-align: middle; }
    .tym-table thead th { font-size: 0.9rem; padding-top:.45rem; padding-bottom:.45rem; }
    .tym-table th, .tym-table td { text-align: center; vertical-align: middle; }
    .pta-table thead th { font-size: 0.85rem; padding-top:.4rem; padding-bottom:.4rem; white-space: nowrap; }
    .pta-table th, .pta-table td { text-align: center; vertical-align: middle; }
    /* ABR table compact styles */
    .abr-col-n { width: 140px; min-width: 140px; text-align: center; }
    .abr-table thead th { font-size: 0.85rem; padding-top: .4rem; padding-bottom: .4rem; }
    .abr-table tbody td { padding-top: .35rem; padding-bottom: .35rem; }
    .abr-table .form-control { padding: .25rem .5rem; height: 32px; }
    /* ASSR tables */
    .assr-table thead th { font-size: 0.9rem; padding-top: .45rem; padding-bottom: .45rem; }
    .assr-table tbody td { padding-top: .4rem; padding-bottom: .4rem; }
    .assr-table th, .assr-table td { text-align: center; vertical-align: middle; }
    .assr-col-n { width: 100px; min-width: 100px; }
    .assr-grid thead th { white-space: nowrap; }
    .assr-grid thead th, .assr-grid tbody td { padding-top: .65rem; padding-bottom: .65rem; }
    .assr-grid input.form-control { height: 2.1rem; padding: .275rem .5rem; font-size: .95rem; }
    /* Speech/Play */
    .speech-table thead th { font-size: 0.85rem; padding-top:.45rem; padding-bottom:.45rem; }
    .speech-table tbody td, .speech-table tbody th { padding-top:.4rem; padding-bottom:.4rem; }
    .speech-table th, .speech-table td { vertical-align: middle; text-align:center; }
    /* Test section header inside modal container */
    .test-section-title { text-align:center; font-size:1.1rem; font-weight:700; margin:2px 0 8px 0; }
    
    /* Checkmark animation */
    .checkmark-animation {
        animation: checkmarkScale 0.6s ease-in-out;
    }
    
    .checkmark-circle {
        stroke-dasharray: 150;
        stroke-dashoffset: 150;
        animation: checkmarkCircle 0.6s ease-in-out forwards;
    }
    
    .checkmark-path {
        stroke-dasharray: 30;
        stroke-dashoffset: 30;
        animation: checkmarkPath 0.3s ease-in-out 0.3s forwards;
    }
    
    @keyframes checkmarkScale {
        0% { transform: scale(0); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    @keyframes checkmarkCircle {
        0% { stroke-dashoffset: 150; }
        100% { stroke-dashoffset: 0; }
    }
    
    @keyframes checkmarkPath {
        0% { stroke-dashoffset: 30; }
        100% { stroke-dashoffset: 0; }
    }
</style>
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-3 header-bar" style="margin-top: -80px;">
        <div>
            <h4 class="fw-bold">List of Patient Appointment</h4>
            <p class="text-muted mb-0">Recently registered patients awaiting their first consultation</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button id="addAppointmentBtn" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newAppointmentModal">Add Appointment</button>
        </div>
    </div>

    <!-- Search and Filter Controls - Separate from header -->
    <div class="d-flex justify-content-end mb-3" style="margin-top: 10px;">
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
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show auto-fade small" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show auto-fade small" role="alert">
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

    <div class="card shadow-sm appointments-card" style="margin-top: 20px;">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table id="appointmentsTable" class="table table-hover mb-0 appointments-table align-middle">
        <thead style="background:#e9e9e9;">
            <tr>
                <th class="text-uppercase" style="font-weight:700; width:50px;">No.</th>
                <th class="text-uppercase" style="font-weight:700;">Fullname</th>       
                <th class="text-uppercase" style="font-weight:700;">Services</th>
                <th class="text-uppercase" style="font-weight:700;">Email</th>
                <th class="text-uppercase" style="font-weight:700;">Appointment time</th>
                <th class="text-uppercase" style="font-weight:700;">Appointment date</th>
                <th class="text-uppercase" style="font-weight:700;">Status</th>
                <th class="text-uppercase text-center" style="font-weight:700;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appt)
            @php
                $patient = $appt->patient;
                $serviceName = optional($appt->serviceRef)->service_name ?? '';
                $branchName = optional($appt->branchRef)->branch_name ?? '';
                $fullName = $patient ? trim(($patient->patient_firstname ?? '').' '.($patient->patient_surname ?? '')) : '';
                $emailVal = $patient->patient_email ?? '';
                $contactVal = $patient->patient_contact_number ?? '';
                $addressVal = $patient->patient_address ?? '';
                $genderVal = $patient->patient_gender ?? '';
                $birthdateVal = $patient->patient_birthdate ?? '';
                $firstNameVal = $patient->patient_firstname ?? '';
                $surnameVal = $patient->patient_surname ?? '';
                $middlenameVal = $patient->patient_middlename ?? '';
                // Normalize service name to a key used by test forms
                $svcLower = strtolower(trim($serviceName));
                $svcKey = 'oae';
                if (str_contains($svcLower, 'oae') || str_contains($svcLower, 'oto') || str_contains($svcLower, 'emission') || str_contains($svcLower, 'emession')) { $svcKey = 'oae'; }
                elseif (str_contains($svcLower, 'tym')) { $svcKey = 'tym'; }
                elseif (str_contains($svcLower, 'abr') || str_contains($svcLower, 'brain')) { $svcKey = 'abr'; }
                elseif (str_contains($svcLower, 'assr') || str_contains($svcLower, 'steady')) { $svcKey = 'assr'; }
                elseif (str_contains($svcLower, 'speech')) { $svcKey = 'speech'; }
                elseif (str_contains($svcLower, 'play')) { $svcKey = 'play'; }
                elseif (str_contains($svcLower, 'hearing') || str_contains($svcLower, 'fitting') || str_contains($svcLower, 'aid')) { $svcKey = 'hearing'; }
                elseif (str_contains($svcLower, 'pta') || str_contains($svcLower, 'puretone') || str_contains($svcLower, 'puretone') || str_contains($svcLower, 'puretone') || str_contains($svcLower, 'audiometry')) { $svcKey = 'pta'; }
            @endphp
            <tr data-id="{{ $appt->id }}"
                data-first_name="{{ $firstNameVal }}"
                data-surname="{{ $surnameVal }}"
                data-middlename="{{ $middlenameVal }}"
                data-gender="{{ $genderVal }}"
                data-birthdate="{{ $birthdateVal }}"
                data-address="{{ $addressVal }}"
                data-contact="{{ $contactVal }}"
                data-patient_type="{{ $appt->patient_type ?? '' }}"
                data-branch="{{ $branchName }}"
                data-referred_by="{{ $appt->referred_by ?? '' }}"
                data-purpose="{{ $appt->purpose ?? '' }}"
                data-medical_history="{{ $appt->medical_history ?? '' }}"
                data-patient_id="{{ $appt->patient_id ?? '' }}"
                data-service_name="{{ $serviceName }}"
                data-service_key="{{ $svcKey }}"
            >
                <td data-label="No.">{{ sprintf('%02d', $loop->iteration) }}</td>
                <td data-label="Fullname">{{ $fullName }}</td>
                <td data-label="Services">{{ $serviceName }}</td>
                <td data-label="Email">{{ $emailVal }}</td>
                <td data-label="Time" class="text-nowrap">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('g:i A') }}</td>
                <td data-label="Date">{{ \Carbon\Carbon::parse($appt->appointment_date)->format('M d, Y') }}</td>
                @php 
                    $status = $appt->status ?? 'Pending';
                    $statusClass = $status === 'completed' ? 'bg-success-subtle text-success-emphasis' : 'bg-secondary-subtle text-secondary-emphasis';
                    $statusText = $status === 'completed' ? 'Completed' : strtoupper($status);
                @endphp
                <td data-label="Status">
                    <span class="badge {{ $statusClass }}" style="font-size:.75rem;">{{ $statusText }}</span>
                </td>
                <td data-label="Action" class="text-center actions-cell">
                    <button type="button" class="btn btn-sm btn-primary btn-test">Test</button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-4">No appointments yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="d-flex align-items-center gap-3">
            <div id="paginationInfo" class="text-muted" style="font-size:0.85rem;"></div>
            <div id="paginationControls"></div>
        </div>
    </div>

    <!-- New Appointment Modal (Styled to match screenshot) -->
    <div class="modal fade" id="newAppointmentModal" tabindex="-1" aria-labelledby="newAppointmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content appt-modal">
                <form id="newAppointmentForm" novalidate>
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold" id="newAppointmentLabel">New Patient Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-2">
                        @csrf
                        <div class="appt-panel p-3">
                            <div class="row g-3">
                                <!-- Left column -->
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label class="form-label">First Name:</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname" required>
                                        <div class="invalid-feedback">Please enter first name.</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Surname:</label>
                                        <input type="text" class="form-control" id="surname" name="surname" required>
                                        <div class="invalid-feedback">Please enter surname.</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Middle:</label>
                                        <input type="text" class="form-control" id="middle" name="middle">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Age:</label>
                                        <input type="number" min="0" class="form-control" id="age" name="age">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Birthdate:</label>
                                        <div class="bd-group">
                                            <input type="number" min="1" max="31" class="form-control" id="bd_day" name="bd_day" placeholder="DD">
                                            <input type="number" min="1" max="12" class="form-control" id="bd_month" name="bd_month" placeholder="MM">
                                            <input type="number" min="1900" max="2100" class="form-control" id="bd_year" name="bd_year" placeholder="YYYY">
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Address:</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Contact:</label>
                                        <input type="tel" class="form-control" id="phone" name="phone">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Email:</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="invalid-feedback">Please enter a valid email.</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Gender:</label>
                                        <select id="gender" name="gender" class="form-select">
                                            <option value="">Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Right column -->
                                <div class="col-lg-6">
                                    <div class="mb-2">
                                        <label class="form-label">Services:</label>
                                        <select id="service" name="service" class="form-select" required>
                                            <option value="">Select service</option>
                                            <option value="PTA - Pureton Audiometry">PTA - Pureton Audiometry</option>
                                            <option value="Speech Audiometry">Speech Audiometry</option>
                                            <option value="Tympanometry">Tympanometry</option>
                                            <option value="ABR - Auditory Brain Response">ABR - Auditory Brain Response</option>
                                            <option value="ASSR - Auditory State Steady Response">ASSR - Auditory State Steady Response</option>
                                            <option value="OAE - Oto Acoustic with Emession">OAE - Oto Acoustic with Emession</option>
                                            <option value="Aided Testing">Aided Testing</option>
                                            <option value="Play Audiometry">Play Audiometry</option>
                                            <option value="Hearing Aid Fitting">Hearing Aid Fitting</option>
                                        </select>
                                        <div class="invalid-feedback">Select a service.</div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Patient type :</label>
                                        <select id="patient_type" name="patient_type" class="form-select">
                                            <option value="">Select type</option>
                                            <option value="PWD">PWD</option>
                                            <option value="Senior Citizen">Senior Citizen</option>
                                            <option value="Regular">Regular</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Branch :</label>
                                        <select id="branch" name="branch" class="form-select">
                                            <option value="">Select branch</option>
                                            <option value="CDO Branch">CDO Branch</option>
                                            <option value="Davao City Branch">Davao City Branch</option>
                                            <option value="Butuan City Branch">Butuan City Branch</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Referred by :</label>
                                        <input type="text" id="referred_by" name="referred_by" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Purpose :</label>
                                        <input type="text" id="purpose" name="purpose" class="form-control">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Medical History :</label>
                                        <textarea id="medical_history" name="medical_history" class="form-control" rows="3"></textarea>
                                    </div>
                                    <div class="mt-2 mb-1">
                                        <h5 class="fw-bold mb-2">Schedule</h5>
                                        <input type="date" class="form-control mb-2" id="appointment_date" name="appointment_date" placeholder="mm/dd/yyyy" required>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white" style="min-width:70px;">TIME</span>
                                            <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                                            <span class="input-group-text bg-white"><i class="bi bi-clock"></i></span>
                                        </div>
                                        <div class="invalid-feedback d-block" style="display:none;">Choose date and time.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" class="btn btn-confirm">Confirm</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Info Modal -->
    <div class="modal fade" id="viewInfoModal" tabindex="-1" aria-labelledby="viewInfoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewInfoLabel">View Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Name :</strong> <span id="vi_name"></span></p>
                            <p><strong>Surname :</strong> <span id="vi_surname"></span></p>
                            <p><strong>Middle :</strong> <span id="vi_middle"></span></p>
                            <p><strong>Service :</strong> <span id="vi_service"></span></p>
                            <p><strong>Email :</strong> <span id="vi_email"></span></p>
                            <p><strong>Contact :</strong> <span id="vi_contact"></span></p>
                            <p><strong>Address :</strong> <span id="vi_address"></span></p>
                            <p><strong>Patient type :</strong> <span id="vi_patient_type"></span></p>
                        </div>
                        <div class="col-6">
                            <p><strong>Time :</strong> <span id="vi_time"></span></p>
                            <p><strong>Date :</strong> <span id="vi_date"></span></p>
                            <p><strong>Age :</strong> <span id="vi_age"></span></p>
                            <p><strong>Gender :</strong> <span id="vi_gender"></span></p>
                            <p><strong>Branch :</strong> <span id="vi_branch"></span></p>
                            <p><strong>Referred by :</strong> <span id="vi_referred_by"></span></p>
                            <p><strong>Purpose :</strong> <span id="vi_purpose"></span></p>
                            <p><strong>Medical history :</strong> <span id="vi_medical_history"></span></p>
                            <p><strong>Notes :</strong> <span id="vi_notes"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Modal: Shows appropriate service form for the selected appointment -->
    <div class="modal fade" id="testModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content appt-modal">
                <div class="modal-header border-0 pb-0">
                    <div class="d-flex align-items-center w-100">
                        <div id="tm-initials" class="rounded-circle d-flex align-items-center justify-content-center me-2" style="width:36px;height:36px;background:#f97316;color:#fff;font-weight:700;">--</div>
                        <div class="me-auto">
                            <div id="tm-name" class="fw-bold">Patient</div>
                            <div id="tm-service" class="text-muted small">Service</div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-sm d-inline-flex align-items-center dropdown-toggle" type="button" id="addTestDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" title="Add Test">
                                <i class="bi bi-plus-circle me-1"></i>
                                Add Test
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="addTestDropdownBtn">
                                <li><h6 class="dropdown-header">Choose Test</h6></li>
                                <li><a class="dropdown-item test-select" data-key="oae" href="#">OAE - Oto Acoustic with Emession</a></li>
                                <li><a class="dropdown-item test-select" data-key="abr" href="#">ABR - Auditory Brain Response</a></li>
                                <li><a class="dropdown-item test-select" data-key="assr" href="#">ASSR - Auditory Steady State Response</a></li>
                                <li><a class="dropdown-item test-select" data-key="pta" href="#">PTA - Puretone Audiometry</a></li>
                                <li><a class="dropdown-item test-select" data-key="tym" href="#">Tympanometry</a></li>
                                <li><a class="dropdown-item test-select" data-key="speech" href="#">Speech Audiometry</a></li>
                                <li><a class="dropdown-item test-select" data-key="play" href="#">Play Audiometry</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item test-select" data-key="hearing" href="#">Hearing Aid Fitting</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-body pt-2">
                    <div class="appt-panel p-3">
                        <!-- Dynamic form goes here -->
                        <div id="testFormContainer"></div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-success" id="testsSaveCombined"><i class="bi bi-check-lg"></i> Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Save modal for Test forms -->
    <div class="modal fade" id="testsSaveConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="tsc-confirm">
                        <h5 class="fw-bold mb-2">Are you sure?</h5>
                        <p class="mb-4">Are you sure want to save test results</p>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="testsConfirmYes">Yes</button>
                        </div>
                    </div>
                    <div class="tsc-success text-center" style="display:none;">
                        <svg viewBox="0 0 52 52" aria-hidden="true" style="width:56px;height:56px;display:block;margin:0 auto 6px auto;" class="checkmark-animation">
                            <circle cx="26" cy="26" r="24" stroke="#198754" stroke-width="3" fill="none" class="checkmark-circle"></circle>
                            <path d="M14 27 l8 8 l16 -16" stroke="#198754" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round" class="checkmark-path"></path>
                        </svg>
                        <div class="fw-semibold">Saved Successfully</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Form Templates (no patient info, just the test UI) -->
    <template id="tpl-oae">
        <form method="POST" data-action-pattern="/staff/patient-record/details/{id}/service/oae/save" novalidate>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="table-responsive">
                <table class="table table-bordered align-middle oae-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width:140px;">Ear</th>
                            <th class="text-center" style="width:120px;">Pass (✓)</th>
                            <th class="text-center" style="width:120px;">Refer (✓)</th>
                            <th class="text-center" style="width:140px;">Not tested (✓)</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Left Ear</td>
                            <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_left_pass"></td>
                            <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_left_refer"></td>
                            <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_left_not_tested"></td>
                            <td><input type="text" class="form-control" name="oae_left_remarks"></td>
                        </tr>
                        <tr>
                            <td>Right Ear</td>
                            <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_right_pass"></td>
                            <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_right_refer"></td>
                            <td class="text-center"><input class="form-check-input" type="checkbox" name="oae_right_not_tested"></td>
                            <td><input type="text" class="form-control" name="oae_right_remarks"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date Taken</label>
                    <input type="date" name="date_taken" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="oae_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations..."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i>Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </template>

    <template id="tpl-hearing">
        <form method="POST" data-action-pattern="/staff/patient-record/details/{id}/hearing/save" novalidate>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date Issued <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="date_issued" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hearing Aid Brand <span class="text-danger">*</span></label>
                    <select class="form-select" name="brand" id="ha-brand-modal" required>
                        <option value="">Select Brand</option>
                        <option value="Unitron" selected>Unitron</option>
                    </select>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Hearing Aid Model <span class="text-danger">*</span></label>
                    <select class="form-select" name="model" id="ha-model-modal" required>
                        <option value="">Select Model</option>
                        <option value="TMAXX600 Chargable">TMAXX600 Chargable</option>
                        <option value="TMAXX600 Battery">TMAXX600 Battery</option>
                        <option value="StrideP500 Chargable">StrideP500 Chargable</option>
                        <option value="StrideP500 Battery">StrideP500 Battery</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ear Side <span class="text-danger">*</span></label>
                    <select class="form-select" name="ear_side" required>
                        <option value="">Select Ear Side</option>
                        <option value="Left">Left</option>
                        <option value="Right">Right</option>
                        <option value="Both">Both</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </template>

    <template id="tpl-tym">
        <form method="POST" data-action-pattern="/staff/patient-record/details/{id}/service/tym/save" novalidate>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle tym-table text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width:160px;">&nbsp;</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Right</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="tym_right_type"></td>
                        </tr>
                        <tr>
                            <th>Left</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="tym_left_type"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date Taken</label>
                    <input type="date" name="date_taken" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="tym_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations..."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i>Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </template>

    <template id="tpl-pta">
        <form method="POST" data-action-pattern="/staff/patient-record/details/{id}/service/pta/save" novalidate>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle pta-table text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width:200px;">&nbsp;</th>
                            <th>250<br><span class="text-muted">Hz</span></th>
                            <th>500<br><span class="text-muted">Hz</span></th>
                            <th>1000<br><span class="text-muted">Hz</span></th>
                            <th>1500<br><span class="text-muted">Hz</span></th>
                            <th>2000<br><span class="text-muted">Hz</span></th>
                            <th>3000<br><span class="text-muted">Hz</span></th>
                            <th>4000<br><span class="text-muted">Hz</span></th>
                            <th>6000<br><span class="text-muted">Hz</span></th>
                            <th>8000<br><span class="text-muted">Hz</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>RIGHT AC</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac[]"></td>
                        </tr>
                        <tr>
                            <th>MASKED</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_ac_masked[]"></td>
                        </tr>
                        <tr>
                            <th>RIGHT BC</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc[]"></td>
                        </tr>
                        <tr>
                            <th>MASKED</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_right_bc_masked[]"></td>
                        </tr>
                        <tr>
                            <th>LEFT AC</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac[]"></td>
                        </tr>
                        <tr>
                            <th>MASKED</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_ac_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text center" name="pta_left_ac_masked[]"></td>
                        </tr>
                        <tr>
                            <th>LEFT BC</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc[]"></td>
                        </tr>
                        <tr>
                            <th>MASKED</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="pta_left_bc_masked[]"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date Taken</label>
                    <input type="date" name="date_taken" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="pta_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations..."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i>Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </template>

    <template id="tpl-abr">
        <form method="POST" data-action-pattern="/staff/patient-record/details/{id}/service/abr/save" novalidate>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <h6 style="font-weight:600;">Latencies & amplitudes (right ear)</h6>
            <div class="table-responsive mb-2">
                <table class="table table-bordered align-middle abr-table">
                    <thead class="table-light">
                        <tr>
                            <th class="abr-col-n">N</th>
                            <th style="width:110px;">I (ms)</th>
                            <th style="width:100px;">III (ms)</th>
                            <th style="width:100px;">V (ms)</th>
                            <th style="width:100px;">I–III (ms)</th>
                            <th style="width:100px;">III–V (ms)</th>
                            <th style="width:100px;">I–V (ms)</th>
                            <th style="width:120px;">V–V(a) (µV)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="abr-col-n"><input type="text" class="form-control" name="abr_rn[]"></td>
                            <td><input type="text" class="form-control" name="abr_ri[]"></td>
                            <td><input type="text" class="form-control" name="abr_r3[]"></td>
                            <td><input type="text" class="form-control" name="abr_rv[]"></td>
                            <td><input type="text" class="form-control" name="abr_r13[]"></td>
                            <td><input type="text" class="form-control" name="abr_r35[]"></td>
                            <td><input type="text" class="form-control" name="abr_r15[]"></td>
                            <td><input type="text" class="form-control" name="abr_rvv[]"></td>
                        </tr>
                        <tr>
                            <td class="abr-col-n"><input type="text" class="form-control" name="abr_rn[]"></td>
                            <td><input type="text" class="form-control" name="abr_ri[]"></td>
                            <td><input type="text" class="form-control" name="abr_r3[]"></td>
                            <td><input type="text" class="form-control" name="abr_rv[]"></td>
                            <td><input type="text" class="form-control" name="abr_r13[]"></td>
                            <td><input type="text" class="form-control" name="abr_r35[]"></td>
                            <td><input type="text" class="form-control" name="abr_r15[]"></td>
                            <td><input type="text" class="form-control" name="abr_rvv[]"></td>
                        </tr>
                        <tr>
                            <td class="abr-col-n"><input type="text" class="form-control" name="abr_rn[]"></td>
                            <td><input type="text" class="form-control" name="abr_ri[]"></td>
                            <td><input type="text" class="form-control" name="abr_r3[]"></td>
                            <td><input type="text" class="form-control" name="abr_rv[]"></td>
                            <td><input type="text" class="form-control" name="abr_r13[]"></td>
                            <td><input type="text" class="form-control" name="abr_r35[]"></td>
                            <td><input type="text" class="form-control" name="abr_r15[]"></td>
                            <td><input type="text" class="form-control" name="abr_rvv[]"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <h6 style="font-weight:600;">Latencies & amplitudes (left ear)</h6>
            <div class="table-responsive mb-2">
                <table class="table table-bordered align-middle abr-table">
                    <thead class="table-light">
                        <tr>
                            <th class="abr-col-n">N</th>
                            <th style="width:110px;">I (ms)</th>
                            <th style="width:100px;">III (ms)</th>
                            <th style="width:100px;">V (ms)</th>
                            <th style="width:100px;">I–III (ms)</th>
                            <th style="width:100px;">III–V (ms)</th>
                            <th style="width:100px;">I–V (ms)</th>
                            <th style="width:120px;">V–V(a) (µV)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="abr-col-n"><input type="text" class="form-control" name="abr_ln[]"></td>
                            <td><input type="text" class="form-control" name="abr_li[]"></td>
                            <td><input type="text" class="form-control" name="abr_l3[]"></td>
                            <td><input type="text" class="form-control" name="abr_lv[]"></td>
                            <td><input type="text" class="form-control" name="abr_l13[]"></td>
                            <td><input type="text" class="form-control" name="abr_l35[]"></td>
                            <td><input type="text" class="form-control" name="abr_l15[]"></td>
                            <td><input type="text" class="form-control" name="abr_lvv[]"></td>
                        </tr>
                        <tr>
                            <td class="abr-col-n"><input type="text" class="form-control" name="abr_ln[]"></td>
                            <td><input type="text" class="form-control" name="abr_li[]"></td>
                            <td><input type="text" class="form-control" name="abr_l3[]"></td>
                            <td><input type="text" class="form-control" name="abr_lv[]"></td>
                            <td><input type="text" class="form-control" name="abr_l13[]"></td>
                            <td><input type="text" class="form-control" name="abr_l35[]"></td>
                            <td><input type="text" class="form-control" name="abr_l15[]"></td>
                            <td><input type="text" class="form-control" name="abr_lvv[]"></td>
                        </tr>
                        <tr>
                            <td class="abr-col-n"><input type="text" class="form-control" name="abr_ln[]"></td>
                            <td><input type="text" class="form-control" name="abr_li[]"></td>
                            <td><input type="text" class="form-control" name="abr_l3[]"></td>
                            <td><input type="text" class="form-control" name="abr_lv[]"></td>
                            <td><input type="text" class="form-control" name="abr_l13[]"></td>
                            <td><input type="text" class="form-control" name="abr_l35[]"></td>
                            <td><input type="text" class="form-control" name="abr_l15[]"></td>
                            <td><input type="text" class="form-control" name="abr_lvv[]"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date Taken</label>
                    <input type="date" name="date_taken" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="abr_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations..."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i>Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </template>

    <template id="tpl-assr">
        <form method="POST" data-action-pattern="/staff/patient-record/details/{id}/service/assr/save" novalidate>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle assr-table text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width:120px;">EAR</th>
                            <th>500 Hz</th>
                            <th>1000 Hz</th>
                            <th>2000 Hz</th>
                            <th>4000 Hz</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">RIGHT</td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_r_500"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_r_1000"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_r_2000"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_r_4000"></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Left</td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_l_500"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_l_1000"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_l_2000"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_l_4000"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-responsive mb-2">
                <table class="table table-bordered align-middle assr-table assr-grid text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="assr-col-n">N</th>
                            <th>Electr.</th>
                            <th>LFF, Hz</th>
                            <th>HFF, Hz</th>
                            <th>50 Hz</th>
                            <th>Rejection µV</th>
                            <th>Aver.</th>
                            <th>Reject.</th>
                            <th>Transducer</th>
                            <th>Stimulus</th>
                            <th>Noise</th>
                            <th>Emn</th>
                            <th>RN, nV</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="assr-col-n"><input type="text" class="form-control form-control-sm text-center" name="assr_n[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_electr[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_lff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_hff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_50hz[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject_uv[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_aver[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_transducer[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_stimulus[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_noise[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_emn[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_rn_nv[]"></td>
                        </tr>
                        <tr>
                            <td class="assr-col-n"><input type="text" class="form-control form-control-sm text-center" name="assr_n[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_electr[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_lff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_hff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_50hz[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject_uv[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_aver[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_transducer[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_stimulus[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_noise[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_emn[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_rn_nv[]"></td>
                        </tr>
                        <tr>
                            <td class="assr-col-n"><input type="text" class="form-control form-control-sm text-center" name="assr_n[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_electr[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_lff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_hff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_50hz[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject_uv[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_aver[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_transducer[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_stimulus[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_noise[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_emn[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_rn_nv[]"></td>
                        </tr>
                        <tr>
                            <td class="assr-col-n"><input type="text" class="form-control form-control-sm text-center" name="assr_n[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_electr[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_lff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_hff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_50hz[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject_uv[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_aver[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_transducer[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_stimulus[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_noise[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_emn[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_rn_nv[]"></td>
                        </tr>
                        <tr>
                            <td class="assr-col-n"><input type="text" class="form-control form-control-sm text-center" name="assr_n[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_electr[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_lff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_hff[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_50hz[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject_uv[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_aver[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_reject[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_transducer[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_stimulus[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_noise[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_emn[]"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="assr_rn_nv[]"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date Taken</label>
                    <input type="date" name="date_taken" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="assr_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations..."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i>Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </template>

    <template id="tpl-speech">
        <form method="POST" data-action-pattern="/staff/patient-record/details/{id}/service/speech/save" novalidate>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle speech-table text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width:140px;"></th>
                            <th>SRT</th>
                            <th>SDS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="text-center speech-ear">Right</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="speech_right_srt"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="speech_right_sds"></td>
                        </tr>
                        <tr>
                            <th class="text-center speech-ear">Left</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="speech_left_srt"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="speech_left_sds"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date Taken</label>
                    <input type="date" name="date_taken" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="speech_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations.."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i>Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </template>

    <template id="tpl-play">
        <!-- Play Audiometry shares the same fields as Speech; we save under 'speech' service -->
        <form method="POST" data-action-pattern="/staff/patient-record/details/{id}/service/speech/save" novalidate>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="table-responsive mb-3">
                <table class="table table-bordered align-middle speech-table text-center">
                    <thead class="table-light">
                        <tr>
                            <th style="width:140px;"></th>
                            <th>SRT</th>
                            <th>SDS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="text-center speech-ear">Right</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="speech_right_srt"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="speech_right_sds"></td>
                        </tr>
                        <tr>
                            <th class="text-center speech-ear">Left</th>
                            <td><input type="text" class="form-control form-control-sm text-center" name="speech_left_srt"></td>
                            <td><input type="text" class="form-control form-control-sm text-center" name="speech_left_sds"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Date Taken</label>
                    <input type="date" name="date_taken" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="speech_notes" class="form-control" rows="4" placeholder="Enter any additional observations or recommendations.."></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i>Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </template>
</div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    var viewModalEl = document.getElementById('viewInfoModal');
    if (!viewModalEl) return;
    var viewModal = new bootstrap.Modal(viewModalEl);

    function text(el){ return (el && el.textContent ? el.textContent.trim() : ''); }

    document.querySelectorAll('.btn-view').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            var row = btn.closest('tr');
            if (!row) return;
            // Fill known fields from table; others default to —
            var set = function(id, val){ var el = document.getElementById(id); if (el) el.textContent = val || '—'; };
            set('vi_name', text(row.querySelector('td[data-label="Fullname"]')));
            set('vi_service', text(row.querySelector('td[data-label="Services"]')));
            set('vi_email', text(row.querySelector('td[data-label="Email"]')));
            set('vi_time', text(row.querySelector('td[data-label="Time"]')));
            set('vi_date', text(row.querySelector('td[data-label="Date"]')));
                        // Fill additional info from data- attributes
                        var get = function(attr){ return (row.getAttribute('data-'+attr) || '').trim(); };
                        var fullName = get('first_name');
                        var sur = get('surname');
                        var mid = get('middlename');
                        if (!document.getElementById('vi_surname').textContent) document.getElementById('vi_surname').textContent = sur || '—';
                        if (!document.getElementById('vi_middle').textContent) document.getElementById('vi_middle').textContent = mid || '—';
                        var gender = get('gender');
                        var bdate = get('birthdate');
                        var addr = get('address');
                        var contact = get('contact');
                        var ptype = get('patient_type');
                        var branch = get('branch');
                        var refby = get('referred_by');
                        var purpose = get('purpose');
                        var medhist = get('medical_history');
                        var setIf = function(id, val){ var el = document.getElementById(id); if (el) el.textContent = val && val.length ? val : '—'; };
                        setIf('vi_gender', gender);
                        setIf('vi_branch', branch);
                        setIf('vi_patient_type', ptype);
                        setIf('vi_address', addr);
                        setIf('vi_contact', contact);
                        setIf('vi_referred_by', refby);
                        setIf('vi_purpose', purpose);
                        setIf('vi_medical_history', medhist);

            viewModal.show();
        });
    });

    // Ensure no lingering backdrop after closing (defensive cleanup)
    viewModalEl.addEventListener('hidden.bs.modal', function(){
        document.querySelectorAll('.modal-backdrop').forEach(function(b){ b.remove(); });
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });
});
</script>
<script>
// Slot enforcement for staff modal (map to appointment_date/time if fields exist)
(function(){
    const form = document.getElementById('newAppointmentForm');
    if(!form) return;
    // Try to detect date/time inputs commonly used in modal
    let dateEl = form.querySelector('input[name="appointment_date"], input[name="schedule_date"], input[type="date"]');
    let timeEl = form.querySelector('input[name="appointment_time"], input[name="schedule_time"], input[type="time"]');
    if(!dateEl || !timeEl) return;
    const allowed = ['08:00','10:00','12:00','14:00','15:00'];
    const dlId = 'slotOptionsSTAFF';
    let dl = document.getElementById(dlId);
    if(!dl){ dl = document.createElement('datalist'); dl.id = dlId; allowed.forEach(t=>{ const o=document.createElement('option'); o.value=t; dl.appendChild(o); }); document.body.appendChild(dl); }
    try{ timeEl.setAttribute('list', dlId); }catch(e){}
    function ensureErr(){ let e=timeEl.nextElementSibling; if(!e||!e.classList||!e.classList.contains('invalid-feedback')){ e=document.createElement('div'); e.className='invalid-feedback'; timeEl.parentNode.appendChild(e);} return e; }
    function setErr(m){ const e=ensureErr(); e.textContent=m||''; if(m){ timeEl.classList.add('is-invalid'); } else { timeEl.classList.remove('is-invalid'); } }
    function qs(p){ return Object.entries(p).map(([k,v])=> `${encodeURIComponent(k)}=${encodeURIComponent(v??'')}`).join('&'); }
    async function j(u){ const r=await fetch(u,{headers:{'Accept':'application/json'}}); if(!r.ok) throw new Error('x'); return r.json(); }
    async function suggest(){ if(!dateEl.value) return; try{ const d=await j(`/api/appointments/next-slot?${qs({appointment_date: dateEl.value})}`); if(d&&d.next_available){ timeEl.value=d.next_available; setErr(''); } else { setErr('No available slots for the selected date.'); timeEl.value=''; } }catch(err){} }
    async function validateTime(){ const dv=dateEl.value; let tv=(timeEl.value||'').slice(0,5); if(!dv||!tv) return; if(!allowed.includes(tv)){ setErr('Start time must be 08:00, 10:00, 12:00, 14:00, or 15:00.'); return; } try{ const d=await j(`/api/appointments/check-slot?${qs({appointment_date: dv, appointment_time: tv})}`); if(!d.within_hours||!d.allowed_start){ setErr('Selected time is outside allowed clinic hours.'); return; } if(!d.available){ if(d.next_available){ timeEl.value=d.next_available; setErr('That time is already booked. Next available has been set.'); setTimeout(()=>setErr(''),2500);} else { setErr('No available slots remain for this date.'); } } else { setErr(''); } }catch(err){} }
    dateEl.addEventListener('change', suggest);
    timeEl.addEventListener('change', validateTime);
})();
</script>
<script>
// Test button modal logic: render service-specific form without patient sidebar
document.addEventListener('DOMContentLoaded', function(){
    var testModalEl = document.getElementById('testModal');
    if(!testModalEl) return;
    var testModal = new bootstrap.Modal(testModalEl);
    var container = document.getElementById('testFormContainer');
    var currentPatientId = null;

    function makeInitials(name){
        name = (name||'').trim();
        if(!name) return '--';
        var parts = name.split(/\s+/);
        var s = '';
        parts.forEach(function(p){ if(p) s += p[0].toUpperCase(); });
        return s.slice(0,2);
    }

    function serviceToKey(label){
        label = (label||'').toLowerCase();
        if(label.includes('oae') || label.includes('oto') || label.includes('emission') || label.includes('emession')) return 'oae';
        if(label.includes('tym')) return 'tym';
        if(label.includes('hearing') || label.includes('fitting') || label.includes('aid')) return 'hearing';
        if(label.includes('pure') || label.includes('puretone') || label.includes('pta') || label.includes('audiometry')) return 'pta';
        if(label.includes('brain') || label.includes('abr')) return 'abr';
        if(label.includes('steady') || label.includes('assr')) return 'assr';
        if(label.includes('speech')) return 'speech';
        if(label.includes('play')) return 'play';
        return 'oae';
    }

    function openForRow(row){
        var fullName = row.querySelector('td[data-label="Fullname"]').textContent.trim();
        var serviceLabel = row.querySelector('td[data-label="Services"]').textContent.trim();
        var pid = row.getAttribute('data-patient_id') || '';
        var explicitKey = row.getAttribute('data-service_key');

        document.getElementById('tm-name').textContent = fullName || 'Patient';
        document.getElementById('tm-service').textContent = serviceLabel || '';
        document.getElementById('tm-initials').textContent = makeInitials(fullName);

        currentPatientId = pid || null;
        var key = explicitKey || serviceToKey(serviceLabel);
        container.innerHTML = '';
        renderTestByKey(key);

        var form = container.querySelector('form');
        if(form){
            var pattern = form.getAttribute('data-action-pattern') || '';
            if(pid){ form.action = pattern.replace('{id}', String(pid)); }
            // Default date today if empty
            var dt = form.querySelector('input[type="date"][name="date_taken"]');
            if(dt && !dt.value){ try{ dt.value = new Date().toISOString().slice(0,10); }catch(e){} }
        }

        testModal.show();
    }
    function renderTestByKey(key){
        var tpl = document.getElementById('tpl-'+key);
        if(!tpl){
            var miss = document.createElement('div');
            miss.className = 'text-muted';
            miss.textContent = 'No form available for this service.';
            container.appendChild(miss);
            return;
        }
        // Section wrapper per test
        var section = document.createElement('div');
        section.className = 'mb-3';
        // Small title
        var titleMap = { oae:'OAE - Oto Acoustic with Emession', abr:'ABR - Auditory Brain Response', assr:'ASSR - Auditory Steady State Response', pta:'PTA - Puretone Audiometry', tym:'Tympanometry', speech:'Speech Audiometry', play:'Play Audiometry', hearing:'Hearing Aid Fitting' };
        var h = document.createElement('div');
        h.className = 'test-section-title';
        h.textContent = titleMap[key] || key.toUpperCase();
        section.appendChild(h);
        var hr = document.createElement('hr');
        hr.style.margin = '6px 0 10px 0';
        section.appendChild(hr);
        // Append cloned form
        section.appendChild(tpl.content.cloneNode(true));
        container.appendChild(section);
        // Normalize inner form
        var form = section.querySelector('form');
        if(form){
            // Remove individual save/cancel buttons inside templates
            form.querySelectorAll('button[type="submit"], button[data-bs-dismiss="modal"]').forEach(function(btn){ btn.remove(); });
            var pattern = form.getAttribute('data-action-pattern') || '';
            if(currentPatientId && pattern.includes('{id}')){ form.action = pattern.replace('{id}', String(currentPatientId)); }
            var dt = form.querySelector('input[type="date"][name="date_issued"], input[type="date"][name="date_taken"]');
            if(dt && !dt.value){ try{ dt.value = new Date().toISOString().slice(0,10); }catch(e){} }
        }
    }

    // Dropdown: choose a different test to render
    document.addEventListener('click', function(e){
        var a = e.target.closest('.dropdown-item.test-select');
        if(!a) return;
        e.preventDefault();
        var key = a.getAttribute('data-key');
        if(!key) return;
        renderTestByKey(key);
    });

    // Combined save with confirm modal
    (function(){
        var saveBtnHandler = async function(){
            console.log('saveBtnHandler called'); // Debug log
            var confirmEl = document.getElementById('testsSaveConfirmModal');
            if(!confirmEl){ 
                console.log('Confirm modal not found, using fallback'); // Debug log
                return fallbackSave(); 
            }
            console.log('Showing confirm modal'); // Debug log
            var m = bootstrap.Modal.getOrCreateInstance(confirmEl);
            // reset
            confirmEl.querySelector('.tsc-confirm').style.display = 'block';
            confirmEl.querySelector('.tsc-success').style.display = 'none';
            m.show();
            var yesBtn = document.getElementById('testsConfirmYes');
            var once = false;
            var onYes = async function(){
                if(once) return; once = true;
                confirmEl.querySelector('.tsc-confirm').style.display = 'none';
                confirmEl.querySelector('.tsc-success').style.display = 'block';
                // perform the actual save
                await performSave();
                setTimeout(function(){ try { m.hide(); } catch(_){} try { bootstrap.Modal.getInstance(testModalEl).hide(); } catch(_){} }, 600);
                yesBtn && yesBtn.removeEventListener('click', onYes);
            };
            yesBtn && yesBtn.addEventListener('click', onYes);
        };

        async function performSave(){
            const forms = Array.from(container.querySelectorAll('form'));
            let hasSuccessfulSave = false;
            for (const f of forms) {
                if (f && typeof f.reportValidity === 'function' && !f.reportValidity()) { return; }
                const fd = new FormData(f);
                const action = f.getAttribute('action') || f.dataset.actionPattern || '';
                const method = (f.getAttribute('method') || 'POST').toUpperCase();
                if (!action) continue;
                try {
                    const res = await fetch(action, {
                        method,
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: fd,
                        credentials: 'same-origin'
                    });
                    if (res.ok) { 
                        hasSuccessfulSave = true;
                        console.log('Successfully saved test form', action); 
                    } else { 
                        console.warn('Failed saving a test form', action); 
                    }
                } catch (err) { console.warn('Error saving a test form', err); }
            }
            
            // If we had a successful save, refresh the appointments table
            if (hasSuccessfulSave) {
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        }

        function fallbackSave(){ performSave(); }

        document.addEventListener('click', function(e){
            if(!e.target.closest('#testsSaveCombined')) return;
            e.preventDefault();
            console.log('Save button clicked'); // Debug log
            saveBtnHandler();
        });
    })();

    document.querySelectorAll('.btn-test').forEach(function(btn){
        btn.addEventListener('click', function(){
            var row = btn.closest('tr');
            if(row) openForRow(row);
        });
    });
});
</script>
<script>
// Auto-dismiss flash alerts after a short delay
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.alert.auto-fade').forEach(function(el){
        setTimeout(function(){
            try { bootstrap.Alert.getOrCreateInstance(el).close(); } catch(e) { el.remove(); }
        }, 2500);
    });
});

// Search, Filter, and Pagination Functionality
document.addEventListener('DOMContentLoaded', function(){
    const nameSearch = document.getElementById('nameSearch');
    const appointmentDate = document.getElementById('appointmentDate');
    const clearFilters = document.getElementById('clearFilters');
    const appointmentsTable = document.getElementById('appointmentsTable');
    
    if (!appointmentsTable) return;
    
    // Pagination variables
    let currentPage = 1;
    let entriesPerPage = 10;
    let allAppointments = [];
    let filteredAppointments = [];
    
    // Initialize data
    function initializeData() {
        const rows = appointmentsTable.querySelectorAll('tbody tr');
        allAppointments = Array.from(rows).filter(row => row.querySelector('td[data-label="Fullname"]'));
        
        // Sort by first come first serve (by appointment creation order)
        // The rows are already in the correct order from the server (sorted by created_at)
        // So we just need to maintain that order
        filteredAppointments = [...allAppointments];
        updateDisplay();
    }
    
    function filterAppointments() {
        const nameFilter = (nameSearch.value || '').toLowerCase().trim();
        const selectedDate = appointmentDate.value;
        
        filteredAppointments = allAppointments.filter(function(row) {
            let showRow = true;
            
            // Name filter
            if (nameFilter) {
                const nameCell = row.querySelector('td[data-label="Fullname"]');
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
                        // Parse the date (format: "Oct 04, 2025")
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
        
        // Show current page rows and update numbering
        currentAppointments.forEach(function(row, index) {
            row.style.display = '';
            // Update the "No." column with first come first serve numbering
            const noCell = row.querySelector('td[data-label="No."]');
            if (noCell) {
                const globalIndex = startIndex + index + 1;
                noCell.textContent = sprintf('%02d', globalIndex);
            }
        });
        
        updatePaginationInfo();
        updatePaginationControls();
    }
    
    // Helper function for sprintf-like formatting
    function sprintf(format, value) {
        if (format === '%02d') {
            return String(value).padStart(2, '0');
        }
        return String(value);
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
    
    function clearAllFilters() {
        nameSearch.value = '';
        appointmentDate.value = '';
        filteredAppointments = [...allAppointments];
        currentPage = 1;
        updateDisplay();
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
</script>
@endpush