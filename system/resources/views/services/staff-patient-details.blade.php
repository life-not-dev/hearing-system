@extends('layouts.staff')

@section('title', 'Patient Details | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
	@php
		$rec = $record ?? null;
		$fn = $rec['first_name'] ?? '';
		$mn = $rec['middle_name'] ?? '';
		$ln = $rec['last_name'] ?? '';
		$full = trim($fn . ' ' . ($mn ? $mn . ' ' : '') . $ln);
		$parts = preg_split('/\s+/', $full, -1, PREG_SPLIT_NO_EMPTY);
		$initials = '--';
		if ($full) {
				$init = '';
				foreach ($parts as $p) { $init .= strtoupper(substr($p, 0, 1)); }
				$initials = substr($init, 0, 2);
		}
		$dateReg = $rec['date_registered'] ?? null;
		$dateRegPretty = $dateReg ? date('F j, Y', strtotime($dateReg)) : '';
		
		// Format birthday and date registered for display
		$birthdayFormatted = '';
		$dateRegisteredFormatted = '';
		if (!empty($rec['birthday'])) {
			try {
				$birthdayFormatted = date('F j, Y', strtotime($rec['birthday']));
			} catch (Exception $e) {
				$birthdayFormatted = $rec['birthday'];
			}
		}
		if (!empty($rec['date_registered'])) {
			try {
				$dateRegisteredFormatted = date('F j, Y', strtotime($rec['date_registered']));
			} catch (Exception $e) {
				$dateRegisteredFormatted = $rec['date_registered'];
			}
		}
	@endphp
	<div style="margin-top:8px; margin-bottom:18px;" class="d-flex justify-content-between flex-wrap align-items-center">
		<div>
			<h4 style="font-weight:bold; margin-bottom:4px;"><i class="bi bi-person-lines-fill me-2"></i>Patient Information & Test Results</h4>
			<div class="text-muted" style="font-size:.95rem;">Complete record of patient details and diagnostic findings.</div>
		</div>
		<div class="d-flex gap-2">
			<button type="button" class="btn btn-primary" id="printButton" style="font-weight:600;">
				<i class="bi bi-printer"></i> Print
			</button>
		</div>

<!-- Inline fallback to render hearing aids if layout doesn't include @stack('scripts') -->
<script>
(function(){
	const id = {{ json_encode($id) }};
	function escapeHtml(str){
		return String(str)
			.replace(/&/g,'&amp;')
			.replace(/</g,'&lt;')
			.replace(/>/g,'&gt;')
			.replace(/"/g,'&quot;')
			.replace(/'/g,'&#039;');
	}
	function formatDatePretty(iso){
		if(!iso) return '';
		const m = String(iso).match(/^(\d{4})-(\d{2})-(\d{2})$/);
		if(!m) return iso;
		const y=+m[1], mo=+m[2]-1, d=+m[3];
		const dt=new Date(y,mo,d);
		const mn=dt.toLocaleString('en-US',{month:'long'});
		return `${mn} ${d}, ${y}`;
	}
	function hideHearingAidSectionIfEmpty(){
		const card = document.getElementById('hearing-aid-records');
		const tbody = document.getElementById('ha-results-tbody');
		if (card && tbody && tbody.querySelectorAll('tr[data-id]').length === 0) {
			card.remove();
		}
	}
	function appendRow(r){
		const tbody = document.getElementById('ha-results-tbody');
		if (!tbody) return;
		const tr = document.createElement('tr');
		tr.dataset.id = r.id;
		tr.innerHTML = `
			<td data-label="Patient Name">${escapeHtml(r.patient_name||'')}</td>
			<td data-label="Brand">${escapeHtml(r.brand||'')}</td>
			<td data-label="Model">${escapeHtml(r.model||'')}</td>
			<td data-label="Ear Side">${escapeHtml(r.ear_side||'')}</td>
			<td data-label="Date Issued">${escapeHtml(formatDatePretty(r.date_issued))}</td>
			<td data-label="Action"></td>`;
		tbody.appendChild(tr);
	}
	async function loadFallback(){
		const tbody = document.getElementById('ha-results-tbody');
		if (!tbody) return;
		// If another script already populated rows, skip
		if (tbody.querySelectorAll('tr:not(.no-results)').length > 0) return;
		try {
			const res = await fetch(`/staff/api/session/patient/${id}/hearing-aids`, { headers: { 'Accept':'application/json' }, credentials: 'same-origin' });
			if (!res.ok) throw new Error('fetch failed');
			const js = await res.json();
			const rows = js.data || [];
			if (!rows.length) { hideHearingAidSectionIfEmpty(); return; }
			rows.forEach(appendRow);
		} catch(e){ hideHearingAidSectionIfEmpty(); }
	}
	document.addEventListener('DOMContentLoaded', loadFallback);
})();
</script>

	</div>

	<!-- Patient Summary -->
	<div class="row g-3 mb-4">
		<div class="col-12">
			<div class="card shadow-sm patient-summary-card" style="border:1px solid #e2e8f0; border-radius:10px;">
				<div class="card-body">
					<div class="d-flex align-items-center">
						<div class="rounded-circle d-flex align-items-center justify-content-center" style="width:60px;height:60px;background:#f97316;color:#fff;font-weight:700;font-size:1.2rem;" id="pt-initials">{{ $initials }}</div>
						<div class="ms-4">
							<div id="pt-fullname" style="font-weight:700; font-size:1.3rem;">{{ $full ?: 'Not found' }}</div>
							<div id="pt-registered" class="text-muted" style="font-size:1rem;">{{ $dateRegPretty ? ('Registered on ' . $dateRegPretty) : '' }}</div>
							<div class="text-muted" style="font-size:.9rem;">Patient ID: {{ $id }}</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Patient Information and Test Results -->
	<div class="row g-3">
		<div class="col-12">
			<div class="card shadow-sm" style="border:1px solid #e2e8f0; border-radius:10px;">
				<div class="card-body">
					<div id="panel-patient-info">
						<h5 style="font-weight:700;">Patient Information</h5>
						<div class="text-muted mb-3" style="font-size:.9rem;">Basic info for the patient information base on the programs.</div>

							<div class="row">
								<div class="col-12 mb-3">
									<div class="inline-field">
										<label class="form-label mb-0">First Name</label>
										<input type="text" class="form-control" readonly value="{{ $rec['first_name'] ?? '' }}">
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="inline-field">
										<label class="form-label mb-0">Middle Name</label>
										<input type="text" class="form-control" readonly value="{{ $rec['middle_name'] ?? '' }}">
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="inline-field">
										<label class="form-label mb-0">Last Name</label>
										<input type="text" class="form-control" readonly value="{{ $rec['last_name'] ?? '' }}">
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="inline-field">
										<label class="form-label mb-0">Birthday</label>
										<input type="text" class="form-control" readonly value="{{ $birthdayFormatted }}">
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="inline-field">
										<label class="form-label mb-0">Gender</label>
										<input type="text" class="form-control" readonly value="{{ $rec['gender'] ?? '' }}">
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="inline-field">
										<label class="form-label mb-0">Patient Type</label>
										<input type="text" class="form-control" readonly value="{{ $rec['patient_type'] ?? '' }}">
									</div>
								</div>
								<div class="col-12 mb-3">
									<div class="inline-field">
										<label class="form-label mb-0">Date Registered</label>
										<input type="text" class="form-control" readonly value="{{ $dateRegisteredFormatted }}">
									</div>
								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Test Results Section -->
	<div class="row g-3" style="margin-top:32px;">
		<div class="col-12">
			<div style="margin-bottom:24px;">
				<h4 style="font-weight:bold; margin-bottom:4px;"><i class="bi bi-clipboard-data me-2"></i>Test Results</h4>
				<div class="text-muted" style="font-size:.95rem;">Summary of diagnostic findings and evaluations.</div>
			</div>
			
			{{-- Hearing aid table removed per request; the partial results.hearing-aid-results was intentionally omitted here. --}}

			<!-- Container where svcRecords (test summaries) will be rendered client-side -->
			<div id="patientTestsContainer" class="mb-3 small"></div>

			{{-- Service summaries --}}
			
			@include('results.pta-results')
			@include('results.tym-results')
			@include('results.speech-results')
			@include('results.oae-results')
			@include('results.abr-results')
			@include('results.assr-results')
		</div>
	</div>
@endsection

@push('styles')
<style>
	/* Bring page content a bit closer to the sticky topbar on this page only */
	.container-main { padding-top: 45px; }
	
	/* Form styling */
	.inline-field { display:flex; gap:12px; align-items:center; }
	.inline-field > label { width: 200px; flex: 0 0 auto; }
	.inline-field > .form-control, .inline-field > .form-select { flex: 1 1 auto; }
	
	/* Patient summary styling */
	.patient-summary-card { background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); }
	
	/* Readonly field styling */
	.form-control[readonly] { 
		background-color: #f8f9fa; 
		border-color: #e9ecef; 
		color: #495057;
		cursor: default;
	}
	.form-control[readonly]:focus { 
		box-shadow: none; 
		border-color: #e9ecef;
	}
	
	/* Remove icons from readonly fields */
	.form-control[readonly]::-webkit-calendar-picker-indicator,
	.form-control[readonly]::-webkit-inner-spin-button,
	.form-control[readonly]::-webkit-outer-spin-button {
		display: none;
		-webkit-appearance: none;
	}

	/* Hearing Aid Results table responsive styles */
	.ha-results-table thead th { background:#f1f5f9; font-weight:600; font-size:.8rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
	.ha-results-table tbody td { font-size:.9rem; }
	.ha-results-table tbody tr:hover { background:#f8fafc; }
	@media (max-width: 768px){
		.ha-results-table thead { display:none; }
		.ha-results-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
		.ha-results-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
		.ha-results-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
	}
</style>
@endpush

@push('scripts')
<script>
(function(){
	const id = {{ json_encode($id) }};
	const csrfEl = document.querySelector('meta[name="csrf-token"]');
	const CSRF = csrfEl ? csrfEl.content : '';
	const hasServerRecord = {!! json_encode(!empty($record)) !!};

	function formatDatePretty(iso){
		if(!iso) return '';
		const m = String(iso).match(/^(\d{4})-(\d{2})-(\d{2})$/);
		if(!m) return iso;
		const year = parseInt(m[1],10), month = parseInt(m[2],10)-1, day = parseInt(m[3],10);
		const d = new Date(year, month, day);
		const monthName = d.toLocaleString('en-US', { month: 'long' });
		return `${monthName} ${day}, ${year}`;
	}

	function escapeHtml(str){
		return String(str)
			.replace(/&/g,'&amp;')
			.replace(/</g,'&lt;')
			.replace(/>/g,'&gt;')
			.replace(/"/g,'&quot;')
			.replace(/'/g,'&#039;');
	}

	function setValue(name, val){ const el = document.querySelector(`[name="${name}"]`); if(el) el.value = val || ''; }

	async function load(){
		if (hasServerRecord) {
			// Prefilled server-side; nothing else to do
			return;
		}
		// Fallback: fetch if server didn't pass record
		try {
			const res = await fetch("{{ url('/staff/api/patient-records') }}/"+encodeURIComponent(id), { headers: { 'Accept':'application/json' } });
			if(!res.ok) throw new Error('not found');
			const js = await res.json();
			const r = js.data || {};
			const full = `${r.first_name||''}${r.middle_name? ' '+r.middle_name:''}${r.last_name? ' '+r.last_name:''}`.trim();
			document.getElementById('pt-fullname').textContent = full || '—';
			const initials = full ? full.split(/\s+/).map(p=>p[0]).join('').substring(0,2).toUpperCase() : '--';
			document.getElementById('pt-initials').textContent = initials;
			document.getElementById('pt-registered').textContent = r.date_registered ? `Registered on ${formatDatePretty(r.date_registered)}` : '';
			setValue('first_name', r.first_name);
			setValue('middle_name', r.middle_name);
			setValue('last_name', r.last_name);
			setValue('gender', r.gender);
			setValue('patient_type', r.patient_type);
			setValue('birthday', r.birthday ? formatDatePretty(r.birthday) : '');
			setValue('date_registered', r.date_registered ? formatDatePretty(r.date_registered) : '');
		} catch(e){
			document.getElementById('pt-fullname').textContent = 'Not found';
			document.getElementById('pt-registered').textContent = '';
		}
	}

	function ensureEmptyState(){
		const tbody = document.getElementById('ha-results-tbody');
		if (!tbody) return;
		if (tbody.querySelectorAll('tr:not(.no-results)').length === 0) {
			tbody.innerHTML = `
				<tr class="no-results">
					<td colspan="6" class="text-center text-muted py-4">
						<i class="bi bi-ear me-2"></i>No hearing aid records yet.
					</td>
				</tr>
			`;
		}
	}

	function appendHearingAidRow(record) {
		const tbody = document.getElementById('ha-results-tbody');
		if (!tbody) return;

		// Remove no-results row if exists
		const noResultsRow = tbody.querySelector('.no-results');
		if (noResultsRow) noResultsRow.remove();

		const tr = document.createElement('tr');
		tr.dataset.id = record.id;
		tr.innerHTML = `
			<td data-label="Patient Name">${escapeHtml(record.patient_name || '')}</td>
			<td data-label="Brand">${escapeHtml(record.brand || '')}</td>
			<td data-label="Model">${escapeHtml(record.model || '')}</td>
			<td data-label="Ear Side">${escapeHtml(record.ear_side || '')}</td>
			<td data-label="Date Issued">${escapeHtml(formatDatePretty(record.date_issued))}</td>
			<td data-label="Action">
				<button type="button" class="btn btn-sm btn-outline-danger icon-btn" title="Remove" onclick="deleteHearingAid(${record.id})">
					<i class="bi bi-trash"></i>
				</button>
			</td>
		`;
		tbody.appendChild(tr);
	}

	async function loadHearingAids() {
		const tbody = document.getElementById('ha-results-tbody');
		if (!tbody) return;

		try {
			const apiUrl = `/staff/api/session/patient/${id}/hearing-aids`;
			const response = await fetch(apiUrl, {
				headers: { 
					'Accept': 'application/json',
					'X-CSRF-TOKEN': CSRF
				},
				credentials: 'same-origin'
			});

			if (!response.ok) {
				throw new Error(`Failed to fetch hearing aids: ${response.status}`);
			}

			const data = await response.json();
			const hearingAids = data.data || [];

			if (!hearingAids.length) {
				// If no records at all, hide the entire section if present
				const card = document.getElementById('hearing-aid-records');
				if (card) {
					const hasRows = tbody.querySelectorAll('tr[data-id]').length > 0;
					if (!hasRows) card.remove();
				}
				return;
			}

			// We have fresh rows from API: replace table body
			tbody.innerHTML = '';
			hearingAids.forEach(appendHearingAidRow);
		} catch (error) {
			const card = document.getElementById('hearing-aid-records');
			if (card) card.remove();
		}
	}

	// Make deleteHearingAid globally available
	window.deleteHearingAid = async function(hearingAidId) {
		if (!confirm('Are you sure you want to delete this hearing aid record?')) return;

		try {
			const response = await fetch(`/staff/api/session/patient/${id}/hearing-aids/${hearingAidId}`, {
				method: 'DELETE',
				headers: {
					'X-CSRF-TOKEN': CSRF,
					'Accept': 'application/json'
				},
				credentials: 'same-origin'
			});

			if (!response.ok) throw new Error('Failed to delete hearing aid');

			// Remove row from table
			const tbody = document.getElementById('ha-results-tbody');
			const row = tbody?.querySelector(`tr[data-id="${hearingAidId}"]`);
			if (row) row.remove();

			// If table has no more data rows, remove the whole section to honor hide-if-empty
			if (tbody && tbody.querySelectorAll('tr[data-id]').length === 0) {
				const card = document.getElementById('hearing-aid-records');
				if (card) card.remove();
			}

		} catch (error) {
			console.error('Error deleting hearing aid:', error);
			alert('Failed to delete hearing aid record. Please try again.');
		}
	};

	// Delete a service result row (session-backed)
	window.deleteServiceResult = async function(serviceKey, resultId){
		const id = {{ json_encode($id) }};
		const csrfEl = document.querySelector('meta[name="csrf-token"]');
		const CSRF = csrfEl ? csrfEl.content : '';
		if (!confirm('Delete this record?')) return;
		try {
			const res = await fetch(`/staff/api/session/patient/${id}/services/${encodeURIComponent(serviceKey)}/${encodeURIComponent(resultId)}`, {
				method: 'DELETE',
				headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
				credentials: 'same-origin'
			});
			if (!res.ok) throw new Error('Delete failed');
			// Remove the result card/row
			const el = document.querySelector(`[data-id="${CSS.escape(String(resultId))}"]`);
			if (el) el.remove();
			// If this service section now has no more result items, hide the entire section
			const section = document.getElementById(`svc-${serviceKey}`);
			if (section && !section.querySelector('[data-id]')) {
				section.remove();
			}
		} catch(e){ alert('Failed to delete.'); }
	};

	function checkForSuccessMessage() {
		const urlParams = new URLSearchParams(window.location.search);
		if (urlParams.has('ha_success')) {
			// Show success message
			const alertHtml = `
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<i class="bi bi-check-circle me-1"></i> Hearing aid record saved successfully!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			`;
			
			const cardBody = document.querySelector('.col-lg-8 .card-body');
			if (cardBody) {
				cardBody.insertAdjacentHTML('afterbegin', alertHtml);
				
				// Auto-dismiss after 3 seconds
				setTimeout(() => {
					const alert = cardBody.querySelector('.alert');
					if (alert) {
						try {
							bootstrap.Alert.getOrCreateInstance(alert).close();
						} catch(e) {
							alert.remove();
						}
					}
				}, 3000);
			}

			// Remove the parameter from URL without refreshing
			urlParams.delete('ha_success');
			const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
			window.history.replaceState({}, '', newUrl);

			// Scroll to hearing aid records section
			setTimeout(() => {
				const target = document.getElementById('hearing-aid-records');
				if (target) target.scrollIntoView({ behavior:'smooth', block:'start' });
			}, 100);
		}

		// Service save success (svc_success=pta|tym|speech|oae|abr|assr)
		if (urlParams.has('svc_success')) {
			const svc = urlParams.get('svc_success');
			const labelMap = { pta:'PTA', tym:'Tympanometry', speech:'Speech Audiometry', oae:'OAE', abr:'ABR', assr:'ASSR' };
			const label = labelMap[svc] || 'Service';
			const alertHtml = `
				<div class="alert alert-success alert-dismissible fade show" role="alert">
					<i class="bi bi-check-circle me-1"></i> ${label} record saved successfully!
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>
			`;
			const cardBody = document.querySelector('.col-lg-8 .card-body');
			if (cardBody) {
				cardBody.insertAdjacentHTML('afterbegin', alertHtml);
				setTimeout(() => {
					const alert = cardBody.querySelector('.alert');
					if (alert) {
						try { bootstrap.Alert.getOrCreateInstance(alert).close(); } catch(e) { alert.remove(); }
					}
				}, 3000);
			}
			// Clean URL
			urlParams.delete('svc_success');
			const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
			window.history.replaceState({}, '', newUrl);
			// Scroll to the corresponding section
			setTimeout(() => {
				const anchor = document.getElementById(`svc-${svc}`);
				if (anchor) anchor.scrollIntoView({ behavior:'smooth', block:'start' });
			}, 120);
		}
	}

	async function init(){
		await load();
		await loadHearingAids();
		checkForSuccessMessage();
		
		// Print functionality
		const printButton = document.getElementById('printButton');
		if (printButton) {
			printButton.addEventListener('click', function(){
				window.print();
			});
		}
	}

	document.addEventListener('DOMContentLoaded', init);
})();
</script>
@endpush

@push('scripts')
<script>
(function(){
	// Render svcRecords passed from controller into the patientTestsContainer
	var svcRecords = {};
	try { svcRecords = {!! json_encode($svcRecords ?? []) !!}; } catch(e) { svcRecords = {}; }

	var hearingFallback = [];
	try { hearingFallback = {!! json_encode($hearingAids ?? []) !!}; } catch(e) { hearingFallback = []; }

	document.addEventListener('DOMContentLoaded', function(){
		var container = document.getElementById('patientTestsContainer');
		if(!container) return;
		container.innerHTML = '';

		function renderHtml(html){ var d = document.createElement('div'); d.innerHTML = html; container.appendChild(d); }

		function simple(title, headers, rows){
			var h = '<div class="card shadow-sm mb-2"><div class="card-body p-2">';
			h += '<div class="d-flex justify-content-between align-items-center mb-2"><h6 class="mb-0">'+title+'</h6></div>';
			h += '<div class="table-responsive"><table class="table table-bordered align-middle small"><thead class="table-light"><tr>';
			headers.forEach(function(th){ h += '<th>'+th+'</th>'; });
			h += '</tr></thead><tbody>';
			rows.forEach(function(r){ h += '<tr>' + r.map(function(c){ return '<td>'+ (c ?? '') +'</td>'; }).join('') + '</tr>'; });
			h += '</tbody></table></div></div></div>';
			renderHtml(h);
		}

		function detailedHearingAid(title, date, data){
			var h = '<div class="card shadow-sm mb-3" style="border:1px solid #e2e8f0; border-radius:10px;">';
			h += '<div class="card-body">';
			h += '<div class="text-center mb-2">';
			h += '<h5 class="mb-0" style="font-weight:700;">' + title + '</h5>';
			h += '<div class="text-muted" style="font-size:.9rem;">Date: ' + date + '</div>';
			h += '</div>';
			h += '<div class="table-responsive">';
			h += '<table class="table table-bordered align-middle text-center">';
			h += '<thead class="table-light">';
			h += '<tr>';
			h += '<th style="width:200px;">Brand</th>';
			h += '<th style="width:200px;">Model</th>';
			h += '<th style="width:150px;">Ear Side</th>';
			h += '<th style="width:150px;">Date Issued</th>';
			h += '</tr>';
			h += '</thead>';
			h += '<tbody>';
			h += '<tr>';
			h += '<td>' + (data.brand || 'N/A') + '</td>';
			h += '<td>' + (data.model || 'N/A') + '</td>';
			h += '<td>' + (data.ear_side || 'N/A') + '</td>';
			h += '<td>' + (data.date_issued || data.test_date || 'N/A') + '</td>';
			h += '</tr>';
			h += '</tbody>';
			h += '</table>';
			h += '</div>';
			h += '</div>';
			h += '</div>';
			renderHtml(h);
		}

		var any = false;
		var hearing = svcRecords.hearing || (svcRecords['hearing'] || []);
		if ((!hearing || hearing.length === 0) && hearingFallback && hearingFallback.length) {
			hearing = hearingFallback;
		}

		if (hearing && hearing.length) {
			any = true;
			// Show only one table for hearing aid fitting, regardless of number of records
			var hearingRows = [];
			var latestDate = '';
			
			hearing.forEach(function(r){
				var dateStr = '';
				if (r.date_issued) {
					var date = new Date(r.date_issued);
					dateStr = date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
				} else if (r.test_date) {
					var date = new Date(r.test_date);
					dateStr = date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
				}
				
				hearingRows.push([r.brand||'', r.model||'', r.ear_side||'', dateStr]);
				
				// Use the latest date for the table header
				if (dateStr && (!latestDate || dateStr > latestDate)) {
					latestDate = dateStr;
				}
			});
			
			// Create a single table with multiple rows
			var h = '<div class="card shadow-sm mb-3" style="border:1px solid #e2e8f0; border-radius:10px;">';
			h += '<div class="card-body">';
			h += '<div class="text-center mb-2">';
			h += '<h5 class="mb-0" style="font-weight:700;">Hearing Aid Fitting</h5>';
			h += '<div class="text-muted" style="font-size:.9rem;">Date: ' + latestDate + '</div>';
			h += '</div>';
			h += '<div class="table-responsive">';
			h += '<table class="table table-bordered align-middle text-center">';
			h += '<thead class="table-light">';
			h += '<tr>';
			h += '<th style="width:200px;">Brand</th>';
			h += '<th style="width:200px;">Model</th>';
			h += '<th style="width:150px;">Ear Side</th>';
			h += '<th style="width:150px;">Date Issued</th>';
			h += '</tr>';
			h += '</thead>';
			h += '<tbody>';
			
			hearingRows.forEach(function(row){
				h += '<tr>';
				h += '<td>' + row[0] + '</td>';
				h += '<td>' + row[1] + '</td>';
				h += '<td>' + row[2] + '</td>';
				h += '<td>' + row[3] + '</td>';
				h += '</tr>';
			});
			
			h += '</tbody>';
			h += '</table>';
			h += '</div>';
			h += '</div>';
			h += '</div>';
			
			renderHtml(h);
		}

		if(!any){ container.innerHTML = '<div class="text-muted">No hearing aid fitting results recorded.</div>'; }
	});
})();
</script>
@endpush
