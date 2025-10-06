{{-- Admin Login Page --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Login | Kamatage Hearing Aid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #fff;
        }
        .admin-login-box {
            max-width: 400px;
            margin: 60px auto;
            padding: 30px 30px 20px 30px;
            border: 1px solid #c2babaff;
            border-radius: 8px;
            background: #ffffff; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.08), 0 4px 8px rgba(0,0,0,0.06);        
            text-align: center;
          
        }
        .admin-logo {
            width: 140px;
            margin-bottom: 10px;
        }
        .login-btn {
            background: #f39200;
            color: #fff;
            font-weight: bold;
        }
        .login-btn:hover {
            background: #d87c00;
        }
        .forgot-link {
            font-size: 0.95em;
            color: #333;
        }
        /* forgot-password link: no underline */
        .forgot-password-link {
            text-decoration: none;
            color: #0d6efd; /* bootstrap link color */
        }
        .forgot-password-link:hover {
            text-decoration: none; /* keep no underline on hover as requested */
            color: #0b5ed7;
        }
        /* password toggle: positioned inside the input (no separate column) */
        .password-wrapper { position: relative; }
        .password-wrapper .form-control { padding-right: 46px; /* room for the icon */ }
        .password-toggle-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #374151; /* gray-700 */
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

        /* Success modal (small) */ 
        .modal-success .modal-body { padding: 24px 28px; }
        .modal-success .checkmark { width: 72px; height: 72px; display: block; margin: 0 auto 8px auto; }
        .modal-success .checkmark__circle { stroke: #28a745; stroke-width: 3; fill: none; }
        .modal-success .checkmark__check {
            stroke: #28a745; stroke-width: 4; fill: none; stroke-linecap: round; stroke-linejoin: round;
            stroke-dasharray: 48; stroke-dashoffset: 48; animation: check-draw .6s .15s forwards ease-out;
        }
        @keyframes check-draw { to { stroke-dashoffset: 0; } }

        /* Error modal (small) */
        .modal-error .modal-body { padding: 18px 20px; }
        .modal-error .cross { width: 56px; height: 56px; display: block; margin: 0 auto 8px auto; }
        .modal-error .cross__circle { stroke: #dc3545; stroke-width: 3; fill: none; }
        .modal-error .cross__path {
            stroke: #dc3545; stroke-width: 3.5; fill: none; stroke-linecap: round; stroke-linejoin: round;
            stroke-dasharray: 40; stroke-dashoffset: 40; animation: cross-draw .6s .15s forwards ease-out;
        }
        @keyframes cross-draw { to { stroke-dashoffset: 0; } }
    </style>
</head>
<body>
    <div class="admin-login-box">
        <img src="/images/logos.png" alt="Kamatage Logo" class="admin-logo">
        <h4 style="font-weight:bold; margin-bottom:2px;">Welcome to Kamatage Hearing Aid Solution</h4>
        <br>
         <div class="bold-text-muted" style="font-size:1rem;">Sign in to access your dashboard!</div>
         <br>

        @if(session('error'))
            <div class="alert alert-danger d-none" role="alert">
                {{ session('error') }}
            </div>
        @endif
    <form method="POST" action="{{ route('login.submit') }}" id="loginForm" novalidate>
        @csrf
        <div class="mb-3 text-start">
            <label for="email" class="form-label"><i class="bi bi-person-fill text-dark me-2"></i>Email</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
            <div class="invalid-feedback" id="emailError"></div>
        </div>
        <div class="mb-3 text-start">
            <label for="password" class="form-label"><i class="bi bi-lock-fill text-dark me-2"></i>Password</label>
            <div class="password-wrapper">
                <input type="password" class="form-control" id="password" name="password" required>
                <button class="password-toggle-btn" type="button" id="togglePassword" aria-label="Show password">
                    <i id="toggleIcon" class="bi bi-eye" aria-hidden="true"></i>
                </button>
            </div>
            <div class="invalid-feedback" id="passwordError"></div>
        </div>
        <div class="mb-2 text-end">
            @if (Route::has('password.request'))
                <a href="#" class="small forgot-password-link" id="openForgot">Forgot password?</a>
            @else
                <a href="#" class="small forgot-password-link" id="openForgot">Forgot password?</a>
            @endif
        </div>
        <!-- Remember me removed per request -->
        <button type="submit" class="btn login-btn w-100 mb-2">Log-in</button>
    </form>
        <!-- Forgot password link removed: Route not defined -->
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const passwordInput = document.getElementById('password');
    const toggleBtn = document.getElementById('togglePassword');
    const toggleIcon = document.getElementById('toggleIcon');
    if(toggleBtn){
        toggleBtn.addEventListener('click', function(){
            const isHidden = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
            // Swap icon classes
            if(isHidden){
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
                toggleBtn.setAttribute('aria-label', 'Hide password');
            } else {
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
                toggleBtn.setAttribute('aria-label', 'Show password');
            }
        });
    }

    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        var email = document.getElementById('email').value;
        var emailError = document.getElementById('emailError');
        var password = document.getElementById('password').value;
        var passwordError = document.getElementById('passwordError');
        var forbidden = /[<>;=\',]/;
        var emailPattern = /^[^@\s]+@[^@\s]+\.[^@\s]+$/;
        var passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
        emailError.textContent = '';
        passwordError.textContent = '';
        document.getElementById('email').classList.remove('is-invalid');
        document.getElementById('password').classList.remove('is-invalid');
        var hasError = false;
        if (!email.trim()) {
            emailError.textContent = 'Email is required.';
            document.getElementById('email').classList.add('is-invalid');
            hasError = true;
        } else if (forbidden.test(email)) {
            emailError.textContent = 'Do not input special characters <, >, ;, =, \' or ,';
            document.getElementById('email').classList.add('is-invalid');
            hasError = true;
        } else if (!emailPattern.test(email)) {
            emailError.textContent = 'Must follow email format (example: name@example.com).';
            document.getElementById('email').classList.add('is-invalid');
            hasError = true;
        }
        if (!password.trim()) {
            passwordError.textContent = 'Password is required.';
            document.getElementById('password').classList.add('is-invalid');
            hasError = true;
        } else if (!passwordPattern.test(password)) {
            passwordError.textContent = 'Password must be at least 8 characters and include 1 uppercase, 1 lowercase, 1 number, and 1 special character (e.g., Test@123).';
            document.getElementById('password').classList.add('is-invalid');
            hasError = true;
        }
        if (hasError) {
            e.preventDefault();
            return;
        }

        // Submit via AJAX to control modals on the same page
        e.preventDefault();
        try {
            const form = e.currentTarget;
            const url = form.getAttribute('action');
            const csrf = document.querySelector('input[name="_token"]').value;
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ email, password, remember: (document.getElementById('remember') ? document.getElementById('remember').checked : false) })
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok && data && data.ok) {
                // Show success modal then redirect
                var sucEl = document.getElementById('loginSuccessModal');
                if (sucEl && window.bootstrap) {
                    var successModal = new bootstrap.Modal(sucEl, { backdrop: 'static', keyboard: true });
                    successModal.show();
                    setTimeout(function(){ try { successModal.hide(); } catch(e){}; window.location.href = data.redirect || '/'; }, 1500);
                } else {
                    window.location.href = data.redirect || '/';
                }
            } else {
                // Failure: show error modal with message
                var message = (data && data.message) ? data.message : 'Incorrect credentials. Double-check your email and password before login';
                var errEl = document.getElementById('loginErrorModal');
                if (errEl) {
                    var textEl = errEl.querySelector('.fw-semibold.text-danger');
                    if (textEl) { textEl.textContent = message; }
                }
                if (errEl && window.bootstrap) {
                    var errorModal = new bootstrap.Modal(errEl, { backdrop: 'static', keyboard: true });
                    errorModal.show();
                    setTimeout(function(){ try { errorModal.hide(); } catch(e){} }, 1800);
                }
            }
        } catch (err) {
            // Network or parse error: show generic error modal
            var errEl = document.getElementById('loginErrorModal');
            if (errEl && window.bootstrap) {
                var errorModal = new bootstrap.Modal(errEl, { backdrop: 'static', keyboard: true });
                errorModal.show();
                setTimeout(function(){ try { errorModal.hide(); } catch(e){} }, 1800);
            }
        }
    });

    // Auto-open error modal if backend placed an error in session (non-AJAX fallback)
    document.addEventListener('DOMContentLoaded', function(){
        var hasErrorFromServer = {{ session('error') ? 'true' : 'false' }};
        if(hasErrorFromServer && window.bootstrap){
            var errEl = document.getElementById('loginErrorModal');
            if(errEl){
                var errorModal = new bootstrap.Modal(errEl, { backdrop: 'static', keyboard: true });
                errorModal.show();
                errEl.addEventListener('shown.bs.modal', function(){
                    setTimeout(function(){
                        var inst = bootstrap.Modal.getInstance(errEl) || errorModal;
                        inst && inst.hide();
                    }, 1800);
                }, { once: true });
            }
        }
    });

    // Auto-open success modal only if server set a success flag (non-AJAX fallback)
    document.addEventListener('DOMContentLoaded', function(){
        var hasSuccessFromServer = {{ session('success') ? 'true' : 'false' }};
        if(hasSuccessFromServer && window.bootstrap){
            var sucEl = document.getElementById('loginSuccessModal');
            if(sucEl){
                var successModal = new bootstrap.Modal(sucEl, { backdrop: 'static', keyboard: false });
                successModal.show();
                sucEl.addEventListener('shown.bs.modal', function(){
                    setTimeout(function(){
                        var inst = bootstrap.Modal.getInstance(sucEl) || successModal;
                        inst && inst.hide();
                    }, 1800);
                }, { once: true });
            }
        }
    });
    </script>
