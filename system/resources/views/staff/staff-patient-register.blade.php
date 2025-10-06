@extends('layouts.staff')

@section('title', 'Register Patient Account | Kamatage Hearing Aid')

@section('content')
<div class="container-fluid" style="padding: 0 24px;">
  <div style="margin-top: -90px; margin-bottom: 18px; text-align:center;"></div>
  <div class="d-flex justify-content-center align-items-start" style="min-height:60vh;">
    <div class="register-card">
      <div class="register-header d-flex justify-content-between align-items-start">
        <div>
          <h3 class="reg-title">Register Patient Account</h3>
          <p class="reg-subtitle">Create a login so the patient can access the portal.</p>
        </div>
      </div>
      <hr class="reg-separator" />
      @if(session('success'))
        <div class="alert alert-success" style="font-size:0.85rem; padding:10px 14px; border-radius:6px;">{{ session('success') }}</div>
      @endif
      <form method="POST" action="{{ route('staff.patient.register.store') }}" class="reg-form" id="patientRegisterForm" novalidate>
        @csrf
        <div class="mb-3">
          <label class="form-label">Username</label>
          <input type="text" name="name" class="form-control reg-input" placeholder="Enter patient username here.." required>
          <div class="invalid-feedback" id="pUsernameError"></div>
        </div>
        <div class="mb-3">
          <label class="form-label">Email Address</label>
          <input type="email" name="email" class="form-control reg-input" placeholder="Enter patient email address here.." required>
          <div class="invalid-feedback" id="pEmailError"></div>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="password-wrapper">
            <input type="password" name="password" class="form-control reg-input" placeholder="Enter password.." required>
            <button class="password-toggle-btn" type="button" id="togglePassword" aria-label="Show password">
              <i id="toggleIcon" class="bi bi-eye" aria-hidden="true"></i>
            </button>
          </div>
          <div class="invalid-feedback" id="pPasswordError"></div>
        </div>
        <div class="mb-4">
          <label class="form-label">Confirm Password</label>
          <div class="password-wrapper">
            <input type="password" name="password_confirmation" class="form-control reg-input" placeholder="Confirm password here.." required>
            <button class="password-toggle-btn" type="button" id="toggleConfirmPassword" aria-label="Show password">
              <i id="toggleConfirmIcon" class="bi bi-eye" aria-hidden="true"></i>
            </button>
          </div>
          <div class="invalid-feedback" id="pConfirmError"></div>
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
  .register-card { background:#fff; border:1px solid #d8dde6; border-radius:8px; padding:30px 32px 36px 32px; width:100%; max-width:890px; font-family: 'Inter', 'Segoe UI', Arial, sans-serif; box-shadow:0 2px 8px rgba(0,0,0,0.05); }
  .reg-title { font-weight:700; font-size:1.55rem; margin:0 0 4px 0; color:#111827; }
  .reg-subtitle { margin:0; font-size:0.94rem; color:#4b5563; }
  .reg-separator { margin:18px 0 22px 0; border:0; height:1px; background:#e5e7eb; }
  .reg-form .form-label { font-weight:600; font-size:0.88rem; color:#374151; }
  .reg-input { font-size:0.95rem !important; }
  .reg-form input.reg-input { background:#f3f7fe; border:1px solid #c7d2fe; border-radius:4px; padding:10px 14px; box-shadow:none; transition: border-color .15s, box-shadow .15s; }
  .reg-form input.reg-input:focus { border-color:#818cf8; outline:0; box-shadow:0 0 0 3px rgba(129,140,248,0.35); background:#f0f4ff; }
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
// Minimal client-side validation (mirrors style, simplified for patient)
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('patientRegisterForm');
  if(!form) return;
  let attempted = false;
  const username = form.querySelector('input[name="name"]');
  const email = form.querySelector('input[name="email"]');
  const password = form.querySelector('input[name="password"]');
  const confirm = form.querySelector('input[name="password_confirmation"]');

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

  function setError(el, msg, id){
    const err = document.getElementById(id); if(err) err.textContent = msg || ''; if(msg){ el.classList.add('is-invalid'); } else { el.classList.remove('is-invalid'); }
  }
  function vUser(show){ const v = username.value.trim(); if(!v && show){ setError(username,'Username is required.','pUsernameError'); return false;} if(v && !/^[A-Za-z0-9]+$/.test(v)){ setError(username,'Letters and numbers only.','pUsernameError'); return false;} setError(username,'','pUsernameError'); return true; }
  function vEmail(show){ const v = email.value.trim(); if(!v && show){ setError(email,'Email is required.','pEmailError'); return false;} const ok = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/.test(v); if(v && !ok){ setError(email,'Enter a valid email.','pEmailError'); return false;} setError(email,'','pEmailError'); return true; }
  function vPass(show){ const v = password.value; if(!v && show){ setError(password,'Password required.','pPasswordError'); return false;} if(v && v.length < 8){ setError(password,'At least 8 characters.','pPasswordError'); return false;} setError(password,'','pPasswordError'); return true; }
  function vConfirm(show){ const v = confirm.value; if(!v && show){ setError(confirm,'Confirm password.','pConfirmError'); return false;} if(v && v !== password.value){ setError(confirm,'Passwords must match.','pConfirmError'); return false;} setError(confirm,'','pConfirmError'); return true; }
  function all(show){ return vUser(show)&&vEmail(show)&&vPass(show)&&vConfirm(show); }
  form.addEventListener('submit', e=>{ attempted = true; if(!all(true)){ e.preventDefault(); }});
  [username,email,password,confirm].forEach(inp=> inp.addEventListener('input', ()=> attempted && all(false)) );
});
</script>
@endpush
@endsection
