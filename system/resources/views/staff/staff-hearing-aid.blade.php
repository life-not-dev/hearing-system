@extends('layouts.staff')

@section('title', 'Hearing Aid | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
    <div style="margin-bottom:18px;">
        <h4 style="font-weight:bold;">Hearing Aid</h4>
        <p class="text-muted mb-0" style="font-size:0.9rem;">Quick reference for hearing aid models and pricing.</p>
    </div>

    <div class="card shadow-sm" style="border:1px solid #e2e8f0;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 hearing-table align-middle">
                    <thead>
                        <tr>
                            <th style="width:70px;">no.</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th style="width:140px;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($hearingAids ?? []) as $item)
                        <tr data-id="{{ $item->id }}">
                            <td data-label="ID" class="fw-semibold">{{ sprintf('%02d',$item->id) }}</td>
                            <td data-label="Brand">{{ $item->brand }}</td>
                            <td data-label="Model">{{ $item->model }}</td>
                            <td data-label="Price">₱ {{ number_format($item->price,0) }}</td>
                        </tr>
                        @empty
                        <tr class="empty-row">
                            <td colspan="4" class="text-center text-muted py-5">No hearing aids found</td>
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
    .hearing-table thead th { background:#f1f5f9; font-weight:700; font-size:.75rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
    .hearing-table tbody td { font-size:.85rem; }
    .hearing-table tbody tr:hover { background:#f8fafc; }
    .card { border-radius:10px; }
    @media (max-width: 768px){
        .hearing-table thead { display:none; }
        .hearing-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
        .hearing-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
        .hearing-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
    }
</style>
@endpush
@endsection
