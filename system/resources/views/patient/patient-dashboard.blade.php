@extends('layouts.patient')

@section('title', 'Patient Dashboard')

@push('head')
<style>
	.sidebar { background:#3f3f47; color:#fff; min-height:100vh; padding-top:18px; }
	.sidebar a{ color:#fff; display:block; padding:12px 18px; }
	.sidebar .logo { background: #18913b; padding:18px; font-weight:800; }
	.calendar { border:1px solid #ddd; padding:18px; }
	.notification { border:1px solid #cfcfcf; padding:12px; }
</style>
@endpush

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center mb-3">
				<div style="background:#9ea4b1; padding:18px; flex:1; margin-right:18px; border-radius:4px;">
					<h4 style="color:#fff;">Welcome Patient!</h4>
					<p style="color:#fff;">We're glad to have you with us. Our goal is to ensure you feel comfortable and fully informed throughout the process.</p>
				</div>
				<div style="width:260px;">
					<input class="form-control mb-2" placeholder="Patient name">
					<input class="form-control" placeholder="Date">
				</div>
			</div>

			<div class="row">
				<div class="col-8">
					<div class="d-flex justify-content-between align-items-center">
						<h5 id="calTitle">Month Year</h5>
						<div>
							<button class="btn btn-sm btn-outline-secondary" id="prevMonth">&lt;</button>
							<button class="btn btn-sm btn-outline-secondary" id="nextMonth">&gt;</button>
						</div>
					</div>
					<div class="calendar mt-2">
						<div id="calendar" style="display:grid; grid-template-columns: repeat(7,1fr); gap:6px;">
							<!-- weekdays header -->
						</div>
						<div id="eventsList" class="mt-3"></div>
					</div>
				</div>
				<div class="col-4">
					<div class="notification">
						<h5>Notification</h5>
						<div id="notificationsList" class="mt-2"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
<script>
// Calendar + Notifications script
(function(){
	const calEl = document.getElementById('calendar');
	const calTitle = document.getElementById('calTitle');
	const eventsList = document.getElementById('eventsList');
	const notificationsList = document.getElementById('notificationsList');

	// sample events keyed by yyyy-mm-dd
	const sampleEvents = {
		// today + a couple of sample items
	};

	/* build list of sample events for the current month */
	function sampleEventsFor(monthStart){
		const yyyy = monthStart.getFullYear();
		const mm = (monthStart.getMonth()+1).toString().padStart(2,'0');
		const events = {};
		events[`${yyyy}-${mm}-09`] = [{title:'Consultation with Dr. A', time:'10:00 AM'}];
		events[`${yyyy}-${mm}-18`] = [{title:'Hearing Aid Fitting', time:'02:30 PM'}];
		return events;
	}

	let viewDate = new Date();

	function renderCalendar(){
		// clear
		calEl.innerHTML = '';

		const year = viewDate.getFullYear();
		const month = viewDate.getMonth();

		// title
		const monthName = viewDate.toLocaleString('default', { month: 'long' });
		calTitle.textContent = `${monthName} ${year}`;

		const first = new Date(year, month, 1);
		const last = new Date(year, month+1, 0);
		const startDay = first.getDay(); // 0..6

		// weekdays header
		const weekdays = ['Su','Mo','Tu','We','Th','Fr','Sa'];
		weekdays.forEach(w => {
			const el = document.createElement('div');
			el.style.fontWeight = '700';
			el.style.padding = '6px 0';
			el.style.textAlign = 'center';
			el.style.background = '#f2f2f2';
			el.textContent = w;
			calEl.appendChild(el);
		});

		// add blank cells for startDay
		for(let i=0;i<startDay;i++){
			const blank = document.createElement('div');
			blank.innerHTML = '';
			calEl.appendChild(blank);
		}

		// sample events for this month
		const events = sampleEventsFor(first);

		for(let d=1; d<= last.getDate(); d++){
			const dt = new Date(year, month, d);
			const cell = document.createElement('div');
			cell.style.border = '1px solid transparent';
			cell.style.padding = '10px';
			cell.style.minHeight = '48px';
			cell.style.cursor = 'pointer';
			cell.style.textAlign = 'center';

			// highlight today
			const today = new Date();
			if(dt.toDateString() === today.toDateString()){
				cell.style.background = '#fff3cd';
				cell.style.border = '1px solid #f0ad4e';
				cell.style.fontWeight = '700';
			}

			cell.textContent = d.toString().padStart(2,'0');

			const key = `${year}-${(month+1).toString().padStart(2,'0')}-${d.toString().padStart(2,'0')}`;
			if(events[key]){
				const dot = document.createElement('div');
				dot.style.width = '8px';
				dot.style.height = '8px';
				dot.style.background = '#17a2b8';
				dot.style.borderRadius = '50%';
				dot.style.margin = '6px auto 0 auto';
				cell.appendChild(dot);
			}

			cell.addEventListener('click', function(){
				renderEventsFor(key, events[key]||[]);
			});

			calEl.appendChild(cell);
		}

		// fill remaining cells to complete grid (optional)
	}

	function renderEventsFor(key, items){
		eventsList.innerHTML = '';
		const box = document.createElement('div');
		box.style.borderTop = '1px solid #eee';
		box.style.paddingTop = '12px';
		if(items.length===0){
			box.innerHTML = '<div class="text-muted">No events for this day.</div>';
		} else {
			items.forEach(it=>{
				const r = document.createElement('div');
				r.style.padding = '8px 0';
				r.innerHTML = `<strong>${it.title}</strong><div class="text-muted">${it.time}</div>`;
				box.appendChild(r);
			});
		}
		eventsList.appendChild(box);
	}

	// sample notifications
	const sampleNotifications = [
		{ id:1, title:'Welcome Patient', body:'Welcome to Hearing Aid Trading Center' },
		{ id:2, title:'Reminder', body:'Please bring previous audiogram if available.' }
	];

	function renderNotifications(){
		notificationsList.innerHTML = '';
		sampleNotifications.forEach(n=>{
			const el = document.createElement('div');
			el.className = 'p-2 mb-2';
			el.style.background = '#eee';
			el.style.cursor = 'pointer';
			el.innerHTML = `<strong>${n.title}</strong><div class='text-muted' style='font-size:0.9rem;'>${n.body}</div>`;
			el.addEventListener('click', function(){
				showNotification(n);
			});
			notificationsList.appendChild(el);
		});
	}

	function showNotification(n){
		// populate and show the Bootstrap modal in the layout
		const titleEl = document.getElementById('notificationModalTitle');
		const bodyEl = document.getElementById('notificationModalBody');
		const metaEl = document.getElementById('notificationModalMeta');
		if(titleEl) titleEl.textContent = n.title || 'Notification';
		if(bodyEl) bodyEl.textContent = n.body || '';
		if(metaEl) metaEl.textContent = n.time ? `Time: ${n.time}` : '';

		const modalEl = document.getElementById('notificationModal');
		if(modalEl){
			const bsModal = new bootstrap.Modal(modalEl);
			bsModal.show();
		} else {
			// fallback
			alert(`${n.title}\n\n${n.body}`);
		}
	}

	document.getElementById('prevMonth').addEventListener('click', function(){ viewDate.setMonth(viewDate.getMonth()-1); renderCalendar(); });
	document.getElementById('nextMonth').addEventListener('click', function(){ viewDate.setMonth(viewDate.getMonth()+1); renderCalendar(); });

	// init
	renderCalendar();
	renderNotifications();
})();
</script>
@endpush
