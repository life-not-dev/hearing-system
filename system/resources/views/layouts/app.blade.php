{{-- Main App Layout --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @stack('styles')
    <style>
    :root { --sidebar-width: 290px; }
        body {
            background: #fff;
            margin: 0;
        }
        .sidebar {
            background: #6c7a89;
            color: #fff;
            min-height: 100vh;
            width: var(--sidebar-width);
            position: fixed;
            left: 0;
            top: 0;
            padding: 30px 0 0 0;
            border-right: 1px solid #222;
            border-radius: 0;
            overflow-y: auto;
            max-height: 100vh;
            box-shadow: 0 4px 18px 0 rgba(60,60,60,0.25), 0 1px 6px 0 rgba(60,60,60,0.10);
            scrollbar-width: thin; /* Firefox */
            scrollbar-color: #5a6470 #6c7a89; /* thumb track */
        }
        /* WebKit scrollbar for admin sidebar */
        .sidebar::-webkit-scrollbar { width:8px; }
        .sidebar::-webkit-scrollbar-track { background:#6c7a89; }
        .sidebar::-webkit-scrollbar-thumb { background:#5a6470; border-radius:4px; }
        .sidebar::-webkit-scrollbar-thumb:hover { background:#4e5660; }
        .sidebar .logo {
            display: block;
            margin: 0 auto 20px auto;
            width: 120px;
        }
        .sidebar h5 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 16px 30px;
            font-size: 1.05em;
            font-weight: 600;
            cursor: pointer;
            line-height: 1.3;
            box-sizing: border-box;
            transition: background-color .15s ease, color .15s ease;
            margin-bottom: 4px;
        }
        .sidebar ul li a i.bi { 
            font-size: 1.1em; 
            line-height: 1; 
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }
        .sidebar ul li.active {
            background: #e0e0e0;
            color: #333;
        }
        .sidebar ul li:hover {
            background: #e0e0e0;
            color: #333;
        }
        .sidebar ul li.open {
            background: #6c7a89 !important;
            color: #fff !important;
            /* keep font-weight steady to prevent layout shift */
            font-weight: inherit;
        }
        .sidebar ul li.open ul li {
            color: #fff !important;
        }
        /* Unified submenu (e.g., Reports/Account) styles */
        .sidebar ul li ul {
            font-size: 0.95em;
            margin-top: 8px;
            background: transparent !important;
            border-radius: 0;
            position: relative;
            box-shadow: none;
            padding: 0;
            overflow: hidden;
            max-height: 0;
            transition: max-height 0.3s ease, margin-top 0.3s ease;
        }
        .sidebar ul li.open ul {
            max-height: 500px;
            margin-top: 8px;
        }
        .sidebar ul li ul li {
            background: transparent;
            color: #fff;
            border-radius: 6px;
            margin: 2px 0;
            padding: 12px 20px;
            cursor: pointer;
            transition: background-color .15s ease, color .15s ease;
            font-weight: 500;
        }
        /* anchor inside submenu list items */
        .sidebar ul li ul li a,
        #reportsDropdown li a,
        #accountDropdown li a,
        #accountDropdown div {
            color: inherit;
            text-decoration: none;
            display: block;
            padding: 0;
            border-radius: 6px;
            font-weight: 500;
        }
        /* Active/hover state unified color for all Report items */
        #reportsDropdown li a:hover,
        #reportsDropdown li a:focus,
        #reportsDropdown li.active a,
        .sidebar ul li ul li:hover,
        .sidebar ul li ul li.active,
        #accountDropdown div.active {
            background: #7a8699 !important;
            color: #fff !important;
        }
        
        /* Chevron rotation animation */
        .sidebar ul li.open .chevron-icon {
            transform: rotate(180deg);
        }
        
        /* Responsive sidebar */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .topbar {
                margin-left: 0;
                width: 100%;
            }
        }
        /* Ensure default state of report/account dropdown text is consistent */
        #reportsDropdown,
        #accountDropdown {
            color: #fff;
        }
        .topbar {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            height: 70px;
            padding: 12px 30px 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 102;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            box-sizing: border-box;
            border: 1px solid #d1d5db; /* uniform 1px border */
        }
        /* Removed ::after border; using real 1px border on the bar itself */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 88px 24px 24px 24px; /* adjust top padding to match staff layout */
            border: none;
            border-radius: 0;
            background: #fff;
            box-sizing: border-box;
            min-height: 80vh;
            position: relative;
        }
        .main-content::before {
            content: none;
        }
        .greeting {
            background: #f39200;
            color: #fff;
            border-radius: 18px;
            padding: 8px 32px 8px 32px;
            margin-bottom: 14px;
            margin-top: 8px; /* lighter default spacing; page can override inline */
            min-height: 56px;
            display: flex;
            align-items: center;
            box-sizing: border-box;
        }
        .greeting .icon {
            font-size: 2.5em;
            margin-right: 18px;
        }
        .pie-chart-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #eee;
            padding: 20px;
            margin-right: 30px;
            border: 2px solid #d1d5db;
        }
        .report-box {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px #eee;
            padding: 20px;
            min-width: 220px;
            border: 2px solid #d1d5db;
        }
        .dashboard-row {
            display: flex;
            gap: 20px;
        }
        .admin-label-btn {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }
        .admin-label-btn .admin-icon {
            width: 32px;
            height: 32px;
            background: #f7b500;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
        }
        .admin-label-btn span {
            font-weight: bold;
            color: #222;
            font-size: 1.1em;
        }
        .profile-modal {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: transparent !important;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }
        .profile-modal.active {
            display: flex;
            pointer-events: all;
        }
        .profile-card {
            background: #ededed;
            border: 2px solid #222;
            border-radius: 8px;
            padding: 32px 32px 24px 32px;
            min-width: 420px;
            max-width: 98vw;
            box-shadow: 0 2px 16px #bbb;
            text-align: center;
            margin: auto;
        }
        .profile-card .profile-pic {
            width: 100px;
            height: 100px;
            background: #666;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px auto;
        }
        .profile-card .profile-pic svg {
            width: 60px;
            height: 60px;
            color: #fff;
        }
        .profile-card input {
            border: 1px solid #222;
            border-radius: 0;
            margin-bottom: 12px;
            font-size: 1em;
        }
        .profile-card label {
            font-weight: bold;
            margin-bottom: 6px;
            float: left;
        }
        .profile-card .btn {
            min-width: 100px;
            font-weight: bold;
        }
        .profile-card .btn-cancel {
            background: #c0392b;
            color: #fff;
        }
        .profile-card .btn-save {
            background: #27ae60;
            color: #fff;
        }
        .profile-submenu.active {
            background: #e0e0e0 !important;
            color: #222 !important;
            font-weight: bold;
            border-radius: 8px;
        }
        .btn-cancel:hover { background: #a93226; }
        .btn-save:hover { background: #219150; }
    </style>
    @stack('scripts')
</head>
<body>
    <div class="sidebar">
        <img src="/images/logos.png" alt="Kamatage Logo" class="logo">
        <h5 style="font-weight:bold; margin-bottom:4px;">Kamatage Hearing Aid Center</h5>
        <br>
        @php($role = Auth::check() ? Auth::user()->role : null)
        <ul>
            @if($role === 'admin')
                <li class="@if(request()->routeIs('admin.dashboard')) active @endif"><a href="{{ route('admin.dashboard') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-house-door"></i><span>Home</span></a></li>
                <li class="@if(request()->routeIs('admin.appointment.record')) active @endif"><a href="{{ route('admin.appointment.record') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-calendar2-check"></i><span>Appointment Record</span></a></li>
                <li class="@if(request()->routeIs('admin.services')) active @endif"><a href="{{ route('admin.services') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-gear"></i><span>Services</span></a></li>
                <li class="@if(request()->routeIs('admin.hearing.aid')) active @endif"><a href="{{ route('admin.hearing.aid') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-ear"></i><span>Hearing Aid</span></a></li>
                <li class="@if(request()->routeIs('admin.patient.record')) active @endif"><a href="{{ route('admin.patient.record') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-people"></i><span>Patient Record</span></a></li>
                <li class="@if(request()->routeIs('admin.billing')) active @endif"><a href="{{ route('admin.billing') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-receipt"></i><span>Billing</span></a></li>
                <li id="reportsMenu" style="position:relative;">
                    <span onclick="toggleDropdown('reportsDropdown')" style="display:flex; align-items:center; cursor:pointer; font-weight:600;">
                        <i class="bi bi-graph-up" style="margin-right:8px; width:20px; text-align:center;"></i>
                        <span style="flex:1;">Reports</span>
                        <i class="bi bi-chevron-down chevron-icon" style="transition: transform 0.3s ease;"></i>
                    </span>
                    <ul id="reportsDropdown" style="font-size:0.95em; margin-top:8px; background:#5a6470; border-radius:6px; position:relative;">
                        <li class="report-submenu">
                            <a href="{{ route('admin.report.appointment') }}" style="color:inherit; text-decoration:none; display:block;">Appointment</a>
                        </li>
                        <li class="report-submenu">
                            <a href="{{ route('admin.report.billing') }}" style="color:inherit; text-decoration:none; display:block;">Billing</a>
                        </li>
                        <li class="report-submenu">
                            <a href="{{ route('admin.report.hearing.aid') }}" style="color:inherit; text-decoration:none; display:block;">Hearing Aid</a>
                        </li>
                        <li class="report-submenu">
                            <a href="{{ route('admin.report.monthly.service') }}" style="color:inherit; text-decoration:none; display:block;">Monthly Service Revenue</a>
                        </li>
                        <li class="report-submenu">
                            <a href="{{ route('admin.report.monthly.hearing.aid.revenue') }}" style="color:inherit; text-decoration:none; display:block;">Monthly Hearing Aid Revenue</a>
                        </li>
                    </ul>
                </li>
                <li id="accountMenu" style="font-weight:bold; color:#fff; position:relative;">
                    <span onclick="toggleDropdown('accountDropdown')" style="display:flex; align-items:center; cursor:pointer; font-weight:600;">
                        <i class="bi bi-people-fill" style="margin-right:8px; width:20px; text-align:center;"></i>
                        <span style="flex:1;">Account User Management</span>
                        <i class="bi bi-chevron-down chevron-icon" style="transition: transform 0.3s ease;"></i>
                    </span>
                    <ul id="accountDropdown" style="font-size:0.95em; margin-top:8px; background:#5a6470; border-radius:6px; position:relative;">
                        <li>
                            <a href="{{ route('admin.user.account.list') }}" style="color:inherit; text-decoration:none; display:block;">List of User Account</a>
                        </li>
                        <li>
                            <a href="{{ route('admin.user.account.register') }}" style="color:inherit; text-decoration:none; display:block;">Register New Account</a>
                        </li>
                    </ul>
                </li>
            @elseif($role === 'staff')
                <li class="@if(request()->routeIs('staff.dashboard')) active @endif"><a href="{{ route('staff.dashboard') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-house-door"></i><span>Home</span></a></li>
                <li class="@if(request()->routeIs('staff.appointment.record')) active @endif"><a href="{{ route('staff.appointment.record') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-calendar2-check"></i><span>Appointment Record</span></a></li>
                <li class="@if(request()->routeIs('staff.services')) active @endif"><a href="{{ route('staff.services') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-gear"></i><span>Services</span></a></li>
                <li class="@if(request()->routeIs('staff.hearing.aid')) active @endif"><a href="{{ route('staff.hearing.aid') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-ear"></i><span>Hearing Aid</span></a></li>
                <li class="@if(request()->routeIs('staff.patient.record')) active @endif"><a href="{{ route('staff.patient.record') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-people"></i><span>Patient Record</span></a></li>
                <li class="@if(request()->routeIs('staff.billing')) active @endif"><a href="{{ route('staff.billing') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-receipt"></i><span>Billing</span></a></li>
                {{-- Register Patient Account (route may be enabled later) --}}
                @if(Route::has('staff.patient.register'))
                <li class="@if(request()->routeIs('staff.patient.register')) active @endif"><a href="{{ route('staff.patient.register') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-person-plus"></i><span>Register Patient Account</span></a></li>
                @endif
            @endif
            {{-- Logout moved into topbar account menu --}}
        </ul>
    </div>
    <div class="topbar">
    <div style="display:flex; align-items:center; justify-content:flex-end; width:100%;">
            @php($u = Auth::user())
            @php($displayName = trim((string)($u->name ?? '')))
            @php($displayRole = strtoupper((string)($u->role ?? 'User')))
            <div class="dropdown" id="accountDropdown" style="margin-left:12px; margin-top:-7px;">
                <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border:1px solid #d1d5db; padding:.25rem .5rem; gap:.5rem; white-space:nowrap;">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:26px;height:26px;background:#e5e7eb;">
                        <i class="bi bi-person" style="font-size:.95rem;color:#555;"></i>
                    </span>
                    <span class="d-flex flex-column align-items-center" style="gap:.2rem; font-weight:700;">
                        <span style="font-size:.72rem; color:#6b21a8;">{{ $displayRole }}</span>
                        <span style="font-size:.78rem; color:#111827; text-transform:uppercase;">{{ $displayName }}</span>
                    </span>
                    <i class="bi bi-caret-down-fill ms-1" style="font-size:.7rem; color:#6b7280;"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 260px;">
                    <li class="px-3 py-2">
                        <div class="d-flex align-items-center">
                            <span class="rounded-circle d-inline-flex align-items-center justify-content-center me-2" style="width:36px;height:36px;background:#e5e7eb;">
                                <i class="bi bi-person" style="font-size:1.1rem;color:#555;"></i>
                            </span>
                            <div>
                                <div style="font-weight:700;">{{ $displayName }}</div>
                                <div class="text-muted" style="font-size:.8rem;">{{ $u->email ?? '' }}</div>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center justify-content-center" href="{{ route('profile.manage') }}">
                            <i class="bi bi-person-gear me-2"></i> Manage Account Profile
                        </a>
                    </li>
                    <li>
                        <div class="px-3 py-1">
                            <button type="button" class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center" style="gap:.4rem;" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="bi bi-power"></i> Log Out
                            </button>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="main-content">
        @yield('content')
    </div>
    
    <!-- Global small success modal for admin pages -->
    @if(session('success'))
    <div class="modal fade" id="globalSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <svg viewBox="0 0 52 52" aria-hidden="true" style="width:56px;height:56px;display:block;margin:0 auto 6px auto;">
                        <circle cx="26" cy="26" r="24" stroke="#f39200" stroke-width="3" fill="none"></circle>
                        <path d="M14 27 l8 8 l16 -16" stroke="#f39200" stroke-width="3.5" fill="none" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    <div class="fw-semibold" style="font-size:.95rem;">{{ session('success') }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script>
        function toggleDropdown(id) {
            var el = document.getElementById(id);
            var parent = el.closest('li');
            if (parent.classList.contains('open')) {
                parent.classList.remove('open');
            } else {
                parent.classList.add('open');
            }
        }

        // Auto-open dropdowns based on current route
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're on a reports page
            var currentPath = window.location.pathname;
            if (currentPath.includes('/report/')) {
                var reportsMenu = document.getElementById('reportsMenu');
                if (reportsMenu) {
                    reportsMenu.classList.add('open');
                }
            }
            
            // Check if we're on an account management page
            if (currentPath.includes('/user-account/')) {
                var accountMenu = document.getElementById('accountMenu');
                if (accountMenu) {
                    accountMenu.classList.add('open');
                }
            }
        });

        function setActiveReport(el) {
            document.querySelectorAll('.report-submenu').forEach(function(item) {
                item.classList.remove('active');
            });
            el.classList.add('active');
        }

        // Sidebar nav highlight logic
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar ul > li');
            const reportsDropdownLinks = document.querySelectorAll('#reportsDropdown li');
            const accountDropdownLinks = document.querySelectorAll('#accountDropdown div');

            sidebarLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    // Only highlight top-level items, not dropdown children
                    if (link.querySelector('ul') || link.querySelector('div[id^="accountDropdown"]')) return; // skip parent with dropdown
                    sidebarLinks.forEach(function(l) { l.classList.remove('active'); });
                    link.classList.add('active');
                });
            });

            reportsDropdownLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.stopPropagation();
                    reportsDropdownLinks.forEach(function(l) { l.classList.remove('active'); });
                    link.classList.add('active');
                    // Keep the dropdown open by maintaining the 'open' class on parent
                    var parent = link.closest('li');
                    if (parent) {
                        parent.classList.add('open');
                        // Remove active from parent li but keep dropdown open
                        parent.classList.remove('active');
                    }
                    // Don't prevent default - let the link navigate normally
                    // The dropdown will stay open due to the 'open' class being maintained
                });
            });

            accountDropdownLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.stopPropagation();
                    accountDropdownLinks.forEach(function(l) { l.classList.remove('active'); });
                    link.classList.add('active');
                    // Keep the dropdown open by maintaining the 'open' class on parent
                    var parent = link.closest('li');
                    if (parent) {
                        parent.classList.add('open');
                        // Remove active from parent li but keep dropdown open
                        parent.classList.remove('active');
                    }
                });
            });
        });

        // Auto-open success modal if present (run after DOM is ready)
        document.addEventListener('DOMContentLoaded', function(){
            var hasSuccess = !!@json(session('success'));
            if(hasSuccess && window.bootstrap){
                var el = document.getElementById('globalSuccessModal');
                if(el){
                    var m = new bootstrap.Modal(el, { backdrop: 'static', keyboard: true });
                    m.show();
                    setTimeout(function(){ try { m.hide(); } catch(e){} }, 1800);
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @if(session('success'))
    <script>
    // Ensure success modal opens reliably (immediately if DOM ready, else on DOMContentLoaded)
    (function(){
        function show(){
            var el = document.getElementById('globalSuccessModal');
            if(!el) return;
            try{
                if(window.bootstrap){
                    var m = bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: true });
                    m.show();
                    return true;
                }
            }catch(e){}
            return false;
        }
        function kickoff(){
            if(!show()) setTimeout(show, 120);
            else {
                // Auto-dismiss after showing
                var el = document.getElementById('globalSuccessModal');
                if(el){
                    var inst = bootstrap.Modal.getOrCreateInstance(el);
                    setTimeout(function(){ try { inst.hide(); } catch(e){} }, 1800);
                }
            }
        }
        if(document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', kickoff);
        } else {
            kickoff();
        }
    })();
    </script>
    @endif
    @stack('scripts')
    
    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">
                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                        Confirm Logout
                    </h5>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to logout?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-power me-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
