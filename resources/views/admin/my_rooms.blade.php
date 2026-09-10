<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <title>SecureLab - My Authorized Rooms</title>
    <style>
        /* 🟢 MY ROOMS STYLES */
        .room-control-card {
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            box-shadow: var(--shadow-sm);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .room-control-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: rgba(26, 86, 219, 0.25);
        }
        
        /* Icon Boxes */
        .stat-icon-wrapper { 
            width: 52px; 
            height: 52px; 
            border-radius: var(--radius-md); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 22px; 
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
        }
        .bg-gradient-primary { background: linear-gradient(135deg, #1a56db 0%, #0c1c38 100%); color: white; }
        .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: white; }
        .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; }
        .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%); color: white; }
        .bg-gradient-secondary { background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white; }
        .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0e7490 100%); color: white; }

        /* Faint Background Watermark */
        .watermark-icon { 
            position: absolute; 
            right: -15px; 
            bottom: -20px; 
            font-size: 120px; 
            opacity: 0.03; 
            transform: rotate(-15deg); 
            pointer-events: none; 
        }

        /* Controls & Buttons */
        .control-btn {
            transition: all 0.2s ease;
            border-radius: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .control-btn:hover:not([disabled]) {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        /* Pulse Animation for Online Status */
        .pulse-dot-success { 
            display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981; 
            box-shadow: 0 0 0 rgba(16, 185, 129, 0.4); animation: pulse-green 2s infinite; 
        }
        @keyframes pulse-green { 0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); } 70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }

        .guide-step { display: flex; align-items: flex-start; margin-bottom: 15px; }
        .step-number {
            background: linear-gradient(135deg, #1a56db 0%, #0c1c38 100%);
            color: white; width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700; margin-right: 12px; flex-shrink: 0; margin-top: 2px;
            box-shadow: 0 3px 8px rgba(26, 86, 219, 0.3);
        }
        .pin-input {
            letter-spacing: 12px; font-size: 26px; text-align: center; border-radius: 14px;
            border: 2px solid var(--border-color); background-color: var(--bg-subtle); font-weight: 800; color: var(--text-dark);
        }
        .pin-input:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 4px rgba(26, 86, 219, 0.15); background-color: var(--bg-surface); }

        .enroll-choice-btn { transition: all 0.2s ease; background: var(--bg-surface); border-color: var(--border-color); }
        .enroll-choice-btn:hover { background-color: var(--bg-subtle); border-color: var(--primary-blue) !important; transform: translateY(-2px); box-shadow: var(--shadow-sm); }

        .modal { z-index: 1060 !important; }
        .modal-backdrop { z-index: 1050 !important; }

        .fp-enrolled-box { border-radius: 14px; background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); }
        .pin-status-badge { font-size: 10px; letter-spacing: 0.5px; }
        .x-small { font-size: 0.75rem; }

        .sub-card-box {
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        body.dark-mode .watermark-icon {
            opacity: 0.02 !important;
            color: #ffffff !important;
        }
        body.dark-mode .sub-card-box {
            background: var(--bg-subtle) !important;
            border-color: var(--border-color) !important;
        }

        @media (max-width: 768px) {
            .modal-dialog { margin-top: 80px; margin-bottom: 20px; }
        }
    </style>
</head>
<body>

    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid p-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">My Authorized Rooms</h4>
                    <p class="text-muted small mb-0 mt-1">Manage assigned laboratory locks, remote unlock triggers, PIN codes, and biometrics.</p>
                </div>
                
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-warning text-dark shadow-xs rounded-pill px-3 py-2 small fw-bold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#tipsModal">
                        <i class="fas fa-lightbulb"></i> Security Tips
                    </button>
                    <button type="button" class="btn btn-light border shadow-xs text-primary rounded-pill px-3 py-2 small fw-bold d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#enrollmentGuideModal">
                        <i class="fas fa-fingerprint"></i> Biometric Guide
                    </button>
                </div>
            </div>

            <!-- AJAX Refresh Container -->
            <div class="row g-3 g-md-4" id="rooms-container">
                @forelse($myRooms as $room)
                    @php
                        $hardwareOnline = $room->is_active && $room->room_is_active;
                        $isReadOnly = $room->access_level === 'readonly';
                        $canOperate = $room->access_verified && $hardwareOnline && !$isReadOnly;
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="room-control-card h-100 p-3 p-md-4">
                            <i class="fas fa-door-open watermark-icon"></i>
                            
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div class="stat-icon-wrapper {{ $hardwareOnline ? ($canOperate ? 'bg-gradient-primary' : ($isReadOnly ? 'bg-gradient-info' : 'bg-gradient-warning')) : 'bg-gradient-danger' }}">
                                    <i class="fas fa-door-open"></i>
                                </div>

                                @if(!$hardwareOnline)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 shadow-xs fw-bold">Offline</span>
                                @elseif($room->access_expired)
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-2 shadow-xs fw-bold">Expired</span>
                                @elseif(!$room->access_verified)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 shadow-xs fw-bold">Inactive</span>
                                @elseif($isReadOnly)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3 py-2 shadow-xs fw-bold">View Only</span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 shadow-xs fw-bold">
                                        <span class="pulse-dot-success me-1"></span> Authorized
                                    </span>
                                @endif
                            </div>

                            <h5 class="fw-bold mb-1 text-dark" style="letter-spacing: -0.3px;">{{ $room->room_name }}</h5>
                            
                            <p class="text-muted small mb-2">
                                <strong class="text-primary">{{ $room->room_code }}</strong> | <i class="fas fa-map-marker-alt"></i> {{ $room->location ?? 'No location set' }}
                            </p>
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-light text-dark border shadow-xs"><i class="fas fa-users text-primary"></i> Cap: {{ $room->capacity ?? 'N/A' }}</span>
                                @if(!empty($room->is_air_conditioned))
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info shadow-xs"><i class="fas fa-snowflake"></i> AC</span>
                                @endif
                                @if(!empty($room->backup_ssid))
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary shadow-xs" data-bs-toggle="tooltip" title="Backup Hotspot Enabled"><i class="fas fa-wifi"></i> Hotspot</span>
                                @endif
                            </div>

                            <!-- NOTIFICATIONS FOR ACCESS LEVELS -->
                            @if($isReadOnly)
                                <div class="alert alert-info border-0 small py-2 px-3 mb-3 fw-medium shadow-xs" style="border-radius: 12px; background-color: rgba(6, 182, 212, 0.12); color: #0e7490; border: 1px solid rgba(6, 182, 212, 0.25);">
                                    <i class="fas fa-eye me-1"></i>
                                    You have View-Only access. Remote controls are disabled.
                                </div>
                            @elseif($hardwareOnline && !$room->access_verified)
                                <div class="alert alert-warning border-0 small py-2 px-3 mb-3 fw-medium shadow-xs" style="border-radius: 12px; background-color: rgba(245, 158, 11, 0.12); color: #92400e; border: 1px solid rgba(245, 158, 11, 0.25);">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    {{ $room->access_expired ? 'Your access schedule has expired.' : 'Your access is currently inactive.' }}
                                </div>
                            @endif

                            <!-- OCCUPANCY STATUS TOGGLE -->
                            <div class="sub-card-box p-3 mb-3 shadow-xs">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">Class Status</h6>
                                        <small class="text-muted" style="font-size: 10px;">Is there an ongoing class?</small>
                                    </div>
                                    <button id="occupancyBtn-{{ $room->room_id }}" 
                                            class="btn {{ ($room->occupancy_status ?? 'vacant') == 'occupied' ? 'btn-danger' : 'btn-success' }} fw-bold rounded-pill px-3 py-1 shadow-xs small" 
                                            style="font-size: 11px;"
                                            onclick="toggleOccupancy('{{ $room->room_id }}', '{{ $room->occupancy_status ?? 'vacant' }}')"
                                            {{ !$canOperate ? 'disabled' : '' }}>
                                        <i class="fas {{ ($room->occupancy_status ?? 'vacant') == 'occupied' ? 'fa-users' : 'fa-door-open' }} me-1"></i>
                                        <span id="occupancyText-{{ $room->room_id }}">{{ strtoupper($room->occupancy_status ?? 'VACANT') }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- REMOTE CONTROLS -->
                            <div class="sub-card-box p-3 mb-3 shadow-xs">
                                <label class="fw-bold text-muted text-uppercase d-block mb-3" style="font-size: 10px; letter-spacing: 1px;">
                                    <i class="fas fa-wifi me-1 text-primary"></i> Remote Operation
                                </label>

                                @if($hardwareOnline)
                                    <form action="{{ route('rooms.remote-control') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="room_id" value="{{ $room->room_id }}">

                                        @if(($room->door_state ?? 'locked') === 'locked')
                                            <input type="hidden" name="action" value="unlock">
                                            <button type="submit" class="btn btn-success w-100 control-btn py-3 shadow-xs mb-2" {{ !$canOperate ? 'disabled' : '' }}>
                                                <i class="fas fa-unlock-alt me-2"></i> Unlock Room
                                            </button>
                                        @else
                                            <input type="hidden" name="action" value="lock">
                                            <button type="submit" class="btn btn-danger w-100 control-btn py-3 shadow-xs mb-2" {{ !$canOperate ? 'disabled' : '' }}>
                                                <i class="fas fa-lock me-2"></i> Lock Room
                                            </button>
                                        @endif
                                    </form>

                                    <!-- FINGERPRINT BOX -->
                                    @if($room->fingerprint)
                                        <div class="fp-enrolled-box p-3 mb-2 shadow-xs">
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-fingerprint text-success me-2 fs-5"></i>
                                                    <span class="fw-bold small text-success">Biometric Ready</span>
                                                </div>
                                                <span class="badge bg-success rounded-pill shadow-xs" style="font-size: 10px;">ID {{ $room->fingerprint->template_slot }}</span>
                                            </div>
                                            <button type="button" class="btn btn-outline-danger w-100 control-btn py-2 shadow-xs border-2" data-bs-toggle="modal" data-bs-target="#confirmRemoveFpModal-{{ $room->room_id }}" {{ !$canOperate ? 'disabled' : '' }}>
                                                <i class="fas fa-eraser me-1"></i> Clear Fingerprint
                                            </button>
                                        </div>
                                    @else
                                        <button type="button" class="btn btn-outline-primary w-100 control-btn py-2 shadow-xs border-2 mb-2 bg-white" style="border-style: dashed !important;"
                                                data-bs-toggle="modal" data-bs-target="#chooseEnrollMethodModal-{{ $room->room_id }}"
                                                {{ !$canOperate ? 'disabled' : '' }}>
                                            <i class="fas fa-fingerprint me-2"></i> Enroll Fingerprint
                                        </button>
                                    @endif

                                    <button type="button" class="btn btn-outline-dark w-100 control-btn py-2 shadow-xs border-2 mb-2 bg-white"
                                            data-bs-toggle="modal" data-bs-target="#setPasscodeModal-{{ $room->room_id }}"
                                            {{ !$canOperate ? 'disabled' : '' }}>
                                        <i class="fas fa-keyboard me-2"></i>
                                        {{ !empty($room->pin_code) ? 'Change Access PIN' : 'Set Access PIN' }}
                                        @if(!empty($room->pin_code))
                                            <span class="badge bg-dark bg-opacity-10 text-dark pin-status-badge ms-1 rounded-pill">SET</span>
                                        @endif
                                    </button>

                                    <!-- BACKUP HOTSPOT BUTTON -->
                                    <button type="button" class="btn btn-outline-info w-100 control-btn py-2 shadow-xs border-2 bg-white"
                                            data-bs-toggle="modal" data-bs-target="#setHotspotModal-{{ $room->room_id }}"
                                            {{ !$canOperate ? 'disabled' : '' }}>
                                        <i class="fas fa-satellite-dish me-2"></i>
                                        Set Backup Hotspot
                                    </button>

                                @else
                                    <button class="btn btn-secondary w-100 opacity-50 py-3 rounded-3 mb-2 fw-bold" disabled style="cursor: not-allowed;">
                                        <i class="fas fa-power-off me-2"></i> Hardware Offline
                                    </button>
                                    <small class="text-danger d-block text-center fw-bold" style="font-size: 11px;">The system administrator has disabled this node.</small>
                                @endif
                            </div>

                            <div class="border-top pt-3 mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted x-small fw-bold">Access Level:</span>
                                    <span class="badge bg-info bg-opacity-10 text-info fw-bold text-uppercase rounded-pill" style="font-size: 10px;">{{ str_replace('_', ' ', $room->access_level) }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted x-small fw-bold">Current State:</span>
                                    <span class="fw-bolder small {{ ($room->door_state ?? 'locked') === 'locked' ? 'text-danger' : 'text-success' }}">
                                        {{ strtoupper($room->door_state ?? 'locked') }}
                                    </span>
                                </div>
                                @if($room->valid_until)
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted x-small fw-bold">Valid Until:</span>
                                        <span class="fw-bolder small {{ $room->access_expired ? 'text-danger' : 'text-dark' }}">
                                            {{ \Carbon\Carbon::parse($room->valid_until)->format('M d, Y') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- SET BACKUP HOTSPOT MODAL --}}
                    <div class="modal fade" id="setHotspotModal-{{ $room->room_id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                <div class="modal-header border-0 p-4 pb-0">
                                    <h5 class="modal-title fw-bold text-dark">Backup Hotspot Settings</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('rooms.set-hotspot') }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4">
                                        <div class="alert alert-info border-0 small shadow-sm fw-semibold" style="border-radius: 10px; background-color: #e0f7fa; color: #0c5460;">
                                            <i class="fas fa-info-circle me-1"></i> <strong>Tip 1:</strong> Don't forget to set up your mobile hotspot before your class starts, especially if school internet connectivity drops.
                                        </div>

                                        <input type="hidden" name="room_id" value="{{ $room->room_id }}">

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Hotspot Name (SSID)</label>
                                            <input type="text" name="hotspot_ssid" class="form-control form-control-lg bg-light border-0 shadow-inner" 
                                                   placeholder="e.g. MyPhone_Hotspot" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-muted">Hotspot Password</label>
                                            <input type="text" name="hotspot_password" class="form-control form-control-lg bg-light border-0 shadow-inner" 
                                                   placeholder="e.g. password123" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill fw-bold px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn bg-gradient-success text-white rounded-pill fw-bold px-4 shadow-sm">
                                            <i class="fas fa-paper-plane me-1"></i> Send to Hardware
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- SET/CHANGE ACCESS PIN MODAL --}}
                    <div class="modal fade" id="setPasscodeModal-{{ $room->room_id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                <div class="modal-header border-0 p-4 pb-0">
                                    <h5 class="modal-title fw-bold text-dark">{{ !empty($room->pin_code) ? 'Change Access PIN' : 'Set Access PIN' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('rooms.set-pin') }}" method="POST">
                                    @csrf
                                    <div class="modal-body p-4 text-center">
                                        <p class="text-muted small mb-4 fw-semibold">
                                            <strong>Tip 2:</strong> Set up your unique 4-digit PIN code as your secondary keypad fallback for <strong class="text-primary">{{ $room->room_name }}</strong>.
                                        </p>

                                        <input type="hidden" name="room_id" value="{{ $room->room_id }}">

                                        <div class="mb-3 px-4">
                                            <input type="text" name="pin_code" class="form-control form-control-lg pin-input mb-2 shadow-inner" 
                                                   maxlength="4" pattern="\d{4}" placeholder="••••" required 
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                            <small class="text-muted fw-bold" style="font-size: 11px;">MUST BE EXACTLY 4 NUMBERS</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill fw-bold px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-dark rounded-pill fw-bold px-4 shadow-sm">
                                            {{ !empty($room->pin_code) ? 'Update PIN' : 'Save PIN' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- CONFIRM REMOVE FINGERPRINT MODAL --}}
                    @if($room->fingerprint)
                    <div class="modal fade" id="confirmRemoveFpModal-{{ $room->room_id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg text-center" style="border-radius: 20px;">
                                <div class="modal-body p-5">
                                    <div class="stat-icon-wrapper bg-gradient-danger mx-auto mb-4" style="width: 80px; height: 80px; font-size: 35px;">
                                        <i class="fas fa-trash-alt"></i>
                                    </div>
                                    <h4 class="fw-bold mb-3 text-dark">Remove Fingerprint?</h4>
                                    <p class="text-muted small mb-4 fw-semibold">Are you sure you want to remove your fingerprint from <strong class="text-primary">{{ $room->room_name }}</strong>? This will automatically clear your data from the hardware sensor.</p>
                                    <form action="{{ route('rooms.unenroll') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="room_id" value="{{ $room->room_id }}">
                                        <div class="d-flex justify-content-center gap-3">
                                            <button type="button" class="btn btn-light rounded-pill fw-bold px-4 py-2 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger rounded-pill fw-bold px-4 py-2 shadow-sm">Yes, Remove</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- CHOOSE ENROLLMENT METHOD MODAL --}}
                    @if(!$room->fingerprint)
                    <div class="modal fade" id="chooseEnrollMethodModal-{{ $room->room_id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                <div class="modal-header border-0 p-4 pb-0">
                                    <h5 class="modal-title fw-bold text-dark">Enroll Fingerprint (Tip 3)</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body p-4">
                                    <p class="text-muted small mb-4 fw-semibold">Register your biometric fingerprint after setting up your hotspot and PIN code.</p>

                                    <button type="button" class="btn border enroll-choice-btn w-100 py-3 rounded-4 mb-3 text-start px-4 shadow-sm" 
                                            data-bs-toggle="modal" data-bs-target="#enterIdModal-{{ $room->room_id }}" 
                                            data-bs-dismiss="modal" onclick="this.blur()">
                                        <i class="fas fa-laptop text-primary fs-4 me-3 align-middle"></i>
                                        <div class="d-inline-block align-middle">
                                            <strong class="d-block text-dark">Website Input</strong>
                                            <span class="text-muted x-small">Enter ID here, then scan on hardware.</span>
                                        </div>
                                    </button>

                                    <form action="{{ route('rooms.enroll') }}" method="POST" class="mb-0">
                                        @csrf
                                        <input type="hidden" name="room_id" value="{{ $room->room_id }}">
                                        <button type="submit" class="btn border enroll-choice-btn w-100 py-3 rounded-4 text-start px-4 shadow-sm">
                                            <i class="fas fa-keyboard text-dark fs-4 me-3 align-middle"></i>
                                            <div class="d-inline-block align-middle">
                                                <strong class="d-block text-dark">Physical Keypad Input</strong>
                                                <span class="text-muted x-small">Type ID directly on the door lock keypad.</span>
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- ENTER ID MODAL --}}
                    <div class="modal fade" id="enterIdModal-{{ $room->room_id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                <div class="modal-header border-0 p-4 pb-0">
                                    <h5 class="modal-title fw-bold text-dark">Choose ID Number</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('rooms.enroll') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="room_id" value="{{ $room->room_id }}">
                                    <div class="modal-body p-4 text-center">
                                        <p class="text-muted small mb-4 fw-semibold">Enter a unique number (1-127) for this fingerprint.</p>
                                        <div class="mb-3 px-4">
                                            <input type="number" name="enroll_id" min="1" max="127" 
                                                   class="form-control form-control-lg pin-input mb-3 shadow-inner" 
                                                   placeholder="1-127" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 p-4 pt-0">
                                        <button type="button" class="btn btn-light rounded-pill fw-bold px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn bg-gradient-primary text-white rounded-pill fw-bold px-4 shadow-sm">Activate Sensor</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 mt-5">
                        <div class="stat-icon-wrapper bg-gradient-secondary mx-auto mb-4" style="width: 90px; height: 90px; font-size: 40px; opacity: 0.5;">
                            <i class="fas fa-user-lock"></i>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">No Assigned Laboratories</h4>
                        <p class="text-muted mx-auto mb-4 fw-semibold" style="max-width: 400px;">
                            You are not currently authorized for any laboratory access.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- 🟢 AUTOMATIC SETUP TIPS MODAL (Appears on first load) --}}
    <div class="modal fade" id="autoSetupTipsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 p-4 pb-0 mb-2">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-lightbulb text-warning me-2"></i>Welcome Guide & Setup Tips</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <div class="alert alert-warning border-0 small py-2 px-3 mb-3 fw-semibold shadow-sm" style="border-radius: 12px;">
                        <i class="fas fa-exclamation-triangle me-1"></i> <strong>Reminder:</strong> Don't forget to turn on and set up your mobile hotspot before your class starts!
                    </div>
                    <p class="text-muted small mb-4 fw-semibold">For the best experience with the Smart Door Lock, please follow this setup sequence:</p>

                    <div class="guide-step">
                        <div class="step-number">1</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Set Backup Hotspot</h6>
                            <p class="text-muted x-small mb-0">Click <strong>"Set Backup Hotspot"</strong> to prepare the network in case of a power or WiFi outage at school.</p>
                        </div>
                    </div>

                    <div class="guide-step">
                        <div class="step-number">2</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Set Access PIN</h6>
                            <p class="text-muted x-small mb-0">Click <strong>"Set Access PIN"</strong> to configure a 4-digit numeric code as a backup if your biometric data cannot be read.</p>
                        </div>
                    </div>

                    <div class="guide-step">
                        <div class="step-number">3</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Enroll Fingerprint</h6>
                            <p class="text-muted x-small mb-0">Finally, click <strong>"Enroll Fingerprint"</strong> to register your finger on the hardware sensor.</p>
                        </div>
                    </div>

                    <!-- Don't show again checkbox -->
                    <div class="form-check mt-4 pt-2 border-top">
                        <input class="form-check-input" type="checkbox" id="dontShowAgainCheck">
                        <label class="form-check-label small fw-bold text-muted" for="dontShowAgainCheck" style="cursor: pointer;">
                            Don't show this automatically again
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn bg-gradient-primary text-white w-100 rounded-pill fw-bold shadow-sm" onclick="closeTipsModal()">Got it, Let's Start!</button>
                </div>
            </div>
        </div>
    </div>

    {{-- 🟢 MANUAL TIPS MODAL (Appears when clicking the Tips button) --}}
    <div class="modal fade" id="tipsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 p-4 pb-0 mb-2">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-lightbulb text-warning me-2"></i>Quick Setup Tips</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <div class="guide-step">
                        <div class="step-number">1</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Backup Hotspot First</h6>
                            <p class="text-muted x-small mb-0">Always update your hotspot details (SSID and Password) if you're using the laboratory to ensure connection.</p>
                        </div>
                    </div>
                    <div class="guide-step">
                        <div class="step-number">2</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Passcode Fallback</h6>
                            <p class="text-muted x-small mb-0">Use your 4-digit PIN on the keypad if your finger is wet or dirty while accessing the room.</p>
                        </div>
                    </div>
                    <div class="guide-step">
                        <div class="step-number">3</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Fingerprint Enrollment</h6>
                            <p class="text-muted x-small mb-0">Follow the double-scan process carefully when registering a new finger on the sensor.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-dark w-100 rounded-pill fw-bold shadow-sm" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- FINGERPRINT ENROLLMENT GUIDE MODAL --}}
    <div class="modal fade" id="enrollmentGuideModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0 p-4 pb-0 mb-2">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-fingerprint text-primary me-2"></i>Fingerprint Enrollment Guide</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-2">
                    <p class="text-muted small mb-4 fw-semibold">Follow these steps to properly register your biometric data to the lock hardware:</p>
                    
                    <div class="guide-step">
                        <div class="step-number">1</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Choose Your Method</h6>
                            <p class="text-muted x-small mb-0">Click <strong>"Enroll Fingerprint"</strong> on your room card, then choose whether to type your ID number here on the website, or directly on the physical keypad.</p>
                        </div>
                    </div>
                    
                    <div class="guide-step">
                        <div class="step-number">2</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Activate the Sensor</h6>
                            <p class="text-muted x-small mb-0">Once submitted, this immediately triggers the hardware and turns on the fingerprint scanner.</p>
                        </div>
                    </div>

                    <div class="guide-step">
                        <div class="step-number">3</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Place Your Finger</h6>
                            <p class="text-muted x-small mb-0">Once the sensor lights up, place your enrolled finger firmly on the glass. Ensure your finger is dry and clean.</p>
                        </div>
                    </div>

                    <div class="guide-step">
                        <div class="step-number">4</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">The "Double-Scan" Process</h6>
                            <p class="text-muted x-small mb-0">Remove your finger when the light blinks or the system beeps, then place the same finger again to confirm and save your print.</p>
                        </div>
                    </div>

                    <div class="guide-step">
                        <div class="step-number">5</div>
                        <div>
                            <h6 class="fw-bold mb-1 small text-dark">Hardware Confirmation</h6>
                            <p class="text-muted x-small mb-0">Wait for the success indicator on the hardware (LCD screen or LED light) to confirm that your fingerprint is saved.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn bg-gradient-primary text-white w-100 rounded-pill fw-bold shadow-sm" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.components.footer')

    <!-- JAVASCRIPT LOGIC -->
    <script>
        // 🟢 AUTO-POPUP TIPS MODAL SCRIPT (Using LocalStorage)
        document.addEventListener("DOMContentLoaded", function() {
            let hideTips = localStorage.getItem('securelab_hide_setup_tips');
            if (!hideTips) {
                setTimeout(function() {
                    let autoModal = new bootstrap.Modal(document.getElementById('autoSetupTipsModal'));
                    autoModal.show();
                }, 500);
            }
        });

        function closeTipsModal() {
            let checkbox = document.getElementById('dontShowAgainCheck');
            if (checkbox && checkbox.checked) {
                localStorage.setItem('securelab_hide_setup_tips', 'true');
            }
            let modalEl = document.getElementById('autoSetupTipsModal');
            let modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
        }

        function toggleOccupancy(roomId, currentStatus) {
            let newStatus = currentStatus === 'occupied' ? 'vacant' : 'occupied';
            let btn = document.getElementById('occupancyBtn-' + roomId);
            let text = document.getElementById('occupancyText-' + roomId);
            
            let originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
            btn.disabled = true;

            fetch('{{ route("rooms.toggle-occupancy") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ room_id: roomId, status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                if(data.success) {
                    if(data.new_status === 'occupied') {
                        btn.className = 'btn btn-danger fw-bold rounded-pill px-3 py-1 shadow-sm small';
                        btn.innerHTML = '<i class="fas fa-users me-1"></i><span id="occupancyText-' + roomId + '">OCCUPIED</span>';
                    } else {
                        btn.className = 'btn btn-success fw-bold rounded-pill px-3 py-1 shadow-sm small';
                        btn.innerHTML = '<i class="fas fa-door-open me-1"></i><span id="occupancyText-' + roomId + '">VACANT</span>';
                    }
                } else {
                    alert("Error updating status!");
                    location.reload(); 
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.innerHTML = originalContent; 
            });
        }

        function fetchRoomsData() {
            if (document.querySelector('.modal.show')) {
                setTimeout(fetchRoomsData, 5000); 
                return;
            }

            let fetchUrl = window.location.href.split('?')[0] + '?t=' + new Date().getTime();

            fetch(fetchUrl)
                .then(response => {
                    if (!response.ok) throw new Error("Network issue or Timeout");
                    return response.text();
                })
                .then(html => {
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');

                    let newContainer = doc.getElementById('rooms-container');
                    let oldContainer = document.getElementById('rooms-container');

                    if (newContainer && oldContainer) {
                        oldContainer.innerHTML = newContainer.innerHTML;
                    }

                    setTimeout(fetchRoomsData, 5000); 
                })
                .catch(error => {
                    console.error('Error auto-refreshing rooms:', error);
                    setTimeout(fetchRoomsData, 5000); 
                });
        }

        setTimeout(fetchRoomsData, 5000);
    </script>
</body>
</html>