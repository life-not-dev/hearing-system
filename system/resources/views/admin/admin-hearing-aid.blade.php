@extends('layouts.app')

@section('title', 'Hearing Aid | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding:0 24px;">
    <div style="margin-top:-30px; margin-bottom:18px;">
        <h3 style="font-weight:bold;">Hearing Aid</h3>
        <p class="text-muted mb-3" style="font-size:0.9rem;">Manage and update the list of available hearing aids with brand, model, and price.</p>
    </div>
    
    <div style="margin-bottom:18px;" class="d-flex justify-content-end">
        @if(empty($isStaff) || !$isStaff)
            <button id="openAddHearingBtn" class="btn btn-primary" style="font-weight:600;">+ Add Hearing Aid</button>
        @endif
    </div>

    <div class="card shadow-sm" style="border:1px solid #e2e8f0;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 hearing-table align-middle">
                    <thead>
                        <tr>
                            <th style="width:70px;">No.</th>
                            <th>Brand</th>
                            <th>Model</th>
                            <th style="width:140px;">Price</th>
                            @if(empty($isStaff) || !$isStaff)
                                <th style="width:110px;" class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($hearingAids ?? []) as $item)
                            <tr data-id="{{ $item->id }}">
                                <td data-label="ID" class="fw-semibold">{{ sprintf('%02d',$item->id) }}</td>
                                <td data-label="Brand">{{ $item->brand }}</td>
                                <td data-label="Model">{{ $item->model }}</td>
                                <td data-label="Price">₱ {{ number_format($item->price,0) }}</td>
                                @if(empty($isStaff) || !$isStaff)
                                <td data-label="Action">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-primary edit-hearing"
                                                data-id="{{ $item->id }}"
                                                data-brand="{{ e($item->brand) }}"
                                                data-model="{{ e($item->model) }}"
                                                data-price="{{ $item->price }}"
                                                title="Edit">Edit</button>
                                        <form action="{{ route('admin.hearing.delete',$item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this hearing aid?');">
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
                                <td colspan="{{ (empty($isStaff) || !$isStaff) ? '5':'4' }}" class="text-center text-muted py-5">No hearing aids found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(empty($isStaff) || !$isStaff)
        <!-- Add Hearing Aid Modal -->
        <div id="addHearingAidModal" class="hearing-modal hidden">
            <div class="modal-card">
                <button type="button" class="modal-close" onclick="closeAddHearingAidModal()">&times;</button>
                <h5 class="fw-bold mb-3">Add Hearing Aid</h5>
                <form id="addHearingForm" method="POST" action="{{ route('admin.hearing.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Brand</label>
                        <input type="text" name="brand" class="form-control" required />
                        <div class="invalid-feedback" style="display:block; color:#dc3545;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Model</label>
                        <input type="text" name="model" class="form-control" required />
                        <div class="invalid-feedback" style="display:block; color:#dc3545;"></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Price (₱)</label>
                        <input type="number" name="price" class="form-control" required />
                        <div class="invalid-feedback" style="display:block; color:#dc3545;"></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary flex-fill" onclick="closeAddHearingAidModal()">Cancel</button>
                        <button type="submit" class="btn btn-success flex-fill">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Hearing Aid Modal -->
        <div id="editHearingAidModal" class="hearing-modal hidden">
            <div class="modal-card">
                <button type="button" class="modal-close" onclick="closeEditHearingAidModal()">&times;</button>
                <h5 class="fw-bold mb-3">Edit Hearing Aid</h5>
                <form id="editHearingForm" method="POST" action="{{ route('admin.hearing.update','__ID__') }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" />
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Brand</label>
                        <input type="text" name="brand" class="form-control" required />
                        <div class="invalid-feedback" style="display:block; color:#dc3545;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Model</label>
                        <input type="text" name="model" class="form-control" required />
                        <div class="invalid-feedback" style="display:block; color:#dc3545;"></div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Price (₱)</label>
                        <input type="number" name="price" class="form-control" required />
                        <div class="invalid-feedback" style="display:block; color:#dc3545;"></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger flex-fill" onclick="deleteFromEdit()">Delete</button>
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
    .hearing-table thead th { background:#f1f5f9; font-weight:600; font-size:.75rem; text-transform:uppercase; color:#475569; border-bottom:1px solid #e2e8f0; }
    .hearing-table tbody td { font-size:.85rem; }
    .hearing-table tbody tr:hover { background:#f8fafc; }
    .btn-icon { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; padding:0; }
    .btn-icon + .btn-icon { margin-left:4px; }
    /* Action buttons styling */
    .btn-danger { background-color:#dc2626; border-color:#dc2626; }
    .btn-danger:hover { background-color:#b91c1c; border-color:#b91c1c; }
    /* Make action buttons same size */
    .edit-hearing, .btn-danger { width: 60px; font-size: 0.75rem; padding: 0.25rem 0.5rem; }
    .hearing-modal { position:fixed; inset:0; background:rgba(0,0,0,.25); display:flex; align-items:center; justify-content:center; z-index:1050; padding:20px; }
    .hearing-modal.hidden { display:none; }
    .modal-card { background:#fff; width:100%; max-width:430px; border-radius:14px; box-shadow:0 4px 24px -4px rgba(0,0,0,.15); padding:28px 26px 26px; position:relative; }
    .modal-close { position:absolute; top:10px; right:14px; background:none; border:none; font-size:24px; line-height:1; cursor:pointer; color:#475569; }
    @media (max-width: 768px){
        .hearing-table thead { display:none; }
        .hearing-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
        .hearing-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
        .hearing-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
    }
</style>
@endpush

@if(empty($isStaff) || !$isStaff)
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        try {
            const addModal = document.getElementById('addHearingAidModal');
            const editModal = document.getElementById('editHearingAidModal');
            const addForm = document.getElementById('addHearingForm');
            const editForm = document.getElementById('editHearingForm');
            const openBtn = document.getElementById('openAddHearingBtn');

            // --- BEGIN: Hearing aid form validation ---
            function validateBrand(input){
                let err = input.nextElementSibling;
                if(!err || !err.classList.contains('invalid-feedback')){
                    err = input.parentNode.querySelector('.invalid-feedback') || document.createElement('div');
                    err.className = 'invalid-feedback';
                    if(!err.parentNode) input.parentNode.appendChild(err);
                }
                err.textContent='';
                input.classList.remove('is-invalid');
                const val = input.value.trim();
                // Allow letters, numbers, spaces, hyphen. Disallow < > = ' and period/comma/semicolon (mainly the ones user listed)
                const forbidden = /[<>=']/; // list user wants blocked
                if(!val){
                    err.textContent = 'Brand is required.';
                    input.classList.add('is-invalid');
                    return false;
                }
                if(!/^[A-Za-z0-9\s\-]+$/.test(val) || forbidden.test(val)){
                    err.textContent = "Text only. Do not input special characters <,>,=,' .";
                    input.classList.add('is-invalid');
                    return false;
                }
                return true;
            }
            function validateModel(input){
                let err = input.nextElementSibling;
                if(!err || !err.classList.contains('invalid-feedback')){
                    err = input.parentNode.querySelector('.invalid-feedback') || document.createElement('div');
                    err.className = 'invalid-feedback';
                    if(!err.parentNode) input.parentNode.appendChild(err);
                }
                err.textContent='';
                input.classList.remove('is-invalid');
                const val = input.value.trim();
                const forbidden = /[<>=']/;
                if(!val){
                    err.textContent = 'Model is required.';
                    input.classList.add('is-invalid');
                    return false;
                }
                if(!/^[A-Za-z0-9\s\-]+$/.test(val) || forbidden.test(val)){
                    err.textContent = "Text only. Do not input special characters <,>,=,' .";
                    input.classList.add('is-invalid');
                    return false;
                }
                return true;
            }
            function validatePrice(input){
                let err = input.nextElementSibling;
                if(!err || !err.classList.contains('invalid-feedback')){
                    err = input.parentNode.querySelector('.invalid-feedback') || document.createElement('div');
                    err.className = 'invalid-feedback';
                    if(!err.parentNode) input.parentNode.appendChild(err);
                }
                err.textContent='';
                input.classList.remove('is-invalid');
                if(!input.value.trim() || isNaN(input.value) || Number(input.value) < 0){
                    err.textContent = 'Must be numeric. Reject negative values.';
                    input.classList.add('is-invalid');
                    return false;
                }
                return true;
            }
            function sanitizeHearingForm(form){
                const brandValid = validateBrand(form.querySelector('input[name="brand"]'));
                const modelValid = validateModel(form.querySelector('input[name=\"model\"]'));
                const priceValid = validatePrice(form.querySelector('input[name="price"]'));
                return brandValid && modelValid && priceValid;
            }
            function attachLiveValidation(form){
                if(!form) return;
                const brand = form.querySelector('input[name="brand"]');
                const model = form.querySelector('input[name="model"]');
                const price = form.querySelector('input[name="price"]');
                if(brand){
                    brand.addEventListener('input', ()=>validateBrand(brand));
                    brand.addEventListener('blur', ()=>validateBrand(brand));
                }
                if(model){
                    model.addEventListener('input', ()=>validateModel(model));
                    model.addEventListener('blur', ()=>validateModel(model));
                }
                if(price){
                    price.addEventListener('input', ()=>validatePrice(price));
                    price.addEventListener('blur', ()=>validatePrice(price));
                }
            }
            attachLiveValidation(addForm);
            attachLiveValidation(editForm);
            if(addForm){
                addForm.addEventListener('submit', function(e){
                    if(!sanitizeHearingForm(addForm)) e.preventDefault();
                });
            }
            if(editForm){
                editForm.addEventListener('submit', function(e){
                    if(!sanitizeHearingForm(editForm)) e.preventDefault();
                });
            }
            // --- END: Hearing aid form validation ---

            if(openBtn){
                openBtn.addEventListener('click', () => {
                    if(addForm) addForm.reset();
                    if(addModal) addModal.classList.remove('hidden');
                });
            }

            function attachEditHandlers(){
                document.querySelectorAll('.edit-hearing').forEach(btn => {
                    btn.onclick = () => {
                        try {
                            const id = btn.getAttribute('data-id');
                            if(editForm){
                                editForm.id.value = id;
                                editForm.brand.value = btn.getAttribute('data-brand') || '';
                                editForm.model.value = btn.getAttribute('data-model') || '';
                                editForm.price.value = btn.getAttribute('data-price') || 0;
                                editForm.action = editForm.getAttribute('action').replace('__ID__', id);
                            }
                            if(editModal) editModal.classList.remove('hidden');
                        } catch(e){ console.warn('Edit handler error', e); }
                    };
                });
            }
            attachEditHandlers();

            window.closeAddHearingAidModal = ()=>{ if(addModal) addModal.classList.add('hidden'); };
            window.closeEditHearingAidModal = ()=>{ if(editModal) editModal.classList.add('hidden'); };

            window.deleteFromEdit = ()=>{
                if(!editForm) return;
                if(!confirm('Delete this hearing aid?')) return;
                const id = editForm.id.value;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = editForm.action.replace(/__ID__/, id).replace('/__ID__','/'+id);
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
        } catch(err){ console.error('Hearing aid modal script error', err); }
    });
    </script>
    @endpush
@endif
@endsection
