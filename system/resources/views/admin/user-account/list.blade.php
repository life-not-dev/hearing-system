@extends('layouts.app')

@section('title', 'List of User Account | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding: 0 24px;">
	<div style="margin-top: -60px; margin-bottom: 8px;">
		<h4 class="mb-1" style="font-weight:700;">Manage Staff Account</h4>
		<p class="mb-0 text-muted" style="font-size:.9rem;">You can manage your account and register new account here.</p>
	</div>


	@if(session('success'))
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			{{ session('success') }}
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	@endif

	@if(session('error'))
		<div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-left:4px solid #dc2626;">
			{{ session('error') }}
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	@endif

	<!-- Staff & Admin Accounts Table -->
	@if(isset($staffAdmins) && $staffAdmins)
	<div class="card shadow-sm mb-2" style="border:1px solid #e2e8f0;">
		<div class="card-header bg-white border-0 pb-0 pt-3 px-4">
			<h6 class="mb-2" style="font-weight:600;">Staff & Admin Accounts</h6>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover mb-0 user-table align-middle">
					<thead>
						<tr>
							<th style="width:60px;">No.</th>
							<th>Username</th>
							<th>Email</th>
							<th style="width:320px;">Branch Assign</th>
							<th style="width:130px;">User Type</th>
										<th style="width:120px;" class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
						@forelse($staffAdmins as $user)
							<tr>
								<td class="fw-semibold">{{ ($staffAdmins->firstItem() ?? 0) + $loop->index }}</td>
								<td>{{ $user->name }}</td>
								<td><span class="text-muted" style="font-size:.9rem;">{{ $user->email }}</span></td>
								<td>
									@if($user->branch_id && $user->branchRef)
										<span style="font-size:.9rem;">{{ $user->branchRef->branch_name }}</span>
									@elseif($user->branch)
										<span style="font-size:.9rem;">{{ $user->branch }}</span>
									@else
										<span class="text-muted" style="font-size:.9rem;">No branch assigned</span>
									@endif
								</td>
								<td>
									<span style="font-size:.9rem;">{{ ucfirst($user->role) }}</span>
								</td>
								<td class="text-center">
									<div class="d-flex gap-1 justify-content-center">
										<button type="button" class="btn btn-xs btn-primary" style="min-width: 45px; padding: 2px 8px; font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#editUserModal" 
											data-user-id="{{ $user->id }}" 
											data-user-name="{{ $user->name }}" 
											data-user-email="{{ $user->email }}" 
											data-user-role="{{ $user->role }}">
											Edit
										</button>
										<form method="POST" action="{{ route('admin.user.account.delete', $user->id) }}" onsubmit="return confirm('Delete this user?');" class="d-inline-block">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-xs btn-danger" style="min-width: 45px; padding: 2px 8px; font-size: 0.75rem;">Delete</button>
										</form>
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="text-center text-muted py-4">No staff or admin accounts found.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@endif

	<!-- Patient Accounts Section -->
	@if(isset($patients) && $patients)
	<div style="margin-top: 35px; margin-bottom: 8px;">
		<h4 class="mb-1" style="font-weight:700;">Manage Patient Account</h4>
		<p class="mb-0 text-muted" style="font-size:.9rem;">View and manage all patient accounts registered in the system.</p>
	</div>
	<div class="card shadow-sm" style="border:1px solid #e2e8f0;">
		<div class="card-header bg-white border-0 pb-0 pt-3 px-4">
			<h6 class="mb-2" style="font-weight:600;">Patient Accounts</h6>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover mb-0 user-table align-middle">
					<thead>
						<tr>
							<th style="width:60px;">No.</th>
							<th>Username</th>
							<th>Email</th>
							<th style="width:210px;">Date Registered</th>
							<th style="width:130px;">User Type</th>
										<th style="width:120px;" class="text-center">Action</th>
						</tr>
					</thead>
					<tbody>
						@forelse($patients as $user)
							<tr>
								<td class="fw-semibold">{{ ($patients->firstItem() ?? 0) + $loop->index }}</td>
								<td>{{ $user->name }}</td>
								<td><span class="text-muted" style="font-size:.9rem;">{{ $user->email }}</span></td>
								<td><span class="text-muted" style="font-size:.8rem;">{{ optional($user->created_at)->timezone(config('app.timezone'))->format('M d, Y') }}</span></td>
								<td>
									<span style="font-size:.9rem;">Patient</span>
								</td>
								<td class="text-center">
									<div class="d-flex gap-1 justify-content-center">
										<button type="button" class="btn btn-xs btn-primary" style="min-width: 45px; padding: 2px 8px; font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#editUserModal" 
											data-user-id="{{ $user->id }}" 
											data-user-name="{{ $user->name }}" 
											data-user-email="{{ $user->email }}" 
											data-user-role="{{ $user->role }}">
											Edit
										</button>
										<form method="POST" action="{{ route('admin.user.account.delete', $user->id) }}" onsubmit="return confirm('Delete this patient?');" class="d-inline-block">
											@csrf
											@method('DELETE')
											<button type="submit" class="btn btn-xs btn-danger" style="min-width: 45px; padding: 2px 8px; font-size: 0.75rem;">Delete</button>
										</form>
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="6" class="text-center text-muted py-4">No patient accounts found.</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- Pagination for Patients (outside table, left aligned) -->
	<div class="d-flex justify-content-start p-3">
		<nav>
			<ul class="pagination pagination-sm mb-0">
				@php($staffPagePersist = request()->get('staff_page',1))
				<li class="page-item {{ $patients->onFirstPage() ? 'disabled' : '' }}">
					<a class="page-link" href="{{ $patients->previousPageUrl() ? $patients->previousPageUrl().'&staff_page='.$staffPagePersist : '#' }}">Previous</a>
				</li>
				@foreach ($patients->getUrlRange(1, $patients->lastPage()) as $page => $url)
					<li class="page-item {{ $page == $patients->currentPage() ? 'active' : '' }}">
						<a class="page-link" href="{{ $url.'&staff_page='.$staffPagePersist }}">{{ $page }}</a>
					</li>
				@endforeach
				@if($patients->lastPage() === 1)
					<!-- Keep a single page indicator when only one page -->
				@endif
				<li class="page-item {{ !$patients->hasMorePages() ? 'disabled' : '' }}">
					<a class="page-link" href="{{ $patients->nextPageUrl() ? $patients->nextPageUrl().'&staff_page='.$staffPagePersist : '#' }}">Next</a>
				</li>
			</ul>
		</nav>
	</div>
	@endif

	<!-- Edit User Modal -->
	<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editUserModalLabel">Edit User Account</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="editUserForm" method="POST">
					@csrf
					@method('PUT')
					<div class="modal-body">
						<div class="mb-3">
							<label for="editUserName" class="form-label">Username</label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-person"></i></span>
								<input type="text" class="form-control" id="editUserName" name="name" required>
							</div>
						</div>
						<div class="mb-3">
							<label for="editUserEmail" class="form-label">Email</label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-envelope"></i></span>
								<input type="email" class="form-control" id="editUserEmail" name="email" required>
							</div>
						</div>
						<div class="mb-3">
							<label for="editUserPassword" class="form-label">New Password (leave blank to keep current)</label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-lock"></i></span>
								<input type="password" class="form-control" id="editUserPassword" name="password" minlength="6">
							</div>
						</div>
						<div class="mb-3">
							<label for="editUserPasswordConfirm" class="form-label">Confirm New Password</label>
							<div class="input-group">
								<span class="input-group-text"><i class="bi bi-lock"></i></span>
								<input type="password" class="form-control" id="editUserPasswordConfirm" name="password_confirmation" minlength="6">
							</div>
						</div>
						<input type="hidden" id="editUserId" name="user_id">
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary w-100">Confirm</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@push('styles')
<style>
	@import url('https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css');
	.user-table thead th {
		background:#f1f5f9;
		font-weight:600;
		font-size:.8rem;
		text-transform:uppercase;
		color:#475569;
		border-bottom:1px solid #e2e8f0;
	}
	.user-table tbody td { font-size:.9rem; }
	.user-table tbody tr:hover { background:#f8fafc; }
	.badge { padding:.45em .6em; }
	.card { border-radius:10px; }
	.card-body { border-radius:10px; }
	.alert { border-left:4px solid #16a34a; }
	.btn-xs { padding: 0.25rem 0.5rem; font-size: 0.75rem; border-radius: 0.25rem; }
	.btn-primary { background-color: #0d6efd; border-color: #0d6efd; color: #fff; }
	.btn-primary:hover { background-color: #0b5ed7; border-color: #0a58ca; }
	.btn-danger { background-color: #dc3545; border-color: #dc3545; color: #fff; }
	.btn-danger:hover { background-color: #bb2d3b; border-color: #b02a37; }
	.pagination { --bs-pagination-active-bg:#0d6efd; --bs-pagination-active-border-color:#0d6efd; }
	.pagination .page-link { color:#0d6efd; }
	.pagination .page-item.active .page-link { color:#fff; font-weight:600; }
	@media (max-width: 768px){
		.user-table thead { display:none; }
		.user-table tbody tr { display:block; margin-bottom:12px; background:#fff; border:1px solid #e2e8f0; border-radius:8px; padding:10px 12px; }
		.user-table tbody td { display:flex; justify-content:space-between; padding:.35rem .25rem; }
		.user-table tbody td:before { content: attr(data-label); font-weight:600; color:#334155; }
	}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editUserModal = document.getElementById('editUserModal');
    const editUserForm = document.getElementById('editUserForm');
    
    // Handle modal show event
    editUserModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const userId = button.getAttribute('data-user-id');
        const userName = button.getAttribute('data-user-name');
        const userEmail = button.getAttribute('data-user-email');
        const userRole = button.getAttribute('data-user-role');
        
        // Populate form fields
        document.getElementById('editUserId').value = userId;
        document.getElementById('editUserName').value = userName;
        document.getElementById('editUserEmail').value = userEmail;
        
        // Clear password fields
        document.getElementById('editUserPassword').value = '';
        document.getElementById('editUserPasswordConfirm').value = '';
        
        // Update modal title
        document.getElementById('editUserModalLabel').textContent = `Edit ${userRole.charAt(0).toUpperCase() + userRole.slice(1)} Account`;
        
        // Set form action
        editUserForm.action = `/admin/user-account/update/${userId}`;
    });
    
    // Handle form submission
    editUserForm.addEventListener('submit', function(e) {
        const password = document.getElementById('editUserPassword').value;
        const passwordConfirm = document.getElementById('editUserPasswordConfirm').value;
        
        // Validate password confirmation if password is provided
        if (password && password !== passwordConfirm) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }
        
        // If password is provided, ensure it meets minimum length
        if (password && password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long!');
            return false;
        }
    });
    
    // Clear form when modal is hidden
    editUserModal.addEventListener('hidden.bs.modal', function () {
        editUserForm.reset();
    });
});
</script>
@endpush
@endsection