</body>

<!-- Small success modal -->
<div class="modal fade modal-success" id="loginSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <svg class="checkmark" viewBox="0 0 52 52" aria-hidden="true">
                    <circle class="checkmark__circle" cx="26" cy="26" r="24"></circle>
                    <path class="checkmark__check" d="M14 27 l8 8 l16 -16"></path>
                </svg>
                <div class="fw-semibold" style="font-size:.95rem;">You have successfully logged in!</div>
            </div>
        </div>
    </div>
</div>

<!-- Small error modal -->
<div class="modal fade modal-error" id="loginErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <svg class="cross" viewBox="0 0 52 52" aria-hidden="true">
                    <circle class="cross__circle" cx="26" cy="26" r="24"></circle>
                    <path class="cross__path" d="M18 18 L34 34"></path>
                    <path class="cross__path" d="M34 18 L18 34"></path>
                </svg>
                <div class="fw-semibold text-danger" style="font-size:.95rem;">Incorrect credentials. Double-check your email and password before login</div>
            </div>
        </div>
    </div>
</div>
</html>
        <!-- Forgot password modal -->
        <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Forgot Password</h5>
                    </div>
                    <div class="modal-body">
                        <p>Enter your email address and we'll send you a password reset link.</p>
                        <div id="forgotAlert" class="alert d-none" role="alert"></div>
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-envelope-fill text-dark me-2"></i>Email</label>
                            <input type="email" id="forgotEmail" class="form-control" placeholder="Enter your email address">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="sendForgot" class="btn login-btn w-100"><i class="bi bi-envelope me-2"></i>Send Reset Link</button>
                    </div>
                    <div class="w-100 text-center pb-3">
                        <a href="#" id="backToLogin" class="small forgot-password-link">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            (function(){
                var forgotModalEl = document.getElementById('forgotPasswordModal');
                var forgotModal = forgotModalEl ? new bootstrap.Modal(forgotModalEl) : null;
                var openBtn = document.getElementById('openForgot');
                var sendBtn = document.getElementById('sendForgot');
                var alertEl = document.getElementById('forgotAlert');
                var emailInput = document.getElementById('forgotEmail');
                if(openBtn && forgotModal) openBtn.addEventListener('click', function(e){ e.preventDefault(); alertEl.classList.add('d-none'); emailInput.value=''; forgotModal.show(); });
                if(sendBtn){
                    sendBtn.addEventListener('click', async function(){
                        var email = (emailInput && emailInput.value||'').trim();
                        if(!email){ alertEl.className='alert alert-danger'; alertEl.textContent='Please enter your email address.'; alertEl.classList.remove('d-none'); return; }
                        try{
                            var url = '@if(Route::has("password.email")){{ route("password.email") }}@else{{ "#" }}@endif';
                            var csrf = document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '';
                            const res = await fetch(url, { method:'POST', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ email }) });
                            const js = await res.json().catch(()=>({}));
                            if(res.ok){ alertEl.className='alert alert-success'; alertEl.textContent = (js.message||'Reset link sent. Check your email.'); alertEl.classList.remove('d-none'); }
                            else { alertEl.className='alert alert-danger'; alertEl.textContent = (js.message||'Failed to send reset link.'); alertEl.classList.remove('d-none'); }
                        }catch(e){ alertEl.className='alert alert-danger'; alertEl.textContent='Network error.'; alertEl.classList.remove('d-none'); }
                    });
                }
                    var backLink = document.getElementById('backToLogin');
                    if(backLink){ backLink.addEventListener('click', function(e){ e.preventDefault(); forgotModal.hide(); }); }
            })();
        </script>

        <!-- Forgot password link removed: Route not defined -->
