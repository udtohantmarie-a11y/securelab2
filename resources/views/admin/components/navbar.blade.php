<!-- 🟢 SECURELAB MODERN MOBILE HEADER -->
<div class="mobile-header shadow-sm d-lg-none mb-3 px-3 py-2">
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-dark btn-sm rounded-3 p-2 d-flex align-items-center justify-content-center" id="mobileToggle" style="width: 36px; height: 36px;" title="Open Menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-2 d-flex align-items-center justify-content-center text-white" style="width: 30px; height: 30px; background: linear-gradient(135deg, #2563eb, #1d4ed8); font-size: 14px;">
                <i class="fas fa-fingerprint"></i>
            </div>
            <span class="fw-bold text-dark mb-0" style="letter-spacing: 0.5px; font-size: 15px;">SECURE<span class="text-primary">LAB</span></span>
        </div>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <!-- MOBILE MESSAGES -->
        <div class="dropdown">
            <div class="icon-btn cursor-pointer" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Messages">
                <i class="fas fa-comment-dots text-primary small"></i>
                <span class="icon-badge msg-count-badge" style="display: {{ ($totalUnreadMessages ?? 0) > 0 ? 'flex' : 'none' }}">{{ $totalUnreadMessages ?? 0 }}</span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg p-0 border-0 dropdown-responsive">
                <li class="p-2 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top">
                    <span class="fw-bold small text-primary d-flex align-items-center gap-1">
                        <i class="fas fa-envelope-open-text"></i> Messages
                    </span>
                    <button type="button" class="btn btn-link p-0 small text-decoration-none text-muted mark-all-read-msgs-btn" style="font-size: 11px;"><i class="fas fa-check-double me-1"></i>Mark all read</button>
                </li>
                <div class="dropdown-scrollable message-dropdown-container">
                    @forelse($messages as $msg)
                        <li style="position: relative; z-index: 1010;">
                            <a class="dropdown-item small py-2 rounded-3" href="{{ route('messages.index', ['user' => $msg->contact_id]) }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fw-bold text-dark">{{ $msg->contact_name }}</div>
                                    @if(isset($msg->unread_count) && $msg->unread_count > 0)
                                        <span class="badge bg-success x-small-badge">New</span>
                                    @endif
                                </div>
                                <div class="text-muted text-truncate" style="font-size: 11px;">
                                    @if(Str::startsWith($msg->last_message, '[IMAGE]:')) <i class="fas fa-image me-1"></i>Sent a photo
                                    @elseif(Str::startsWith($msg->last_message, '[FILE]:')) <i class="fas fa-paperclip me-1"></i>Sent a file
                                    @else {{ $msg->last_message }} @endif
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="p-3 text-center small text-muted no-messages-placeholder">No new messages</li>
                    @endforelse
                </div>
                <li><a href="{{ route('messages.index') }}" class="view-all-link">View All Messages</a></li>
            </ul>
        </div>

        <!-- MOBILE NOTIFICATIONS -->
        <div class="dropdown">
            <div class="icon-btn cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                <i class="fas fa-bell text-primary small"></i>
                <span class="icon-badge bg-danger notif-count-badge" style="display: {{ ($totalUnreadNotifs ?? 0) > 0 ? 'flex' : 'none' }}">{{ $totalUnreadNotifs ?? 0 }}</span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg p-0 border-0 dropdown-responsive">
                <li class="p-2 border-bottom d-flex justify-content-between align-items-center bg-light rounded-top">
                    <span class="fw-bold small text-primary d-flex align-items-center gap-1">
                        <i class="fas fa-bell"></i> Alerts & Activity
                    </span>
                    <button type="button" class="btn btn-link p-0 small text-decoration-none text-muted mark-all-notifs-btn" style="font-size: 11px;"><i class="fas fa-check-double me-1"></i>Mark all read</button>
                </li>
                <div class="dropdown-scrollable notification-dropdown-container">
                    @forelse($notifications as $notif)
                        @php
                            $icon = 'fa-info-circle'; $iconColor = 'text-primary'; $bgLight = 'bg-primary bg-opacity-10';
                            $link = route('audit.logs');
                            
                            if (Str::contains($notif->title, 'Door Left Open')) {
                                $icon = 'fa-door-open'; $iconColor = 'text-warning'; $bgLight = 'bg-warning bg-opacity-10'; $link = route('dashboard.alerts');
                            } elseif ($notif->type === 'intrusion_alert' || Str::contains($notif->title, 'CRITICAL')) {
                                $icon = 'fa-exclamation-triangle'; $iconColor = 'text-danger'; $bgLight = 'bg-danger bg-opacity-10'; $link = route('dashboard.alerts');
                            } elseif ($notif->type === 'doorbell') {
                                $icon = 'fa-bell-concierge'; $iconColor = 'text-warning'; $bgLight = 'bg-warning bg-opacity-10';
                            } elseif (Str::contains($notif->title, 'Web Dashboard') || Str::contains($notif->title, 'remotely')) {
                                $icon = 'fa-mobile-alt'; $iconColor = 'text-primary'; $bgLight = 'bg-primary bg-opacity-10';
                            } elseif (Str::contains($notif->title, 'Passcode')) {
                                $icon = 'fa-keyboard'; $iconColor = 'text-info'; $bgLight = 'bg-info bg-opacity-10';
                            } elseif (Str::contains($notif->title, 'Fingerprint Enrolled')) {
                                $icon = 'fa-fingerprint'; $iconColor = 'text-success'; $bgLight = 'bg-success bg-opacity-10';
                            } elseif (Str::contains($notif->title, 'Fingerprint Removed')) {
                                $icon = 'fa-fingerprint'; $iconColor = 'text-secondary'; $bgLight = 'bg-secondary bg-opacity-10';
                            } elseif ($notif->type === 'door_unlocked') {
                                $icon = Str::contains($notif->title, 'Fingerprint') ? 'fa-fingerprint' : 'fa-unlock'; 
                                $iconColor = 'text-success'; $bgLight = 'bg-success bg-opacity-10';
                            } elseif ($notif->type === 'door_locked') {
                                $icon = 'fa-lock'; $iconColor = 'text-secondary'; $bgLight = 'bg-secondary bg-opacity-10';
                            }
                        @endphp
                        <li style="position: relative; z-index: 1010;">
                            <a class="dropdown-item py-2 border-bottom text-wrap single-notif-link" href="{{ $link }}" data-notif-id="{{ $notif->notif_id }}">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="mt-1 p-2 rounded-circle {{ $bgLight }} {{ $iconColor }}">
                                        <i class="fas {{ $icon }}"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block" style="font-size: 12px;">{{ $notif->title }}</span>
                                        <span class="text-muted d-block mb-1" style="font-size: 11px;">{{ $notif->body }}</span>
                                        <small class="text-muted" style="font-size: 9px;"><i class="far fa-clock me-1"></i>{{ \Carbon\Carbon::parse($notif->sent_at)->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="p-3 text-center small text-muted no-notifs-placeholder">No hardware alerts or doorbell events</li>
                    @endforelse
                </div>
                <li><a href="{{ route('dashboard.alerts') }}" class="view-all-link">View All Alerts</a></li>
            </ul>
        </div>

        <!-- MOBILE PROFILE LINK -->
        <a href="{{ route('profile.index') }}" class="d-inline-flex position-relative" title="My Profile">
            @if(Auth::user()->profile_photo)
                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="rounded-circle border shadow-sm" width="34" height="34" style="object-fit: cover;">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=1a56db&color=fff" class="rounded-circle border shadow-sm" width="34" height="34">
            @endif
            <span class="profile-online-dot" style="width: 8px; height: 8px; bottom: 0; right: 0;"></span>
        </a>
    </div>
</div>

<!-- 🟢 SECURELAB MODERN DESKTOP TOPBAR -->
<header class="desktop-topbar mb-4 d-none d-lg-flex align-items-center justify-content-between p-3 rounded-4 bg-white border shadow-sm">
    <div class="d-flex align-items-center gap-3">
        <button class="icon-btn border-0 shadow-xs" id="desktopToggle" title="Toggle Sidebar">
            <i class="fas fa-bars-staggered"></i>
        </button>
        <div>
            @php
                $hour = \Carbon\Carbon::now()->hour;
                $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
            @endphp
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ $greeting }}, {{ Auth::user()->full_name }}!</h4>
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 fw-bold" style="font-size: 10px;">
                    <i class="fas fa-shield-alt me-1"></i>{{ Auth::user()->role }}
                </span>
            </div>
            <p class="text-muted small mb-0 d-flex align-items-center gap-2" style="font-size: 11px;">
                <span><i class="far fa-calendar-alt text-primary me-1"></i>{{ \Carbon\Carbon::now()->format('l, M d, Y') }}</span>
                <span class="text-muted">•</span>
                <span class="text-success fw-semibold"><i class="fas fa-circle text-success me-1" style="font-size: 7px;"></i>Real-time Monitoring Active</span>
            </p>
        </div>
    </div>
    
    <div class="d-flex align-items-center gap-3">
        <!-- DESKTOP MESSAGES DROPDOWN -->
        <div class="dropdown">
            <div class="icon-btn cursor-pointer bg-white shadow-xs" data-bs-toggle="dropdown" aria-expanded="false" title="Messages">
                <i class="fas fa-comment-dots text-primary"></i>
                <span class="icon-badge bg-danger msg-count-badge" style="display: {{ ($totalUnreadMessages ?? 0) > 0 ? 'flex' : 'none' }}">{{ $totalUnreadMessages ?? 0 }}</span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg p-0 border-0 mt-2" style="border-radius: 16px; width: 340px; z-index: 1050; overflow: hidden;">
                <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <span class="small fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-envelope-open-text text-primary"></i> Direct Messages
                    </span>
                    <button type="button" class="btn btn-link p-0 small text-decoration-none text-muted mark-all-read-msgs-btn" style="font-size: 11px;"><i class="fas fa-check-double me-1"></i>Mark all read</button>
                </li>
                <div class="dropdown-scrollable message-dropdown-container">
                    @forelse($messages as $msg)
                        <li style="position: relative; z-index: 1060;" data-contact-id="{{ $msg->contact_id }}" data-unread="{{ $msg->unread_count }}">
                            <a class="dropdown-item px-3 py-2 border-bottom text-wrap" href="{{ route('messages.index', ['user' => $msg->contact_id]) }}">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark d-block text-truncate" style="max-width: 190px; font-size: 12px;">{{ $msg->contact_name }}</span> 
                                    @if(isset($msg->unread_count) && $msg->unread_count > 0)
                                        <span class="badge bg-success x-small-badge">New</span>
                                    @endif
                                </div>
                                <span class="text-muted text-truncate d-block" style="font-size: 11px;">
                                    @if(Str::startsWith($msg->last_message, '[IMAGE]:')) <i class="fas fa-image me-1"></i>Sent a photo
                                    @elseif(Str::startsWith($msg->last_message, '[FILE]:')) <i class="fas fa-paperclip me-1"></i>Sent a file
                                    @else {{ Str::limit($msg->last_message, 42) }} @endif
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="p-4 text-center small text-muted no-messages-placeholder">
                            <i class="fas fa-comment-slash text-muted opacity-50 fa-2x mb-2 d-block"></i>
                            No recent conversations
                        </li>
                    @endforelse
                </div>
                <li><a href="{{ route('messages.index') }}" class="view-all-link">View All Messages</a></li>
            </ul>
        </div>
        
        <!-- DESKTOP NOTIFICATIONS DROPDOWN -->
        <div class="dropdown">
            <div class="icon-btn cursor-pointer bg-white shadow-xs" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                <i class="fas fa-bell text-primary"></i>
                <span class="icon-badge bg-danger notif-count-badge" style="display: {{ ($totalUnreadNotifs ?? 0) > 0 ? 'flex' : 'none' }}">{{ $totalUnreadNotifs ?? 0 }}</span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg p-0 border-0 mt-2" style="border-radius: 16px; width: 360px; z-index: 1050; overflow: hidden;">
                <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <span class="small fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="fas fa-shield-alt text-primary"></i> Notifications & Alerts
                    </span>
                    <button type="button" class="btn btn-link p-0 small text-decoration-none text-muted mark-all-notifs-btn" style="font-size: 11px;"><i class="fas fa-check-double me-1"></i>Mark all read</button>
                </li>
                <div class="dropdown-scrollable notification-dropdown-container">
                    @forelse($notifications as $notif)
                        @php
                            $icon = 'fa-info-circle'; $iconColor = 'text-primary'; $bgLight = 'bg-primary bg-opacity-10';
                            $link = route('audit.logs');
                            
                            if (Str::contains($notif->title, 'Door Left Open')) {
                                $icon = 'fa-door-open'; $iconColor = 'text-warning'; $bgLight = 'bg-warning bg-opacity-10'; $link = route('dashboard.alerts');
                            } elseif ($notif->type === 'intrusion_alert' || Str::contains($notif->title, 'CRITICAL')) {
                                $icon = 'fa-exclamation-triangle'; $iconColor = 'text-danger'; $bgLight = 'bg-danger bg-opacity-10'; $link = route('dashboard.alerts');
                            } elseif ($notif->type === 'doorbell') {
                                $icon = 'fa-bell-concierge'; $iconColor = 'text-warning'; $bgLight = 'bg-warning bg-opacity-10';
                            } elseif (Str::contains($notif->title, 'Web Dashboard') || Str::contains($notif->title, 'remotely')) {
                                $icon = 'fa-mobile-alt'; $iconColor = 'text-primary'; $bgLight = 'bg-primary bg-opacity-10';
                            } elseif (Str::contains($notif->title, 'Passcode')) {
                                $icon = 'fa-keyboard'; $iconColor = 'text-info'; $bgLight = 'bg-info bg-opacity-10';
                            } elseif (Str::contains($notif->title, 'Fingerprint Enrolled')) {
                                $icon = 'fa-fingerprint'; $iconColor = 'text-success'; $bgLight = 'bg-success bg-opacity-10';
                            } elseif (Str::contains($notif->title, 'Fingerprint Removed')) {
                                $icon = 'fa-fingerprint'; $iconColor = 'text-secondary'; $bgLight = 'bg-secondary bg-opacity-10';
                            } elseif ($notif->type === 'door_unlocked') {
                                $icon = Str::contains($notif->title, 'Fingerprint') ? 'fa-fingerprint' : 'fa-unlock';
                                $iconColor = 'text-success'; $bgLight = 'bg-success bg-opacity-10';
                            } elseif ($notif->type === 'door_locked') {
                                $icon = 'fa-lock'; $iconColor = 'text-secondary'; $bgLight = 'bg-secondary bg-opacity-10';
                            }
                        @endphp
                        <li style="position: relative; z-index: 1060;">
                            <a class="dropdown-item px-3 py-2 border-bottom text-wrap single-notif-link" href="{{ $link }}" data-notif-id="{{ $notif->notif_id }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="mt-1 p-2 rounded-circle {{ $bgLight }} {{ $iconColor }} flex-shrink-0" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                        <i class="fas {{ $icon }}"></i>
                                    </div>
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="fw-bold text-dark d-block" style="font-size: 12px; line-height: 1.2;">{{ $notif->title }}</span>
                                            <small class="text-muted ms-2 text-end" style="font-size: 9px; min-width: 45px;">{{ \Carbon\Carbon::parse($notif->sent_at)->diffForHumans(null, true, true) }}</small>
                                        </div>
                                        <span class="text-muted d-block mt-1" style="font-size: 11px;">{{ $notif->body }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="p-4 text-center small text-muted no-notifs-placeholder">
                            <i class="fas fa-bell-slash text-muted opacity-50 fa-2x mb-2 d-block"></i>
                            No hardware alerts or doorbell events
                        </li>
                    @endforelse
                </div>
                <li><a href="{{ route('dashboard.alerts') }}" class="view-all-link">View All Notifications</a></li>
            </ul>
        </div>

        <!-- PROFILE CHIP -->
        <a href="{{ route('profile.index') }}" class="user-profile-chip d-flex align-items-center gap-2 ps-2 pe-3 py-1 rounded-pill border text-decoration-none shadow-xs" title="My Profile">
            <div class="position-relative">
                @if(Auth::user()->profile_photo)
                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="rounded-circle border" width="36" height="36" style="object-fit: cover;">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=1a56db&color=fff" class="rounded-circle border" width="36" height="36">
                @endif
                <span class="profile-online-dot"></span>
            </div>
            <div class="text-start d-none d-xl-block">
                <span class="d-block fw-bold text-dark" style="font-size: 12px; line-height: 1.2;">{{ Auth::user()->full_name }}</span>
                <span class="d-block text-muted" style="font-size: 10px;">{{ Auth::user()->role }}</span>
            </div>
            <i class="fas fa-chevron-right text-muted ms-1 d-none d-xl-inline" style="font-size: 9px;"></i>
        </a>
    </div>
</header>

<!-- 🟢 GLOBAL URGENT INTRUSION MODAL -->
<div class="modal fade" id="globalUrgentModal" tabindex="-1" data-bs-backdrop="static" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-center" style="border-radius: 24px;">
            <div class="modal-body p-5">
                <div id="urgentModalIcon" class="mb-4"></div>
                <h3 id="urgentModalTitle" class="fw-bold mb-2"></h3>
                <p id="urgentModalBody" class="text-muted mb-4"></p>
                <div class="d-grid gap-2">
                    <button type="button" id="acknowledgeAlertBtn" class="btn btn-primary rounded-pill py-3 fw-bold shadow-sm" data-bs-dismiss="modal">
                        Acknowledge Alert
                    </button>
                    <a href="{{ route('dashboard.alerts') }}" id="viewAlertDetailsBtn" class="btn btn-light rounded-pill py-2 fw-bold text-muted">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🟢 DOORBELL LIVE ALERT MODAL -->
<div class="modal fade" id="doorbellLiveModal" tabindex="-1" data-bs-backdrop="static" style="z-index: 10000;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-center" style="border-radius: 24px; background: linear-gradient(145deg, #ffffff 0%, #fffbeb 100%);">
            <div class="modal-body p-5">
                <div class="mb-4">
                    <div class="rounded-circle bg-warning bg-opacity-15 d-inline-flex p-4 mb-3" style="box-shadow: 0 0 25px rgba(245, 158, 11, 0.4); animation: pulse 1.5s infinite;">
                        <i class="fas fa-bell fa-4x text-warning fa-shake"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-2 text-dark">Someone is at the door!</h3>
                <p class="text-muted mb-4">A visitor just rang the laboratory doorbell.</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-warning rounded-pill py-3 fw-bold shadow-sm text-dark" id="dismissDoorbellBtn" data-bs-dismiss="modal">
                        <i class="fas fa-check-circle me-2"></i>I got it
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<audio id="doorbellChime" src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" preload="auto"></audio>

<style>
    .cursor-pointer { cursor: pointer; }
    .x-small { font-size: 10px; }
    .x-small-badge { font-size: 9px; padding: 2px 6px; border-radius: 5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .desktop-topbar {
        background-color: var(--bg-surface) !important;
        border-color: var(--border-color) !important;
    }
    
    .user-profile-chip {
        background-color: var(--bg-surface) !important;
        border-color: var(--border-color) !important;
        transition: all 0.2s ease;
    }
    .user-profile-chip:hover {
        border-color: rgba(26, 86, 219, 0.35) !important;
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    .profile-online-dot {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #10b981;
        border: 2px solid #ffffff;
    }

    .dropdown-responsive { width: 85vw !important; max-width: 350px !important; border-radius: 16px !important; margin-top: 10px !important; }
    .dropdown-scrollable { max-height: 320px; overflow-y: auto; position: relative; z-index: 1005; }
    
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
        70% { transform: scale(1.08); box-shadow: 0 0 0 15px rgba(245, 158, 11, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    /* Dark Mode Overrides Scoped */
    body.dark-mode .profile-online-dot {
        border-color: var(--bg-surface) !important;
    }
    body.dark-mode .user-profile-chip {
        background-color: var(--bg-surface) !important;
        border-color: var(--border-color) !important;
    }
    body.dark-mode .desktop-topbar {
        background-color: var(--bg-surface) !important;
        border-color: var(--border-color) !important;
    }
</style>

<script>
    function triggerGlobalAlertModal(notif) {
        if (notif.type === 'doorbell') {
            const doorbellModalEl = document.getElementById('doorbellLiveModal');
            const doorbellModal = new bootstrap.Modal(doorbellModalEl);
            const chime = document.getElementById('doorbellChime');
            
            chime.play().catch(e => console.log('Audio autoplay blocked by browser:', e));
            
            document.getElementById('dismissDoorbellBtn').onclick = function() {
                fetch('{{ route("messages.read") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ notif_id: notif.notif_id })
                });
                chime.pause();
                chime.currentTime = 0;
            };

            doorbellModal.show();
            return;
        }

        const modalEl = document.getElementById('globalUrgentModal');
        const modal = new bootstrap.Modal(modalEl);
        
        const iconDiv = document.getElementById('urgentModalIcon');
        const titleEl = document.getElementById('urgentModalTitle');
        const bodyEl = document.getElementById('urgentModalBody');
        const ackBtn = document.getElementById('acknowledgeAlertBtn');
        const viewBtn = document.getElementById('viewAlertDetailsBtn');

        if (notif.title.includes('Door Left Open')) {
            iconDiv.innerHTML = '<i class="fas fa-door-open fa-4x text-warning fa-shake"></i>';
            titleEl.className = 'fw-bold mb-2 text-warning';
        } else if (notif.type === 'intrusion_alert' || notif.title.includes('Invalid')) {
            iconDiv.innerHTML = '<i class="fas fa-exclamation-triangle fa-4x text-danger fa-fade"></i>';
            titleEl.className = 'fw-bold mb-2 text-danger';
        } else {
            iconDiv.innerHTML = '<i class="fas fa-shield-alt fa-4x text-primary"></i>';
            titleEl.className = 'fw-bold mb-2 text-primary';
        }

        titleEl.innerText = notif.title;
        bodyEl.innerText = notif.body;

        let markAsRead = function() {
            fetch('{{ route("messages.read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ notif_id: notif.notif_id })
            });
        };

        ackBtn.onclick = markAsRead;
        viewBtn.onclick = markAsRead;

        modal.show();
    }

    document.addEventListener('click', function(e) {
        let notifLink = e.target.closest('.single-notif-link');
        if (notifLink) {
            e.preventDefault(); 
            let notifId = notifLink.getAttribute('data-notif-id');
            let targetUrl = notifLink.getAttribute('href');

            fetch('{{ route("messages.read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ notif_id: notifId })
            }).then(() => {
                window.location.href = targetUrl; 
            }).catch(() => {
                window.location.href = targetUrl; 
            });
        }
    });

    document.querySelectorAll('.mark-all-read-msgs-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fetch('{{ route("messages.read") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ sender_id: 'all' })
            }).then(() => location.reload());
        });
    });

    document.querySelectorAll('.mark-all-notifs-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fetch('{{ route("messages.read") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ sender_id: 'clear_all_notifications' })
            }).then(() => location.reload());
        });
    });

    // 5-second Silent Polling for Unread Messages and Alerts
    setInterval(() => {
        fetch('{{ route("messages.unreadCount") }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.querySelectorAll('.msg-count-badge').forEach(badge => {
                    badge.innerText = data.unread_count;
                    badge.style.display = data.unread_count > 0 ? 'flex' : 'none';
                });

                document.querySelectorAll('.notif-count-badge').forEach(badge => {
                    badge.innerText = data.unread_notifs_count;
                    badge.style.display = data.unread_notifs_count > 0 ? 'flex' : 'none';
                });

                if(data.recent_notifications) {
                    let notifHtml = '';
                    let lastGlobalAlertId = parseInt(localStorage.getItem('lastGlobalAlertId')) || 0;

                    if(data.recent_notifications.length > 0) {
                        data.recent_notifications.forEach(notif => {

                            if (notif.is_read == 0 && (notif.type === 'doorbell' || notif.type === 'intrusion_alert') && parseInt(notif.notif_id) > lastGlobalAlertId) {
                                localStorage.setItem('lastGlobalAlertId', notif.notif_id); 
                                triggerGlobalAlertModal(notif);
                            }

                            let icon = 'fa-info-circle'; let iconColor = 'text-primary'; let bgLight = 'bg-primary bg-opacity-10'; let link = '{{ route("audit.logs") }}';

                            if (notif.title.includes('Door Left Open')) {
                                icon = 'fa-door-open'; iconColor = 'text-warning'; bgLight = 'bg-warning bg-opacity-10'; link = '{{ route("dashboard.alerts") }}';
                            } else if (notif.type === 'intrusion_alert' || notif.title.includes('CRITICAL')) {
                                icon = 'fa-exclamation-triangle'; iconColor = 'text-danger'; bgLight = 'bg-danger bg-opacity-10'; link = '{{ route("dashboard.alerts") }}';
                            } else if (notif.type === 'doorbell') {
                                icon = 'fa-bell-concierge'; iconColor = 'text-warning'; bgLight = 'bg-warning bg-opacity-10';
                            } else if (notif.title.includes('Web Dashboard') || notif.title.includes('remotely')) {
                                icon = 'fa-mobile-alt'; iconColor = 'text-primary'; bgLight = 'bg-primary bg-opacity-10';
                            } else if (notif.title.includes('Passcode')) {
                                icon = 'fa-keyboard'; iconColor = 'text-info'; bgLight = 'bg-info bg-opacity-10';
                            } else if (notif.title.includes('Fingerprint Enrolled')) {
                                icon = 'fa-fingerprint'; iconColor = 'text-success'; bgLight = 'bg-success bg-opacity-10';
                            } else if (notif.title.includes('Fingerprint Removed')) {
                                icon = 'fa-fingerprint'; iconColor = 'text-secondary'; bgLight = 'bg-secondary bg-opacity-10';
                            } else if (notif.type === 'door_unlocked') {
                                icon = notif.title.includes('Fingerprint') ? 'fa-fingerprint' : 'fa-unlock';
                                iconColor = 'text-success'; bgLight = 'bg-success bg-opacity-10';
                            } else if (notif.type === 'door_locked') {
                                icon = 'fa-lock'; iconColor = 'text-secondary'; bgLight = 'bg-secondary bg-opacity-10';
                            }
                            
                            let timeDisplay = notif.time_ago ? notif.time_ago : 'Just now';

                            notifHtml += `
                            <li style="position: relative; z-index: 1060;">
                                <a class="dropdown-item px-3 py-2 border-bottom text-wrap single-notif-link" href="${link}" data-notif-id="${notif.notif_id}">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="mt-1 p-2 rounded-circle ${bgLight} ${iconColor} flex-shrink-0" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; font-size: 13px;">
                                            <i class="fas ${icon}"></i>
                                        </div>
                                        <div class="w-100">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <span class="fw-bold text-dark d-block" style="font-size: 12px; line-height: 1.2;">${notif.title}</span>
                                                <small class="text-muted ms-2 text-end" style="font-size: 9px; min-width: 45px;">${timeDisplay}</small>
                                            </div>
                                            <span class="text-muted d-block mt-1" style="font-size: 11px;">${notif.body}</span>
                                        </div>
                                    </div>
                                </a>
                            </li>`;
                        });
                    } else {
                        notifHtml = '<li class="p-4 text-center small text-muted no-notifs-placeholder"><i class="fas fa-bell-slash text-muted opacity-50 fa-2x mb-2 d-block"></i>No hardware alerts or doorbell events</li>';
                    }

                    document.querySelectorAll('.notification-dropdown-container').forEach(container => {
                        container.innerHTML = notifHtml;
                    });
                }
            }
        })
        .catch(err => console.error('Silent polling error:', err));
    }, 5000);
</script>