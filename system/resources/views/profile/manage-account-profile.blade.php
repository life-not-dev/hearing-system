@extends('layouts.staff')

@section('title', 'Manage Account Profile | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="margin-top: -50px;">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0 d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-gear me-2"></i>
                        Manage Account Profile
                    </h4>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="username" class="form-label fw-bold">Username:</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-person-fill text-primary"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       id="username" 
                                       name="username" 
                                       value="{{ old('username', Auth::user()->name) }}" 
                                       required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Password:</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock-fill text-primary"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Leave blank to keep current password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Leave blank if you don't want to change your password</small>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-bold">Confirm Password:</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="bi bi-lock-fill text-primary"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Confirm new password">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-arrow-left me-1"></i>
                                Back
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Confirm
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    
    .card-header {
        border-radius: 12px 12px 0 0 !important;
        border: none;
    }
    
    .input-group-text {
        border-right: none;
        border-color: #d1d5db;
    }
    
    .form-control {
        border-left: none;
        border-color: #d1d5db;
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .form-control:focus + .input-group-text {
        border-color: #0d6efd;
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        font-weight: 600;
    }
    
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
    
    .btn-secondary {
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form validation
        const form = document.querySelector('form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        // Real-time password confirmation validation
        function validatePasswordMatch() {
            if (password.value && confirmPassword.value) {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                    confirmPassword.classList.add('is-invalid');
                } else {
                    confirmPassword.setCustomValidity('');
                    confirmPassword.classList.remove('is-invalid');
                }
            }
        }
        
        password.addEventListener('input', validatePasswordMatch);
        confirmPassword.addEventListener('input', validatePasswordMatch);
        
        // Form submission
        form.addEventListener('submit', function(e) {
            if (password.value && password.value !== confirmPassword.value) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });
    });
</script>
@endpush
@endsection
