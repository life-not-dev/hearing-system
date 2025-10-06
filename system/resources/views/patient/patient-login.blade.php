@extends('layouts.auth')

@section('title', 'Patient Login')

@push('head')
<style>
    .login-frame { border: 1px solid #111; padding: 0; max-width: 980px; margin: 40px auto; }
    .login-left { padding: 0; }
    .login-left img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .login-right { padding: 48px; }
    .login-title { font-weight: 700; font-size: 1.6rem; margin-bottom: 18px; text-align: center; }
    .login-logo { display:block; margin: 4px auto 5px; width: 140px; height: auto; }
    .form-control { border-radius: 2px; }
    .btn-login { background: #000; color: #fff; border-radius: 20px; padding: 10px 26px; }
    .footer-note { text-align: center; margin-top: 28px; color: #666; }
    @media (max-width: 767px) {
        .login-frame { margin: 18px; }
        .login-right { padding: 24px; }
    }
</style>
@endpush

@section('content')
    <div class="login-frame d-flex flex-column flex-md-row">
        <div class="login-left col-12 col-md-6">
            <img src="/images/surgery.jpg" alt="Clinic"> 
        </div>
        <div class="login-right col-12 col-md-6">
                <img class="login-logo" src="/images/logos.png" alt="Kamatage Hearing Aid and Solution">
            <h4 class="login-title">Login to access your Account</h4>
            <form method="POST" action="{{ route('patient.login.submit') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" />
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                @if($errors->any())
                <div class="alert alert-danger py-2">
                    {{ $errors->first() }}
                </div>
                @endif
                <div class="mb-3 text-center">
                    <button class="btn btn-login" type="submit">Log in</button>
                </div>
            </form>
            <div class="footer-note">Kamatage Hearing Aid and Solution</div>
        </div>
    </div>
@endsection
