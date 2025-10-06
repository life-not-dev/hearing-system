<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Staff') | Kamatage Hearing Aid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; overflow-x: hidden; font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }
    :root { --staff-sidebar-width: 290px; }
        .sidebar {
            width: var(--staff-sidebar-width);
            min-height: 100vh;
            background: #6c7a89;
            color: #fff;
            padding: 30px 0 0 0;
            position: fixed;
            left: 0; top: 0; bottom: 0;
            border-radius: 0;
            box-shadow: 0 4px 18px rgba(0,0,0,0.15);
            border-right: 1px solid #222;
            z-index: 101;
            overflow-y: auto;
            max-height: 100vh;
            scrollbar-width: thin; /* Firefox */
            scrollbar-color: #5a6470 #6c7a89; /* thumb track */
        }
        /* WebKit scrollbar */
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
            font-weight: inherit;
        }
        .sidebar ul li.open ul li {
            color: #fff !important;
        }
        /* Unified submenu styles */
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
        .sidebar ul li ul li a {
            color: inherit;
            text-decoration: none;
            display: block;
            padding: 0;
            border-radius: 6px;
            font-weight: 500;
        }
        /* Active/hover state unified color for all submenu items */
        .sidebar ul li ul li:hover,
        .sidebar ul li ul li.active,
        .sidebar ul li ul li a:hover,
        .sidebar ul li ul li a:focus,
        .sidebar ul li ul li a.active {
            background: #7a8699 !important;
            color: #fff !important;
        }
        
        /* Chevron rotation animation */
        .sidebar ul li.open .chevron-icon {
            transform: rotate(180deg);
        }

    .topbar { width: calc(100% - var(--staff-sidebar-width)); height: 70px; background: #fff; border: 1px solid #d1d5db; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; position: sticky; top: 0; z-index: 102; margin-left: var(--staff-sidebar-width); border-radius: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.06); }
        .topbar .notification { margin-right: 1px; }
        .topbar .notification svg { vertical-align: middle; }
        .topbar .notification .badge {
            display: none; /* Hide badge by default */
        }
        .topbar .staff-label { display: flex; align-items: center; font-weight: bold; color:  #222; font-size: 1.05em; }
        .topbar .staff-label .fa-user-circle { margin-right: 8px; color: #ffbe0b; font-size: 1.3em; }

        /* Small helpers used by pages */
    .container-main { margin-left: var(--staff-sidebar-width); padding: 88px 24px 24px 24px; }

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
            .container-main {
                margin-left: 0;
            }
            .topbar {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="sidebar">
        <img src="/images/logos.png" alt="Kamatage Logo" class="logo">
        <h5 style="font-weight:bold; margin-bottom:4px;">Kamatage Hearing Aid Center</h5>
        <br>
        <ul>
            <li class="@if(request()->routeIs('staff.dashboard')) active @endif"><a href="/staff/dashboard" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-house-door"></i><span>Home</span></a></li>
            <li id="appointmentMenu" style="position:relative;">
                <span id="appointmentToggle" style="display:flex; align-items:center; cursor:pointer; font-weight:600;">
                    <i class="bi bi-calendar2-event" style="margin-right:8px; width:20px; text-align:center;"></i>
                    <span style="flex:1;">Appointment</span>
                    <i class="bi bi-chevron-down chevron-icon" style="transition: transform 0.3s ease;"></i>
                </span>
                <ul id="appointmentDropdown" style="font-size:0.95em; margin-top:8px; background:#5a6470; border-radius:6px; position:relative;">
                    <li>
                        <a href="/staff/appointment/new-patient" style="color:inherit; text-decoration:none; display:block;">List of Appointments</a>
                    </li>
                    <li>
                        <a href="{{ route('staff.appointment.schedule') }}" style="color:inherit; text-decoration:none; display:block;">Appointments Schedule</a>
                    </li>
                    <li>
                        <a href="{{ route('staff.appointment.record') }}" style="color:inherit; text-decoration:none; display:block;">Appointment Record</a>
                    </li>
                </ul>
            </li>
            <li class="@if(request()->routeIs('staff.message')) active @endif"><a href="{{ route('staff.message') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-envelope"></i><span>Message</span></a></li>
            <li class="@if(request()->routeIs('staff.services')) active @endif"><a href="{{ route('staff.services') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-gear"></i><span>Services</span></a></li>
            <li class="@if(request()->routeIs('staff.hearing.aid')) active @endif"><a href="{{ route('staff.hearing.aid') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-ear"></i><span>Hearing Aid</span></a></li>
            <li class="@if(request()->routeIs('staff.patient.record')) active @endif"><a href="{{ route('staff.patient.record') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-people"></i><span>Patient Record</span></a></li>
            @if(Route::has('staff.patient.register'))
            <li class="@if(request()->routeIs('staff.patient.register')) active @endif"><a href="{{ route('staff.patient.register') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-person-plus"></i><span>Register Patient Account</span></a></li>
            @endif
            <li class="@if(request()->routeIs('staff.billing')) active @endif"><a href="{{ route('staff.billing') }}" style="color:inherit; text-decoration:none; display:flex; align-items:center; gap:10px;"><i class="bi bi-receipt"></i><span>Billing</span></a></li>
        </ul>
    </div>
    <div class="topbar">
        @php($u = Auth::user())
        @php($branchName = $u?->branchRef?->branch_name ?? $u?->branch ?? '—')
        <div class="topbar-left" style="display:flex; align-items:center; gap:10px;">
            <span class="badge text-bg-light" style="border:1px solid #e2e8f0; color:#0f172a; font-weight:600; font-size:0.9rem;">Branch: {{ $branchName }}</span>
        </div>

        <div class="topbar-right" style="display:flex; align-items:center; gap:16px; position:relative;">
            <span class="notification position-relative" id="staffNotifBell" style="cursor:pointer;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#222" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            @if(isset($appointmentsCount) && $appointmentsCount > 0)

                      

            <span class="position-absolute translate-middle badge rounded-pill" style="top:4px; left:22px; font-size:.65rem; min-width:20px; background-color:#28a745; color:#fff;">{{ $appointmentsCount }}</span>
            @endif
        </span>
            <div id="staffNotifPanel" style="position:absolute; top:64px; right:0; width:320px; max-height:420px; background:#ffffff; border:1px solid #d1d5db; border-radius:10px; box-shadow:0 6px 22px -4px rgba(0,0,0,.18); padding:8px 0 6px 0; display:none; z-index:1080; overflow:hidden;">
                <div style="padding:4px 14px 8px 16px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #e2e8f0;">
                    <h6 style="margin:0; font-weight:700; font-size:.8rem; letter-spacing:.5px;">APPOINTMENTS</h6>
                    <button id="staffNotifMark" style="border:none; background:none; font-size:.65rem; font-weight:600; color:#2563eb; padding:4px 6px; border-radius:4px;">Mark seen</button>
                </div>
                <ul id="staffNotifList" style="list-style:none; margin:0; padding:0; max-height:340px; overflow-y:auto;">
                    <li style="padding:28px 12px; text-align:center; font-size:.75rem; color:#64748b;" id="staffNotifEmpty">Loading...</li>
                </ul>
                <div style="padding:6px 0 0 0; text-align:center;">
                    <a href="{{ route('staff.appointment.new.patient') }}" style="font-size:.65rem; text-decoration:none; font-weight:600; letter-spacing:.4px; color:#2563eb; padding:6px 10px; display:inline-block;">View All</a>
                </div>
            </div>
            @php($u = Auth::user())
            @php($displayName = trim((string)($u->name ?? '')))
            @php($displayRole = strtoupper((string)($u->role ?? 'STAFF')))
            <div class="dropdown" id="staffAccountDropdown">
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
                        <a class="dropdown-item d-flex align-items-center" href="{{ route('profile.manage') }}">
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

    <div class="container-main">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
        // Staff notification logic (panel + list)
        (function(){
            const bell = document.getElementById('staffNotifBell');
            const panel = document.getElementById('staffNotifPanel');
            const listEl = document.getElementById('staffNotifList');
            const emptyEl = document.getElementById('staffNotifEmpty');
            const markBtn = document.getElementById('staffNotifMark');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            let badge = bell ? bell.querySelector('.badge') : null;

            function syncBadge(unseen){
                if(!bell) return;
                if(!badge){ badge = bell.querySelector('.badge'); }
                if(!badge && unseen>0){
                    badge = document.createElement('span');
                    badge.className = 'position-absolute translate-middle badge rounded-pill';
                    badge.style.top='4px'; badge.style.left='22px'; badge.style.fontSize='.65rem'; badge.style.minWidth='20px'; badge.style.backgroundColor='#28a745'; badge.style.color='#fff';
                    bell.appendChild(badge);
                }
                if(badge){
                    if(unseen>0){ 
                        badge.textContent = unseen; 
                        badge.style.display='inline-block'; 
                    } else { 
                        badge.style.display='none'; 
                        badge.remove(); // Remove the badge element completely when no notifications
                    }
                }
            }
            async function fetchCounts(){
                try { const r = await fetch('{{ route('notifications.appointments.count') }}'); if(!r.ok) return; const js = await r.json(); syncBadge(js.unseen||0); } catch(e){ }
            }

            // Real-time notifications using Server-Sent Events
            function initRealTimeNotifications() {
                const eventSource = new EventSource('{{ route('notifications.stream') }}');
                
                eventSource.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        
                        if (data.type === 'notification_update') {
                            syncBadge(data.count);
                        } else if (data.type === 'connected') {
                            console.log('SSE connected');
                        } else if (data.type === 'heartbeat') {
                            // Keep connection alive
                        }
                    } catch (e) {
                        console.error('SSE parse error:', e);
                    }
                };
                
                eventSource.onerror = function(event) {
                    console.error('SSE error:', event);
                    // Fallback to polling if SSE fails
                    setTimeout(() => {
                        eventSource.close();
                        setInterval(fetchCounts, 5000);
                    }, 5000);
                };
                
                return eventSource;
            }
            async function fetchList(){
                if(!listEl) return;
                try {
                    const r = await fetch('{{ route('notifications.appointments.list') }}');
                    if(!r.ok) throw new Error('bad response');
                    const js = await r.json();
                    const data = js.data || [];
                    listEl.innerHTML = '';
                    if(!data.length){
                        listEl.innerHTML = '<li style="padding:26px 10px; text-align:center; font-size:.7rem; color:#64748b;">No recent appointments</li>';
                        return;
                    }
                    data.forEach(item => {
                        const li = document.createElement('li');
                        li.style.cssText = 'padding:10px 14px 10px 14px; border-bottom:1px solid #f1f5f9; display:flex; gap:10px; background:'+ (item.seen? '#fff':'#fff9ed');
                        const initials = (item.name||'?').split(/\s+/).map(p=>p[0]).join('').substring(0,2).toUpperCase();
                        li.innerHTML = `<div style=\"width:32px;height:32px;border-radius:50%;background:#f97316;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.75rem;\">${initials}</div>
                        <div style=\"flex:1;min-width:0;\"><div style=\"display:flex;justify-content:space-between;font-size:.75rem;font-weight:600;color:#1e293b;\"><span>${item.name}</span><span style=\"color:#64748b;font-weight:500;white-space:nowrap;\">${item.time||''}</span></div>
                        <div style=\"display:flex;justify-content:space-between;font-size:.65rem;color:#475569;margin-top:2px;\"><span>${item.services||''}</span><span>${item.date||''}</span></div></div>`;
                        listEl.appendChild(li);
                    });
                } catch(e){
                    listEl.innerHTML = '<li style="padding:26px 10px; text-align:center; font-size:.7rem; color:#64748b;">Failed to load.</li>';
                }
            }
            bell && bell.addEventListener('click', function(e){
                e.stopPropagation();
                panel.style.display = panel.style.display==='block' ? 'none' : 'block';
                if(panel.style.display==='block'){ fetchList(); fetchCounts(); }
            });
            document.addEventListener('click', function(ev){ if(panel && !panel.contains(ev.target) && ev.target!==bell){ panel.style.display='none'; }});
            markBtn && markBtn.addEventListener('click', async function(){ try { await fetch('{{ route('notifications.appointments.markSeen') }}', { method:'POST', headers:{'X-CSRF-TOKEN': csrf} }); fetchCounts(); fetchList(); } catch(e){} });
            
            // Initialize real-time notifications
            fetchCounts(); // Initial load
            const eventSource = initRealTimeNotifications();
        })();
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
            // Check if we're on an appointment page
            var currentPath = window.location.pathname;
            if (currentPath.includes('/appointment/')) {
                var appointmentMenu = document.getElementById('appointmentMenu');
                if (appointmentMenu) {
                    appointmentMenu.classList.add('open');
                }
            }
        });

        // Sidebar nav highlight logic
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar ul > li');
            const appointmentDropdownLinks = document.querySelectorAll('#appointmentDropdown li');

            sidebarLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    // Only highlight top-level items, not dropdown children
                    if (link.querySelector('ul') || link.querySelector('div[id^="appointmentDropdown"]')) return; // skip parent with dropdown
                    sidebarLinks.forEach(function(l) { l.classList.remove('active'); });
                    link.classList.add('active');
                });
            });

            appointmentDropdownLinks.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.stopPropagation();
                    appointmentDropdownLinks.forEach(function(l) { l.classList.remove('active'); });
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

            // Handle appointment dropdown toggle properly
            const appointmentToggle = document.getElementById('appointmentToggle');
            if (appointmentToggle) {
                appointmentToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleDropdown('appointmentDropdown');
                });
            }
        });

        // Auto-open success modal if session has success (ensure Bootstrap availability)
        @if(session('success'))
        document.addEventListener('DOMContentLoaded', function(){
            var el = document.getElementById('globalSuccessModal');
            if(!el) return;
            function showModal(){
                try{
                    if(window.bootstrap){
                        var m = bootstrap.Modal.getOrCreateInstance(el, { backdrop: 'static', keyboard: true });
                        m.show();
                        setTimeout(function(){ try { m.hide(); } catch(e){} }, 1800);
                        return true;
                    }
                }catch(e){}
                return false;
            }
            if(!showModal()){
                setTimeout(showModal, 120);
            }
        });
        @endif
    </script>
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