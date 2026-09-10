<!-- 🟢 SECURELAB MODERN DASHBOARD FOOTER -->
<footer class="mt-auto py-3 border-top" style="position: relative; z-index: 10; background: var(--bg-surface); border-color: var(--border-color) !important;">
    <div class="container-fluid px-4">
        <div class="row align-items-center gy-2">
            
            <!-- Left Content: School Identity & Copyright -->
            <div class="col-md-6 text-center text-md-start">
                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                    <img src="{{ asset('assets/img/tpc-logo.jpg') }}" alt="TPC Logo" width="36" height="36" class="rounded-circle shadow-xs border border-2 border-white" onerror="this.src='https://ui-avatars.com/api/?name=TPC&background=007613&color=fff'">
                    <div>
                        <p class="mb-0 small fw-bold text-dark" style="letter-spacing: 0.3px; line-height: 1.2;">Talibon Polytechnic College</p>
                        <p class="mb-0 text-muted" style="font-size: 11px;">© 2026 SecureLab IoT Access Control System. All rights reserved.</p>
                    </div>
                </div>
            </div>

            <!-- Right Content: System Info & Status -->
            <div class="col-md-6 text-center text-md-end">
                <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-3 flex-wrap">
                    <span class="text-muted small d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                        <i class="fas fa-shield-alt text-primary"></i> <strong class="text-dark">SecureLab Smart Access Control</strong>
                    </span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold d-inline-flex align-items-center gap-2 shadow-xs" style="font-size: 10px; letter-spacing: 0.5px;">
                        <span class="spinner-grow spinner-grow-sm text-success" style="width: 7px; height: 7px;" role="status"></span>
                        Hardware & Cloud Online
                    </span>
                </div>
            </div>
            
        </div>
    </div>
</footer>

<!-- 🟢 SECURE LOGOUT MODAL -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                        <i class="fas fa-power-off fa-2x"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-1 text-dark">Ready to Leave?</h5>
                <p class="text-muted small mb-4">Confirm to terminate your secure session, <strong class="text-dark">{{ Auth::user()->full_name ?? 'User' }}</strong>.</p>
                <div class="d-flex flex-column gap-2">
                    <form action="{{ route('logout') }}" method="POST" class="w-100 m-0" id="secureLogoutForm">
                        @csrf
                        <button type="submit" class="btn text-white w-100 rounded-pill py-2 fw-bold shadow-sm" id="confirmLogoutBtn" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); border: none;">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout Now
                        </button>
                    </form>
                    <button type="button" class="btn btn-light w-100 rounded-pill py-2 fw-semibold text-muted shadow-xs" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements Selection
        const mobileToggle = document.getElementById('mobileToggle');
        const desktopToggle = document.getElementById('desktopToggle');
        const sidebar = document.getElementById('sidebar');
        const body = document.body;

        // 1. Desktop Toggle Logic (Collapse/Expand Sidebar)
        if (desktopToggle) {
            desktopToggle.addEventListener('click', function(e) {
                e.preventDefault();
                body.classList.toggle('sidebar-collapsed');
                // I-save ang preference sa localStorage para hindi bumalik sa dati pag refresh
                const isCollapsed = body.classList.contains('sidebar-collapsed');
                localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
            });
        }

        // Restore sidebar state from localStorage
        if (localStorage.getItem('sidebarState') === 'collapsed') {
            body.classList.add('sidebar-collapsed');
        }

        // 2. Mobile Toggle Logic (Hamburger Menu Slide-in)
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        if (mobileToggle) {
            mobileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (sidebar) {
                    sidebar.classList.toggle('active');
                    if (sidebarBackdrop) {
                        sidebarBackdrop.classList.toggle('active', sidebar.classList.contains('active'));
                    }
                }
            });
        }

        // Close sidebar when clicking backdrop
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', function() {
                if (sidebar) sidebar.classList.remove('active');
                sidebarBackdrop.classList.remove('active');
            });
        }

        // 3. Close sidebar when clicking outside (Para sa Mobile View)
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 992 && sidebar && sidebar.classList.contains('active')) {
                if (!sidebar.contains(e.target) && e.target !== mobileToggle && !mobileToggle?.contains(e.target)) {
                    sidebar.classList.remove('active');
                    if (sidebarBackdrop) sidebarBackdrop.classList.remove('active');
                }
            }
        });

        // 4. Auto-cleanup on Window Resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992 && sidebar) {
                sidebar.classList.remove('active');
                if (sidebarBackdrop) sidebarBackdrop.classList.remove('active');
            }
        });

        // 5. Tooltip Initialization
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // 🟢 6. Hard Reset Logout Logic (Pumipigil sa CSRF & Passkey Cache Issue)
        const logoutForm = document.getElementById('secureLogoutForm');
        if (logoutForm) {
            logoutForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Pigilan ang normal na page reload
                
                const btn = document.getElementById('confirmLogoutBtn');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging out...';
                btn.disabled = true;

                // Ipadala ang logout request sa background
                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(() => {
                    // Linisin ang anumang naiwan sa frontend storage
                    sessionStorage.clear();
                    
                    // Awtomatikong mag-redirect sa front page gamit ang unique timestamp.
                    // Pipilitin nito ang browser na kumuha ng panibagong index.blade.php na may bagong CSRF token.
                    window.location.replace('/?reload=' + new Date().getTime());
                }).catch(() => {
                    // Failsafe
                    window.location.replace('/');
                });
            });
        }

        // 🟢 7. Global WAI-ARIA & Modal Focus Sanitizer (Chrome 122+ / Bootstrap 5 Fix)
        // Fixes: "Blocked aria-hidden on an element because its descendant retained focus"
        // Ensures that no focused element remains inside a modal when Bootstrap hides it and applies aria-hidden="true".
        document.addEventListener('hide.bs.modal', function (event) {
            if (document.activeElement && event.target && event.target.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        }, true);

        // Preemptive blur on any dismiss button click before Bootstrap sets aria-hidden="true"
        document.addEventListener('click', function (event) {
            const dismissTrigger = event.target.closest('[data-bs-dismiss="modal"], .btn-close');
            if (dismissTrigger) {
                dismissTrigger.blur();
            }
        }, true);

        // Extra fallback cleanup when modal is completely hidden
        document.addEventListener('hidden.bs.modal', function (event) {
            if (document.activeElement && event.target && event.target.contains(document.activeElement)) {
                document.activeElement.blur();
            }
        });
    });
</script>

<style>
    /* Pinapataas ang z-index para hindi matakpan ng main content ang clicks */
    .dropdown-menu {
        z-index: 1060 !important;
    }
    
    /* Smooth transition para sa mobile sidebar */
    #sidebar {
        transition: all 0.3s ease-in-out;
    }

    /* Hand pointer para sa lahat ng dropdown toggles */
    [data-bs-toggle="dropdown"], [data-bs-toggle="modal"] {
        cursor: pointer !important;
    }

    /* Fix para sa mobile scroll locking */
    body.sidebar-collapsed {
        overflow-x: hidden;
    }
</style>