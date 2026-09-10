<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <style>
        /* 🟢 MODERN EXECUTIVE IOT ENHANCEMENTS */
        body { 
            background-color: var(--bg-body, #f4f7f6); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--text-main, #1e293b);
        }
        
        .dashboard-card { 
            border-radius: 20px; 
            border: 1px solid var(--border-color, rgba(0,0,0,0.06)) !important; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.03); 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
            position: relative; 
            overflow: hidden; 
            background: var(--bg-surface, #fff); 
        }
        .dashboard-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 16px 35px rgba(0,0,0,0.08); 
        }
        
        /* Gradient Icon Boxes */
        .stat-icon-wrapper { 
            width: 55px; 
            height: 55px; 
            border-radius: 16px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 24px; 
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }
        .bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%); color: white; }
        .bg-gradient-success { background: linear-gradient(135deg, #20c997 0%, #198754 100%); color: white; }
        .bg-gradient-danger { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; }
        .bg-gradient-secondary { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); color: white; }

        /* Faint Background Icons (Watermarks) */
        .watermark-icon { 
            position: absolute; 
            right: -15px; 
            bottom: -20px; 
            font-size: 130px; 
            opacity: 0.03; 
            transform: rotate(-15deg); 
            pointer-events: none; 
            color: var(--text-main, #000);
        }

        /* Animations */
        .pulse-dot-success { 
            display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #20c997; 
            box-shadow: 0 0 0 rgba(32, 201, 151, 0.4); animation: pulse-green 2s infinite; 
        }
        .pulse-dot-danger { 
            display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #dc3545; 
            box-shadow: 0 0 0 rgba(220, 53, 69, 0.4); animation: pulse-red 2s infinite; 
        }
        @keyframes pulse-green { 0% { box-shadow: 0 0 0 0 rgba(32, 201, 151, 0.6); } 70% { box-shadow: 0 0 0 10px rgba(32, 201, 151, 0); } 100% { box-shadow: 0 0 0 0 rgba(32, 201, 151, 0); } }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.6); } 70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); } 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); } }

        /* Progress Bars & Containers */
        .health-bar { height: 8px; border-radius: 10px; background-color: var(--border-color, #e2e8f0); }
        .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.03); }

        .surface-box {
            background-color: var(--bg-subtle, #f8fafc) !important;
            border: 1px solid var(--border-color, rgba(0,0,0,0.06)) !important;
        }

        .icon-circle-badge {
            background-color: var(--bg-surface, #fff);
            border: 1px solid var(--border-color, rgba(0,0,0,0.06));
        }

        .control-btn {
            transition: all 0.25s ease;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .control-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(13, 110, 253, 0.18);
        }

        /* 🟢 SCHOOL REPORT HEADER STYLE FOR PRINT */
        @media print {
            .sidebar, .navbar, .btn-refresh, .mobile-header { display: none !important; }
            body { background-color: white !important; margin: 0; padding: 0; color: black !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
            .print-header { display: block !important; text-align: center; font-family: Arial, sans-serif; margin-bottom: 30px; }
            .dashboard-card { border: 1px solid #ddd !important; box-shadow: none !important; page-break-inside: avoid; background: white !important; }
            .surface-box { background: white !important; border: 1px solid #eee !important; }
        }
        .print-header { display: none; }
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            {{-- School Report Header (Lalabas lang pag nai-Print) --}}
            <div class="print-header mb-4">
                <h3 class="fw-bold mb-0">TALIBON POLYTECHNIC COLLEGE</h3>
                <h6 class="text-muted">San Isidro, Talibon, Bohol</h6>
                <hr style="border-top: 2px solid black;">
                <h4 class="text-uppercase mt-3 fw-bold">IoT Infrastructure Health Report</h4>
                <p style="font-size: 12px;">Generated on: {{ date('F d, Y h:i A') }}</p>
            </div>

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-2 fw-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">
                            <i class="fas fa-microchip me-1"></i> IOT HARDWARE TELEMETRY
                        </span>
                        <span class="text-muted small">•</span>
                        <span class="text-muted small fw-medium">NodeMCU ESP8266 Hubs</span>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">IoT Device Management</h4>
                    <p class="text-muted small mb-0 mt-1">Monitor hardware vitals, signal strength, storage, and power status.</p>
                </div>
                <button onclick="window.location.reload()" class="btn bg-gradient-primary text-white rounded-pill px-4 py-2 fw-bold shadow-sm control-btn btn-refresh align-self-start align-self-sm-center">
                    <i class="fas fa-sync-alt me-2"></i> Refresh Vitals
                </button>
            </div>

            <!-- 🟢 HARDWARE VITALS SUMMARY STRIP -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="dashboard-card p-3 h-100" style="border-top: 3.5px solid #00d2ff !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-muted text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">Registered Nodes</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-info" style="width: 34px; height: 34px; background: rgba(0, 210, 255, 0.12); font-size: 13px;">
                                <i class="fas fa-satellite-dish"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ count($devices) }}</h3>
                        <small class="text-muted" style="font-size: 11px;">Hardware MCU units</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dashboard-card p-3 h-100" style="border-top: 3.5px solid #10b981 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-success text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">Active Nodes</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 34px; height: 34px; background: rgba(16, 185, 129, 0.12); font-size: 13px;">
                                <i class="fas fa-server"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-success" style="letter-spacing: -0.5px;">{{ collect($devices)->where('is_online', 1)->count() }}</h3>
                        <small class="text-muted" style="font-size: 11px;">Online & transmitting</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dashboard-card p-3 h-100" style="border-top: 3.5px solid #f59e0b !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-warning text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">Average Battery</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-warning" style="width: 34px; height: 34px; background: rgba(245, 158, 11, 0.12); font-size: 13px;">
                                <i class="fas fa-battery-three-quarters"></i>
                            </div>
                        </div>
                        @php
                            $avgBattery = count($devices) > 0 ? round(collect($devices)->avg('battery_pct')) : 100;
                        @endphp
                        <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ $avgBattery }}%</h3>
                        <small class="text-muted" style="font-size: 11px;">12V auxiliary power</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="dashboard-card p-3 h-100" style="border-top: 3.5px solid #8b5cf6 !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-muted text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">Optimal Link</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; background: rgba(139, 92, 246, 0.12); color: #8b5cf6; font-size: 13px;">
                                <i class="fas fa-wifi"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ collect($devices)->where('wifi_signal_dbm', '>', -75)->count() }}</h3>
                        <small class="text-muted" style="font-size: 11px;">Signal > -75 dBm</small>
                    </div>
                </div>
            </div>

            <div class="row g-3 g-md-4" id="devices-container">
                @forelse($devices as $device)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="dashboard-card h-100 p-3 p-md-4" style="border-top: 3.5px solid {{ $device->is_online ? '#10b981' : '#ef4444' }} !important;">
                            <i class="fas fa-microchip watermark-icon"></i>
                            
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="stat-icon-wrapper {{ $device->is_online ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                    <i class="fas fa-server"></i>
                                </div>
                                
                                @if($device->is_online)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2 shadow-sm fw-bold">
                                        <span class="pulse-dot-success me-1"></span> ONLINE
                                    </span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-2 shadow-sm fw-bold">
                                        <span class="pulse-dot-danger me-1"></span> OFFLINE
                                    </span>
                                @endif
                            </div>

                            <div class="mb-4">
                                <h5 class="fw-bolder mb-1 text-dark">{{ $device->device_code }}</h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 py-1 fw-semibold">
                                    <i class="fas fa-door-open me-1"></i> {{ $device->room_name }}
                                </span>
                            </div>

                            {{-- BATTERY SECTION --}}
                            <div class="surface-box p-3 rounded-4 mb-3 shadow-inner">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="fw-bold text-dark"><i class="fas fa-battery-three-quarters text-primary me-1"></i> Battery Health</small>
                                    <small class="fw-bolder {{ $device->battery_pct <= 20 ? 'text-danger' : 'text-success' }}">
                                        {{ $device->battery_pct }}%
                                    </small>
                                </div>
                                <div class="progress health-bar mb-2 shadow-inner">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $device->battery_pct <= 20 ? 'bg-danger' : 'bg-success' }}" 
                                         role="progressbar" style="width: {{ $device->battery_pct }}%"></div>
                                </div>
                                <small class="text-muted fw-semibold" style="font-size: 0.7rem;">Voltage: {{ $device->battery_voltage }}V ({{ $device->power_source }})</small>
                            </div>

                            {{-- NETWORK SECTION --}}
                            <div class="surface-box p-3 rounded-4 mb-3 shadow-inner">
                                <div class="row g-0 align-items-center">
                                    <div class="col-auto me-3">
                                        <div class="icon-circle-badge p-2 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-wifi {{ $device->wifi_signal_dbm < -70 ? 'text-warning' : 'text-primary' }} fs-5"></i>
                                        </div>
                                    </div>
                                    <div class="col border-end" style="border-color: var(--border-color, rgba(0,0,0,0.08)) !important;">
                                        <small class="d-block fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">WIFI SIGNAL</small>
                                        <span class="fw-bolder text-dark">{{ $device->wifi_signal_dbm }} <small class="text-muted fw-normal">dBm</small></span>
                                    </div>
                                    <div class="col text-end">
                                        <small class="d-block fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">LATENCY</small>
                                        <span class="fw-bolder text-dark">{{ $device->network_latency_ms ?? 0 }} <small class="text-muted fw-normal">ms</small></span>
                                    </div>
                                </div>
                            </div>

                            {{-- STORAGE SECTION --}}
                            <div class="surface-box p-3 rounded-4 mb-4 shadow-inner">
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="fw-bold text-dark"><i class="fas fa-sd-card text-primary me-1"></i> Local Storage</small>
                                    @php
                                        $sdTotal = $device->sd_total_mb ?? 0;
                                        $sdUsed = $device->sd_used_mb ?? 0;
                                        $sdPct = ($sdTotal > 0) ? round(($sdUsed / $sdTotal) * 100) : 0;
                                    @endphp
                                    <small class="fw-bolder {{ $sdPct > 90 ? 'text-danger' : 'text-primary' }}">
                                        {{ $sdPct }}% Used
                                    </small>
                                </div>
                                <div class="progress health-bar mb-2 shadow-inner">
                                    <div class="progress-bar {{ $sdPct > 90 ? 'bg-danger' : 'bg-primary' }}" 
                                         role="progressbar" style="width: {{ $sdPct }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted fw-semibold" style="font-size: 0.7rem;">{{ number_format($sdUsed) }} MB used</small>
                                    <small class="text-muted fw-semibold" style="font-size: 0.7rem;">{{ number_format($sdTotal) }} MB total</small>
                                </div>
                            </div>

                            <div class="mt-auto border-top pt-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted x-small fw-bold">Firmware Ver:</span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold rounded-pill" style="font-size: 10px;">v{{ $device->firmware_version }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted x-small fw-bold">System Uptime:</span>
                                    <span class="fw-bolder small text-dark">{{ number_format($device->uptime_seconds / 3600, 1) }} hrs</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-muted x-small fw-bold">Last Sync:</span>
                                    <span class="text-muted x-small fst-italic">{{ \Carbon\Carbon::parse($device->recorded_at)->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 mt-4">
                        <div class="stat-icon-wrapper bg-gradient-secondary mx-auto mb-4" style="width: 90px; height: 90px; font-size: 40px; opacity: 0.5;">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">No Devices Registered</h4>
                        <p class="text-muted mx-auto mb-4 fw-semibold" style="max-width: 400px;">
                            There are currently no IoT devices registered in the system database.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @include('admin.components.footer')

    <!-- AUTO REFRESH SCRIPT -->
    <script>
        // Mag-a-auto refresh kada 5 segundo
        setInterval(function() {
            let refreshBtnIcon = document.querySelector('.btn-refresh i');
            
            // Paikutin ang icon habang kumukuha ng latest data
            if (refreshBtnIcon) {
                refreshBtnIcon.classList.add('fa-spin');
            }

            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    
                    let newContent = doc.getElementById('devices-container').innerHTML;
                    document.getElementById('devices-container').innerHTML = newContent;
                    
                    if (refreshBtnIcon) {
                        refreshBtnIcon.classList.remove('fa-spin');
                    }
                })
                .catch(error => {
                    console.error('Error auto-refreshing device vitals:', error);
                    if (refreshBtnIcon) refreshBtnIcon.classList.remove('fa-spin');
                });
        }, 5000); 
    </script>
</body>
</html>