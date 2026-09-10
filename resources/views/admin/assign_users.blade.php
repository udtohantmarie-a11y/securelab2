<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <title>SecureLab - User Access Management</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* 🟢 ASSIGN USERS STYLES */
        .room-access-card {
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            box-shadow: var(--shadow-sm);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        .room-access-card:hover {
            box-shadow: var(--shadow-md);
            border-color: rgba(26, 86, 219, 0.25);
        }

        .room-badge-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            background: rgba(26, 86, 219, 0.1);
            color: var(--primary-blue);
            flex-shrink: 0;
        }

        .action-btn-revoke {
            transition: all 0.2s ease;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            color: #ef4444;
        }
        .action-btn-revoke:hover {
            background-color: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444 !important;
            transform: scale(1.08);
        }

        .guideline-box {
            background-color: rgba(26, 86, 219, 0.05);
            border-left: 4px solid var(--primary-blue);
            border-radius: 12px;
        }

        /* Select2 Modern Enhancements */
        .select2-container--default .select2-selection--single {
            background-color: var(--bg-surface) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 12px !important;
            height: 48px !important;
            padding: 10px 14px !important;
            box-shadow: var(--shadow-xs);
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--text-dark) !important;
            line-height: 26px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            right: 12px !important;
        }
        .select2-dropdown {
            border: 1px solid var(--border-color) !important;
            box-shadow: var(--shadow-lg) !important;
            border-radius: 14px !important;
            overflow: hidden;
            background-color: var(--bg-surface) !important;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-blue) !important;
            color: #ffffff !important;
        }
        .select2-container--default .select2-results__option {
            color: var(--text-dark);
            padding: 10px 14px;
        }

        /* Responsive */
        @media (max-width: 767.98px) {
            .responsive-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px;
            }
            .responsive-header button {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid p-0">
            <!-- HEADER -->
            <div class="d-flex justify-content-between align-items-center mb-4 responsive-header">
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">User Access Management</h4>
                    <p class="text-muted small mb-0 mt-1">Authorize faculty, staff, and personnel permissions per laboratory room.</p>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-xs d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#assignAccessModal">
                    <i class="fas fa-plus-circle"></i> Grant New Access
                </button>
            </div>

            <!-- FLASH ALERTS -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-xs mb-4 fw-medium d-flex align-items-center gap-3" role="alert" style="border-radius: 14px; background-color: rgba(16, 185, 129, 0.12); color: #065f46; border: 1px solid rgba(16, 185, 129, 0.25);">
                    <i class="fas fa-check-circle fa-lg text-success"></i>
                    <div class="flex-grow-1">
                        <strong class="d-block" style="font-size: 13px;">Operation Successful</strong>
                        <span class="small">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-xs mb-4 fw-medium d-flex align-items-center gap-3" role="alert" style="border-radius: 14px; background-color: rgba(239, 68, 68, 0.12); color: #991b1b; border: 1px solid rgba(239, 68, 68, 0.25);">
                    <i class="fas fa-exclamation-triangle fa-lg text-danger"></i>
                    <div class="flex-grow-1">
                        <strong class="d-block" style="font-size: 13px;">Error Encountered</strong>
                        <span class="small">{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- GROUPING LOGIC (Laravel Blade) -->
            @php
                $groupedAssignments = collect($assignments)->groupBy('room_name');
            @endphp

            @forelse($groupedAssignments as $roomName => $group)
                @php $currentRoomId = $group->first()->room_id; @endphp
                
                <div class="room-access-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom responsive-header" style="border-color: var(--border-color) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="room-badge-icon shadow-xs">
                                <i class="fas fa-door-closed"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h5 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.3px;">{{ $roomName }}</h5>
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-bold" style="font-size: 11px;">
                                        <i class="fas fa-users me-1"></i>{{ $group->count() }} Authorized
                                    </span>
                                </div>
                                <span class="text-muted small">Laboratory Room Access Permissions</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill fw-bold px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#revokeAllModal_{{ $currentRoomId }}">
                            <i class="fas fa-users-slash me-1"></i> Revoke All
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-custom align-middle table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3" style="min-width: 220px;">Authorized User</th>
                                    <th class="text-center" style="min-width: 150px;">Access Level</th>
                                    <th class="text-center" style="min-width: 140px;">Valid Until</th>
                                    <th class="text-center pe-3" style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group as $as)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if(!empty($as->profile_photo))
                                                <img src="{{ asset('storage/' . $as->profile_photo) }}" alt="{{ $as->full_name }}" class="rounded-circle shadow-xs border" style="width: 38px; height: 38px; min-width: 38px; object-fit: cover; flex-shrink: 0;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($as->full_name) }}&background=1a56db&color=fff&bold=true';">
                                            @else
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-xs" style="width: 38px; height: 38px; min-width: 38px; flex-shrink: 0; font-size: 13px;">
                                                    {{ strtoupper(substr($as->full_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <span class="fw-bold text-dark d-block mb-0" style="font-size: 0.9rem;">{{ $as->full_name }}</span>
                                                <span class="text-muted" style="font-size: 11px;">{{ $as->role }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = match($as->access_level) {
                                                'full' => 'bg-success bg-opacity-10 text-success border-success border-opacity-25',
                                                'schedule_only' => 'bg-warning bg-opacity-10 text-warning border-warning border-opacity-25',
                                                default => 'bg-primary bg-opacity-10 text-primary border-primary border-opacity-25'
                                            };
                                            $badgeIcon = match($as->access_level) {
                                                'full' => 'fa-check-circle',
                                                'schedule_only' => 'fa-clock',
                                                default => 'fa-eye'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} border rounded-pill text-uppercase px-3 py-1 fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                                            <i class="fas {{ $badgeIcon }} me-1"></i>{{ str_replace('_', ' ', $as->access_level) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($as->valid_until)
                                            <span class="text-dark small fw-semibold d-block">{{ \Carbon\Carbon::parse($as->valid_until)->format('M d, Y') }}</span>
                                            <span class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($as->valid_until)->diffForHumans() }}</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border rounded-pill px-2 py-1" style="font-size: 10px;">Permanent / 24/7</span>
                                        @endif
                                    </td>
                                    <td class="text-center pe-3">
                                        <button type="button" class="btn rounded-circle shadow-xs action-btn-revoke" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#revokeModal_{{ $as->user_id }}_{{ $as->room_id }}"
                                                title="Revoke Permission">
                                            <i class="fas fa-user-minus" style="font-size: 12px;"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="room-access-card p-5 text-center mt-4">
                    <div class="stat-icon-wrapper bg-secondary bg-opacity-10 text-secondary mx-auto mb-3" style="width: 72px; height: 72px; font-size: 32px; opacity: 0.6;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">No Active Assignments</h5>
                    <p class="text-muted mb-3" style="max-width: 480px; margin: 0 auto;">No personnel have been granted laboratory room permissions yet. Click the button below to assign access.</p>
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-xs" data-bs-toggle="modal" data-bs-target="#assignAccessModal">
                        <i class="fas fa-plus-circle me-1"></i> Grant New Access
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- INDIVIDUAL REVOKE MODALS -->
    @foreach($assignments as $as)
    <div class="modal fade" id="revokeModal_{{ $as->user_id }}_{{ $as->room_id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <div class="modal-body text-center p-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3" style="width: 60px; height: 60px; background: rgba(239, 68, 68, 0.12); color: #ef4444; font-size: 24px;">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-1">Revoke Access?</h5>
                    <p class="text-muted small mb-4">Remove authorization for <strong class="text-dark">{{ $as->full_name }}</strong> in <strong>{{ $as->room_name }}</strong>?</p>
                    <div class="d-flex flex-column gap-2">
                        <form action="{{ route('users.assign.revoke', [$as->user_id, $as->room_id]) }}" method="POST" class="w-100 mb-0">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-bold shadow-xs">Yes, Revoke Access</button>
                        </form>
                        <button type="button" class="btn btn-light w-100 rounded-pill py-2 fw-semibold text-muted shadow-xs" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- REVOKE ALL MODALS PER ROOM -->
    @if(isset($groupedAssignments))
        @foreach($groupedAssignments as $roomName => $group)
            @php 
                $currentRoomId = $group->first()->room_id; 
                $userCount = $group->count();
            @endphp
            <div class="modal fade" id="revokeAllModal_{{ $currentRoomId }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                        <div class="modal-body text-center p-4 p-md-5">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mx-auto mb-3" style="width: 72px; height: 72px; background: rgba(239, 68, 68, 0.12); color: #ef4444; font-size: 30px;">
                                <i class="fas fa-users-slash"></i>
                            </div>
                            <h4 class="fw-bold text-dark mb-2">Revoke All Room Access?</h4>
                            <p class="text-muted mb-4">Are you sure you want to remove all <strong>{{ $userCount }} user(s)</strong> authorized for <strong class="text-dark">{{ $roomName }}</strong>? This action will immediately revoke their physical lock credentials.</p>
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center w-100">
                                <form action="{{ route('users.assign.revokeAll', $currentRoomId) }}" method="POST" class="mb-0 w-100 order-sm-2">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger py-2 rounded-pill fw-bold shadow-xs w-100">Yes, Revoke All</button>
                                </form>
                                <button type="button" class="btn btn-light py-2 rounded-pill fw-semibold text-muted shadow-xs w-100 order-sm-1" data-bs-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- MODAL: GRANT NEW ACCESS -->
    <div class="modal fade" id="assignAccessModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                <div class="modal-header border-0 p-4 pb-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Grant Laboratory Access</h5>
                            <span class="text-muted small">Authorize a personnel member for laboratory entry</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.assign.save') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="small fw-bold text-dark mb-2 d-block">Authorized Personnel</label>
                            <select name="user_id" class="form-select searchable-select" style="width: 100%" required>
                                <option value="" selected disabled>Search name or select user...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->user_id }}">{{ $user->full_name }} ({{ $user->role }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="small fw-bold text-dark mb-2">Target Laboratory Room</label>
                            <select name="room_id" class="form-select py-2 px-3 fw-semibold border" style="border-radius: 12px; height: 48px;" required>
                                <option value="" selected disabled>Select laboratory room...</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->room_id }}">{{ $room->room_name }} (Capacity: {{ $room->capacity }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="small fw-bold text-dark mb-2">Permission Level</label>
                                <select name="access_level" class="form-select py-2 px-3 fw-semibold border" style="border-radius: 12px; height: 48px;">
                                    <option value="full">Full Access (24/7)</option>
                                    <option value="schedule_only">Schedule Only</option>
                                    <option value="readonly">Read Only</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="small fw-bold text-dark mb-2">Expiry Date (Optional)</label>
                                <input type="date" name="valid_until" class="form-control py-2 px-3 fw-semibold border" style="border-radius: 12px; height: 48px;">
                            </div>
                        </div>

                        <!-- Access Level Guidelines Box -->
                        <div class="guideline-box p-3 mb-0 shadow-xs">
                            <h6 class="fw-bold text-primary mb-2" style="font-size: 12px;"><i class="fas fa-info-circle me-1"></i> Access Level Guidelines</h6>
                            <ul class="text-muted small mb-0 ps-3" style="font-size: 11px; line-height: 1.5;">
                                <li><strong class="text-dark">Full Access:</strong> Unlocks door 24/7, configures keypad PINs, enrolls biometrics, and triggers remote unlock.</li>
                                <li><strong class="text-dark">Schedule Only:</strong> Physical unlock credentials remain active until the specified <em>Expiry Date</em>.</li>
                                <li><strong class="text-dark">Read Only:</strong> View-only monitoring rights without solenoid door activation.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 d-flex flex-column flex-sm-row gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-xs w-100 order-sm-2">Confirm Authorization</button>
                        <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-muted shadow-xs w-100 order-sm-1" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('admin.components.footer')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.searchable-select').select2({
                dropdownParent: $('#assignAccessModal'),
                placeholder: "Search name or select user...",
                allowClear: true
            });
        });
    </script>
</body>
</html>