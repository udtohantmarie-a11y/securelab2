<!-- 🟢 SECURELAB EXECUTIVE CYBER-DEFENSE SIDEBAR -->
<aside class="sidebar" id="sidebar">
    
    <!-- BRAND HEADER & TELEMETRY BADGE -->
    <div class="sidebar-brand-header px-4 py-3 border-bottom border-white border-opacity-10">
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-3 text-decoration-none">
            <div class="brand-icon-box shadow-sm">
                <i class="fas fa-fingerprint text-white"></i>
                <div class="brand-icon-pulse"></div>
            </div>
            <div class="brand-text">
                <span class="brand-title">SECURE<span class="text-primary-accent">LAB</span></span>
                <span class="brand-subtitle">IoT Access Control • TPC</span>
            </div>
        </a>
        <div class="mt-2 pt-1 d-flex align-items-center justify-content-between px-1">
            <span class="portal-badge">
                <span class="portal-dot"></span>
                {{ Auth::user()?->role ?? 'User' }} Portal
            </span>
            <span class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 fw-bold" style="font-size: 9.5px; letter-spacing: 0.3px;">
                <span class="pulse-dot-success me-1" style="width: 5px; height: 5px;"></span> ESP8266 Live
            </span>
        </div>
    </div>
    
    <!-- NAVIGATION LINKS -->
    <div class="sidebar-content">
        <nav class="nav flex-column">
            
            <div class="section-label">Mission Control</div>
            
            <!-- Overview/Dashboard -->
            <a href="{{ route('dashboard') }}" 
               class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large me-3 text-center text-info" style="width: 20px;"></i>
                <span>Overview</span>
            </a>
            
            <!-- My Assigned Rooms -->
            <a href="{{ route('rooms.my') }}" 
               class="nav-link {{ Route::is('rooms.my') ? 'active' : '' }}">
                <i class="fas fa-door-open me-3 text-center text-success" style="width: 20px;"></i>
                <span>My Assigned Rooms</span>
            </a>
            
            {{-- Admin & Dean monitoring tools --}}
            @if(in_array(Auth::user()?->role, ['Admin', 'Dean']))
                <div class="section-label mt-3">Security Intelligence</div>

                <a href="{{ route('audit.logs') }}" 
                   class="nav-link {{ Route::is('audit.logs') ? 'active' : '' }}">
                    <i class="fas fa-history me-3 text-center text-primary" style="width: 20px;"></i>
                    <span>Audit Logs</span>
                </a>
                <a href="{{ route('dashboard.alerts') }}" 
                   class="nav-link {{ Route::is('dashboard.alerts') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle me-3 text-center text-danger" style="width: 20px;"></i>
                    <span>Alerts Feed</span>
                </a>
            @endif

            {{-- MASTER ADMIN TOOLS --}}
            @if(Auth::user()?->role == 'Admin')
                <div class="section-label mt-3">Master Configuration</div>
                
                <a href="{{ route('rooms.manage') }}" 
                   class="nav-link {{ Route::is('rooms.manage') ? 'active' : '' }}">
                    <i class="fas fa-layer-group me-3 text-center text-info" style="width: 20px;"></i>
                    <span>Manage Rooms</span>
                </a>
                
                <a href="{{ route('users.assign') }}" 
                   class="nav-link {{ Route::is('users.assign') ? 'active' : '' }}">
                    <i class="fas fa-user-plus me-3 text-center text-success" style="width: 20px;"></i>
                    <span>Assign Users</span>
                </a>
                
                <a href="{{ route('users.database') }}" 
                   class="nav-link {{ Route::is('users.database') ? 'active' : '' }}">
                    <i class="fas fa-users-cog me-3 text-center text-primary" style="width: 20px;"></i>
                    <span>User Database</span>
                </a>

                {{-- PENDING ACCOUNTS APPROVAL --}}
                <a href="{{ route('users.pending') }}" 
                   class="nav-link {{ Route::is('users.pending') ? 'active' : '' }}">
                    <i class="fas fa-user-clock me-3 text-center text-warning" style="width: 20px;"></i>
                    <span>Pending Accounts</span>
                </a>
                
                <a href="{{ route('devices.manage') }}" 
                   class="nav-link {{ Route::is('devices.manage') ? 'active' : '' }}">
                    <i class="fas fa-microchip me-3 text-center text-danger" style="width: 20px;"></i>
                    <span>IoT Devices</span>
                </a>

                {{-- DEDICATED HARDWARE WIFI PAGE FOR ADMINS --}}
                <a href="{{ route('hardware.wifi') }}" 
                   class="nav-link {{ Route::is('hardware.wifi') ? 'active' : '' }}">
                    <i class="fas fa-wifi me-3 text-center text-info" style="width: 20px;"></i>
                    <span>Hardware WiFi</span>
                </a>
            @endif

            {{-- DEAN TOOLS --}}
            @if(Auth::user()?->role == 'Dean')
                <div class="section-label mt-3">Executive Intelligence</div>
                
                <a href="{{ route('dean.analytics') }}" 
                   class="nav-link {{ Route::is('dean.analytics') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-3 text-center text-info" style="width: 20px;"></i>
                    <span>Usage Analytics</span>
                </a>
                
                <a href="{{ route('dean.reports') }}" 
                   class="nav-link {{ Route::is('dean.reports') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice me-3 text-center text-success" style="width: 20px;"></i>
                    <span>Monthly Reports</span>
                </a>
            @endif

            {{-- SETTINGS FOR ALL ROLES --}}
            <div class="section-label mt-3">System & Account</div>
            
            <a href="{{ route('system.settings') }}" 
               class="nav-link {{ Route::is('system.settings') ? 'active' : '' }}">
                <i class="fas fa-cog me-3 text-center text-secondary" style="width: 20px;"></i>
                <span>{{ Auth::user()?->role == 'Admin' ? 'System Settings' : 'Account Settings' }}</span>
            </a>
            
        </nav>
    </div>

    <!-- LOGOUT & USER QUICK FOOTER -->
    <div class="sidebar-user-footer p-3 border-top border-white border-opacity-10">
        <div class="d-flex align-items-center justify-content-between p-2 rounded-3" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.06);">
            <a href="{{ route('profile.index') }}" class="d-flex align-items-center gap-2 text-decoration-none text-truncate" style="max-width: 175px;">
                <div class="position-relative">
                    @if(Auth::user()?->profile_photo)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="rounded-circle border border-white border-opacity-25" width="36" height="36" style="object-fit: cover;">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()?->full_name ?? 'User') }}&background=2563eb&color=fff" class="rounded-circle border border-white border-opacity-25" width="36" height="36">
                    @endif
                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-dark rounded-circle" style="width: 8px; height: 8px;"></span>
                </div>
                <div class="text-truncate">
                    <span class="d-block text-white fw-bold text-truncate" style="font-size: 12px; line-height: 1.2;">{{ Auth::user()?->full_name ?? 'User' }}</span>
                    <span class="d-block text-white-50" style="font-size: 10px;">{{ Auth::user()?->role }}</span>
                </div>
            </a>
            <button type="button" class="btn btn-sm border-0 rounded-circle d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#logoutModal" title="Logout" style="width: 32px; height: 32px; background: rgba(239, 68, 68, 0.15); color: #f87171; transition: all 0.2s ease;">
                <i class="fas fa-power-off" style="font-size: 12px;"></i>
            </button>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Backdrop Overlay -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<style>
    .sidebar-brand-header {
        background: rgba(255, 255, 255, 0.02);
    }
    .brand-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0d6efd 0%, #00d2ff 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 19px;
        box-shadow: 0 4px 14px rgba(13, 110, 253, 0.45);
        flex-shrink: 0;
        position: relative;
    }
    .brand-icon-pulse {
        position: absolute;
        top: -2px; left: -2px; right: -2px; bottom: -2px;
        border-radius: 14px;
        border: 1px solid rgba(0, 210, 255, 0.4);
        pointer-events: none;
        animation: cyberPulse 2.5s infinite ease-in-out;
    }
    @keyframes cyberPulse {
        0%, 100% { transform: scale(1); opacity: 0.3; }
        50% { transform: scale(1.08); opacity: 0.7; }
    }
    .brand-title {
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: 1.5px;
        color: #ffffff;
        display: block;
        line-height: 1.1;
    }
    .text-primary-accent {
        color: #38bdf8;
    }
    .brand-subtitle {
        font-size: 0.63rem;
        color: rgba(255, 255, 255, 0.55);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-weight: 600;
        display: block;
    }
    .portal-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(37, 99, 235, 0.22);
        color: #93c5fd;
        border: 1px solid rgba(96, 165, 250, 0.35);
        border-radius: 9999px;
        padding: 3px 10px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
    .portal-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #10b981;
        box-shadow: 0 0 8px #10b981;
    }
    .sidebar-user-footer {
        background: rgba(0, 0, 0, 0.2);
    }
    .sidebar-user-footer button:hover {
        background: rgba(239, 68, 68, 0.35) !important;
        color: #ffffff !important;
        transform: scale(1.08);
    }
</style>