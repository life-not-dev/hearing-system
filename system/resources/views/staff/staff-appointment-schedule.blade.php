
@extends('layouts.staff')

@section('content')

<style>
	/* Outer panel to match mock */
	.schedule-panel {
		background: #fff;
		border: 2px solid #111;
		padding: 18px;
		border-radius: 2px;
	}
	.schedule-top {
		display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;
	}
	.schedule-title { font-size:1.45rem; font-weight:700; }
	.schedule-controls { display:flex; align-items:center; gap:12px; }
	.mode-buttons { display:flex; gap:8px; align-items:center; }
	.mode-buttons .mode { padding:.35rem .6rem; border-radius:4px; border:1px solid #000; background:#fff; cursor:pointer; }
	.mode-buttons .mode.active { background:#000; color:#fff; }

	.schedule-box { border:1px solid #111; padding:18px; }
	.date-row { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; }
	.date-left { display:flex; align-items:center; gap:10px; font-weight:600; }
	.date-left .nav { cursor:pointer; font-size:1.1rem; }

	.schedule-area { display:flex; }
	.times-col { width:90px; border-right:1px solid #111; padding-right:12px; }
	.times-col .time { height:80px; display:flex; align-items:center; color:#000; font-weight:600; }
	.grid-col { flex:1; position:relative; }
	.grid-slot { height:80px; border-bottom:1px solid #ddd; }

	/* appointment block styling to match mock */
	.appt { position:absolute; left:12px; right:12px; background:#f15a5a; color:#000; padding:14px; border:1px solid rgba(0,0,0,0.2); }
	.appt .title { font-weight:800; }
	.appt .patient { font-weight:700; margin-top:8px; }
	.appt .time { color:#111; margin-top:8px; font-weight:600; }
	.appt .status { margin-top:8px; font-weight:700; }

	/* small helpers */
	.create-btn { background:#0d6efd; color:#fff; border:none; padding:.5rem .9rem; }

	@media (max-width: 1000px) {
		.times-col { width:70px; }
		.times-col .time { font-size:.9rem; }
	}
</style>

<div class="main-content">
	<div class="schedule-panel">
		<div class="schedule-top">
			<div class="schedule-title">Appointment Schedule</div>
			<div class="schedule-controls">
				<button class="create-btn">Create Schedule</button>
				<div class="mode-buttons">
					<div class="mode active" data-mode="day">Day</div>
					<div class="mode" data-mode="week">Week</div>
					<div class="mode" data-mode="month">Month</div>
				</div>
			</div>
		</div>

		<div class="schedule-box">
			<div class="date-row">
				<div class="date-left">
					<div id="prevDay" class="nav">&#8592;</div>
					<div id="currentDate">Mon, April 07, 2025</div>
					<div id="nextDay" class="nav">&#8594;</div>
				</div>
				<div><!-- empty right side (keeps day toggle visually near top-right in header) --></div>
			</div>

			<!-- DAY view -->
				<div class="schedule-area day-view" data-mode="day">
					<div class="times-col">
						@php
							// Render hourly labels from 08:00 to 17:00
							for ($h = 8; $h <= 17; $h++) {
								$label = date('h:00 A', strtotime(sprintf('%02d:00', $h)));
								echo '<div class="time">'.$label.'</div>';
							}
						@endphp
					</div>

					<div class="grid-col" id="scheduleBody">
						<!-- grid slots (visual rows) 08:00 -> 17:00 inclusive boundary -->
						@for($i=0; $i<10; $i++)
							<div class="grid-slot"></div>
						@endfor

						@php
							// Helper to compute top offset from appointment_time relative to 08:00 baseline
							function slotTop($timeStr) {
								$base = strtotime('08:00');
								$t = strtotime($timeStr);
								$diffMin = max(0, (int)(($t - $base) / 60));
								// 80px per hour => ~1.333px per minute
								return (int) round(($diffMin / 60) * 80) + 6; // +6px top padding
							}
						@endphp

						@if(isset($appointments) && count($appointments))
							@foreach($appointments as $a)
								@php
									$top = slotTop($a->appointment_time);
									$height = 160; // 2-hour blocks
									$serviceName = optional($a->serviceRef)->service_name ?? '';
									$fullName = optional($a->patient)->patient_firstname || optional($a->patient)->patient_surname
										? trim((optional($a->patient)->patient_firstname ?? '').' '.(optional($a->patient)->patient_surname ?? ''))
										: '';
								@endphp
								<div class="appt" style="top:{{ $top }}px; height:{{ $height }}px; background:#f15a5a;">
									<div class="title">{{ $serviceName }}</div>
									<div class="patient">{{ $fullName }}</div>
									<div class="time">{{ date('h:i A', strtotime($a->appointment_time)) }} - {{ date('h:i A', strtotime($a->appointment_time.' +2 hours')) }}</div>
									<div class="status">Confirmed</div>
								</div>
							@endforeach
						@endif
					</div>

				<!-- WEEK view (hidden by default) -->
				<div class="schedule-area week-view d-none" data-mode="week">
					<div style="width:60px"></div>
					<div style="flex:1; display:flex; flex-direction:column; gap:8px;">
						<!-- week header row -->
						<div id="weekHeader" style="display:flex; gap:8px; align-items:center;"></div>
						<!-- columns container -->
						<div style="display:flex; gap:8px;" id="weekColumns"></div>
					</div>
				</div>

				<!-- MONTH view (hidden by default) -->
				<div class="schedule-area month-view d-none" data-mode="month">
					<div style="width:60px"></div>
					<div style="flex:1; display:flex; flex-direction:column; gap:12px;">
						<!-- month header with weekday labels -->
						<div id="monthHeader" style="display:flex; gap:8px;">
							<div style="flex:1; text-align:center; font-weight:800;">SUN</div>
							<div style="flex:1; text-align:center; font-weight:800;">MON</div>
							<div style="flex:1; text-align:center; font-weight:800;">TUE</div>
							<div style="flex:1; text-align:center; font-weight:800;">WED</div>
							<div style="flex:1; text-align:center; font-weight:800;">THU</div>
							<div style="flex:1; text-align:center; font-weight:800;">FRI</div>
							<div style="flex:1; text-align:center; font-weight:800;">SAT</div>
						</div>
						<!-- month grid: weeks will be injected here -->
						<div id="monthGrid" style="display:flex; flex-direction:column; gap:8px;"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script>
(function(){
	// Wire up date navigation and display
	const currentDateEl = document.getElementById('currentDate');
	const prevBtn = document.getElementById('prevDay');
	const nextBtn = document.getElementById('nextDay');
	if(!currentDateEl || !prevBtn || !nextBtn) return;

	// Values injected from controller
	const serverDate = @json($date ?? null);
	const serverDisplay = @json($displayDate ?? null);

	function toISO(d){
		const y = d.getFullYear();
		const m = String(d.getMonth()+1).padStart(2,'0');
		const day = String(d.getDate()).padStart(2,'0');
		return `${y}-${m}-${day}`;
	}

	function go(dateObj){
		// Reload same route with ?date=YYYY-MM-DD
		const iso = toISO(dateObj);
		const url = new URL(window.location.href);
		url.searchParams.set('date', iso);
		window.location.href = url.toString();
	}

	// Initialize label
	if(serverDisplay){
		currentDateEl.textContent = serverDisplay;
	}

	// Base date from server; fallback to today
	const base = serverDate ? new Date(serverDate + 'T00:00:00') : new Date();

	prevBtn.addEventListener('click', function(){
		const d = new Date(base);
		d.setDate(d.getDate() - 1);
		go(d);
	});
	nextBtn.addEventListener('click', function(){
		const d = new Date(base);
		d.setDate(d.getDate() + 1);
		go(d);
	});
})();
</script>
@endpush

