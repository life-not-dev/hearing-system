@extends('layouts.app')

@section('title', 'Services | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
    <div style="margin-top:-20px; margin-bottom:20px;">
        <h3 style="font-weight:bold;">Services</h3>
        <p class="text-muted mb-3" style="font-size:0.9rem;">View and Update the services provided by the clinic, along with pricing and status.</p>
    </div>
    
    <div style="margin-bottom:18px;" class="d-flex justify-content-end">
        @if(empty($isStaff) || !$isStaff)
            <button id="openAddServiceBtn" class="btn btn-primary" style="font-weight:600;">+ Add Service</button>
        @endif
    </div>
    <div class="card shadow-sm" style="border:1px solid #e2e8f0;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 services-table align-middle">
                    <thead>
                        <tr>
                            <th style="width:90px;">#</th>
                            <th>Service Name</th>
                            <th style="width:140px;">Price</th>
                            <th style="width:120px;">Status</th>
                            @if(empty($isStaff) || !$isStaff)
                                <th style="width:110px;" class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($services ?? []) as $service)
                            <tr data-service-id="{{ $service->id }}">
                                <td data-label="ID" class="fw-semibold">{{ sprintf('%02d',$service->id) }}</td>
                                <td data-label="Service">{{ $service->name }}</td>
                                <td data-label="Price">₱ {{ number_format($service->price,0) }}</td>
                                <td data-label="Status"><span class="badge {{ $service->status==='active' ? 'bg-success' : 'bg-secondary' }} status-badge">{{ strtoupper($service->status) }}</span></td>
                                @if(empty($isStaff) || !$isStaff)
                                <td data-label="Action">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-primary edit-service" title="Edit"
                                                data-id="{{ $service->id }}"
                                                data-name="{{ e($service->name) }}"
                                                data-price="{{ $service->price }}"
                                                data-status="{{ $service->status }}">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.services.delete',$service->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this service?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">Delete</button>
                                        </form>
                                    </div>
                                </td>
                                @endif
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="{{ (empty($isStaff) || !$isStaff) ? '5':'4' }}" class="text-center text-muted py-5">No services found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@if(empty($isStaff) || !$isStaff)
    <!-- Add Service Modal -->
    <div id="addServiceModal" class="service-modal hidden">
        <div class="modal-card">
            <button type="button" onclick="closeAddServiceModal()" class="modal-close">&times;</button>
            <h5 class="fw-bold mb-3">Add Service</h5>
            <form method="POST" action="{{ route('admin.services.store') }}" id="addServiceForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Service name</label>
                    <input type="text" name="name" class="form-control" placeholder="Service name" required />
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Price (₱)</label>
                    <input type="number" name="price" class="form-control" placeholder="0" required />
                    <div class="invalid-feedback" style="display:block; color:#dc3545;"></div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100 fw-semibold">Confirm</button>
            </form>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div id="editServiceModal" class="service-modal hidden">
        <div class="modal-card">
            <button type="button" onclick="closeEditServiceModal()" class="modal-close">&times;</button>
            <h5 class="fw-bold mb-3">Edit Service</h5>
            <form method="POST" action="{{ route('admin.services.update','__ID__') }}" id="editServiceForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" />
                <div class="mb-3">
                    <label class="form-label fw-semibold">Service name</label>
                    <input type="text" name="name" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Price (₱)</label>
                    <input type="number" name="price" class="form-control" required />
                    <div class="invalid-feedback" style="display:block; color:#dc3545;"></div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-danger flex-fill" onclick="deleteServiceFromEdit()">Delete</button>
                    <button type="submit" class="btn btn-success flex-fill">Save</button>
                </div>
            </form>
        </div>
    </div>
@endif
</div>

