@extends('layouts.staff')

@section('title', 'Services | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
	<div style="margin-bottom:18px;">
		<h4 style="font-weight:bold;">Services</h4>
		<p class="text-muted mb-0" style="font-size:0.9rem;">View the services provided by the clinic, along with pricing and status.</p>
	</div>

	<div class="card shadow-sm" style="border:1px solid #e2e8f0;">
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover mb-0 services-table align-middle">
					<thead>
						<tr>
							<th style="width:90px;">no</th>
							<th>Service Name</th>
							<th style="width:140px;">Price</th>
							<th style="width:120px;">Status</th>
						</tr>
					</thead>
					<tbody>
									@forelse(($services ?? []) as $service)
									<tr data-service-id="{{ $service->id }}">
										<td data-label="ID" class="fw-semibold">{{ sprintf('%02d',$service->id) }}</td>
										<td data-label="Service">{{ $service->name }}</td>
										<td data-label="Price">₱ {{ number_format($service->price,0) }}</td>
										<td data-label="Status"><span class="badge {{ $service->status==='active' ? 'bg-success' : 'bg-secondary' }} status-badge">{{ strtoupper($service->status) }}</span></td>
									</tr>
									@empty
									<tr class="empty-row">
										<td colspan="4" class="text-center text-muted py-5">No services found</td>
									</tr>
									@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@push('styles')
<style>
	@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
	.services-table thead th { background:#f1f5f9; font-weight:700; font-size:.8rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
	.services-table tbody td { font-size:.9rem; }
	.services-table tbody tr:hover { background:#f8fafc; }
	.badge.status-badge { font-size:.65rem; letter-spacing:.5px; padding:.45em .55em; }
	.card { border-radius:10px; }
	@media (max-width: 768px){
		.services-table thead { display:none; }
		.services-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
		.services-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
		.services-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
	}
</style>
@endpush
@endsection
