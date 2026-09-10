<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <style>
        .profile-card-executive { 
            border-radius: 22px; 
            border: 1px solid var(--border-color); 
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.035); 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
            position: relative; 
            overflow: hidden; 
            background: var(--bg-surface); 
        }

        .bg-gradient-primary { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important; color: white; }
        .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: white; }
        .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important; color: white; }

        .watermark-icon { 
            position: absolute; 
            right: -15px; 
            bottom: -20px; 
            font-size: 130px; 
            opacity: 0.03; 
            transform: rotate(-12deg); 
            pointer-events: none; 
            color: var(--text-main);
        }

        .profile-img-container { 
            position: relative; 
            display: inline-block; 
            cursor: zoom-in; 
            border-radius: 50%; 
            padding: 5px; 
            background: var(--bg-surface); 
            border: 2px solid var(--border-color);
            box-shadow: 0 8px 24px rgba(0,0,0,0.08); 
        }
        .profile-img-overlay { 
            position: absolute; 
            top: 5px; 
            left: 5px; 
            right: 5px; 
            bottom: 5px; 
            border-radius: 50%; 
            background: rgba(15, 23, 42, 0.65); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            opacity: 0; 
            transition: 0.3s ease; 
        }
        .profile-img-container:hover .profile-img-overlay { opacity: 1; }
        .profile-img-overlay i { color: white; font-size: 26px; }

        .nav-pills-executive .nav-link { 
            border-radius: 12px; 
            color: var(--text-muted); 
            font-weight: 600; 
            padding: 10px 22px; 
            margin-right: 8px;
            transition: all 0.25s ease; 
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            font-size: 0.88rem;
        }
        .nav-pills-executive .nav-link:hover:not(.active) { 
            background-color: var(--bg-surface); 
            color: #2563eb; 
            transform: translateY(-2px);
        }
        .nav-pills-executive .nav-link.active { 
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important; 
            color: #ffffff !important; 
            border-color: #2563eb;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.25); 
        }

        .asset-box-container {
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            overflow: hidden;
        }

        .sub-info-row {
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .control-btn { 
            transition: all 0.25s ease; 
            font-weight: 600; 
        }
        .control-btn:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2); 
        }
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            <div class="mb-4">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-2 fw-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">
                        <i class="fas fa-shield-halved me-1"></i> IDENTITY & CREDENTIAL SECURITY
                    </span>
                    <span class="text-muted small">•</span>
                    <span class="text-muted small fw-medium">Active Session Profile</span>
                </div>
                <h4 class="fw-bold mb-1" style="color: var(--text-main); letter-spacing: -0.5px;">Account Settings & Credentials</h4>
                <p class="small mb-0" style="color: var(--text-muted);">Manage your personal identity, biometric enrollment assets, and password security.</p>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 fw-semibold d-flex align-items-center" style="border-radius: 14px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="fas fa-check-circle me-2 fs-5"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 fw-semibold d-flex align-items-center" style="border-radius: 14px; background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                    <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 fw-semibold" style="border-radius: 14px; background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <!-- LEFT COLUMN: PROFILE SUMMARY -->
                <div class="col-lg-4 col-md-5">
                    <div class="profile-card-executive p-4 text-center h-100" style="border-top: 3.5px solid #2563eb;">
                        <i class="fas fa-id-badge watermark-icon"></i>
                        
                        <div class="mb-3 position-relative d-inline-block mx-auto mt-2">
                            @if(Auth::user()->profile_photo)
                                <div class="profile-img-container" data-bs-toggle="modal" data-bs-target="#viewPhotoModal">
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="rounded-circle" width="130" height="130" style="object-fit: cover;">
                                    <div class="profile-img-overlay">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </div>
                            @else
                                <div class="profile-img-container">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=2563eb&color=fff&size=130&bold=true" class="rounded-circle">
                                </div>
                            @endif
                        </div>

                        <h5 class="fw-bold mb-1" style="color: var(--text-main);">{{ Auth::user()->full_name }}</h5>
                        
                        @php
                            $roleClass = match(Auth::user()->role) {
                                'Dean' => 'bg-danger bg-opacity-10 text-danger border-danger',
                                'Admin' => 'bg-primary bg-opacity-10 text-primary border-primary',
                                default => 'bg-info bg-opacity-10 text-info border-info'
                            };
                        @endphp
                        <span class="badge {{ $roleClass }} border rounded-pill mb-3 px-4 py-2 text-uppercase fw-bold shadow-sm" style="letter-spacing: 0.75px; font-size: 0.72rem;">
                            {{ Auth::user()->role }}
                        </span>
                        
                        @if(Auth::user()->profile_photo)
                            <div class="mt-1 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-4 fw-semibold shadow-sm control-btn" data-bs-toggle="modal" data-bs-target="#removePhotoModal">
                                    <i class="fas fa-trash-alt me-1"></i> Remove Photo
                                </button>
                            </div>
                        @endif

                        <div class="text-start mt-3 pt-3" style="border-top: 1px solid var(--border-color);">
                            <div class="sub-info-row p-3">
                                
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 36px; height: 36px; background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <span class="d-block text-uppercase" style="font-size: 10px; font-weight: 700; color: var(--text-muted);">Email Address</span>
                                        <span class="small fw-semibold d-block text-truncate" style="color: var(--text-main);">{{ Auth::user()->email }}</span>
                                    </div>
                                </div>

                                @if(Auth::user()->contact_number)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 36px; height: 36px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <span class="d-block text-uppercase" style="font-size: 10px; font-weight: 700; color: var(--text-muted);">Contact Number</span>
                                        <span class="small fw-semibold d-block text-truncate" style="color: var(--text-main);">{{ Auth::user()->contact_number }}</span>
                                    </div>
                                </div>
                                @endif

                                @if(Auth::user()->birthdate)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 36px; height: 36px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                        <i class="fas fa-birthday-cake"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <span class="d-block text-uppercase" style="font-size: 10px; font-weight: 700; color: var(--text-muted);">Birthdate</span>
                                        <span class="small fw-semibold d-block text-truncate" style="color: var(--text-main);">{{ \Carbon\Carbon::parse(Auth::user()->birthdate)->format('F d, Y') }}</span>
                                    </div>
                                </div>
                                @endif

                                @if(Auth::user()->address)
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 36px; height: 36px; background: rgba(148, 163, 184, 0.15); color: var(--text-muted);">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <span class="d-block text-uppercase" style="font-size: 10px; font-weight: 700; color: var(--text-muted);">Home Address</span>
                                        <span class="small fw-semibold d-block text-truncate" style="color: var(--text-main);">{{ Auth::user()->address }}</span>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: FORMS AREA -->
                <div class="col-lg-8 col-md-7">
                    <div class="profile-card-executive p-4 h-100" style="border-top: 3.5px solid #10b981;">
                        <i class="fas fa-sliders-h watermark-icon"></i>
                        <ul class="nav nav-pills nav-pills-executive mb-4 pb-3 flex-nowrap overflow-auto" id="pills-tab" role="tablist" style="border-bottom: 1px solid var(--border-color); white-space: nowrap;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-personal-tab" data-bs-toggle="pill" data-bs-target="#pills-personal" type="button" role="tab">
                                    <i class="fas fa-user-edit me-2"></i>Personal Info
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-assets-tab" data-bs-toggle="pill" data-bs-target="#pills-assets" type="button" role="tab">
                                    <i class="fas fa-shield-alt me-2"></i>Security Assets
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-password-tab" data-bs-toggle="pill" data-bs-target="#pills-password" type="button" role="tab">
                                    <i class="fas fa-key me-2"></i>Change Password
                                </button>
                            </li>
                        </ul>
                        
                        <div class="tab-content position-relative" id="pills-tabContent" style="z-index: 2;">
                            
                            <!-- PERSONAL INFO TAB -->
                            <div class="tab-pane fade show active" id="pills-personal" role="tabpanel">
                                <form action="{{ route('profile.update.info') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Update Profile Photo</label>
                                            <input type="file" name="profile_photo" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" accept="image/*">
                                            <small class="d-block mt-1" style="font-size: 11px; color: var(--text-muted);"><i class="fas fa-info-circle me-1"></i> Max size: 5MB (JPEG, PNG, JPG). Leave empty to retain current avatar.</small>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" name="full_name" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" value="{{ Auth::user()->full_name }}" required>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Contact Number</label>
                                            <input type="text" name="contact_number" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" value="{{ Auth::user()->contact_number }}" placeholder="e.g. +639123456789">
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Birthdate</label>
                                            <input type="date" name="birthdate" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" value="{{ Auth::user()->birthdate ? \Carbon\Carbon::parse(Auth::user()->birthdate)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Address</label>
                                            <textarea name="address" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" rows="3" placeholder="Enter your full home address">{{ Auth::user()->address }}</textarea>
                                        </div>
                                    </div>
                                    <div class="text-end pt-4 mt-4" style="border-top: 1px solid var(--border-color);">
                                        <button type="submit" class="btn bg-gradient-primary text-white rounded-pill px-4 py-2 fw-semibold shadow-sm control-btn">
                                            <i class="fas fa-save me-1"></i> Update Profile
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- SECURITY ASSETS TAB -->
                            <div class="tab-pane fade" id="pills-assets" role="tabpanel">
                                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--text-main);">
                                    <i class="fas fa-fingerprint text-primary"></i> Hardware Biometric Records
                                </h6>
                                <div class="asset-box-container mb-4">
                                    @php
                                        $fps = \DB::table('fingerprints')
                                                ->join('rooms', 'fingerprints.room_id', '=', 'rooms.room_id')
                                                ->where('fingerprints.user_id', Auth::id())
                                                ->where('fingerprints.is_active', 1)
                                                ->select('fingerprints.*', 'rooms.room_name')
                                                ->get();
                                    @endphp
                                    
                                    @if($fps->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead>
                                                    <tr style="background: var(--bg-surface); border-bottom: 1px solid var(--border-color);">
                                                        <th class="ps-4" style="font-size: 0.72rem; text-transform: uppercase; color: var(--text-muted);">Laboratory</th>
                                                        <th class="text-center" style="font-size: 0.72rem; text-transform: uppercase; color: var(--text-muted);">Slot ID</th>
                                                        <th style="font-size: 0.72rem; text-transform: uppercase; color: var(--text-muted);">Enrolled At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($fps as $fp)
                                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                                        <td class="ps-4 fw-bold" style="color: var(--text-main);"><i class="fas fa-door-closed text-muted me-2"></i>{{ $fp->room_name }}</td>
                                                        <td class="text-center">
                                                            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: rgba(37, 99, 235, 0.1); color: #2563eb; border: 1px solid rgba(37, 99, 235, 0.25);">ID {{ $fp->template_slot }}</span>
                                                        </td>
                                                        <td class="small fw-semibold" style="color: var(--text-muted);">{{ \Carbon\Carbon::parse($fp->enrolled_at)->format('M d, Y • h:i A') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="p-4 text-center small fw-semibold" style="color: var(--text-muted);">
                                            <i class="fas fa-info-circle me-1 opacity-50"></i> You have no fingerprint records registered for laboratory locks.
                                        </div>
                                    @endif
                                </div>

                                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--text-main);">
                                    <i class="fas fa-key text-success"></i> WebAuthn Passkeys
                                </h6>
                                <div class="asset-box-container mb-4">
                                    @php
                                        $pks = collect();
                                        try {
                                            $pks = \DB::table('passkeys')->where('user_id', Auth::id())->get();
                                        } catch(\Exception $e) { }
                                    @endphp

                                    @if($pks->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead>
                                                    <tr style="background: var(--bg-surface); border-bottom: 1px solid var(--border-color);">
                                                        <th class="ps-4" style="font-size: 0.72rem; text-transform: uppercase; color: var(--text-muted);">Device / Credential ID</th>
                                                        <th style="font-size: 0.72rem; text-transform: uppercase; color: var(--text-muted);">Registered At</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pks as $pk)
                                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                                        <td class="ps-4 small font-monospace text-truncate" style="max-width: 250px; color: var(--text-main);">
                                                            <i class="fas fa-mobile-alt text-success me-2"></i>{{ $pk->credential_id ?? 'Passkey Device' }}
                                                        </td>
                                                        <td class="small fw-semibold" style="color: var(--text-muted);">{{ \Carbon\Carbon::parse($pk->created_at ?? now())->format('M d, Y • h:i A') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="p-4 text-center small fw-semibold" style="color: var(--text-muted);">
                                            <i class="fas fa-info-circle me-1 opacity-50"></i> No passkeys linked to your administrator account.
                                        </div>
                                    @endif
                                </div>

                                <h6 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: var(--text-main);">
                                    <i class="fas fa-hashtag text-warning"></i> Override Passcode
                                </h6>
                                <div class="sub-info-row p-3 d-flex align-items-center">
                                    @if(Auth::user()->auth_code)
                                        <div class="rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold" style="color: var(--text-main); font-size: 0.9rem;">Passcode is Active</h6>
                                            <p class="mb-0 small fw-semibold" style="color: var(--text-muted);">You can manually authorize laboratory entry via hardware keypad using your designated PIN.</p>
                                        </div>
                                    @else
                                        <div class="rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: rgba(148, 163, 184, 0.15); color: var(--text-muted);">
                                            <i class="fas fa-times"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold" style="color: var(--text-main); font-size: 0.9rem;">Passcode Not Set</h6>
                                            <p class="mb-0 small fw-semibold" style="color: var(--text-muted);">No backup keypad override code is currently assigned to your account.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- CHANGE PASSWORD TAB -->
                            <div class="tab-pane fade" id="pills-password" role="tabpanel">
                                <form action="{{ route('profile.update.password') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold" style="color: var(--text-muted);">Current Password <span class="text-danger">*</span></label>
                                        <input type="password" name="current_password" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" required placeholder="Verify current credentials">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold" style="color: var(--text-muted);">New Password <span class="text-danger">*</span></label>
                                        <input type="password" name="new_password" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" required minlength="8" placeholder="Must be at least 8 characters">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label small fw-bold" style="color: var(--text-muted);">Confirm New Password <span class="text-danger">*</span></label>
                                        <input type="password" name="new_password_confirmation" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" required minlength="8" placeholder="Re-type your new password">
                                    </div>
                                    <div class="text-end pt-4 mt-4" style="border-top: 1px solid var(--border-color);">
                                        <button type="submit" class="btn bg-gradient-danger text-white rounded-pill px-4 py-2 fw-semibold shadow-sm control-btn">
                                            <i class="fas fa-key me-1"></i> Change Password
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW PHOTO MODAL -->
    @if(Auth::user()->profile_photo)
    <div class="modal fade" id="viewPhotoModal" tabindex="-1" aria-hidden="true" style="background: rgba(15, 23, 42, 0.85);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-transparent border-0 text-center">
                <div class="modal-header border-0 p-2 justify-content-end">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="img-fluid rounded-4 shadow-lg mx-auto border border-4 border-white" style="max-height: 70vh; object-fit: cover;" alt="Profile Full View">
            </div>
        </div>
    </div>

    <!-- REMOVE PHOTO MODAL -->
    <div class="modal fade" id="removePhotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-4 pt-0">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mb-3" style="width: 64px; height: 64px;">
                        <i class="fas fa-trash-alt fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--text-main);">Remove Photo?</h5>
                    <p class="small mb-4 fw-medium" style="color: var(--text-muted);">Your profile photo will be permanently deleted and replaced with an avatar initial.</p>
                    <form action="{{ route('profile.remove.photo') }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-center gap-2">
                            <button type="button" class="btn btn-light rounded-pill flex-grow-1 fw-semibold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger rounded-pill flex-grow-1 fw-semibold shadow-sm">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    @include('admin.components.footer')
</body>
</html>