@push('styles')
<style>
    @import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
    .services-table thead th { background:#f1f5f9; font-weight:600; font-size:.8rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
    .services-table tbody td { font-size:.9rem; }
    .services-table tbody tr:hover { background:#f8fafc; }
    .badge.status-badge { font-size:.65rem; letter-spacing:.5px; padding:.45em .55em; }
    .card { border-radius:10px; }
    .btn-icon { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; padding:0; }
    .btn-icon i { font-size:1rem; }
    /* Action buttons styling */
    .btn-danger { background-color:#dc2626; border-color:#dc2626; }
    .btn-danger:hover { background-color:#b91c1c; border-color:#b91c1c; }
    /* Make action buttons same size */
    .edit-service, .btn-danger { width: 60px; font-size: 0.75rem; padding: 0.25rem 0.5rem; }
    .service-modal { position:fixed; inset:0; background:rgba(0,0,0,.25); z-index:1050; display:flex; align-items:center; justify-content:center; padding:20px; }
    .service-modal.hidden { display:none; }
    .modal-card { background:#fff; width:100%; max-width:420px; border-radius:14px; box-shadow:0 4px 24px -4px rgba(0,0,0,.15); padding:28px 26px 26px; position:relative; }
    .modal-close { position:absolute; top:10px; right:14px; background:none; border:none; font-size:24px; line-height:1; cursor:pointer; color:#475569; }
    @media (max-width: 768px){
        .services-table thead { display:none; }
        .services-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
        .services-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
        .services-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
    }
</style>
@endpush

@if(empty($isStaff) || !$isStaff)
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        try {
            const addModal = document.getElementById('addServiceModal');
            const editModal = document.getElementById('editServiceModal');
            const addForm = document.getElementById('addServiceForm');
            const editForm = document.getElementById('editServiceForm');
            const openBtn = document.getElementById('openAddServiceBtn');

            // --- BEGIN: Service form sanitation ---
            function validateServiceName(input){
                let nameError = input.nextElementSibling;
                if (!nameError || !nameError.classList.contains('invalid-feedback')) {
                    nameError = input.parentNode.querySelector('.invalid-feedback') || document.createElement('div');
                    nameError.className = 'invalid-feedback';
                    if (!nameError.parentNode) input.parentNode.appendChild(nameError);
                }
                nameError.textContent = '';
                input.classList.remove('is-invalid');
                if (!input.value.trim() || /[0-9<>;=\',\.]/.test(input.value)) {
                    nameError.textContent = "Text only. Letters and spaces only. Do not input numbers or special characters <, >, ;, =, ', .";
                    input.classList.add('is-invalid');
                    return false;
                }
                return true;
            }
            function validateServicePrice(input){
                let priceError = input.nextElementSibling;
                if (!priceError || !priceError.classList.contains('invalid-feedback')) {
                    priceError = input.parentNode.querySelector('.invalid-feedback') || document.createElement('div');
                    priceError.className = 'invalid-feedback';
                    if (!priceError.parentNode) input.parentNode.appendChild(priceError);
                }
                priceError.textContent = '';
                input.classList.remove('is-invalid');
                if (!input.value.trim() || isNaN(input.value) || Number(input.value) < 0) {
                    priceError.textContent = 'Must be numeric. Reject negative values.';
                    input.classList.add('is-invalid');
                    return false;
                }
                return true;
            }
            function sanitizeServiceForm(form) {
                const nameValid = validateServiceName(form.querySelector('input[name="name"]'));
                const priceValid = validateServicePrice(form.querySelector('input[name="price"]'));
                return nameValid && priceValid;
            }
            // Live validation
            function attachLiveValidation(form){
                if(!form) return;
                const nameInput = form.querySelector('input[name="name"]');
                const priceInput = form.querySelector('input[name="price"]');
                if(nameInput){
                    nameInput.addEventListener('input', ()=> validateServiceName(nameInput));
                    nameInput.addEventListener('blur', ()=> validateServiceName(nameInput));
                }
                if(priceInput){
                    priceInput.addEventListener('input', ()=> validateServicePrice(priceInput));
                    priceInput.addEventListener('blur', ()=> validateServicePrice(priceInput));
                }
            }
            attachLiveValidation(addForm);
            attachLiveValidation(editForm);
            if(addForm){
                addForm.addEventListener('submit', function(e){
                    if(!sanitizeServiceForm(addForm)){
                        e.preventDefault();
                    }
                });
            }
            if(editForm){
                editForm.addEventListener('submit', function(e){
                    if(!sanitizeServiceForm(editForm)){
                        e.preventDefault();
                    }
                });
            }
            // --- END: Service form sanitation ---

            if(openBtn){
                openBtn.addEventListener('click', () => {
                    if(addForm) addForm.reset();
                    if(addModal) addModal.classList.remove('hidden');
                });
            }

            function attachEditHandlers(){
                document.querySelectorAll('.edit-service').forEach(btn => {
                    btn.onclick = () => {
                        const id = btn.getAttribute('data-id');
                        if(editForm){
                            editForm.id.value = id;
                            editForm.name.value = btn.getAttribute('data-name') || '';
                            editForm.price.value = btn.getAttribute('data-price') || 0;
                            editForm.status.value = btn.getAttribute('data-status') || 'active';
                            editForm.action = editForm.getAttribute('action').replace('__ID__', id);
                        }
                        if(editModal) editModal.classList.remove('hidden');
                    };
                });
            }
            attachEditHandlers();

            window.closeAddServiceModal = () => { if(addModal) addModal.classList.add('hidden'); };
            window.closeEditServiceModal = () => { if(editModal) editModal.classList.add('hidden'); };

            window.deleteServiceFromEdit = () => {
                if(!editForm) return;
                if(!confirm('Delete this service?')) return;
                const id = editForm.id.value;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = editForm.action;
                form.innerHTML = `@csrf @method('DELETE')`;
                document.body.appendChild(form);
                form.submit();
            };

            document.addEventListener('keydown', (e)=>{
                if(e.key === 'Escape'){
                    if(addModal && !addModal.classList.contains('hidden')) addModal.classList.add('hidden');
                    if(editModal && !editModal.classList.contains('hidden')) editModal.classList.add('hidden');
                }
            });
        } catch(err){ console.error('Services modal script error', err); }
    });
    </script>
    @endpush
@endif
@endsection
