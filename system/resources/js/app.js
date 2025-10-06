import './bootstrap';

// Simple client-side form handling for the New Appointment modal
document.addEventListener('DOMContentLoaded', function () {
	const form = document.getElementById('newAppointmentForm');
	const preview = document.getElementById('appointmentPreview');

	function updatePreview() {
		const fname = document.getElementById('firstname').value.trim();
		const sname = document.getElementById('surname').value.trim();
		const service = document.getElementById('service').value;
		const date = document.getElementById('appointment_date').value;
		const time = document.getElementById('appointment_time').value;

		if (!fname && !sname && !service && !date && !time) {
			preview.textContent = 'No preview yet.';
			return;
		}

		const parts = [];
		if (fname || sname) parts.push(`${fname} ${sname}`.trim());
		if (service) parts.push(service);
		if (date) parts.push(new Date(date).toLocaleDateString());
		if (time) parts.push(time);

		preview.textContent = parts.join(' • ');
	}

	// update preview on input change
	['firstname', 'surname', 'service', 'appointment_date', 'appointment_time'].forEach(id => {
		const el = document.getElementById(id);
		if (el) el.addEventListener('input', updatePreview);
	});

	if (form) {
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			// simple HTML5 validation feedback
			if (!form.checkValidity()) {
				form.classList.add('was-validated');
				return;
			}

			// gather data (here we'd normally POST via fetch)
			const data = new FormData(form);
			const row = document.createElement('tr');
			// build displayed cells
			row.innerHTML = `
				<td>${escapeHtml(data.get('firstname') || '')}</td>
				<td>${escapeHtml(data.get('surname') || '')}</td>
				<td>${escapeHtml(data.get('service') || '')}</td>
				<td>${escapeHtml(data.get('email') || '')}</td>
				<td>${escapeHtml(data.get('appointment_time') || '')}</td>
				<td>${escapeHtml(data.get('appointment_date') || '')}</td>
				<td>
					<button class="btn btn-sm btn-outline-primary me-1 btn-view" title="View"><i class="fa fa-eye"></i></button>
					<button class="btn btn-sm btn-outline-success me-1" title="Accept"><i class="fa fa-check"></i></button>
					<button class="btn btn-sm btn-outline-danger" title="Decline"><i class="fa fa-times"></i></button>
				</td>
			`;

			// attach data attributes for extra fields so the view modal can read them
			row.setAttribute('data-middle', data.get('middle') || '');
			row.setAttribute('data-age', data.get('age') || '');
			// birthdate join
			const bd_day = data.get('bd_day');
			const bd_month = data.get('bd_month');
			const bd_year = data.get('bd_year');
			if (bd_day || bd_month || bd_year) row.setAttribute('data-birthdate', `${bd_day || ''}/${bd_month || ''}/${bd_year || ''}`);
			row.setAttribute('data-address', data.get('address') || '');
			row.setAttribute('data-patient-type', data.get('patient_type') || '');
			row.setAttribute('data-referred-by', data.get('referred_by') || '');
			row.setAttribute('data-purpose', data.get('purpose') || '');
			row.setAttribute('data-medical-history', data.get('medical_history') || '');
			row.setAttribute('data-gender', data.get('gender') || '');
			row.setAttribute('data-branch', data.get('branch') || '');

			const table = document.querySelector('#appointmentsTable tbody');
			if (table) table.prepend(row);

			// reset form and close modal
			form.reset();
			form.classList.remove('was-validated');
			updatePreview();
			const modalEl = document.getElementById('newAppointmentModal');
			if (modalEl && typeof bootstrap !== 'undefined') {
				const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
				modal.hide();
			}
		});
	}


	// handle view button click - populate and show the View Info modal

	function handleViewClick(btn) {
		try {
			const row = btn.closest('tr');
			if (!row) return;
			// read cells by index (matches table columns)
			const cells = row.children;
			const firstname = cells[0] ? cells[0].textContent.trim() : '';
			const surname = cells[1] ? cells[1].textContent.trim() : '';
			const service = cells[2] ? cells[2].textContent.trim() : '';
			const email = cells[3] ? cells[3].textContent.trim() : '';
			const time = cells[4] ? cells[4].textContent.trim() : '';
			const date = cells[5] ? cells[5].textContent.trim() : '';

			// optional extra columns or data-* attributes
			const middle = row.getAttribute('data-middle') || '';
			const contact = row.getAttribute('data-contact') || '';
			const age = row.getAttribute('data-age') || '';
			const gender = row.getAttribute('data-gender') || '';
			const notes = row.getAttribute('data-notes') || '';
			const address = row.getAttribute('data-address') || '';
			const patientType = row.getAttribute('data-patient-type') || '';
			const referredBy = row.getAttribute('data-referred-by') || '';
			const purpose = row.getAttribute('data-purpose') || '';
			const medicalHistory = row.getAttribute('data-medical-history') || '';
			const branch = row.getAttribute('data-branch') || '';
			const birthdate = row.getAttribute('data-birthdate') || '';

			// populate modal fields using helper (setIf)
			function setIf(id, value) {
				const el = document.getElementById(id);
				if (!el) return;
				el.textContent = value || '';
			}

			setIf('vi_name', firstname);
			setIf('vi_surname', surname);
			setIf('vi_middle', middle);
			setIf('vi_service', service);
			setIf('vi_email', email);
			setIf('vi_time', time);
			setIf('vi_date', date);
			setIf('vi_contact', contact);
			setIf('vi_age', age);
			setIf('vi_gender', gender);
			setIf('vi_address', address);
			setIf('vi_patient_type', patientType);
			setIf('vi_branch', branch);
			setIf('vi_referred_by', referredBy);
			setIf('vi_purpose', purpose);
			setIf('vi_medical_history', medicalHistory);
			setIf('vi_notes', notes);

			// birthdate formatting
			let formattedBirth = birthdate;
			if (birthdate) {
				try {
					const parts = birthdate.split('/').map(p => p.trim());
					if (parts.length === 3) {
						const [d, m, y] = parts;
						const bd = new Date(Number(y), Number(m) - 1, Number(d));
						if (!isNaN(bd.getTime())) {
							formattedBirth = bd.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' });
						}
					}
				} catch (e) { }
			}
			setIf('vi_birthdate', formattedBirth);

			// appointment date parsing
			let apptDayName = '';
			let apptMonthName = '';
			let apptDateShort = date;
			if (date) {
				const parsed = new Date(date);
				if (!isNaN(parsed.getTime())) {
					apptDayName = parsed.toLocaleDateString(undefined, { weekday: 'long' });
					apptMonthName = parsed.toLocaleDateString(undefined, { month: 'long' });
					apptDateShort = String(parsed.getDate()).padStart(2, '0') + ', ' + parsed.getFullYear();
				} else {
					const alt = row.getAttribute('data-date') || row.getAttribute('data-appointment-date');
					if (alt) {
						const p2 = new Date(alt);
						if (!isNaN(p2.getTime())) {
							apptDayName = p2.toLocaleDateString(undefined, { weekday: 'long' });
							apptMonthName = p2.toLocaleDateString(undefined, { month: 'long' });
							apptDateShort = String(p2.getDate()).padStart(2, '0') + ', ' + p2.getFullYear();
						}
					}
				}
			}

			setIf('vi_day', apptDayName);
			setIf('vi_month', apptMonthName);
			setIf('vi_date', apptDateShort);

			// show modal
			const modalEl = document.getElementById('viewInfoModal');
			if (modalEl && typeof bootstrap !== 'undefined') {
				const modal = new bootstrap.Modal(modalEl);
				modal.show();
			}
		} catch (err) {
			console.error('Error in view handler', err);
		}
	}

	// Delegated listener (keeps working for dynamic rows)
	document.addEventListener('click', function (e) {
		const btn = e.target.closest && e.target.closest('.btn-view');
		if (!btn) return;
		e.preventDefault();
		handleViewClick(btn);
	});

	// Direct binding fallback for existing .btn-view elements on load
	document.addEventListener('DOMContentLoaded', function () {
		const els = document.querySelectorAll('.btn-view');
		els.forEach(function (b) { b.addEventListener('click', function (ev) { ev.preventDefault(); handleViewClick(b); }); });
	});

	// delegated handler for delete buttons (used by appointment record page)
	document.addEventListener('click', function (e) {
		const del = e.target.closest && e.target.closest('.btn-delete');
		if (!del) return;
		e.preventDefault();
		const row = del.closest('tr');
		if (!row) return;
		if (!confirm('Delete this appointment record?')) return;
		row.parentNode.removeChild(row);
	});

	function escapeHtml(unsafe) {
		return String(unsafe).replace(/[&<>"']/g, function (m) {
			return ({
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			})[m];
		});
	}

	// Schedule UI behavior (simple)
	(function scheduleUI() {
		const modeButtons = document.querySelectorAll('.mode');
		const displayMode = document.getElementById('displayMode');
		const currentDateEl = document.getElementById('currentDate');
		let mode = 'day';
		let current = new Date(2025, 3, 7); // April 7, 2025 as default from mock

		function formatDate(d) {
			return d.toLocaleDateString(undefined, { weekday: 'short', month: 'long', day: '2-digit', year:'numeric' });
		}

		if (currentDateEl) currentDateEl.textContent = formatDate(current);

		const dayView = document.querySelector('.day-view');
		const weekView = document.querySelector('.week-view');

		function setMode(newMode, clickedEl) {
			modeButtons.forEach(b => b.classList.remove('active'));
			if (clickedEl && clickedEl.classList) clickedEl.classList.add('active');
			mode = newMode;
			if (displayMode) displayMode.textContent = mode.charAt(0).toUpperCase() + mode.slice(1);
			if (mode === 'day') {
				if (dayView) dayView.classList.remove('d-none');
				if (weekView) weekView.classList.add('d-none');
				const monthView = document.querySelector('.month-view');
				if (monthView) monthView.classList.add('d-none');
			} else if (mode === 'week') {
				if (dayView) dayView.classList.add('d-none');
				if (weekView) weekView.classList.remove('d-none');
				const monthView = document.querySelector('.month-view');
				if (monthView) monthView.classList.add('d-none');
				// render the week for the current date
				renderWeek(startOfWeek(current));
			} else if (mode === 'month') {
				// hide other views
				if (dayView) dayView.classList.add('d-none');
				if (weekView) weekView.classList.add('d-none');
				const monthView = document.querySelector('.month-view');
				if (monthView) monthView.classList.remove('d-none');
				// update header to month/year
				if (currentDateEl) currentDateEl.textContent = current.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
				renderMonth(current);
			}
		}

		// expose setter globally for manual triggers
		window.setScheduleMode = setMode;

		modeButtons.forEach(btn => {
			btn.addEventListener('click', function () {
				setMode(this.getAttribute('data-mode'), this);
			});
		});

		// Delegated handler for any element with data-mode (fallback)
		document.addEventListener('click', function (e) {
			const el = e.target.closest && e.target.closest('[data-mode]');
			if (!el) return;
			const m = el.getAttribute('data-mode');
			if (m) {
				setMode(m, el);
			}
		});

		const prev = document.getElementById('prevDay');
		const next = document.getElementById('nextDay');
		if (prev) prev.addEventListener('click', () => {
			if (mode === 'month') {
				current.setMonth(current.getMonth() - 1);
				if (currentDateEl) currentDateEl.textContent = current.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
				renderMonth(current);
			} else {
				const step = mode === 'week' ? 7 : 1;
				current.setDate(current.getDate() - step);
				if (currentDateEl) currentDateEl.textContent = formatDate(current);
				if (mode === 'week') renderWeek(startOfWeek(current));
			}
		});
		if (next) next.addEventListener('click', () => {
			if (mode === 'month') {
				current.setMonth(current.getMonth() + 1);
				if (currentDateEl) currentDateEl.textContent = current.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });
				renderMonth(current);
			} else {
				const step = mode === 'week' ? 7 : 1;
				current.setDate(current.getDate() + step);
				if (currentDateEl) currentDateEl.textContent = formatDate(current);
				if (mode === 'week') renderWeek(startOfWeek(current));
			}
		});

		// Week rendering: populate week header columns and place sample appointments
		function startOfWeek(d) {
			const dt = new Date(d);
			const day = dt.getDay();
			// In JS, Sunday=0, Monday=1 -> compute Monday as start
			const diff = (day === 0) ? -6 : (1 - day);
			dt.setDate(dt.getDate() + diff);
			dt.setHours(0,0,0,0);
			return dt;
		}

		function renderWeek(startDate) {
			const container = document.getElementById('weekColumns');
			const header = document.getElementById('weekHeader');
			if (!container) return;
			container.innerHTML = '';
			if (header) header.innerHTML = '';
			for (let i = 0; i < 7; i++) {
				const d = new Date(startDate);
				d.setDate(d.getDate() + i);
				const col = document.createElement('div');
				col.className = 'week-col';
				col.style.flex = '1';
				col.style.borderLeft = '1px solid #111';
				col.style.minHeight = '420px';
				col.style.position = 'relative';
				col.style.paddingTop = '8px';

				const dayNum = document.createElement('div');
				dayNum.style.textAlign = 'center';
				dayNum.style.fontWeight = '700';
				dayNum.textContent = d.getDate();

				const dayName = document.createElement('div');
				dayName.style.textAlign = 'center';
				dayName.style.fontSize = '.85rem';
				dayName.textContent = d.toLocaleDateString(undefined, { weekday: 'short' }).toUpperCase();

				col.appendChild(dayNum);
				col.appendChild(dayName);

				// add grid rows
				for (let r = 0; r < 6; r++) {
					const row = document.createElement('div');
					row.style.height = '80px';
					row.style.borderTop = '1px solid #ddd';
					col.appendChild(row);
				}

				col.setAttribute('data-date', d.toISOString().slice(0,10));
				container.appendChild(col);
			}

			// build header boxes (day number above each column and weekday label)
			if (header) {
				for (let i = 0; i < 7; i++) {
					const d = new Date(startDate);
					d.setDate(d.getDate() + i);
					const headerCell = document.createElement('div');
					headerCell.style.flex = '1';
					headerCell.style.textAlign = 'center';
					headerCell.style.display = 'flex';
					headerCell.style.flexDirection = 'column';
					headerCell.style.alignItems = 'center';
					headerCell.style.gap = '6px';

					const box = document.createElement('div');
					box.style.width = '28px';
					box.style.height = '28px';
					box.style.border = '1px solid #111';
					box.style.display = 'inline-flex';
					box.style.alignItems = 'center';
					box.style.justifyContent = 'center';
					box.textContent = d.getDate();

					const wd = document.createElement('div');
					wd.style.fontWeight = '700';
					wd.style.marginTop = '4px';
					wd.textContent = d.toLocaleDateString(undefined, { weekday: 'short' }).toUpperCase();

					headerCell.appendChild(box);
					headerCell.appendChild(wd);
					header.appendChild(headerCell);
				}
			}

			// place a sample appointment for April 7, 2025 07:00-09:00
			const sampleStart = new Date(2025,3,7,7,0,0);
			const sampleEnd = new Date(2025,3,7,9,0,0);
			placeAppointment(sampleStart, sampleEnd, 'Hearing Test / Audiometry', 'Nina Polinar', '07:00 AM - 09:00 AM');
		}

		// convert time to pixels: each hour = 80px (matching grid row 80px per hour)
		function timeToPixels(date) {
			const hours = date.getHours();
			const minutes = date.getMinutes();
			return (hours * 80) + (minutes / 60) * 80;
		}

		function placeAppointment(start, end, title, patient, timestr) {
			// find column matching start date
			const cols = document.querySelectorAll('#weekColumns .week-col');
			const dateStr = start.toISOString().slice(0,10);
			for (const col of cols) {
				if (col.getAttribute('data-date') === dateStr) {
					const ap = document.createElement('div');
					ap.className = 'appt';
					const top = timeToPixels(start) - timeToPixels(new Date(start.getFullYear(), start.getMonth(), start.getDate(), 0,0,0));
					const height = timeToPixels(end) - timeToPixels(start);
					ap.style.top = (top) + 'px';
					ap.style.height = (height) + 'px';
					ap.style.left = '8px';
					ap.style.right = '8px';
					ap.style.background = '#f15a5a';
					ap.innerHTML = `<div class="title">${escapeHtml(title)}</div><div class="patient">${escapeHtml(patient)}</div><div class="time">${escapeHtml(timestr)}</div>`;
					col.appendChild(ap);
					break;
				}
			}
		}

		// MONTH rendering: show a calendar grid for the month of 'date'
		function renderMonth(date) {
			const grid = document.getElementById('monthGrid');
			if (!grid) return;
			grid.innerHTML = '';
			const year = date.getFullYear();
			const month = date.getMonth();
			// first day of month
			const first = new Date(year, month, 1);
			const startDay = first.getDay(); // 0=Sun
			// how many days in month
			const last = new Date(year, month + 1, 0);
			const daysInMonth = last.getDate();
			// determine number of weeks to show (rows)
			const totalCells = startDay + daysInMonth;
			const rows = Math.ceil(totalCells / 7);
			let dayCounter = 1;
			for (let r = 0; r < rows; r++) {
				const weekRow = document.createElement('div');
				weekRow.style.display = 'flex';
				weekRow.style.gap = '8px';
				for (let c = 0; c < 7; c++) {
					const cell = document.createElement('div');
					cell.style.flex = '1';
					cell.style.minHeight = '90px';
					cell.style.border = '1px solid #111';
					cell.style.padding = '8px';
					cell.style.position = 'relative';
					const cellIndex = r * 7 + c;
					if (cellIndex >= startDay && dayCounter <= daysInMonth) {
						const num = document.createElement('div');
						num.style.fontWeight = '800';
						num.textContent = dayCounter;
						cell.appendChild(num);
						cell.setAttribute('data-date', new Date(year, month, dayCounter).toISOString().slice(0,10));
						dayCounter++;
					} else {
						// empty cell
					}
					weekRow.appendChild(cell);
				}
				grid.appendChild(weekRow);
			}

			// place a sample month appointment highlight (April 7, 2025)
			const sampleDateStr = new Date(2025,3,7).toISOString().slice(0,10);
			const targetCell = grid.querySelector(`[data-date="${sampleDateStr}"]`);
			if (targetCell) {
				const ap = document.createElement('div');
				ap.style.position = 'absolute';
				ap.style.left = '8px';
				ap.style.right = '8px';
				ap.style.top = '28px';
				ap.style.padding = '8px';
				ap.style.background = '#f15a5a';
				ap.style.color = '#000';
				ap.style.fontWeight = '800';
				ap.textContent = 'Hearing Test\nAudiometry'.replace('\\n',' ');
				targetCell.appendChild(ap);
			}
		}

		// render initial week when switching to week mode
		const initialWeekStart = startOfWeek(current);
		renderWeek(initialWeekStart);
	})();

	// Sidebar calendar (compact) - dynamically populate the small calendar in the schedule box,
	// mark today, render a few sample events, and wire clicks to open the View Info modal.
	(function sidebarCalendar() {
		const daysContainer = document.querySelector('.calendar-days');
		const titleEl = document.querySelector('.calendar-title');
		const eventsContainer = document.querySelector('.schedule-events');
		if (!daysContainer || !titleEl) return;

		const now = new Date();
		const year = now.getFullYear();
		const month = now.getMonth();

		// update title to current month/year
		titleEl.textContent = now.toLocaleDateString(undefined, { month: 'long', year: 'numeric' });

		function pad(n) { return String(n).padStart(2, '0'); }

		// sample events for demo (dates keyed as YYYY-MM-DD)
		const sampleEvents = {
			[`${year}-${pad(month+1)}-${pad(now.getDate())}`]: [
				{ time: '09:00 AM', title: 'Tympanometry', person: 'Nina Polinar' }
			],
			[`${year}-${pad(month+1)}-07`]: [
				{ time: '07:00 AM', title: 'Puretone', person: 'Bency Cabugsa' }
			],
			[`${year}-${pad(month+1)}-12`]: [
				{ time: '12:00 PM', title: 'Hearing Test', person: 'Mary Saplot' }
			]
		};

		function isoDate(y, m, d) { return `${y}-${pad(m+1)}-${pad(d)}`; }

		// build calendar cells
		daysContainer.innerHTML = '';
		const first = new Date(year, month, 1);
		const startDay = first.getDay(); // 0=Sun
		const last = new Date(year, month + 1, 0);
		const daysInMonth = last.getDate();

		// create leading empty cells
		for (let i = 0; i < startDay; i++) {
			const el = document.createElement('div');
			el.className = 'calendar-cell empty';
			el.innerHTML = '&nbsp;';
			daysContainer.appendChild(el);
		}

		for (let d = 1; d <= daysInMonth; d++) {
			const el = document.createElement('div');
			el.className = 'calendar-cell';
			el.textContent = pad(d);
			const dateStr = isoDate(year, month, d);
			el.setAttribute('data-date', dateStr);
			// mark today
			const todayStr = isoDate(now.getFullYear(), now.getMonth(), now.getDate());
			if (dateStr === todayStr) el.classList.add('today');
			// if events exist for this date, add a small dot indicator
			if (sampleEvents[dateStr]) {
				const dot = document.createElement('div');
				dot.style.width = '8px';
				dot.style.height = '8px';
				dot.style.borderRadius = '50%';
				dot.style.background = '#f15a5a';
				dot.style.position = 'absolute';
				dot.style.bottom = '6px';
				dot.style.right = '6px';
				el.style.position = 'relative';
				el.appendChild(dot);
			}
			daysContainer.appendChild(el);
		}

		// fill trailing empty cells to complete the last week row
		while (daysContainer.children.length % 7 !== 0) {
			const el = document.createElement('div');
			el.className = 'calendar-cell empty';
			el.innerHTML = '&nbsp;';
			daysContainer.appendChild(el);
		}

		// render events list below calendar (replace existing static events)
		function renderEventsList() {
			if (!eventsContainer) return;
			// clear only the events area (keep calendar itself intact)
			const wrapper = document.createElement('div');
			wrapper.className = 'schedule-events';
			for (const dateKey in sampleEvents) {
				sampleEvents[dateKey].forEach(ev => {
					const row = document.createElement('div');
					row.className = 'event-row';
					row.setAttribute('data-date', dateKey);
					row.setAttribute('data-time', ev.time);
					row.setAttribute('data-title', ev.title);
					row.setAttribute('data-person', ev.person);
					row.innerHTML = `
						<div class="event-time">${ev.time}</div>
						<div class="event-details">
							<div class="event-title">${ev.title}</div>
							<div class="event-person">${ev.person}</div>
						</div>
					`;
					row.addEventListener('click', () => showEventModal(dateKey, ev));
					wrapper.appendChild(row);
				});
			}
			// replace the existing schedule-events block if present
			const parent = eventsContainer.parentElement;
			if (!parent) return;
			const existing = parent.querySelector('.schedule-events');
			if (existing) existing.remove();
			parent.appendChild(wrapper);
		}

		function setIf(id, value) {
			const el = document.getElementById(id);
			if (!el) return;
			el.textContent = value || '';
		}

		function showEventModal(dateKey, ev) {
			// populate some modal fields
			setIf('vi_service', ev.title || '');
			setIf('vi_name', ev.person || '');
			setIf('vi_time', ev.time || '');
			// format date nicely
			try {
				const parts = dateKey.split('-').map(p => Number(p));
				const d = new Date(parts[0], parts[1]-1, parts[2]);
				setIf('vi_date', d.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' }));
				setIf('vi_day', d.toLocaleDateString(undefined, { weekday: 'long' }));
				setIf('vi_month', d.toLocaleDateString(undefined, { month: 'long' }));
			} catch (e) { }

			const modalEl = document.getElementById('viewInfoModal');
			if (modalEl && typeof bootstrap !== 'undefined') {
				const modal = new bootstrap.Modal(modalEl);
				modal.show();
			}
		}

		// click handler for day cells
		daysContainer.addEventListener('click', function (e) {
			const cell = e.target.closest && e.target.closest('.calendar-cell');
			if (!cell || cell.classList.contains('empty')) return;
			const date = cell.getAttribute('data-date');
			if (!date) return;
			const evs = sampleEvents[date];
			if (evs && evs.length) {
				showEventModal(date, evs[0]);
			} else {
				// no event: show a minimal modal with date info
				setIf('vi_service', '');
				setIf('vi_name', '');
				setIf('vi_time', '');
				try {
					const parts = date.split('-').map(p => Number(p));
					const d = new Date(parts[0], parts[1]-1, parts[2]);
					setIf('vi_date', d.toLocaleDateString(undefined, { month: 'short', day: '2-digit', year: 'numeric' }));
					setIf('vi_day', d.toLocaleDateString(undefined, { weekday: 'long' }));
					setIf('vi_month', d.toLocaleDateString(undefined, { month: 'long' }));
				} catch (e) { }
				const modalEl = document.getElementById('viewInfoModal');
				if (modalEl && typeof bootstrap !== 'undefined') {
					const modal = new bootstrap.Modal(modalEl);
					modal.show();
				}
			}
		});

		renderEventsList();
	})();

// Simple chat UI interactions (client-side only)
(function chatUI() {
	const chatListInner = document.getElementById('chatListInner');
	const chatWindow = document.getElementById('chatWindow');
	const chatInput = document.getElementById('chatInput');
	const sendBtn = document.getElementById('sendBtn');

	if (!chatListInner) return;

	// sample users
	const users = [
		{ id: 'u1', name: 'Patient01' },
		{ id: 'u2', name: 'Patient02' },
		{ id: 'u3', name: 'Patient03' },
		{ id: 'u4', name: 'Patient04' },
		{ id: 'u5', name: 'Patient05' }
	];

	let currentConversation = null;
	const conversations = {};

	function renderList() {
		chatListInner.innerHTML = '';
		users.forEach(u => {
			const item = document.createElement('div');
			item.className = 'chat-item';
			item.setAttribute('data-user', u.id);
			item.innerHTML = `<div style="width:36px;height:36px;border-radius:50%;background-image:url('/images/avatar.png');background-size:cover;background-position:center;margin-right:8px;"></div><div>${u.name}</div>`;
			item.addEventListener('click', () => openConversation(u.id));
			chatListInner.appendChild(item);
		});
	}

	function openConversation(userId) {
		currentConversation = userId;
		if (!conversations[userId]) conversations[userId] = [];
		renderConversation();
	}

	function renderConversation() {
		if (!chatWindow) return;
		chatWindow.innerHTML = '';
		const msgs = conversations[currentConversation] || [];
		msgs.forEach(m => {
			const b = document.createElement('div');
			b.style.padding = '8px 12px';
			b.style.marginBottom = '8px';
			b.style.borderRadius = '8px';
			b.style.maxWidth = '70%';
			b.style.background = m.from === 'me' ? '#0d6efd' : '#e9ecef';
			b.style.color = m.from === 'me' ? '#fff' : '#111';
			b.textContent = m.text;
			chatWindow.appendChild(b);
		});
		chatWindow.scrollTop = chatWindow.scrollHeight;
	}

	function sendMessage() {
		const text = (chatInput && chatInput.value || '').trim();
		if (!text || !currentConversation) return;
		conversations[currentConversation].push({ from: 'me', text });
		if (chatInput) chatInput.value = '';
		renderConversation();
	}

	if (sendBtn) sendBtn.addEventListener('click', sendMessage);
	if (chatInput) chatInput.addEventListener('keydown', function (e) { if (e.key === 'Enter') sendMessage(); });

	// initial render
	renderList();
	// open first conversation by default
	openConversation(users[0].id);
})();
});
