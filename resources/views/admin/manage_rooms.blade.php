<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <style>
        .room-card-executive {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 22px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.035);
            transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
        }
        .room-card-executive:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08);
            border-color: rgba(37, 99, 235, 0.35);
        }

        .stat-icon-wrapper {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important;
            color: #ffffff;
        }
        .bg-gradient-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff;
        }
        .bg-gradient-secondary {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
            color: #ffffff;
        }

        .watermark-icon {
            position: absolute;
            right: -15px;
            bottom: -20px;
            font-size: 130px;
            opacity: 0.04;
            transform: rotate(-12deg);
            pointer-events: none;
            color: var(--text-main);
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
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
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
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6); }
            70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .modal-body-scroll {
            max-height: 68vh;
            overflow-y: auto;
        }
        .modal-body-scroll::-webkit-scrollbar { width: 6px; }
        .modal-body-scroll::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.4); border-radius: 10px; }

        .control-btn {
            transition: all 0.25s ease;
            font-weight: 600;
        }
        .control-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.2);
        }

        .sub-panel-box {
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            border-radius: 16px;
        }

        .feature-chip {
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.76rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
    </style>
</head>
<body>

    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            <!-- Header Title and Actions -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--text-main); letter-spacing: -0.5px;">Laboratory Infrastructure</h4>
                    <p class="mb-0 small" style="color: var(--text-muted);">Manage physical rooms, IoT node connectivity, capacities, and emergency hotspot parameters.</p>
                </div>
                <button class="btn bg-gradient-primary text-white rounded-pill px-4 py-2 fw-semibold shadow-sm control-btn d-inline-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#addRoomModal">
                    <i class="fas fa-plus"></i>
                    <span>Add New Room</span>
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm mb-4 fw-semibold d-flex align-items-center" style="border-radius: 14px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="fas fa-check-circle me-2 fs-5"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm mb-4 fw-semibold d-flex align-items-center" style="border-radius: 14px; background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                    <i class="fas fa-exclamation-circle me-2 fs-5"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="row g-3 g-md-4">
                @forelse($rooms as $room)
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="room-card-executive h-100 p-3 p-md-4 d-flex flex-column">
                            <i class="fas fa-server watermark-icon"></i>
                            
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="stat-icon-wrapper {{ $room->is_active ? 'bg-gradient-success' : 'bg-gradient-secondary' }}">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <span class="badge {{ $room->is_active ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-secondary bg-opacity-10 text-secondary border border-secondary' }} rounded-pill px-3 py-2 fw-semibold shadow-sm d-inline-flex align-items-center gap-1">
                                    @if($room->is_active)
                                        <span class="pulse-dot-success"></span>
                                        <span>Active Node</span>
                                    @else
                                        <span class="pulse-dot-danger"></span>
                                        <span>Disabled</span>
                                    @endif
                                </span>
                            </div>

                            <h5 class="fw-bold mb-1" style="color: var(--text-main);">{{ $room->room_name }}</h5>
                            <p class="small mb-3" style="color: var(--text-muted);">
                                <strong class="text-primary font-monospace">{{ $room->room_code }}</strong>
                                <span class="mx-1">•</span>
                                <i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ $room->location ?? 'No location set' }}
                            </p>
                            
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="feature-chip">
                                    <i class="fas fa-users text-primary"></i> Cap: {{ $room->capacity ?? 'N/A' }}
                                </span>
                                @if($room->is_air_conditioned)
                                    <span class="feature-chip text-info border-info border-opacity-25" style="background: rgba(6, 182, 212, 0.08);">
                                        <i class="fas fa-snowflake"></i> Climate Control
                                    </span>
                                @endif
                                @if($room->backup_ssid)
                                    <span class="feature-chip text-primary border-primary border-opacity-25" style="background: rgba(37, 99, 235, 0.08);" data-bs-toggle="tooltip" title="Backup Hotspot: {{ $room->backup_ssid }}">
                                        <i class="fas fa-wifi"></i> Hotspot
                                    </span>
                                @endif
                            </div>

                            @if($room->description)
                                <p class="small text-muted mb-3 line-clamp-2" style="font-size: 0.82rem; line-height: 1.4;">
                                    {{ Str::limit($room->description, 90) }}
                                </p>
                            @endif

                            <!-- LOCK STATE STATUS -->
                            <div class="sub-panel-box p-3 mb-4 mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0" style="font-size: 0.8rem; color: var(--text-main);">Hardware Lock State</h6>
                                        <small style="font-size: 0.72rem; color: var(--text-muted);">Physical magnetic lock</small>
                                    </div>
                                    <span class="badge {{ ($room->door_state ?? 'locked') == 'unlocked' ? 'bg-success' : 'bg-danger' }} rounded-pill px-3 py-2 shadow-sm fw-semibold">
                                        <i class="fas {{ ($room->door_state ?? 'locked') == 'unlocked' ? 'fa-lock-open' : 'fa-lock' }} me-1"></i>
                                        {{ strtoupper($room->door_state ?? 'LOCKED') }}
                                    </span>
                                </div>
                            </div>

                            <div class="pt-1">
                                <button class="btn btn-outline-primary w-100 fw-semibold rounded-pill py-2 shadow-sm control-btn edit-room-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editRoomModal"
                                        data-id="{{ $room->room_id }}"
                                        data-name="{{ $room->room_name }}"
                                        data-code="{{ $room->room_code }}"
                                        data-location="{{ $room->location }}"
                                        data-capacity="{{ $room->capacity }}"
                                        data-ac="{{ $room->is_air_conditioned }}"
                                        data-desc="{{ $room->description }}"
                                        data-ssid="{{ $room->backup_ssid }}"
                                        data-pass="{{ $room->backup_password }}"
                                        data-status="{{ $room->is_active }}"
                                        style="background: var(--bg-surface);">
                                    <i class="fas fa-sliders-h me-2"></i> Edit Configuration
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 mt-4">
                        <div class="stat-icon-wrapper bg-gradient-secondary mx-auto mb-4" style="width: 80px; height: 80px; font-size: 32px; opacity: 0.6;">
                            <i class="fas fa-building"></i>
                        </div>
                        <h4 class="fw-bold mb-2" style="color: var(--text-main);">No Laboratories Registered</h4>
                        <p class="mx-auto mb-4" style="max-width: 440px; color: var(--text-muted);">
                            There are currently no IoT-enabled rooms in the system. Click the button above to register your first laboratory node.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ADD NEW ROOM MODAL --}}
    <div class="modal fade" id="addRoomModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-header border-0 p-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-wrapper bg-gradient-primary" style="width: 40px; height: 40px; font-size: 16px; border-radius: 12px;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">Register New Laboratory</h5>
                            <small style="color: var(--text-muted);">Deploy a new smart laboratory node into the system</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('rooms.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 modal-body-scroll">
                        <div class="alert border-0 small shadow-sm fw-semibold mb-4 d-flex align-items-center gap-2" style="border-radius: 12px; background: rgba(37, 99, 235, 0.08); color: #2563eb;">
                            <i class="fas fa-info-circle fs-5"></i>
                            <span>Register the physical details and identifiers for this IoT NodeMCU door lock hardware.</span>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Laboratory Name <span class="text-danger">*</span></label>
                                <input type="text" name="room_name" class="form-control form-control-lg rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" placeholder="e.g. BSIS Computer Lab 1" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Unique Room Code <span class="text-danger">*</span></label>
                                <input type="text" name="room_code" class="form-control form-control-lg rounded-3 text-uppercase font-monospace" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" placeholder="e.g. LAB-001" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-8 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Location / Building</label>
                                <input type="text" name="location" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" placeholder="e.g. Ground Floor, Main Bldg">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Capacity (Seats)</label>
                                <input type="number" name="capacity" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" placeholder="e.g. 40">
                            </div>
                        </div>

                        <div class="sub-panel-box p-3 my-3">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_air_conditioned" id="addAC" value="1">
                                <label class="form-check-label small fw-bold ms-2" for="addAC" style="color: var(--text-main);">
                                    <i class="fas fa-snowflake text-info me-1"></i> Room is Air-Conditioned
                                </label>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Description</label>
                            <textarea name="description" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" rows="2" placeholder="Brief description of the laboratory, equipment, and usage..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill fw-semibold px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn bg-gradient-primary text-white rounded-pill fw-semibold px-4 shadow-sm control-btn">
                            <i class="fas fa-check me-1"></i> Create Room
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- EDIT ROOM MODAL --}}
    <div class="modal fade" id="editRoomModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-header border-0 p-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-wrapper bg-gradient-primary" style="width: 40px; height: 40px; font-size: 16px; border-radius: 12px;">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">Laboratory Configuration</h5>
                            <small style="color: var(--text-muted);">Update hardware mappings, Wi-Fi fallback, and operational state</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('rooms.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="room_id" id="edit_room_id">
                    
                    <div class="modal-body p-4 modal-body-scroll">
                        
                        <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-info-circle"></i> Basic Specifications
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Laboratory Name <span class="text-danger">*</span></label>
                                <input type="text" name="room_name" id="edit_room_name" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" required>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Unique Room Code <span class="text-danger">*</span></label>
                                <input type="text" name="room_code" id="edit_room_code" class="form-control rounded-3 text-uppercase font-monospace" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-8 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Location</label>
                                <input type="text" name="location" id="edit_location" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Capacity</label>
                                <input type="number" name="capacity" id="edit_capacity" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);">
                            </div>
                        </div>

                        <div class="sub-panel-box p-3 my-3">
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_air_conditioned" id="edit_ac" value="1">
                                <label class="form-check-label small fw-bold ms-2" for="edit_ac" style="color: var(--text-main);">
                                    <i class="fas fa-snowflake text-info me-1"></i> Room is Air-Conditioned
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Description</label>
                            <textarea name="description" id="edit_description" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" rows="2"></textarea>
                        </div>

                        <hr style="border-color: var(--border-color);" class="mb-4">

                        <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-wifi"></i> Hardware Backup Network (Brownout Protocol)
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Backup SSID (Hotspot Name)</label>
                                <input type="text" name="backup_ssid" id="edit_backup_ssid" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" placeholder="e.g. TPC_Emergency_AP">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Backup Password</label>
                                <input type="text" name="backup_password" id="edit_backup_password" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" placeholder="Hotspot security pass...">
                            </div>
                        </div>

                        <hr style="border-color: var(--border-color);" class="my-4">

                        <div class="sub-panel-box p-3">
                            <label class="form-label small fw-bold mb-2" style="color: var(--text-main);">Hardware Device State</label>
                            <select name="is_active" id="edit_is_active" class="form-select form-select-lg rounded-3 fw-bold" style="background: var(--bg-surface); border-color: var(--border-color); color: var(--text-main);">
                                <option value="1" class="text-success">🟢 Active / Online</option>
                                <option value="0" class="text-danger">🔴 Disabled / Offline</option>
                            </select>
                            <small class="d-block mt-2" style="font-size: 11px; color: var(--text-muted);">Disabling this will turn off all remote and biometric operations for this laboratory node.</small>
                        </div>

                    </div>
                    
                    <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between align-items-center">
                        <!-- DELETE BUTTON (Left) -->
                        <button type="button" class="btn btn-outline-danger rounded-pill fw-semibold px-4 shadow-sm" onclick="openDeleteModal()">
                            <i class="fas fa-trash-alt me-1"></i> Delete Room
                        </button>
                        
                        <!-- SAVE & CANCEL (Right) -->
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light rounded-pill fw-semibold px-4 shadow-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn bg-gradient-primary text-white rounded-pill fw-semibold px-4 shadow-sm control-btn">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    <div class="modal fade" id="deleteRoomModal" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle" style="width: 70px; height: 70px;">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--text-main);">Delete Laboratory?</h5>
                    <p class="small mb-4 fw-medium" style="color: var(--text-muted);">Are you sure you want to permanently delete <strong id="delete_room_display_name" style="color: var(--text-main);">this room</strong>? This action cannot be undone and will delete all associated logs.</p>
                    
                    <form action="{{ route('rooms.delete') }}" method="POST" class="d-flex flex-column gap-2 m-0">
                        @csrf
                        <input type="hidden" name="room_id" id="delete_room_id_input">
                        <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 fw-semibold shadow-sm">
                            <i class="fas fa-trash-alt me-2"></i> Yes, Delete It
                        </button>
                        <button type="button" class="btn btn-light w-100 rounded-pill py-2 fw-semibold shadow-sm" data-bs-dismiss="modal" onclick="backToEditModal()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('admin.components.footer')

    <script>
        // Populate Edit Modal Data
        document.querySelectorAll('.edit-room-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_room_id').value = this.dataset.id;
                document.getElementById('edit_room_name').value = this.dataset.name;
                document.getElementById('edit_room_code').value = this.dataset.code;
                document.getElementById('edit_location').value = this.dataset.location;
                document.getElementById('edit_capacity').value = this.dataset.capacity;
                document.getElementById('edit_description').value = this.dataset.desc;
                document.getElementById('edit_backup_ssid').value = this.dataset.ssid;
                document.getElementById('edit_backup_password').value = this.dataset.pass;
                document.getElementById('edit_is_active').value = this.dataset.status;
                
                // Toggle Checkbox for Aircon
                document.getElementById('edit_ac').checked = (this.dataset.ac == '1');
            });
        });

        // Delete Modal Logic
        function openDeleteModal() {
            let roomId = document.getElementById('edit_room_id').value;
            let roomName = document.getElementById('edit_room_name').value;
            
            document.getElementById('delete_room_id_input').value = roomId;
            document.getElementById('delete_room_display_name').innerText = roomName;

            var editModal = bootstrap.Modal.getInstance(document.getElementById('editRoomModal'));
            if (editModal) {
                editModal.hide();
            }
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteRoomModal'));
            deleteModal.show();
        }

        // Kapag nag-cancel sa Delete Modal, ibalik sa Edit Modal
        function backToEditModal() {
            var editModal = new bootstrap.Modal(document.getElementById('editRoomModal'));
            editModal.show();
        }
    </script>
</body>
</html>