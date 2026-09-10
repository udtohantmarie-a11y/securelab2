<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <title>SecureLab - Dashboard Overview</title>
    <style>
        /* 🟢 DASHBOARD MODERN STYLING & RESPONSIVE TOKENS */
        .dashboard-container {
            width: 100%;
            max-width: 100%;
        }

        .stat-card-modern {
            border-radius: var(--radius-lg, 18px);
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .stat-card-modern:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.08);
            border-color: rgba(26, 86, 219, 0.25);
        }

        .stat-icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
            box-shadow: 0 6px 14px -3px rgba(0, 0, 0, 0.12);
        }

        /* Gradient Accents */
        .bg-gradient-primary { 
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%); 
            color: #ffffff; 
            box-shadow: 0 6px 16px -2px rgba(37, 99, 235, 0.35);
        }
        .bg-gradient-success { 
            background: linear-gradient(135deg, #059669 0%, #10b981 100%); 
            color: #ffffff; 
            box-shadow: 0 6px 16px -2px rgba(16, 185, 129, 0.35);
        }
        .bg-gradient-danger { 
            background: linear-gradient(135deg, #dc2626 0%, #f43f5e 100%); 
            color: #ffffff; 
            box-shadow: 0 6px 16px -2px rgba(239, 68, 68, 0.35);
        }
        .bg-gradient-purple { 
            background: linear-gradient(135deg, #6d28d9 0%, #8b5cf6 100%); 
            color: #ffffff; 
            box-shadow: 0 6px 16px -2px rgba(139, 92, 246, 0.35);
        }

        /* Subtle Watermark Icon */
        .watermark-icon {
            position: absolute;
            right: -12px;
            bottom: -15px;
            font-size: 110px;
            opacity: 0.04;
            transform: rotate(-12deg);
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        /* Status & Live Indicators */
        #syncing-indicator {
            display: none;
            font-size: 0.78rem;
            color: #10b981;
            font-weight: 700;
            background: rgba(16, 185, 129, 0.12);
            padding: 6px 14px;
            border-radius: 9999px;
            border: 1px solid rgba(16, 185, 129, 0.28);
        }

        .pulse-dot-success {
            display: inline-block; 
            width: 8px; 
            height: 8px; 
            border-radius: 50%; 
            background: #10b981;
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.4); 
            animation: pulse-green 2s infinite;
        }
        .pulse-dot-danger {
            display: inline-block; 
            width: 8px; 
            height: 8px; 
            border-radius: 50%; 
            background: #ef4444;
            box-shadow: 0 0 0 rgba(239, 68, 68, 0.4); 
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-green { 0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }

        /* Sleek Timeline Feed */
        .timeline-alert-item {
            border-left: 3px solid #ef4444;
            padding-left: 16px;
            position: relative;
            margin-bottom: 18px;
            transition: all 0.2s ease;
        }
        .timeline-alert-item:last-child {
            margin-bottom: 0;
        }
        .timeline-alert-item:hover {
            padding-left: 20px;
            background: rgba(239, 68, 68, 0.04);
            border-radius: 0 10px 10px 0;
        }
        .timeline-alert-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 4px;
            width: 13px;
            height: 13px;
            border-radius: 50%;
            background: #ef4444;
            border: 3px solid var(--bg-surface);
            box-shadow: 0 0 8px rgba(239, 68, 68, 0.45);
        }

        /* Responsive Table & UI Adjustments */
        .dashboard-table th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: var(--text-muted);
            background: var(--bg-subtle);
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
        }
        .dashboard-table td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--border-color);
        }
        .dashboard-table tr:last-child td {
            border-bottom: none;
        }

        /* Media Queries for Perfect Responsiveness */
        @media (max-width: 767.98px) {
            .stat-card-modern {
                padding: 1.15rem !important;
            }
            .stat-icon-wrapper {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }
            .dashboard-header-title {
                font-size: 1.35rem !important;
            }
            .watermark-icon {
                font-size: 85px;
                right: -10px;
                bottom: -10px;
            }
        }

        body.dark-mode .timeline-alert-item::before {
            border-color: var(--bg-surface) !important;
        }
        body.dark-mode .watermark-icon {
            opacity: 0.02 !important;
            color: #ffffff !important;
        }
    </style>
