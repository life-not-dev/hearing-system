@extends('layouts.patient')

@section('title', 'Book Appointment')

@push('head')
<style>
		.required { color: #d93025; margin-left:4px; }
		.form-panel { background:#fff; border:1px solid #e6e6e6; padding:20px; }
		.side-actions { background:#efefef; padding:18px; }
		.appoint-btn { background:#138a0a; color:#fff; }
</style>
@endpush

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<h4 class="mb-3">Appointment</h4>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-3 d-none d-lg-block">
			<div class="side-actions">
				<ul class="list-unstyled">
					<li class="mb-3"><a href="#" id="action-book" style="text-decoration:none; color:inherit;"><strong>&bull; Book New Appointment</strong></a></li>
					<li class="mb-3"><a href="#" id="action-cancel" style="text-decoration:none; color:inherit;">&bull; Cancel Appointment</a></li>
					<li class="mb-3"><a href="#" id="action-reschedule" style="text-decoration:none; color:inherit;">&bull; Reschedule Appointment</a></li>
					<li class="mb-3"><a href="#" id="action-history" style="text-decoration:none; color:inherit;">&bull; Appointment History</a></li>
				</ul>
			</div>
		</div>

		<div class="col-lg-9">
			<div class="form-panel">
				<div id="bookingContainer">
					<form id="appointmentForm" novalidate>
					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">First Name:<span class="required">*</span></label>
								<input type="text" name="first_name" class="form-control" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Surname:<span class="required">*</span></label>
								<input type="text" name="surname" class="form-control" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Middle:</label>
								<input type="text" name="middle" class="form-control">
							</div>

							<div class="mb-3">
								<label class="form-label">Age:<span class="required">*</span></label>
								<input type="number" min="0" name="age" class="form-control" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Date of Birth:<span class="required">*</span></label>
								<div class="d-flex gap-2">
									<input type="number" name="dob_dd" placeholder="DD" min="1" max="31" class="form-control" required>
									<input type="number" name="dob_mm" placeholder="MM" min="1" max="12" class="form-control" required>
									<input type="number" name="dob_yyyy" placeholder="YYYY" min="1900" max="2099" class="form-control" required>
								</div>
							</div>

							<div class="mb-3">
								<label class="form-label">Address:<span class="required">*</span></label>
								<input type="text" name="address" class="form-control" required>
							</div>

							<div class="mb-3">
								<label class="form-label">Contact:<span class="required">*</span></label>
								<input type="tel" name="contact" class="form-control" id="contact" required>
								<div class="invalid-feedback" id="contactError"></div>
							</div>

							<div class="mb-3">
								<label class="form-label">Email:<span class="required">*</span></label>
								<input type="email" name="email" class="form-control" id="email" required>
								<div class="invalid-feedback" id="emailError"></div>
							</div>

							<div class="mb-3">
								<label class="form-label">Gender:<span class="required">*</span></label>
								<select name="gender" class="form-select" id="gender" required>
<div class="invalid-feedback" id="genderError"></div>
									<option value="">Select gender</option>
									<option>Male</option>
									<option>Female</option>
									<option>Other</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">Services:<span class="required">*</span></label>
								<select name="service" class="form-select" required>
									<option value="">Choose service</option>
									<option>OAE</option>
									<option>ABR</option>
									<option>PTA</option>
									<option>Tympanometry</option>
								</select>
							</div>

							<div class="mb-3">
								<label class="form-label">Patient type :<span class="required">*</span></label>
								<select name="patient_type" class="form-select" required>
									<option value="">Select</option>
									<option>Regular</option>
									<option>PWD</option>
                                    <option>Senior Citezen</option>
								</select>
							</div>

							<div class="mb-3">
								<label class="form-label">Branch:<span class="required">*</span></label>
								<select name="branch" class="form-select" required>
									<option value="">Select branch</option>
									<option>CDO</option>
									<option>Butuan</option>
									<option>Davao</option>
								</select>
							</div>

							<div class="mb-3">
								<label class="form-label">Referred by</label>
								<input type="text" name="referred_by" class="form-control">
							</div>

							<div class="mb-3">
								<label class="form-label">Purpose :<span class="required">*</span></label>
								<textarea name="purpose" rows="3" class="form-control" required></textarea>
							</div>

							<div class="mb-3">
								<label class="form-label">Medical History :</label>
								<textarea name="medical_history" rows="4" class="form-control"></textarea>
							</div>

							<div class="mb-3">
								<h6>Schedule</h6>
								<input type="date" name="schedule_date" class="form-control mb-2" required>
								<div class="input-group">
									<input type="time" name="schedule_time" class="form-control" required>
									<span class="input-group-text"><i class="fa fa-clock"></i></span>
								</div>
							</div>

							<div class="mt-4 text-center">
								<button type="submit" class="btn appoint-btn">Appoint</button>
							</div>
						</div>
					</div>
				</form>
				</div>

				<!-- Cancel appointment panel (hidden by default) -->
				<div id="cancelContainer" style="display:none;">
					<div style="background:#f5f5f5; border:1px solid #bfbfbf; padding:22px;">
						<h4>Cancel Appointment</h4>
						<p class="text-muted">Please fill out the form below to cancel your appointment.</p>
						<div style="margin-top:12px;">
							<div><strong>Patient name</strong></div>
							<div id="cancelPatientName" style="margin-bottom:18px;">—</div>

							<div class="mb-3">
								<label class="form-label">MM - DD - YYYY</label>
								<input type="date" id="cancel_date" class="form-control" />
							</div>

							<!-- rescheduleContainer relocated below so it is a sibling of cancel/booking containers -->

							<div class="mb-3">
								<label class="form-label">Notes</label>
								<textarea id="cancel_notes" rows="5" class="form-control"></textarea>
							</div>

							<!-- rescheduleContainer removed from here and placed after cancelContainer -->

							<div class="text-center">
								<button id="cancelConfirmBtn" class="btn appoint-btn">Confirm</button>
							</div>
						</div>
					</div>
				</div>

					<!-- Reschedule appointment panel (hidden by default) -->
					<div id="rescheduleContainer" style="display:none; margin:18px auto; max-width:760px;">
						<div style="background:#f5f5f5; border:1px solid #bfbfbf; padding:22px;">
							<h4 style="text-align:center;">Appointment Reschedule</h4>
							<div style="margin-top:12px;">
								<div><strong>Patient name</strong></div>
								<div id="reschedulePatientName" style="margin-bottom:18px;">—</div>

								<div class="row mb-3">
									<div class="col-md-6">
										<label class="form-label">First name</label>
										<input type="text" id="res_first" class="form-control" />
									</div>
									<div class="col-md-6">
										<label class="form-label">Last name</label>
										<input type="text" id="res_last" class="form-control" />
									</div>
								</div>

								<div class="mb-3">
									<label class="form-label">Email</label>
									<input type="email" id="res_email" class="form-control" />
								</div>

								<div class="row mb-3">
									<div class="col-md-6">
										<label class="form-label">Initial Appointment Date</label>
										<input type="date" id="res_initial_date" class="form-control" />
									</div>
									<div class="col-md-6">
										<label class="form-label">Time</label>
										<input type="time" id="res_initial_time" class="form-control" />
									</div>
								</div>

								<div class="row mb-3">
									<div class="col-md-6">
										<label class="form-label">New Appointment Date</label>
										<input type="date" id="res_new_date" class="form-control" />
									</div>
									<div class="col-md-6">
										<label class="form-label">Time</label>
										<input type="time" id="res_new_time" class="form-control" />
									</div>
								</div>

								<div class="mb-3">
									<label class="form-label">Reason for Rescheduling</label>
									<textarea id="res_reason" rows="4" class="form-control"></textarea>
								</div>

								<div class="text-center">
									<button id="rescheduleConfirmBtn" class="btn appoint-btn">Confirm</button>
								</div>
							</div>
						</div>
					</div>
			</div>

			<!-- Appointment History (hidden by default) -->
			<div id="historyContainer" style="display:none; margin:18px auto; max-width:1000px;">
				<div style="background:#f7f7f7; border:1px solid #cfcfcf; padding:26px;">
					<h4 style="font-weight:700; margin-bottom:18px;">Appointment History</h4>
					<div style="overflow:auto;">
					<table class="table table-sm" style="font-size:0.9rem; border-collapse:collapse;">
						<thead>
							<tr style="border-bottom:1px solid #222;">
								<th>Patient name</th>
								<th>Patient type</th>
								<th>Services</th>
								<th>Time</th>
								<th>Date</th>
								<th>Branch</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Bency Cabugsa</td>
								<td>Senior citizen</td>
								<td>Puretone</td>
								<td>8:00 / 10:00 AM</td>
								<td>Jun 8, 2024</td>
								<td>Carmen CDO</td>
								<td>Completed</td>
							</tr>
							<!-- more rows can be added here -->
						</tbody>
						</table>
					</div>
				</div>
			</div>
	</div>
</div>

<!-- Preview / Confirm Modal -->
<div class="modal fade" id="appointmentPreviewModal" tabindex="-1" aria-labelledby="appointmentPreviewLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="appointmentPreviewLabel">Confirm Appointment</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div id="appointmentPreviewBody"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Back</button>
				<button type="button" id="confirmAppointmentBtn" class="btn btn-success">Confirm Booking</button>
			</div>
		</div>
	</div>
</div>

<!-- Centered confirm overlay (matches requested style) -->
<div id="appointmentConfirmOverlay" style="display:none; position:fixed; left:50%; top:40%; transform:translate(-50%,-50%); z-index:2000;">
	<div style="background:#fff; border:3px solid #13a313; padding:18px 28px; box-shadow:0 6px 18px rgba(0,0,0,0.12); text-align:center; min-width:420px; border-radius:4px;">
		<div style="font-size:28px; color:#13a313; font-weight:700; display:flex; align-items:center; justify-content:center; gap:12px;">
			<span style="background:#e9f7ec; color:#13a313; width:36px; height:36px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; font-size:18px;">.</span>
			<span>Confirm</span>
		</div>
		<div style="font-size:18px; color:#13a313; font-weight:600; margin-top:6px;">Booking appointment</div>
	</div>
</div>

@endsection

@push('scripts')
<script>
(function(){
	const form = document.getElementById('appointmentForm');
	<script>
	form.addEventListener('submit', function(e) {
		let hasError = false;
		// Contact validation
		const contact = document.getElementById('contact');
		const contactError = document.getElementById('contactError');
		contactError.textContent = '';
		contact.classList.remove('is-invalid');
		if (!/^[0-9]{11}$/.test(contact.value)) {
			contactError.textContent = 'Contact must be exactly 11 digits (numbers only).';
			contact.classList.add('is-invalid');
			hasError = true;
		}
		// Email validation
		const email = document.getElementById('email');
		const emailError = document.getElementById('emailError');
		emailError.textContent = '';
		email.classList.remove('is-invalid');
		const forbiddenEmail = /[<>;=\',]/;
		const emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
		if (!emailPattern.test(email.value)) {
			emailError.textContent = 'Must follow email format (example: name@example.com).';
			email.classList.add('is-invalid');
			hasError = true;
		} else if (forbiddenEmail.test(email.value)) {
			emailError.textContent = 'Do not input special characters <, >, ;, =, \\' or ,';
			email.classList.add('is-invalid');
			hasError = true;
		}
		// Gender validation
		const gender = document.getElementById('gender');
		const genderError = document.getElementById('genderError');
		genderError.textContent = '';
		gender.classList.remove('is-invalid');
		if (!gender.value || (gender.value !== 'Male' && gender.value !== 'Female')) {
			genderError.textContent = 'Please select Male or Female.';
			gender.classList.add('is-invalid');
			hasError = true;
		}
		if (hasError) {
			e.preventDefault();
		}
	});
	</script>
	const previewModalEl = document.getElementById('appointmentPreviewModal');
	const previewBody = document.getElementById('appointmentPreviewBody');
	const confirmBtn = document.getElementById('confirmAppointmentBtn');

	function gatherFormData(){
		const fd = new FormData(form);
		const obj = {};
		for(const [k,v] of fd.entries()){ obj[k]=v; }
		return obj;
	}

	function renderPreview(data){
		const html = `
			<div class="row">
				<div class="col-md-6">
					<h6>Patient Details</h6>
					<p><strong>Name:</strong> ${data.first_name || ''} ${data.middle || ''} ${data.surname || ''}</p>
					<p><strong>Age:</strong> ${data.age || ''}</p>
					<p><strong>DOB:</strong> ${data.dob_mm || ''}/${data.dob_dd || ''}/${data.dob_yyyy || ''}</p>
					<p><strong>Contact:</strong> ${data.contact || ''}</p>
					<p><strong>Email:</strong> ${data.email || ''}</p>
					<p><strong>Address:</strong> ${data.address || ''}</p>
				</div>
				<div class="col-md-6">
					<h6>Appointment</h6>
					<p><strong>Service:</strong> ${data.service || ''}</p>
					<p><strong>Patient type:</strong> ${data.patient_type || ''}</p>
					<p><strong>Branch:</strong> ${data.branch || ''}</p>
					<p><strong>Purpose:</strong> ${data.purpose || ''}</p>
					<p><strong>Medical history:</strong> ${data.medical_history || ''}</p>
					<p><strong>Schedule:</strong> ${data.schedule_date || ''} ${data.schedule_time || ''}</p>
				</div>
			</div>
		`;
		previewBody.innerHTML = html;
	}

	form.addEventListener('submit', function(e){
		e.preventDefault();
		// client-side validation (HTML required handles most)
		const data = gatherFormData();
		renderPreview(data);
		const bs = new bootstrap.Modal(previewModalEl);
		bs.show();
	});

	confirmBtn.addEventListener('click', function(){
		// Submit to backend
		const bs = bootstrap.Modal.getInstance(previewModalEl);
		if(bs) bs.hide();
		const fd = new FormData(form);
		// Map fields to backend names
		const payload = {
			first_name: fd.get('first_name') || '',
			surname: fd.get('surname') || '',
			middle: fd.get('middle') || '',
			age: fd.get('age') || '',
			birth_day: fd.get('dob_dd') || '',
			birth_month: fd.get('dob_mm') || '',
			birth_year: fd.get('dob_yyyy') || '',
			address: fd.get('address') || '',
			contact: fd.get('contact') || '',
			email: fd.get('email') || '',
			gender: fd.get('gender') || '',
			service: fd.get('service') || '',
			patient_type: fd.get('patient_type') || '',
			branch: fd.get('branch') || '',
			appointment_date: fd.get('schedule_date') || '',
			appointment_time: (fd.get('schedule_time')||'').slice(0,5),
			purpose: fd.get('purpose') || '',
			medical_history: fd.get('medical_history') || ''
		};
		fetch('{{ route('book.confirm') }}', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			body: JSON.stringify(payload)
		}).then(async (r)=>{
			if(!r.ok){ const j = await r.json().catch(()=>null); throw new Error((j&&j.message)||'Failed'); }
			return r.json();
		}).then((j)=>{
			// success UI
			const overlay = document.getElementById('appointmentConfirmOverlay');
			if(overlay){
				overlay.style.display = 'block';
				setTimeout(()=>{ overlay.style.display = 'none'; form.reset(); }, 1800);
			}
		}).catch(async (e)=>{
			alert(e.message||'Failed to create appointment');
		});
	});

	// Side action handlers: show booking or cancel panels
	const actionBook = document.getElementById('action-book');
	const actionCancel = document.getElementById('action-cancel');
	const bookingContainer = document.getElementById('bookingContainer');
	const cancelContainer = document.getElementById('cancelContainer');
	const cancelPatientName = document.getElementById('cancelPatientName');
	const cancelConfirmBtn = document.getElementById('cancelConfirmBtn');

	function showBooking(){ bookingContainer.style.display = 'block'; cancelContainer.style.display = 'none'; }
	function showCancel(){ bookingContainer.style.display = 'none'; cancelContainer.style.display = 'block'; }

	if(actionBook) actionBook.addEventListener('click', function(e){ e.preventDefault(); showBooking(); });
	if(actionCancel) actionCancel.addEventListener('click', function(e){ e.preventDefault();
		// populate patient name from form if available
		const fname = form.querySelector('input[name="first_name"]').value || '';
		const m = form.querySelector('input[name="middle"]').value || '';
		const s = form.querySelector('input[name="surname"]').value || '';
		cancelPatientName.textContent = [fname, m, s].filter(Boolean).join(' ') || 'Bency Cabugsa';
		showCancel();
	});

	if(cancelConfirmBtn) cancelConfirmBtn.addEventListener('click', function(e){ e.preventDefault();
		// brief success display
		const note = document.createElement('div');
		note.className = 'alert alert-success mt-3';
		note.textContent = 'Appointment cancelled (client-only demo).';
		cancelContainer.parentElement.insertBefore(note, cancelContainer);
		setTimeout(()=>{ if(note) note.remove(); }, 4000);
		// return to booking view
		setTimeout(()=>{ showBooking(); }, 600);
	});

	// Reschedule handlers
	const actionReschedule = document.getElementById('action-reschedule');
	const rescheduleContainer = document.getElementById('rescheduleContainer');
	const reschedulePatientName = document.getElementById('reschedulePatientName');
	const rescheduleConfirmBtn = document.getElementById('rescheduleConfirmBtn');

	const actionHistory = document.getElementById('action-history');
	const historyContainer = document.getElementById('historyContainer');

	function showHistory(){ bookingContainer.style.display = 'none'; cancelContainer.style.display = 'none'; rescheduleContainer.style.display = 'none'; historyContainer.style.display = 'block'; }

	function showReschedule(){ bookingContainer.style.display = 'none'; cancelContainer.style.display = 'none'; rescheduleContainer.style.display = 'block'; }

	if(actionReschedule) actionReschedule.addEventListener('click', function(e){ e.preventDefault();
		const fname = form.querySelector('input[name="first_name"]').value || '';
		const m = form.querySelector('input[name="middle"]').value || '';
		const s = form.querySelector('input[name="surname"]').value || '';
		reschedulePatientName.textContent = [fname, m, s].filter(Boolean).join(' ') || '—';
		// set active styling
		if(actionReschedule) actionReschedule.style.fontWeight = '700';
		if(actionBook) actionBook.style.fontWeight = 'normal';
		if(actionCancel) actionCancel.style.fontWeight = 'normal';
		showReschedule();
	});

	// initialize default view: booking visible, others hidden
	(function initDefault(){
		if(bookingContainer) bookingContainer.style.display = 'block';
		if(cancelContainer) cancelContainer.style.display = 'none';
		if(rescheduleContainer) rescheduleContainer.style.display = 'none';
		// default active is Book
		if(actionBook) actionBook.style.fontWeight = '700';
		if(actionCancel) actionCancel.style.fontWeight = 'normal';
		if(actionReschedule) actionReschedule.style.fontWeight = 'normal';
	})();

	if(rescheduleConfirmBtn) rescheduleConfirmBtn.addEventListener('click', function(e){ e.preventDefault();
		const note = document.createElement('div');
		note.className = 'alert alert-success mt-3';
		note.textContent = 'Appointment rescheduled (client-only demo).';
		rescheduleContainer.parentElement.insertBefore(note, rescheduleContainer);
		setTimeout(()=>{ if(note) note.remove(); }, 4000);
		setTimeout(()=>{ showBooking(); if(actionBook) actionBook.style.fontWeight = '700'; if(actionReschedule) actionReschedule.style.fontWeight = 'normal'; }, 600);
	});

	if(actionHistory) actionHistory.addEventListener('click', function(e){ e.preventDefault();
		if(actionHistory) actionHistory.style.fontWeight = '700';
		if(actionBook) actionBook.style.fontWeight = 'normal';
		if(actionReschedule) actionReschedule.style.fontWeight = 'normal';
		if(actionCancel) actionCancel.style.fontWeight = 'normal';
		showHistory();
	});

})();
</script>
@endpush

@push('scripts')
<script>
// Slot enforcement for patient appointment page (schedule_date/schedule_time)
(function(){
	const form = document.getElementById('appointmentForm');
	if(!form) return;
	const allowed = ['08:00','10:00','12:00','14:00','15:00'];
	const dateEl = form.querySelector('input[name="schedule_date"]');
	const timeEl = form.querySelector('input[name="schedule_time"]');
	if(!dateEl || !timeEl) return;
	const dlId = 'slotOptionsPA';
	let dl = document.getElementById(dlId);
	if(!dl){ dl = document.createElement('datalist'); dl.id = dlId; allowed.forEach(t=>{ const o=document.createElement('option'); o.value=t; dl.appendChild(o); }); document.body.appendChild(dl);} 
	try{ timeEl.setAttribute('list', dlId); }catch(e){}
	function ensureErr(){ let e=timeEl.nextElementSibling; if(!e||!e.classList||!e.classList.contains('invalid-feedback')){ e=document.createElement('div'); e.className='invalid-feedback'; timeEl.parentNode.appendChild(e);} return e; }
	function setErr(m){ const e=ensureErr(); e.textContent=m||''; if(m){ timeEl.classList.add('is-invalid'); } else { timeEl.classList.remove('is-invalid'); } }
	function qs(p){ return Object.entries(p).map(([k,v])=> `${encodeURIComponent(k)}=${encodeURIComponent(v??'')}`).join('&'); }
	async function j(u){ const r=await fetch(u,{headers:{'Accept':'application/json'}}); if(!r.ok) throw new Error('x'); return r.json(); }
	function getBranch(){ const sel=form.querySelector('select[name="branch"]'); return sel? sel.value: ''; }
	async function suggest(){ if(!dateEl.value) return; try{ const d=await j(`/api/appointments/next-slot?${qs({appointment_date: dateEl.value, branch: getBranch()})}`); if(d&&d.next_available){ timeEl.value=d.next_available; setErr(''); } else { setErr('No available slots for the selected date.'); timeEl.value=''; } }catch(err){} }
	async function validateTime(){ const dv=dateEl.value; let tv=(timeEl.value||'').slice(0,5); if(!dv||!tv) return; if(!allowed.includes(tv)){ setErr('Start time must be 08:00, 10:00, 12:00, 14:00, or 15:00.'); return; } try{ const d=await j(`/api/appointments/check-slot?${qs({appointment_date: dv, appointment_time: tv, branch: getBranch()})}`); if(!d.within_hours||!d.allowed_start){ setErr('Selected time is outside allowed clinic hours.'); return; } if(!d.available){ if(d.next_available){ timeEl.value=d.next_available; setErr('That time is already booked. Next available has been set.'); setTimeout(()=>setErr(''),2500);} else { setErr('No available slots remain for this date.'); } } else { setErr(''); } }catch(err){} }
	dateEl.addEventListener('change', suggest);
	const branchSel = form.querySelector('select[name="branch"]');
	if(branchSel){ branchSel.addEventListener('change', suggest); }
	timeEl.addEventListener('change', validateTime);
	if(dateEl.value){ suggest(); }
})();
</script>
@endpush

