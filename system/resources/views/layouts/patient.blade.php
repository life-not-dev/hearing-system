<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Patient')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('head')
    <style>
        /* lightweight header styles to match patient pages */
        .navbar { box-shadow: 0 2px 4px #eee; background:#fff; }
        .navbar-brand img { width:40px; margin-left:8px; }
        .btn-login { background:#000; color:#fff; border-radius:20px; padding:8px 16px; }
        body { font-family: Arial, sans-serif; }
    </style>
</head>
<body>
    <div class="app-shell d-flex" style="min-height:100vh;">
        <aside class="app-sidebar" style="width:240px; background:#555b61; color:#fff; display:flex; flex-direction:column;">
            <div style="background:#18913b; padding:20px; display:flex; align-items:center; gap:12px;">
                <img src="/images/logos.png" alt="logo" style="width:44px;">
                <div style="font-weight:800; font-size:1.25rem;">HATC</div>
            </div>
            <nav style="flex:1; padding-top:18px;">
                <a href="{{ route('patient.dashboard') }}" class="d-flex align-items-center px-3 py-2 text-white" style="text-decoration:none;"><i class="fa fa-home me-2"></i> Home</a>
                <a href="{{ route('patient.testresult') }}" class="d-flex align-items-center px-3 py-2 text-white" style="text-decoration:none;"><i class="fa fa-list-alt me-2"></i> Test Results</a>
                <a href="{{ route('patient.appointment') }}" class="d-flex align-items-center px-3 py-2 text-white" style="text-decoration:none;"><i class="fa fa-calendar-check me-2"></i> Appointment</a>
                <a href="#" class="d-flex align-items-center px-3 py-2 text-white" style="text-decoration:none;"><i class="fa fa-file-invoice-dollar me-2"></i> Billing</a>
                <a href="{{ route('patient.message') }}" class="d-flex align-items-center px-3 py-2 text-white" style="text-decoration:none;"><i class="fa fa-comments me-2"></i> Message</a>
            </nav>
            <div style="padding:18px;">
                <form method="POST" action="{{ route('patient.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light w-100"><i class="fa fa-sign-out-alt me-2"></i> Log out</button>
                </form>
            </div>
        </aside>

        <div class="app-main flex-grow-1">
            <header style="border-bottom:1px solid #e6e6e6; padding:8px 18px; display:flex; justify-content:flex-end; align-items:center; gap:12px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="background:#ffd966; width:36px; height:36px; border-radius:18px; display:inline-flex; align-items:center; justify-content:center;"> <i class="fa fa-user"></i></span>
                    <span>User</span>
                </div>
            </header>

            <main style="padding:20px;">
                @yield('content')
            </main>
        </div>
    </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- Notification modal (used by dashboard notifications) -->
        <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notificationModalTitle">Notification</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="notificationModalBody">
                        <!-- filled dynamically -->
                    </div>
                    <div class="modal-footer" id="notificationModalFooter">
                        <small class="text-muted me-auto" id="notificationModalMeta"></small>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        @stack('scripts')
</body>
</html>