</head>
<body>

    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="dashboard-container">
            <!-- 🟢 TOP HEADER SECTION -->
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-2 fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                            <i class="fas fa-shield-alt me-1"></i> CONTROL HUB
                        </span>
                        <span class="text-muted small d-none d-sm-inline">•</span>
                        <span class="text-muted small fw-medium" id="dashboardLiveTime">Checking sync...</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark dashboard-header-title" style="letter-spacing: -0.5px;">System Overview</h3>
                    <p class="text-muted small mb-0 mt-1">Real-time status of physical laboratory locks, IoT hardware vitals, and access events.</p>
                </div>
                
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span id="syncing-indicator" class="shadow-xs">
                        <i class="fas fa-sync-alt fa-spin me-1"></i> Live update detected
                    </span>
                    <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill fw-bold d-inline-flex align-items-center shadow-xs">
                        <span class="pulse-dot-success me-2"></span> System Online
                    </div>
                </div>
            </div>

            @php
                $todayAlerts = isset($alerts) ? $alerts->filter(function($alert) {
                    return \Carbon\Carbon::parse($alert->triggered_at)->isToday();
                }) : collect();

                $isAdminOrDean = in_array(Auth::user()->role, ['Admin', 'Dean']);
                $cardColClass = $isAdminOrDean ? 'col-12 col-sm-6 col-xl-3' : 'col-12 col-md-6';
            @endphp

            <!-- 🟢 LIVE IOT HARDWARE TELEMETRY HUD STRIP -->
            <div class="cyber-hud-strip p-3 p-md-4 mb-4">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <!-- Node & Telemetry Status -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white flex-shrink-0" style="width: 48px; height: 48px; background: rgba(0, 210, 255, 0.15); border: 1.5px solid rgba(0, 210, 255, 0.4); box-shadow: 0 0 15px rgba(0, 210, 255, 0.25);">
                            <i class="fas fa-satellite-dish fs-5 text-info"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="cyber-badge cyber-badge-cyan">
                                    <span class="pulse-dot-cyan"></span> ESP8266 Live Node
                                </span>
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-20 rounded-pill px-2.5 py-1 small fw-bold">
                                    Room 101 • Dedicated Hub
                                </span>
                            </div>
                            <h6 class="fw-bold mb-0 text-white" style="letter-spacing: -0.2px;">Cyber-Physical Hardware Node Online</h6>
                            <small class="text-white-50" style="font-size: 11.5px;">Fail-Secure 12V Solenoid • 500 DPI Optical Biometrics • Background Push Engine Ready</small>
                        </div>
                    </div>

                    <!-- Quick Hardware Telemetry Metrics -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="px-3 py-1.5 rounded-3 border border-white border-opacity-10" style="background: rgba(255,255,255,0.05); text-align: center;">
                            <span class="d-block text-white-50 small" style="font-size: 10px; letter-spacing: 0.5px;">SOLENOID RELAY</span>
                            <span class="fw-bold text-success" style="font-size: 12px;"><i class="fas fa-bolt me-1"></i>ENGAGED</span>
                        </div>
                        <div class="px-3 py-1.5 rounded-3 border border-white border-opacity-10" style="background: rgba(255,255,255,0.05); text-align: center;">
                            <span class="d-block text-white-50 small" style="font-size: 10px; letter-spacing: 0.5px;">SIGNAL LINK</span>
                            <span class="fw-bold text-info" style="font-size: 12px;"><i class="fas fa-wifi me-1"></i>{{ $health->wifi_signal_dbm ?? '-58' }} dBm</span>
                        </div>
                        <div class="px-3 py-1.5 rounded-3 border border-white border-opacity-10" style="background: rgba(255,255,255,0.05); text-align: center;">
                            <span class="d-block text-white-50 small" style="font-size: 10px; letter-spacing: 0.5px;">BACKUP POWER</span>
                            <span class="fw-bold text-warning" style="font-size: 12px;"><i class="fas fa-battery-three-quarters me-1"></i>{{ (int)($health->battery_pct ?? 100) }}%</span>
                        </div>
                        <div class="px-3 py-1.5 rounded-3 border border-white border-opacity-10" style="background: rgba(255,255,255,0.05); text-align: center;">
                            <span class="d-block text-white-50 small" style="font-size: 10px; letter-spacing: 0.5px;">RESPONSE PING</span>
                            <span class="fw-bold text-light" style="font-size: 12px;"><i class="fas fa-tachometer-alt me-1"></i>{{ $health->network_latency_ms ?? '18' }} ms</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🟢 METRICS & KPI CARDS GRID -->
            <div class="row g-3 g-md-4 mb-4">
                <!-- CARD 1: SECURITY / DOOR LOCK STATUS -->
                <div class="{{ $cardColClass }}">
                    <div class="stat-card-modern p-3 p-md-4 h-100" style="border-top: 3.5px solid #1d4ed8;">
                        <i class="fas fa-lock watermark-icon"></i>
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon-wrapper bg-gradient-primary">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <span class="badge {{ ($health->door_state ?? 'locked') == 'locked' ? 'bg-success text-white' : 'bg-warning text-dark' }} rounded-pill px-3 py-2 shadow-xs fw-bold border border-white" style="font-size: 11px;">
                                    <i class="fas {{ ($health->door_state ?? 'locked') == 'locked' ? 'fa-lock' : 'fa-unlock' }} me-1"></i>
                                    {{ strtoupper($health->door_state ?? 'Locked') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-primary small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px; font-size: 11px;">
                                    {{ $health->room_name ?? 'BSIS Laboratory' }}
                                </span>
                                <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.35rem;">Security Status</h4>
                                <p class="text-muted small mb-0" style="font-size: 12px;">Physical door lock state</p>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between" style="border-color: var(--border-color) !important; font-size: 11.5px;">
                            <span class="text-muted"><i class="fas fa-microchip me-1 text-primary"></i> Mechanism</span>
                            <span class="fw-bold text-dark">{{ ($health->door_state ?? 'locked') == 'locked' ? 'Armed & Secured' : 'Access Granted' }}</span>
                        </div>
                    </div>
                </div>

                @if($isAdminOrDean)
                <!-- CARD 2: HARDWARE VITALS (Admin & Dean Only) -->
                <div class="{{ $cardColClass }}">
                    <div class="stat-card-modern p-3 p-md-4 h-100" style="border-top: 3.5px solid #10b981;">
                        <i class="fas fa-microchip watermark-icon"></i>
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon-wrapper bg-gradient-success">
                                    <i class="fas fa-server"></i>
                                </div>
                                @php
                                    $batteryPct = (int)($health->battery_pct ?? 100);
                                    $batteryBadgeClass = $batteryPct > 50 ? 'bg-success text-success' : ($batteryPct > 20 ? 'bg-warning text-warning' : 'bg-danger text-danger');
                                @endphp
                                <span class="badge {{ $batteryBadgeClass }} bg-opacity-10 fw-bold px-3 py-2 border border-current border-opacity-25 rounded-pill shadow-xs" style="font-size: 11px;">
                                    <i class="fas fa-battery-three-quarters me-1"></i> {{ $batteryPct }}% Battery
                                </span>
                            </div>
                            <div>
                                <span class="text-success small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px; font-size: 11px;">
                                    NodeMCU Core Hub
                                </span>
                                <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.35rem;">Hardware Vitals</h4>
                                <p class="text-muted small mb-0" style="font-size: 12px;">Sensor telemetry & memory</p>
                            </div>
                        </div>

                        @php
                            $sdTotal = $health->sd_total_mb ?? 0;
                            $sdUsed = $health->sd_used_mb ?? 0;
                            $sdPct = ($sdTotal > 0) ? round(($sdUsed / $sdTotal) * 100) : 0;
                        @endphp
                        <div class="mt-3 pt-3 border-top" style="border-color: var(--border-color) !important;">
                            <div class="d-flex justify-content-between mb-1" style="font-size: 11.5px;">
                                <span class="text-muted"><i class="fas fa-sd-card me-1"></i> SD Storage</span>
                                <span class="{{ $sdPct > 90 ? 'text-danger' : 'text-success' }} fw-bold">{{ $sdPct }}% Used</span>
                            </div>
                            <div class="progress rounded-pill" style="height: 6px; background-color: rgba(148, 163, 184, 0.2);">
                                <div class="progress-bar rounded-pill {{ $sdPct > 90 ? 'bg-danger' : 'bg-success' }}" style="width: {{ max($sdPct, 4) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: SECURITY THREATS & ALERTS (Admin & Dean Only) -->
                <div class="{{ $cardColClass }}">
                    <div class="stat-card-modern p-3 p-md-4 h-100" style="border-top: 3.5px solid #ef4444;">
                        <i class="fas fa-exclamation-triangle watermark-icon text-danger"></i>
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon-wrapper bg-gradient-danger">
                                    <i class="fas fa-bell"></i>
                                </div>
                                @php
                                    $unresolvedToday = $todayAlerts->where('is_resolved', 0)->count();
                                @endphp
                                <span class="badge {{ $unresolvedToday > 0 ? 'bg-danger bg-opacity-10 text-danger border-danger' : 'bg-success bg-opacity-10 text-success border-success' }} rounded-pill px-3 py-2 fw-bold border border-opacity-25 shadow-xs" style="font-size: 11px;">
                                    @if($unresolvedToday > 0)
                                        <span class="pulse-dot-danger me-1"></span> {{ $unresolvedToday }} New Today
                                    @else
                                        <i class="fas fa-check-circle me-1"></i> Clear
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-danger small fw-bold text-uppercase d-block mb-1" style="letter-spacing: 0.5px; font-size: 11px;">
                                    Intrusion Telemetry
                                </span>
                                <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.35rem;">Security Alerts</h4>
                                <p class="text-muted small mb-0" style="font-size: 12px;">Tamper & motion detections</p>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between" style="border-color: var(--border-color) !important; font-size: 11.5px;">
                            <span class="text-muted"><i class="fas fa-shield-virus me-1 text-danger"></i> Active Status</span>
                            <span class="fw-bold {{ $unresolvedToday > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $unresolvedToday > 0 ? $unresolvedToday . ' Needs Review' : '0 Anomalies Detected' }}
                            </span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- CARD 4: NETWORK / CONNECTION STATUS -->
                <div class="{{ $cardColClass }}">
                    <div class="stat-card-modern p-3 p-md-4 h-100" style="border-top: 3.5px solid #8b5cf6;">
                        <i class="fas fa-wifi watermark-icon"></i>
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon-wrapper bg-gradient-purple">
                                    <i class="fas fa-network-wired"></i>
                                </div>
                                <span class="badge bg-secondary bg-opacity-10 {{ ($health->is_online ?? 0) ? 'text-success' : 'text-danger' }} rounded-pill px-3 py-2 fw-bold border shadow-xs" style="font-size: 11px;">
                                    <span class="{{ ($health->is_online ?? 0) ? 'pulse-dot-success' : 'pulse-dot-danger' }} me-2"></span>
                                    {{ ($health->is_online ?? 0) ? 'ONLINE' : 'OFFLINE' }}
                                </span>
                            </div>
                            <div>
                                <span class="small fw-bold text-uppercase d-block mb-1" style="color: #8b5cf6; letter-spacing: 0.5px; font-size: 11px;">
                                    Connection Health
                                </span>
                                <h4 class="fw-bold mb-1 text-dark" style="font-size: 1.35rem;">
                                    {{ $health->network_latency_ms ?? '0' }} <span class="fs-6 text-muted fw-normal">ms</span>
                                </h4>
                                <p class="text-muted small mb-0" style="font-size: 12px;">Network response ping</p>
                            </div>
                        </div>

                        <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between" style="border-color: var(--border-color) !important; font-size: 11.5px;">
                            <span class="text-muted"><i class="fas fa-signal me-1 text-primary"></i> Signal Strength</span>
                            <span class="fw-bold text-dark">{{ $health->wifi_signal_dbm ?? '-58' }} dBm</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🟢 MAIN TWO-COLUMN SECTION: ACCESS LOGS & CRITICAL FEED -->
            <div class="row g-4">
                <!-- ACCESS LOGS TABLE CONTAINER -->
                <div class="{{ $isAdminOrDean ? 'col-12 col-xl-8' : 'col-12' }}">
                    <div class="stat-card-modern p-3 p-md-4 h-100" id="access-logs-card">
                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3 pb-3 border-bottom" style="border-color: var(--border-color) !important;">
                            <div>
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="fw-bold mb-0 text-dark">
                                        <i class="fas fa-history text-primary me-2"></i>Laboratory Access History
                                    </h5>
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1 fw-bold" style="font-size: 10px;">
                                        {{ $logs->count() }} Recent
                                    </span>
                                </div>
                                <p class="text-muted small mb-0 mt-1">Latest personnel check-ins and authenticated door unlock events.</p>
                            </div>
                            <a href="{{ route('audit.logs') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-2 fw-bold shadow-xs align-self-start align-self-sm-center" style="font-size: 12px;" title="{{ $isAdminOrDean ? 'View and Export Full Audit Logs' : 'View Full Audit Logs (View-Only)' }}">
                                View Full Log <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>

                        <div class="table-responsive rounded-3 border" style="border-color: var(--border-color) !important;">
                            <table class="table dashboard-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>User Identity</th>
                                        <th>Triggered Action</th>
                                        <th><i class="far fa-clock me-1"></i> Timestamp</th>
                                        <th class="text-center">Door State</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if(!empty($log->profile_photo))
                                                    <img src="{{ asset('storage/' . $log->profile_photo) }}" alt="{{ $log->user_name }}" class="me-3 rounded-circle shadow-xs border" style="width: 38px; height: 38px; min-width: 38px; object-fit: cover; flex-shrink: 0;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($log->user_name) }}&background=1a56db&color=fff&bold=true';">
                                                @else
                                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 text-primary fw-bold" style="width: 38px; height: 38px; min-width: 38px; flex-shrink: 0; font-size: 14px;">
                                                        {{ strtoupper(substr($log->user_name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="fw-bold text-dark d-block" style="font-size: 0.88rem;">{{ $log->user_name }}</span>
                                                    <span class="text-muted" style="font-size: 11px;">
                                                        {{ $log->user_role ?? 'Authorized User' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $actionIcon = 'fa-check';
                                                $actionBadge = 'bg-primary bg-opacity-10 text-primary border-primary';
                                                if (Str::contains($log->action, 'unlock')) {
                                                    $actionIcon = 'fa-unlock-alt';
                                                    $actionBadge = 'bg-success bg-opacity-10 text-success border-success';
                                                } elseif (Str::contains($log->action, 'fingerprint')) {
                                                    $actionIcon = 'fa-fingerprint';
                                                    $actionBadge = 'bg-info bg-opacity-10 text-info border-info';
                                                } elseif (Str::contains($log->action, 'passcode')) {
                                                    $actionIcon = 'fa-key';
                                                    $actionBadge = 'bg-warning bg-opacity-10 text-warning border-warning';
                                                }
                                            @endphp
                                            <span class="badge {{ $actionBadge }} border border-opacity-25 rounded-pill px-3 py-1 fw-bold text-capitalize" style="font-size: 11px;">
                                                <i class="fas {{ $actionIcon }} me-1"></i>{{ str_replace('_', ' ', $log->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark d-block" style="font-size: 0.85rem;">{{ \Carbon\Carbon::parse($log->logged_at)->format('h:i A') }}</span>
                                            <span class="text-muted" style="font-size: 11px;">{{ \Carbon\Carbon::parse($log->logged_at)->format('M d, Y') }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $log->door_state_after == 'locked' ? 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' : 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25' }} rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">
                                                <i class="fas fa-circle me-1" style="font-size: 6px; vertical-align: middle;"></i>
                                                {{ ucfirst($log->door_state_after) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <div class="stat-icon-wrapper bg-secondary bg-opacity-10 text-muted mx-auto mb-3" style="width: 54px; height: 54px; font-size: 24px;">
                                                <i class="fas fa-clipboard-list"></i>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">No Access Events Logged</h6>
                                            <p class="text-muted small mb-0">Events will appear here automatically when doors are accessed.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- CRITICAL TELEMETRY FEED (Admin & Dean Only) --}}
                @if($isAdminOrDean)
                <div class="col-12 col-xl-4">
                    <div class="stat-card-modern p-3 p-md-4 h-100 border-top border-top-4 border-danger">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom" style="border-color: var(--border-color) !important;">
                            <div>
                                <h5 class="fw-bold mb-0 text-danger">
                                    <i class="fas fa-satellite-dish me-2"></i>Critical Feed
                                </h5>
                                <span class="text-muted small" style="font-size: 11.5px;">Live security telemetry & anomalies</span>
                            </div>
                            <a href="{{ route('dashboard.alerts') }}" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold p-0" style="font-size: 12px;">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>

                        <div class="pt-2 flex-grow-1">
                            @forelse($todayAlerts->take(4) as $alert)
                            <div class="timeline-alert-item">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <strong class="small text-uppercase text-danger fw-bold" style="letter-spacing: 0.5px; font-size: 11px;">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ str_replace('_', ' ', $alert->alert_type) }}
                                    </strong>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill" style="font-size: 10px;">
                                        {{ \Carbon\Carbon::parse($alert->triggered_at)->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="mb-0 text-dark fw-medium" style="font-size: 0.82rem; line-height: 1.45;">
                                    {{ $alert->description }}
                                </p>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success mx-auto mb-3" style="width: 60px; height: 60px; font-size: 26px;">
                                    <i class="fas fa-shield-check"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">All Clear</h6>
                                <p class="text-muted small mb-0">No active security threats or door anomalies detected today.</p>
                            </div>
                            @endforelse
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex align-items-center justify-content-between" style="border-color: var(--border-color) !important; font-size: 11.5px;">
                            <span class="text-muted"><i class="fas fa-bell me-1 text-danger"></i> Notification Relay</span>
                            <span class="badge bg-secondary bg-opacity-10 text-muted rounded-pill px-2 py-1">24/7 Monitoring</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @include('admin.components.footer')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // 🕒 Real-time Dynamic Header Clock
            function updateClock() {
                const now = new Date();
                const options = { weekday: 'short', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
                $('#dashboardLiveTime').text(now.toLocaleString('en-US', options));
            }
            updateClock();
            setInterval(updateClock, 1000);

            // 🔄 Real-time Background Polling for New Audit Logs
            @php
                $initialLatestId = \Illuminate\Support\Facades\DB::table('audit_logs')->max('log_id') ?? 0;
            @endphp
            let currentLatestLogId = {{ $initialLatestId }};

            setInterval(function() {
                fetch('/api/latest-log-id')
                    .then(response => response.json())
                    .then(data => {
                        // Kapag may bagong pumasok na log
                        if (data.latest_id > currentLatestLogId) {
                            $('#syncing-indicator').fadeIn();
                            $('#access-logs-card').css({'opacity': '0.6', 'transform': 'scale(0.99)'}); 

                            setTimeout(() => {
                                window.location.reload();
                            }, 800); 
                        }
                    })
                    .catch(error => console.error('Dashboard polling error:', error));
            }, 3000); 
        });
    </script>
</body>
</html>