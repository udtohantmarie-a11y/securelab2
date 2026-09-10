<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <style>
        /* 🟢 MODERN EXECUTIVE SETTINGS ENHANCEMENTS */
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
        
        /* Gradient Accents */
        .bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%); color: white; }
        .bg-gradient-danger { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; }
        .bg-gradient-success { background: linear-gradient(135deg, #20c997 0%, #198754 100%); color: white; }

        /* Navigation Pills */
        .nav-pills .nav-link { 
            border-radius: 14px; 
            color: var(--text-muted, #64748b); 
            font-weight: 600; 
            padding: 14px 20px; 
            transition: all 0.25s ease; 
            margin-bottom: 8px; 
            background: var(--bg-subtle, #f8fafc);
            border: 1px solid var(--border-color, rgba(0,0,0,0.04));
        }
        .nav-pills .nav-link:hover:not(.active) { 
            background-color: var(--bg-surface, #fff); 
            color: #0d6efd; 
            transform: translateX(4px);
            border-color: rgba(13, 110, 253, 0.2);
        }
        .nav-pills .nav-link.active { 
            background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%) !important; 
            color: white !important; 
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.25); 
            border-color: transparent;
        }

        /* Forms & Inputs */
        .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.03); }
        .form-label { font-weight: 700; color: #0d6efd; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .form-control, .form-select {
            background-color: var(--bg-subtle, #f8fafc) !important;
            color: var(--text-main, #1e293b) !important;
            border: 1px solid var(--border-color, #e2e8f0) !important;
            border-radius: 12px;
        }
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.12) !important;
            border-color: #0d6efd !important;
            background-color: var(--bg-surface, #fff) !important;
        }

        /* Faint Background Icons (Watermarks) */
        .watermark-icon { 
            position: absolute; 
            right: -15px; 
            bottom: -20px; 
            font-size: 140px; 
            opacity: 0.02; 
            transform: rotate(-15deg); 
            pointer-events: none; 
            color: var(--text-main, #000);
        }

        /* Surface Containers */
        .surface-box {
            background-color: var(--bg-subtle, #f8fafc) !important;
            border: 1px solid var(--border-color, rgba(0,0,0,0.06)) !important;
        }

        /* Sleek Table for Fingerprints */
        .table-custom th { 
            font-size: 0.75rem; 
            text-transform: uppercase; 
            letter-spacing: 0.8px; 
            border-bottom: 1px solid var(--border-color, #e2e8f0); 
            padding: 14px 12px; 
            color: var(--text-muted, #64748b);
            background: var(--bg-subtle, #f8fafc) !important;
            font-weight: 700;
        }
        .fp-row { transition: 0.2s ease; }
        .fp-row:hover { background-color: rgba(13, 110, 253, 0.03); }
        .table-custom td { border-bottom: 1px solid var(--border-color, #f1f5f9); padding: 14px 12px; color: var(--text-main, #1e293b); }

        /* Switches */
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .stat-icon-wrapper {
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            <div class="mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-2 fw-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">
                            <i class="fas fa-sliders me-1"></i> MASTER SYSTEM CONFIGURATION
                        </span>
                        <span class="text-muted small">•</span>
                        <span class="text-muted small fw-medium">Parameters & Integrations</span>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ Auth::user()->role == 'Admin' ? 'System Settings' : 'Account Settings' }}</h4>
                    <p class="text-muted small mb-0 mt-1">Configure global parameters and security preferences for SecureLab.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 fw-semibold" style="border-radius: 15px; background-color: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25);">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 fw-semibold" style="border-radius: 15px; background-color: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25);">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <!-- 🟢 SIDEBAR TABS -->
                <div class="col-12 col-lg-3 mb-4">
                    <div class="dashboard-card p-3 h-100" style="border-top: 3.5px solid #2563eb !important;">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active text-start" id="security-tab" data-bs-toggle="pill" data-bs-target="#security" type="button">
                                <i class="fas fa-user-shield me-2 w-20px text-center"></i> Account & Security
                            </button>

                            @if(Auth::user()->role == 'Admin')
                            <button class="nav-link text-start" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button">
                                <i class="fas fa-sliders-h me-2 w-20px text-center"></i> General Config
                            </button>
                            <button class="nav-link text-start" id="iot-tab" data-bs-toggle="pill" data-bs-target="#iot" type="button">
                                <i class="fas fa-microchip me-2 w-20px text-center"></i> IoT Integration
                            </button>
                            <button class="nav-link text-start" id="data-tab" data-bs-toggle="pill" data-bs-target="#data-management" type="button">
                                <i class="fas fa-database me-2 w-20px text-center"></i> Data Management
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 🟢 TAB CONTENT -->
                <div class="col-12 col-lg-9">
                    <div class="tab-content" id="v-pills-tabContent">

                        <!-- ACCOUNT & SECURITY TAB -->
                        <div class="tab-pane fade show active" id="security" role="tabpanel">
                            <div class="dashboard-card p-4" style="border-top: 3.5px solid #10b981 !important;">
                                <i class="fas fa-shield-alt watermark-icon"></i>
                                
                                @if(Auth::user()->role == 'Admin')
                                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-lock text-primary me-2"></i> Security & Access Control</h5>
                                <form action="{{ route('system.settings.update') }}" method="POST">
                                    @csrf
                                    <div class="surface-box p-4 rounded-4 mb-4 shadow-inner">
                                        <div class="form-check form-switch mb-4 d-flex align-items-center">
                                            <input class="form-check-input fs-4 me-3 mt-0" type="checkbox" name="maintenance_mode" id="maintenanceMode" {{ isset($settings['maintenance_mode']) && $settings['maintenance_mode'] ? 'checked' : '' }}>
                                            <div>
                                                <label class="form-check-label fw-bold text-dark mb-0" for="maintenanceMode">System Maintenance Mode</label>
                                                <p class="text-muted small mb-0">Disable all remote controls and enrollments during maintenance.</p>
                                            </div>
                                        </div>
                                        <div class="form-check form-switch d-flex align-items-center">
                                            <input class="form-check-input fs-4 me-3 mt-0" type="checkbox" name="audit_alerts" id="auditAlerts" {{ isset($settings['audit_alerts']) && $settings['audit_alerts'] ? 'checked' : '' }}>
                                            <div>
                                                <label class="form-check-label fw-bold text-dark mb-0" for="auditAlerts">Instant Email Alerts</label>
                                                <p class="text-muted small mb-0">Send immediate notification to <strong class="text-primary">{{ Auth::user()->full_name }}</strong> on critical intrusions.</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Failed Scan Threshold</label>
                                            <input type="number" name="failed_threshold" class="form-control form-control-lg shadow-inner fw-bold" value="{{ $settings['failed_threshold'] ?? 3 }}">
                                            <small class="text-muted mt-1 d-block" style="font-size: 11px;">Attempts before triggering siren.</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Session Timeout (minutes)</label>
                                            <input type="number" name="session_timeout" class="form-control form-control-lg shadow-inner fw-bold" value="{{ $settings['session_timeout'] ?? 120 }}">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end mb-4 mt-2">
                                        <button type="submit" class="btn bg-gradient-primary text-white rounded-pill px-4 fw-bold shadow-sm">Save Security Rules</button>
                                    </div>
                                </form>
                                <hr class="my-4 opacity-25" style="border-color: var(--border-color, #e2e8f0);">
                                @endif

                                <!-- PERSONAL APPEARANCE -->
                                <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-paint-roller text-primary me-2"></i> Appearance</h5>
                                <div class="surface-box p-4 rounded-4 mb-4 shadow-inner">
                                    <div class="form-check form-switch d-flex align-items-center">
                                        <input class="form-check-input fs-4 me-3 mt-0" type="checkbox" id="userThemeToggle" {{ Auth::user()->dark_mode ? 'checked' : '' }} style="cursor: pointer;">
                                        <div>
                                            <label class="form-check-label fw-bold text-dark mb-0" for="userThemeToggle" style="cursor: pointer;">Enable Dark Mode</label>
                                            <p class="text-muted small mb-0">Switch your interface to a darker color scheme. This setting applies only to your account.</p>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4 opacity-25" style="border-color: var(--border-color, #e2e8f0);">

                                <!-- WEB BIOMETRICS (PASSKEYS) -->
                                <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-fingerprint text-primary me-2"></i> Web Biometrics (Passkeys)</h5>
                                <p class="text-muted small mb-4 fw-semibold">
                                    Register this device using your fingerprint, face recognition, or device PIN for faster and secure logins. This passkey will be uniquely tied to your SecureLab account.
                                </p>

                                @php
                                    $hasPasskey = Auth::user()->passkeys()->exists();
                                @endphp

                                @if($hasPasskey)
                                    <div class="alert border-0 d-flex justify-content-between align-items-center mb-0 shadow-sm" style="background-color: rgba(32, 201, 151, 0.15); color: #0f5132; border-radius: 16px; border: 1px solid rgba(32, 201, 151, 0.3) !important;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle fa-2x me-3 text-success"></i>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark">Device is Registered</h6>
                                                <span class="text-muted" style="font-size: 11px;">You can use this device to sign in securely.</span>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#removePasskeyModal">
                                            Remove
                                        </button>
                                    </div>
                                @else
                                    <button type="button" id="registerPasskeyBtn" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold shadow-sm border-2">
                                        <i class="fas fa-plus-circle me-2"></i> Register This Device
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- GENERAL CONFIG TAB (ADMIN ONLY) -->
                        @if(Auth::user()->role == 'Admin')
                        <div class="tab-pane fade" id="general" role="tabpanel">
                            <div class="dashboard-card p-4">
                                <i class="fas fa-cog watermark-icon"></i>
                                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-sliders-h text-primary me-2"></i> General Configuration</h5>
                                <form action="{{ route('system.settings.update') }}" method="POST">
                                    @csrf
                                    <div class="row mb-3">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="form-label">Institution Name</label>
                                            <input type="text" name="institution_name" class="form-control form-control-lg shadow-inner fw-semibold text-dark" value="{{ $settings['institution_name'] ?? '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">System Alias</label>
                                            <input type="text" name="system_alias" class="form-control form-control-lg shadow-inner fw-semibold text-dark" value="{{ $settings['system_alias'] ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">System Timezone</label>
                                        <select name="timezone" class="form-select form-select-lg shadow-inner fw-semibold text-dark">
                                            <option selected value="Asia/Manila">Asia/Manila (GMT+08:00)</option>
                                        </select>
                                    </div>

                                    <hr class="my-4 opacity-25" style="border-color: var(--border-color, #e2e8f0);">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn bg-gradient-primary text-white rounded-pill px-4 fw-bold shadow-sm">Update General Settings</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- IOT INTEGRATION TAB (ADMIN ONLY) -->
                        <div class="tab-pane fade" id="iot" role="tabpanel">
                            <div class="dashboard-card p-4">
                                <i class="fas fa-network-wired watermark-icon"></i>
                                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-microchip text-primary me-2"></i> IoT Hardware Bridge</h5>
                                <form action="{{ route('system.settings.update') }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label class="form-label">Hardware API Key</label>
                                        <div class="input-group shadow-inner" style="border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color, #e2e8f0);">
                                            <input type="password" id="apiKeyInput" name="hardware_api_key" class="form-control form-control-lg border-0 fw-bold font-monospace text-primary" value="{{ $settings['hardware_api_key'] ?? '' }}">
                                            <button class="btn border-0 px-4" type="button" id="toggleApiKey" style="background-color: var(--bg-subtle, #f8fafc); border-left: 1px solid var(--border-color, #e2e8f0) !important;">
                                                <i class="fas fa-eye text-muted" id="toggleApiIcon"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted mt-2 d-block" style="font-size: 11px;">This key authenticates the physical NodeMCU locks to the server.</small>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label">MQTT Broker Address <span class="text-muted text-lowercase fw-normal">(Optional)</span></label>
                                        <input type="text" name="mqtt_broker" class="form-control form-control-lg shadow-inner fw-semibold text-dark" value="{{ $settings['mqtt_broker'] ?? '' }}" placeholder="broker.hivemq.com">
                                    </div>
                                    <hr class="my-4 opacity-25" style="border-color: var(--border-color, #e2e8f0);">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn bg-gradient-primary text-white rounded-pill px-4 fw-bold shadow-sm">Update IoT Settings</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- DATA MANAGEMENT TAB (ADMIN ONLY) -->
                        <div class="tab-pane fade" id="data-management" role="tabpanel">
                            <div class="dashboard-card p-4">
                                <i class="fas fa-server watermark-icon"></i>
                                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-database text-primary me-2"></i> Data Management & Archiving</h5>

                                <!-- System Backup -->
                                <h6 class="fw-bold text-dark mb-2">System Backup</h6>
                                <p class="text-muted small mb-3">Download a complete SQL copy of the database. You can use this file to restore the system later.</p>
                                <div class="surface-box p-3 rounded-4 mb-4 d-flex flex-wrap gap-3 align-items-center shadow-inner">
                                    <form action="/settings/backup/create" method="POST" class="m-0">
                                        @csrf
                                        <button class="btn bg-gradient-primary text-white rounded-pill px-4 fw-bold shadow-sm"><i class="fas fa-download me-2"></i> Create Backup</button>
                                    </form>

                                    <div class="vr d-none d-md-block mx-2" style="background-color: var(--border-color, #e2e8f0);"></div>

                                    <form action="/settings/backup/restore" method="POST" enctype="multipart/form-data" class="d-flex gap-2 flex-grow-1 m-0">
                                        @csrf
                                        <input type="file" name="backup_file" class="form-control form-control-sm rounded-pill shadow-inner" style="max-width: 250px;" required accept=".sql,.zip">
                                        <button class="btn btn-outline-dark rounded-pill px-4 fw-bold"><i class="fas fa-upload me-2"></i> Restore</button>
                                    </form>
                                </div>

                                <hr class="my-4 opacity-25" style="border-color: var(--border-color, #e2e8f0);">

                                <!-- Fingerprint Slot Management -->
                                <h6 class="fw-bold text-dark mb-2">Fingerprint Slot Management</h6>
                                <p class="text-muted small mb-4">
                                    View and force-remove any enrolled fingerprint across all laboratories. Use this if a user cannot manage their own fingerprint, or if a slot appears stuck on the hardware sensor. 
                                    <strong class="text-danger">Removing here will automatically clear the slot on the physical sensor as well.</strong>
                                </p>

                                @php
                                    $allFingerprints = DB::table('fingerprints as f')
                                        ->join('users as u', 'f.user_id', '=', 'u.user_id')
                                        ->join('rooms as r', 'f.room_id', '=', 'r.room_id')
                                        ->where('f.is_active', 1)
                                        ->select('f.fp_id', 'f.template_slot', 'f.finger_label', 'f.enrolled_at', 'u.full_name', 'r.room_name', 'r.room_code')
                                        ->orderBy('r.room_name')
                                        ->orderBy('f.template_slot')
                                        ->get();
                                @endphp

                                @if($allFingerprints->isEmpty())
                                    <div class="alert surface-box border text-center small mb-4 fw-semibold" style="border-radius: 14px; color: var(--text-muted, #64748b);">
                                        <i class="fas fa-fingerprint fa-2x mb-2 opacity-50 d-block text-primary"></i>
                                        No active fingerprints are currently enrolled in the system.
                                    </div>
                                @else
                                    <div class="table-responsive surface-box rounded-4 mb-4 overflow-hidden shadow-inner">
                                        <table class="table table-custom align-middle mb-0 table-borderless">
                                            <thead>
                                                <tr>
                                                    <th class="ps-4">Laboratory</th>
                                                    <th>User</th>
                                                    <th class="text-center">Slot ID</th>
                                                    <th>Enrolled At</th>
                                                    <th class="text-end pe-4">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($allFingerprints as $fp)
                                                    <tr class="fp-row">
                                                        <td class="ps-4">
                                                            <span class="fw-bold small text-dark d-block">{{ $fp->room_name }}</span>
                                                            <span class="text-muted" style="font-size: 10px;">{{ $fp->room_code }}</span>
                                                        </td>
                                                        <td class="small fw-semibold text-dark">{{ $fp->full_name }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-2 rounded-pill">ID {{ $fp->template_slot }}</span>
                                                        </td>
                                                        <td class="text-muted small fw-semibold" style="font-size: 11px;">{{ \Carbon\Carbon::parse($fp->enrolled_at)->format('M d, Y g:i A') }}</td>
                                                        <td class="text-end pe-4">
                                                            <form action="{{ route('settings.fingerprints.delete') }}" method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Force remove fingerprint for {{ $fp->full_name }} in {{ $fp->room_name }}? This clears the hardware slot automatically.');">
                                                                @csrf
                                                                <input type="hidden" name="fp_id" value="{{ $fp->fp_id }}">
                                                                <button type="submit" class="btn btn-sm btn-light text-danger border-0 rounded-pill px-3 fw-bold shadow-sm" title="Force Remove">
                                                                    <i class="fas fa-trash-alt me-1"></i> Remove
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                <hr class="my-4 opacity-25" style="border-color: var(--border-color, #e2e8f0);">

                                <!-- End of School Year Cleanup -->
                                <div class="p-4 rounded-4" style="background-color: rgba(220, 53, 69, 0.07); border: 1px solid rgba(220, 53, 69, 0.2); border-radius: 18px;">
                                    <h6 class="fw-bold text-danger mb-2"><i class="fas fa-broom me-2"></i> End of School Year Cleanup</h6>
                                    <p class="text-muted small mb-4">Running this protocol will archive all audit logs, clear current room assignments, and prepare the system for the next academic year. <strong class="text-danger">This action cannot be undone.</strong></p>
                                    <button type="button" class="btn bg-gradient-danger text-white rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                                        Run Cleanup Protocol
                                    </button>
                                </div>

                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- REMOVE PASSKEY MODAL -->
    <div class="modal fade" id="removePasskeyModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background-color: var(--bg-surface, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.08)) !important;">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4 pt-0">
                    <div class="stat-icon-wrapper bg-gradient-danger mx-auto mb-3" style="width: 70px; height: 70px; font-size: 30px;">
                        <i class="fas fa-key text-white"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Remove Passkey?</h5>
                    <p class="text-muted small mb-4">Please enter your account password to confirm removal.</p>
                    <form action="{{ route('profile.remove.passkey') }}" method="POST">
                        @csrf
                        <input type="password" name="password" class="form-control form-control-lg shadow-inner rounded-pill mb-4 text-center fw-bold" placeholder="Enter password" required>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill flex-grow-1 fw-bold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn bg-gradient-danger text-white rounded-pill flex-grow-1 fw-bold shadow-sm">Remove</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- CLEANUP MODAL -->
    @if(Auth::user()->role == 'Admin')
    <div class="modal fade" id="cleanupModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background-color: var(--bg-surface, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.08)) !important;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i> Destructive Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <p class="text-muted small mb-0 fw-semibold">Are you sure you want to run the End of School Year Cleanup? This will format the database for the new semester.</p>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                    <form action="/settings/cleanup" method="POST" class="w-100">
                        @csrf
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary rounded-pill flex-grow-1 fw-bold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn bg-gradient-danger text-white rounded-pill flex-grow-1 fw-bold shadow-sm">Confirm Cleanup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    @include('admin.components.footer')

    <script>
        // AUTO-SAVE DARK MODE TOGGLE SCRIPT
        document.getElementById('userThemeToggle').addEventListener('change', function() {
            let isDarkMode = this.checked ? 1 : 0;
            localStorage.setItem('securelab_dark_mode', isDarkMode ? 'true' : 'false');

            fetch('{{ route("profile.toggle.theme") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ dark_mode: isDarkMode })
            }).then(response => response.json()).then(data => {
                if(data.success) {
                    window.location.reload();
                }
            }).catch(error => console.error('Error updating theme:', error));
        });

        @if(Auth::user()->role == 'Admin')
        // API KEY TOGGLE SCRIPT
        document.getElementById('toggleApiKey').addEventListener('click', function() {
            const input = document.getElementById('apiKeyInput');
            const icon = document.getElementById('toggleApiIcon');
            input.type = (input.type === 'password') ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
            icon.classList.toggle('text-primary');
        });
        @endif
    </script>

    <script>
        // PASSKEY REGISTRATION SCRIPT (UPDATED FIX)
        const registerBtn = document.getElementById('registerPasskeyBtn');

        if (registerBtn) {
            registerBtn.addEventListener('click', async () => {
                if (!window.PublicKeyCredential) {
                    alert("❌ Hindi suportado ng browser na ito ang Passkeys. Gamitin ang Chrome, Edge, o Safari.");
                    return;
                }

                // Security Error handler for Local Dev vs Production
                if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    alert("🔒 SECURITY ERROR: Hindi gagana ang pag-register ng Passkey sa mobile kung walang HTTPS. Please run this on your live domain.");
                    return;
                }

                const originalText = registerBtn.innerHTML;

                try {
                    registerBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Registering...';
                    registerBtn.disabled = true;

                    const challenge = new Uint8Array(32);
                    window.crypto.getRandomValues(challenge);

                    // 🟢 FIX: Gawing pareho ang user ID palagi (Huwag random)
                    const userIdStr = String({{ Auth::user()->user_id }});
                    const userID = new Uint8Array(16);
                    for (let i = 0; i < userIdStr.length; i++) {
                        userID[i] = userIdStr.charCodeAt(i);
                    }

                    const credential = await navigator.credentials.create({
                        publicKey: {
                            challenge: challenge,
                            rp: { name: "SecureLab TPC", id: window.location.hostname },
                            user: {
                                id: userID,
                                name: "{{ Auth::user()->email }}",
                                displayName: "{{ Auth::user()->full_name }}"
                            },
                            pubKeyCredParams: [
                                { type: "public-key", alg: -7 },
                                { type: "public-key", alg: -257 }
                            ],
                            authenticatorSelection: { 
                                authenticatorAttachment: "platform",
                                userVerification: "required",
                                residentKey: "preferred"
                            },
                            timeout: 60000,
                            attestation: "none"
                        }
                    });

                    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    const deviceName = isMobile ? 'Mobile Device' : 'Desktop/Laptop';

                    const response = await fetch('{{ route("profile.passkey.register") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ 
                            credential_id: credential.id,
                            name: deviceName
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert("✅ Success! Na-save na ang device na ito sa database.");
                        window.location.reload();
                    } else {
                        alert("⚠️ Error sa Database: " + (data.message || "Unknown error."));
                        registerBtn.innerHTML = originalText;
                        registerBtn.disabled = false;
                    }

                } catch (error) {
                    console.error('Passkey Registration Error:', error);
                    if (error.name === 'NotAllowedError') {
                        alert("🚫 Na-cancel ang proseso. (Siguraduhing kinumpleto mo ang fingerprint scan o may screen lock ang device).");
                    } else if (error.name === 'InvalidStateError') {
                        alert("ℹ️ Naka-register na ang passkey sa account na ito para sa device mo.");
                    } else {
                        alert("❌ Error: " + error.message);
                    }
                    registerBtn.innerHTML = originalText;
                    registerBtn.disabled = false;
                }
            });
        }
    </script>
</body>
</html>