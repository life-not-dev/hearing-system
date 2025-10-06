@extends('layouts.app')

@section('title', 'Create Account | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding: 0 24px;">
    <div style="margin-top: -85px; margin-bottom: 18px; text-align:center;"></div>
    <div class="d-flex justify-content-center align-items-start" style="min-height:60vh;">
        <div class="register-card">
            <div class="register-header d-flex justify-content-between align-items-start">
                <div>
                    <h3 class="reg-title">Register New Account</h3>
                    <p class="reg-subtitle">You can create new account to monitor your people.</p>
                </div>
            </div>
            <hr class="reg-separator" />
            <form method="POST" action="{{ route('admin.user.account.store') }}" class="reg-form" id="registerForm" novalidate>
                @csrf
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="name" class="form-control reg-input" placeholder="Enter your username here.." required>
                    <div class="invalid-feedback" id="usernameError"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control reg-input" placeholder="Enter your email address here.." required>
                    <div class="invalid-feedback" id="emailError"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" class="form-control reg-input" placeholder="Enter your password.." required>
                        <button class="password-toggle-btn" type="button" id="togglePassword" aria-label="Show password">
                            <i id="toggleIcon" class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="passwordError"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password_confirmation" class="form-control reg-input" placeholder="Confirm your password here.." required>
                        <button class="password-toggle-btn" type="button" id="toggleConfirmPassword" aria-label="Show password">
                            <i id="toggleConfirmIcon" class="bi bi-eye" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="confirmPasswordError"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">User Type</label>
                    <select name="role" class="form-select reg-input" required>
                        <option value="" selected>-</option>
                        <option value="staff">Staff</option>
                        <option value="admin">Admin</option>
                    </select>
                    <div class="invalid-feedback" id="roleError"></div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Branch</label>
                    <select name="branch" class="form-select reg-input" required>
                        <option value="" selected>-</option>
                        <option value="cdo">Cagayan De Oro City Branch</option>
                        <option value="davao">Davao City Branch</option>
                        <option value="butuan">Butuan City Branch</option>
                    </select>
                    <div class="invalid-feedback" id="branchError"></div>
                </div>
                <div class="text-start">
                    <button type="submit" class="btn reg-submit">Register</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .register-card {
        background:#fff;
        border:1px solid #d8dde6;
        border-radius:8px;
        padding:20px 32px 30px 32px;
        width:100%;
        max-width:890px;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        box-shadow:0 2px 8px rgba(0,0,0,0.05);
    }
    .reg-title { font-weight:700; font-size:1.55rem; margin:0 0 4px 0; color:#111827; }
    .reg-subtitle { margin:0; font-size:0.94rem; color:#4b5563; }
    .reg-separator { margin:18px 0 22px 0; border:0; height:1px; background:#e5e7eb; }
    .reg-form .form-label { font-weight:600; font-size:0.88rem; color:#374151; }
    .reg-input { font-size:0.95rem !important; }
    .reg-form input.reg-input, .reg-form select.reg-input {
        background:#f3f7fe;
        border:1px solid #c7d2fe;
        border-radius:4px;
        padding:10px 14px;
        box-shadow:none;
        transition: border-color .15s, box-shadow .15s;
    }
    .reg-form input.reg-input:focus, .reg-form select.reg-input:focus {
        border-color:#818cf8;
        outline:0;
        box-shadow:0 0 0 3px rgba(129,140,248,0.35);
        background:#f0f4ff;
    }
    .reg-form select.reg-input { cursor:pointer; }
    .reg-submit { background:#F48600; color:#fff; font-weight:600; font-size:0.95rem; padding:10px 34px; border-radius:6px; border:none; letter-spacing:.4px; box-shadow:0 2px 6px rgba(0,0,0,0.15); }
    .reg-submit:hover { background:#d97400; }
    .reg-submit:active { background:#c96800; }
    
    /* Password toggle styles */
    .password-wrapper { position: relative; }
    .password-wrapper .reg-input { padding-right: 46px; }
    .password-toggle-btn {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #374151;
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        cursor: pointer;
        padding: 0;
    }
    .password-toggle-btn .bi { font-size: 1.05rem; }
    .password-toggle-btn:hover { background: rgba(0,0,0,0.04); color: #111827; }
    .password-toggle-btn:focus { outline: none; box-shadow: none; }
    @media (max-width: 640px) { .register-card { padding:24px 20px 30px 20px; } }
    @media print { .btn, .btn-success, .d-flex { display: none !important; } .sidebar, .topbar { display: none !important; } .container-fluid { margin: 0; padding: 0; } .main-content { margin: 0; padding: 0; border: none; } }
</style>
@endpush
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('registerForm');
    if(!form) return;
    let attempted = false;

    const username = form.querySelector('input[name="name"]');
    const email = form.querySelector('input[name="email"]');
    const password = form.querySelector('input[name="password"]');
    const confirmPassword = form.querySelector('input[name="password_confirmation"]');
    const role = form.querySelector('select[name="role"]');
    const branch = form.querySelector('select[name="branch"]');

    // Password toggle functionality - Simple approach
    document.getElementById('togglePassword').onclick = function() {
        const passwordInput = document.querySelector('input[name="password"]');
        const icon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            passwordInput.type = 'password';
            icon.className = 'bi bi-eye';
        }
    };

    document.getElementById('toggleConfirmPassword').onclick = function() {
        const confirmPasswordInput = document.querySelector('input[name="password_confirmation"]');
        const icon = document.getElementById('toggleConfirmIcon');
        
        if (confirmPasswordInput.type === 'password') {
            confirmPasswordInput.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            confirmPasswordInput.type = 'password';
            icon.className = 'bi bi-eye';
        }
    };

    function setError(input, msg, id){
        const err = document.getElementById(id);
        if(err){ err.textContent = msg || ''; }
        if(msg){ input.classList.add('is-invalid'); } else { input.classList.remove('is-invalid'); }
    }

    function validateUsername(show){
        const val = username.value.trim();
        if(!val && show){
            setError(username,'Username is required.','usernameError');
            return false;
        }
        const forbidden = /[<>=@#'$%&,]/; // includes characters to block
        if((show || val) && (!/^[A-Za-z0-9]+$/.test(val) || forbidden.test(val))){
            setError(username,'Username fields: Letters and numbers only. Do not input special characters <,>,=,\'., @, #, $, %, &','usernameError');
            return false;
        }
        setError(username,'','usernameError');
        return true;
    }

    function validateEmail(show){
        const val = email.value.trim();
        // Updated: '@' is REQUIRED (exactly one). Forbidden special chars exclude '@'.
        const emailPattern = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
        if(!val && show){
            setError(email,'Email is required.','emailError');
            return false;
        } 
        const atCount = (val.match(/@/g)||[]).length;
        if((show || val) && (!emailPattern.test(val) || atCount !== 1 || /[<>=#$%&']/ .test(val))){
            setError(email,"Email: Must follow email format (example: name@example.com). Do not input special characters <, >, =, ', #, $, %, &","emailError");
            return false;
        }
        setError(email,'','emailError');
        return true;
    }

    function validatePassword(show){
        const val = password.value;
        if(!val && show){
            setError(password,'Password is required.','passwordError');
            return false;
        }
        const strong = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
        if((show || val) && !strong.test(val)){
            setError(password,'Password: At least 8 characters. Strong password must include uppercase, lowercase, number, and 1 special character.','passwordError');
            return false;
        }
        setError(password,'','passwordError');
        return true;
    }

    function validateConfirm(show){
        const val = confirmPassword.value;
        if(!val && show){
            setError(confirmPassword,'Confirm Password is required.','confirmPasswordError');
            return false;
        }
        if((show || val) && val !== password.value){
            setError(confirmPassword,'Confirm Password: Must exactly match password.','confirmPasswordError');
            return false;
        }
        setError(confirmPassword,'','confirmPasswordError');
        return true;
    }

    function validateRole(show){
        const val = role.value;
        if((show || val) && !val){
            setError(role,'User Type: Must be selected from dropdown list.','roleError');
            return false;
        }
        setError(role,'','roleError');
        return true;
    }

    function validateBranch(show){
        const val = branch.value;
        if((show || val) && !val){
            setError(branch,'Branch: Must be selected from dropdown list.','branchError');
            return false;
        }
        setError(branch,'','branchError');
        return true;
    }

    function validateAll(show){
        const u = validateUsername(show);
        const e = validateEmail(show);
        const p = validatePassword(show);
        const c = validateConfirm(show);
        const r = validateRole(show);
        const b = validateBranch(show);
        return u && e && p && c && r && b;
    }

    form.addEventListener('submit', function(e){
        attempted = true;
        if(!validateAll(true)){
            e.preventDefault();
        }
    });

    // Live validation after interaction
    username.addEventListener('input', ()=> attempted && validateUsername(true));
    email.addEventListener('input', ()=> attempted && validateEmail(true));
    password.addEventListener('input', ()=> attempted && validatePassword(true));
    confirmPassword.addEventListener('input', ()=> attempted && validateConfirm(true));
    role.addEventListener('change', ()=> attempted && validateRole(true));
    branch.addEventListener('change', ()=> attempted && validateBranch(true));
});
</script>
@endpush
@endsection