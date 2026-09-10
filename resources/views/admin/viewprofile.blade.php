<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <style>
        /* 🟢 MODERN UI ENHANCEMENTS (Matched with Dashboard) */
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        /* 🟢 MODERN EXECUTIVE UI ENHANCEMENTS */
        body { 
            background-color: var(--bg-body, #f4f7f6); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--text-main, #1e293b);
        }

        .dashboard-card { 
            border-radius: 20px; 
            border: none; 
            border: 1px solid var(--border-color, rgba(0,0,0,0.06)) !important; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.03); 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
            position: relative; 
            overflow: hidden; 
            background: #fff; 
            background: var(--bg-surface, #fff); 
        }
        .dashboard-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 15px 35px rgba(0,0,0,0.08); 
        }

        /* Gradient Accents */
        .bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%); color: white; }
        .bg-gradient-success { background: linear-gradient(135deg, #20c997 0%, #198754 100%); color: white; }
        .bg-gradient-danger { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; }
        .bg-gradient-info { background: linear-gradient(135deg, #0dcaf0 0%, #055160 100%); color: white; }

        /* Faint Background Icons (Watermarks) */
        .watermark-icon { 
            position: absolute; 
            right: -15px; 
            bottom: -20px; 
            font-size: 130px; 
            opacity: 0.02; 
            opacity: 0.03; 
            transform: rotate(-15deg); 
            pointer-events: none; 
            color: var(--text-main, #000);
        }

        /* Profile Image Zoom & Overlay */
        .profile-img-container { position: relative; display: inline-block; cursor: zoom-in; border-radius: 50%; padding: 5px; background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .profile-img-overlay { position: absolute; top: 5px; left: 5px; right: 5px; bottom: 5px; border-radius: 50%; background: rgba(10, 37, 64, 0.6); display: flex; align-items: center; justify-content: center; opacity: 0; transition: 0.3s ease; }
        .profile-img-container { 
            position: relative; 
            display: inline-block; 
            cursor: zoom-in; 
            border-radius: 50%; 
            padding: 5px; 
            background: var(--bg-surface, #fff); 
            box-shadow: 0 8px 24px rgba(0,0,0,0.08); 
            border: 1px solid var(--border-color, rgba(0,0,0,0.08));
        }
        .profile-img-overlay { 
            position: absolute; 
            top: 5px; 
            left: 5px; 
            right: 5px; 
            bottom: 5px; 
            border-radius: 50%; 
            background: rgba(10, 37, 64, 0.65); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            opacity: 0; 
            transition: 0.3s ease; 
        }
        .profile-img-container:hover .profile-img-overlay { opacity: 1; }
        .profile-img-overlay i { color: white; font-size: 28px; }
        .profile-img-overlay i { color: white; font-size: 26px; }

        /* Navigation Pills */
        .nav-pills .nav-link { 
            border-radius: 12px; 
            color: #6c757d; 
            color: var(--text-muted, #64748b); 
            font-weight: 600; 
            padding: 12px 24px; 
            margin-right: 10px;
            transition: 0.3s ease; 
            padding: 12px 22px; 
            margin-right: 8px;
            transition: all 0.25s ease; 
            background: var(--bg-subtle, #f8fafc);
            border: 1px solid var(--border-color, rgba(0,0,0,0.05));
        }
        .nav-pills .nav-link:hover:not(.active) { 
            background-color: #f8faff; 
            background-color: var(--bg-surface, #fff); 
            color: #0d6efd; 
            transform: translateY(-2px);
            border-color: rgba(13, 110, 253, 0.25);
        }
        .nav-pills .nav-link.active { 
            background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%); 
            color: white; 
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2); 
            background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%) !important; 
            color: white !important; 
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.25); 
            border-color: transparent;
        }

        /* Sleek Table */
        .table-custom th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid #f0f2f5; padding-bottom: 12px; color: #6c757d; }
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
        .table-custom tbody tr { transition: 0.2s ease; }
        .table-custom tbody tr:hover { background-color: #f8faff; transform: scale(1.01); border-radius: 10px; }
        .table-custom td { border-bottom: 1px solid #f8f9fa; padding: 15px 10px; }
        .table-custom tbody tr:hover { background-color: rgba(13, 110, 253, 0.03); }
        .table-custom td { border-bottom: 1px solid var(--border-color, #f1f5f9); padding: 14px 12px; color: var(--text-main, #1e293b); }

        /* Modern Timeline */
        .timeline { border-left: 2px dashed #dee2e6; padding-left: 25px; position: relative; margin-left: 10px; }
        .timeline { border-left: 2px dashed var(--border-color, #cbd5e1); padding-left: 25px; position: relative; margin-left: 10px; }
        .timeline-item { margin-bottom: 2rem; position: relative; }
        .timeline-item::before { 
            content: ''; 
            position: absolute; 
            left: -33px; 
            top: 3px; 
            width: 14px; 
            height: 14px; 
            border-radius: 50%; 
            background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%); 
            border: 3px solid #fff; 
            border: 3px solid var(--bg-surface, #fff); 
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2); 
        }

        /* Inner Shadows for Boxes */
        .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.03); }
        .control-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(13, 110, 253, 0.15); }
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
        }
        .control-btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 18px rgba(13, 110, 253, 0.18); 
        }
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-2 fw-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">
                            <i class="fas fa-id-card me-1"></i> PERSONNEL CREDENTIAL AUDIT
                        </span>
                        <span class="text-muted small">•</span>
                        <span class="text-muted small fw-medium">Record Verification</span>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">User Profile</h4>
                    <p class="text-muted small mb-0">Viewing credentials and authorization for <strong class="text-primary">{{ $viewUser->full_name }}</strong></p>
                </div>
                
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if(Auth::user()?->role === 'Admin')
                        <a href="{{ route('users.database') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold shadow-sm control-btn">
                            <i class="fas fa-arrow-left me-1"></i> Back to Users
                        </a>
                    @else
                        <a href="{{ url()->previous() ?: route('dashboard') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold shadow-sm control-btn">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    @endif
                    <a href="{{ route('messages.index') }}?user={{ $viewUser->user_id }}" class="btn bg-gradient-primary text-white rounded-pill px-4 py-2 shadow-sm control-btn fw-bold">
                        <i class="fab fa-facebook-messenger me-2"></i> Send Message
                    </a>
                </div>
            </div>

            <div class="row g-4">
                <!-- 🟢 LEFT COLUMN: PROFILE CARD -->
                <div class="col-12 col-xl-4">
                    <div class="dashboard-card p-4 text-center h-100" style="border-top: 3.5px solid #2563eb !important;">
                        <i class="fas fa-id-badge watermark-icon"></i>
                        
                        <div class="mb-4 position-relative d-inline-block mx-auto mt-2">
                            @if($viewUser->profile_photo)
                                <div class="profile-img-container" data-bs-toggle="modal" data-bs-target="#viewPhotoModal">
                                    <img src="{{ asset('storage/' . $viewUser->profile_photo) }}" class="rounded-circle" width="140" height="140" style="object-fit: cover;">
                                    <div class="profile-img-overlay">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </div>
                            @else
                                <div class="profile-img-container">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($viewUser->full_name) }}&background=0d6efd&color=fff&size=140&bold=true" class="rounded-circle">
                                </div>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-1 text-dark">{{ $viewUser->full_name }}</h4>
                        
                        @php
                            $roleBadge = match($viewUser->role) {
                                'Dean' => 'bg-danger bg-opacity-10 text-danger border-danger',
                                'Admin' => 'bg-primary bg-opacity-10 text-primary border-primary',
                                default => 'bg-info bg-opacity-10 text-info border-info'
                            };
                        @endphp
                        <span class="badge {{ $roleBadge }} border rounded-pill mb-4 px-4 py-2 text-uppercase fw-bold shadow-sm" style="letter-spacing: 1px;">
                            {{ $viewUser->role }}
                        </span>
                        
                        <div class="text-start mt-2 border-top pt-4">
                            <div class="surface-box rounded-4 p-3 shadow-inner border">
                                
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle-badge p-2 rounded-circle shadow-sm me-3 text-center flex-shrink-0" style="width: 38px; height: 38px;">
                                        <i class="fas fa-envelope text-primary mt-1"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</span>
                                        <span class="small fw-semibold text-dark d-block text-break">{{ $viewUser->email }}</span>
                                        @if($viewUser->email_verified_at)
                                            <i class="fas fa-check-circle text-success mt-1" style="font-size: 12px;" title="Email Verified"></i>
                                        @endif
                                    </div>
                                </div>

                                @if($viewUser->contact_number)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle-badge p-2 rounded-circle shadow-sm me-3 text-center flex-shrink-0" style="width: 38px; height: 38px;">
                                        <i class="fas fa-phone text-primary mt-1"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Contact Number</span>
                                        <span class="small fw-semibold text-dark d-block text-break">{{ $viewUser->contact_number }}</span>
                                    </div>
                                </div>
                                @endif

                                @if($viewUser->birthdate)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-circle-badge p-2 rounded-circle shadow-sm me-3 text-center flex-shrink-0" style="width: 38px; height: 38px;">
                                        <i class="fas fa-birthday-cake text-primary mt-1"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Birthdate</span>
                                        <span class="small fw-semibold text-dark d-block text-break">{{ \Carbon\Carbon::parse($viewUser->birthdate)->format('F d, Y') }}</span>
                                    </div>
                                </div>
                                @endif

                                @if($viewUser->address)
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle-badge p-2 rounded-circle shadow-sm me-3 text-center flex-shrink-0" style="width: 38px; height: 38px;">
                                        <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <span class="d-block text-muted" style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Home Address</span>
                                        <span class="small fw-semibold text-dark d-block text-break">{{ $viewUser->address }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🟢 RIGHT COLUMN: TABS AND STATS -->
                <div class="col-12 col-xl-8">
                    
                    <!-- Top Status Boxes -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-4">
                            <div class="dashboard-card p-3 text-center h-100">
                                <span class="text-muted d-block mb-2" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Account Status</span>
                                @if($viewUser->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-2 fw-bold shadow-sm"><i class="fas fa-check-circle me-1"></i> Active</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-2 fw-bold shadow-sm"><i class="fas fa-times-circle me-1"></i> Disabled</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="dashboard-card p-3 text-center h-100">
                                <span class="text-muted d-block mb-2" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Last Seen</span>
                                <span class="fw-bold text-dark fs-6">
                                    {{ $viewUser->last_seen ? \Carbon\Carbon::parse($viewUser->last_seen)->diffForHumans() : 'Never logged in' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="dashboard-card p-3 text-center h-100">
                                <span class="text-muted d-block mb-2" style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Account Created</span>
                                <span class="fw-bold text-dark fs-6">
                                    {{ $viewUser->created_at ? \Carbon\Carbon::parse($viewUser->created_at)->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Main Tab Content -->
                    <div class="dashboard-card p-4" style="border-top: 3.5px solid #10b981 !important;">
                        <i class="fas fa-chart-pie watermark-icon"></i>
                        <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4" id="pills-security-tab" data-bs-toggle="pill" data-bs-target="#pills-security" type="button" role="tab">
                                    <i class="fas fa-shield-alt me-1"></i> Security Assets
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4" id="pills-rooms-tab" data-bs-toggle="pill" data-bs-target="#pills-rooms" type="button" role="tab">
                                    <i class="fas fa-door-open me-1"></i> Assigned Rooms
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4" id="pills-activity-tab" data-bs-toggle="pill" data-bs-target="#pills-activity" type="button" role="tab">
                                    <i class="fas fa-history me-1"></i> Recent Access
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content position-relative" id="pills-tabContent" style="z-index: 2;">
                            
                            <!-- 🟢 TAB 1: Security Assets (NA-UPDATE NA ANG RECORDS) -->
                            <div class="tab-pane fade show active" id="pills-security" role="tabpanel">
                                
                                <!-- BIOMETRICS RECORD TABLE -->
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-fingerprint text-primary me-2"></i> Hardware Biometric Records</h6>
                                <div class="bg-light rounded-4 p-0 shadow-inner border border-secondary border-opacity-10 mb-4 overflow-hidden">
                                <div class="surface-box rounded-4 p-0 shadow-inner mb-4 overflow-hidden">
                                    @php
                                        // Kunin ang mismong fingerprint data ng user na ito
                                        $fps = \DB::table('fingerprints')
                                                ->join('rooms', 'fingerprints.room_id', '=', 'rooms.room_id')
                                                ->where('fingerprints.user_id', $viewUser->user_id)
                                                ->where('fingerprints.is_active', 1)
                                                ->select('fingerprints.*', 'rooms.room_name')
                                                ->get();
                                    @endphp
                                    
                                    @if($fps->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-custom align-middle mb-0 table-borderless">
                                                <thead class="table-light">
                                                <thead>
                                                    <tr>
                                                        <th class="ps-4">Laboratory</th>
                                                        <th class="text-center">Slot ID</th>
                                                        <th>Enrolled At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($fps as $fp)
                                                    <tr>
                                                        <td class="ps-4 fw-bold text-dark"><i class="fas fa-door-closed text-muted me-2"></i>{{ $fp->room_name }}</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill px-3 py-1">ID {{ $fp->template_slot }}</span>
                                                        </td>
                                                        <td class="text-muted small fw-semibold">{{ \Carbon\Carbon::parse($fp->enrolled_at)->format('M d, Y h:i A') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="p-4 text-center text-muted small fw-semibold">
                                            <i class="fas fa-info-circle me-1 opacity-50"></i> No fingerprint records found for this user.
                                        </div>
                                    @endif
                                </div>

                                <!-- PASSKEYS RECORD TABLE -->
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-key text-success me-2"></i> WebAuthn Passkeys</h6>
                                <div class="bg-light rounded-4 p-0 shadow-inner border border-secondary border-opacity-10 mb-4 overflow-hidden">
                                <div class="surface-box rounded-4 p-0 shadow-inner mb-4 overflow-hidden">
                                    @php
                                        // Safely query ang passkeys table
                                        $pks = collect();
                                        try {
                                            $pks = \DB::table('passkeys')->where('user_id', $viewUser->user_id)->get();
                                             $pks = \DB::table('passkeys')->where('user_id', $viewUser->user_id)->get();
                                        } catch(\Exception $e) { }
                                    @endphp

                                    @if($pks->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-custom align-middle mb-0 table-borderless">
                                                <thead class="table-light">
                                                <thead>
                                                    <tr>
                                                        <th class="ps-4">Device / Credential ID</th>
                                                        <th>Registered At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pks as $pk)
                                                    <tr>
                                                        <td class="ps-4 text-dark small font-monospace text-truncate" style="max-width: 250px;">
                                                            <i class="fas fa-mobile-alt text-success me-2"></i>{{ $pk->credential_id ?? 'Passkey Device' }}
                                                        </td>
                                                        <td class="text-muted small fw-semibold">{{ \Carbon\Carbon::parse($pk->created_at ?? now())->format('M d, Y h:i A') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="p-4 text-center text-muted small fw-semibold">
                                            <i class="fas fa-info-circle me-1 opacity-50"></i> No passkeys linked to this account.
                                        </div>
                                    @endif
                                </div>

                                <!-- PASSCODE / OVERRIDE PIN STATUS -->
                                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-hashtag text-warning me-2"></i> Override Passcode</h6>
                                <div class="bg-light rounded-4 p-3 shadow-inner border border-secondary border-opacity-10 d-flex align-items-center">
                                <div class="surface-box rounded-4 p-3 shadow-inner d-flex align-items-center">
                                    @if($viewUser->auth_code)
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">Passcode is Configured</h6>
                                            <p class="mb-0 text-muted small fw-semibold">This user can manually unlock assigned doors using their PIN.</p>
                                        </div>
                                    @else
                                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-3 me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark">Passcode Not Set</h6>
                                            <p class="mb-0 text-muted small fw-semibold">This user has not configured a backup PIN code.</p>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            <!-- TAB 2: Rooms -->
                            <div class="tab-pane fade" id="pills-rooms" role="tabpanel">
                                <div class="table-responsive">
                                    <table class="table table-custom align-middle mb-0 table-borderless">
                                        <thead>
                                            <tr>
                                                <th>Room Name</th>
                                                <th class="text-center">Access Level</th>
                                                <th>Valid Until</th>
                                                <th class="text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($viewUser->roomAssignments) && $viewUser->roomAssignments->count() > 0)
                                                @foreach($viewUser->roomAssignments as $assignment)
                                                    <tr>
                                                        <td>
                                                            <div class="fw-bold text-dark">{{ $assignment->room->room_name ?? 'Unknown Room' }}</div>
                                                            <div class="text-primary small fw-semibold">{{ $assignment->room->room_code ?? '' }}</div>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border text-uppercase px-3 py-1 rounded-pill" style="font-size: 10px;">{{ str_replace('_', ' ', $assignment->access_level) }}</span>
                                                        </td>
                                                        <td class="fw-semibold small text-muted">
                                                            {{ $assignment->valid_until ? \Carbon\Carbon::parse($assignment->valid_until)->format('M d, Y') : 'Lifetime Access' }}
                                                        </td>
                                                        <td class="text-center">
                                                            @if($assignment->is_active)
                                                                <span class="text-success small fw-bold"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Active</span>
                                                            @else
                                                                <span class="text-danger small fw-bold"><i class="fas fa-circle me-1" style="font-size: 8px;"></i> Inactive</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-5">
                                                        <i class="fas fa-building fa-2x mb-3 opacity-25"></i>
                                                        <p class="mb-0 fw-semibold">This user has not been assigned to any rooms yet.</p>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- TAB 3: Activity -->
                            <div class="tab-pane fade" id="pills-activity" role="tabpanel">
                                @if(isset($viewUser->auditLogs) && $viewUser->auditLogs->count() > 0)
                                    <div class="timeline mt-3">
                                        @foreach($viewUser->auditLogs->take(5) as $log)
                                            <div class="timeline-item">
                                                <h6 class="fw-bold mb-1 text-dark">
                                                    @if($log->action == 'unlock' || $log->action == 'remote_unlock')
                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2 py-1 me-2" style="font-size: 10px;">Unlocked</span>
                                                    @elseif($log->action == 'lock' || $log->action == 'remote_lock')
                                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-2 py-1 me-2" style="font-size: 10px;">Locked</span>
                                                    @else
                                                        <span class="badge bg-warning bg-opacity-10 text-dark border border-warning rounded-pill px-2 py-1 me-2" style="font-size: 10px;">Attempted Access</span>
                                                    @endif
                                                    {{ $log->room->room_name ?? 'Room' }}
                                                </h6>
                                                <p class="text-muted small mb-1 fw-semibold">
                                                    <i class="fas fa-clock text-primary me-1"></i> {{ \Carbon\Carbon::parse($log->logged_at)->format('M d, Y h:i A') }}
                                                    <span class="mx-2 text-muted opacity-50">|</span>
                                                    <i class="fas fa-bolt text-warning me-1"></i> Method: <span class="text-dark">{{ ucwords(str_replace('_', ' ', $log->method)) }}</span>
                                                </p>
                                                @if($log->notes)
                                                    <div class="bg-light p-2 rounded-3 mt-2 shadow-inner border border-secondary border-opacity-10">
                                                        <p class="mb-0 text-secondary small fst-italic"><i class="fas fa-info-circle me-1"></i> {{ $log->notes }}</p>
                                                    <div class="surface-box p-2 rounded-3 mt-2 shadow-inner">
                                                        <p class="mb-0 text-muted small fst-italic"><i class="fas fa-info-circle me-1"></i> {{ $log->notes }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="text-center mt-4 border-top pt-4">
                                        <a href="{{ route('audit.logs') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm control-btn">View Full Audit Log</a>
                                    </div>
                                @else
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-history fa-2x mb-3 opacity-25"></i>
                                        <p class="mb-0 fw-semibold">No recent door access logs found for this user.</p>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PHOTO VIEW MODAL -->
    @if($viewUser->profile_photo)
    <div class="modal fade" id="viewPhotoModal" tabindex="-1" aria-hidden="true" style="background: rgba(10, 37, 64, 0.85);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0 text-center">
                <div class="modal-header border-0 p-2 justify-content-end">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <img src="{{ asset('storage/' . $viewUser->profile_photo) }}" class="img-fluid rounded-4 shadow-lg mx-auto border border-5 border-white" style="max-height: 70vh; object-fit: cover;" alt="Profile Full View">
            </div>
        </div>
    </div>
    @endif

    @include('admin.components.footer')
</body>
</html>