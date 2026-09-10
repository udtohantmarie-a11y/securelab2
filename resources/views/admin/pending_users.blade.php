<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <title>Pending Accounts - SecureLab</title>
    <style>
        .pending-card-executive { 
            border-radius: 22px; 
            border: 1px solid var(--border-color); 
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.035); 
            background: var(--bg-surface); 
            overflow: hidden;
        }

        .control-btn {
            transition: all 0.25s ease;
            font-weight: 600;
        }
        .control-btn:hover {
            transform: translateY(-2px);
        }

        .table-pending thead th {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.75px;
            font-weight: 700;
            background-color: var(--bg-subtle);
            border-bottom: 1px solid var(--border-color);
            padding: 14px 16px;
            color: var(--text-muted);
        }
        .table-pending tbody tr {
            transition: background-color 0.15s ease;
            border-bottom: 1px solid var(--border-color);
        }
        .table-pending tbody tr:hover {
            background-color: var(--bg-subtle);
        }
        .table-pending td {
            padding: 14px 16px;
            color: var(--text-main);
            vertical-align: middle;
        }

        .avatar-initial-sm {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
            flex-shrink: 0;
        }

        .role-badge-pending {
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50px;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--text-main); letter-spacing: -0.5px;">Pending User Approvals</h4>
                    <p class="small mb-0" style="color: var(--text-muted);">Review pending registration submissions before granting biometric and lab system access.</p>
                </div>
            </div>

            <div class="card pending-card-executive mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-pending align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">User Details</th>
                                    <th>Email Address</th>
                                    <th class="text-center">Requested Role</th>
                                    <th>Submission Date</th>
                                    <th class="pe-4 text-end">Administrative Review</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingUsers as $user)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-initial-sm me-3">
                                                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold" style="color: var(--text-main); font-size: 0.92rem;">{{ $user->full_name }}</h6>
                                                    <small style="color: var(--text-muted); font-size: 11px;">Ref: #{{ str_pad($user->user_id, 4, '0', STR_PAD_LEFT) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="small fw-semibold" style="color: var(--text-muted);"><i class="fas fa-envelope me-1 opacity-50"></i>{{ $user->email }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="role-badge-pending">{{ $user->role }}</span>
                                        </td>
                                        <td>
                                            <span class="small fw-medium" style="color: var(--text-muted);">
                                                {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y • h:i A') }}
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <form action="{{ route('users.update') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                                                <input type="hidden" name="full_name" value="{{ $user->full_name }}">
                                                <input type="hidden" name="email" value="{{ $user->email }}">
                                                <input type="hidden" name="role" value="{{ $user->role }}">
                                                <input type="hidden" name="is_active" value="1">
                                                
                                                <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1 fw-semibold control-btn me-1 shadow-sm" onclick="return confirm('Approve this user account?');">
                                                    <i class="fas fa-check me-1"></i> Approve
                                                </button>
                                            </form>

                                            <form action="{{ route('users.delete') }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="user_id" value="{{ $user->user_id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-semibold control-btn shadow-sm" onclick="return confirm('Reject and delete this user registration?');">
                                                    <i class="fas fa-times me-1"></i> Reject
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="py-3">
                                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 70px; height: 70px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                                                    <i class="fas fa-check-double fa-2x"></i>
                                                </div>
                                                <h5 class="fw-bold mb-1" style="color: var(--text-main);">Queue is Clean!</h5>
                                                <p class="small mb-0" style="color: var(--text-muted);">There are no pending accounts waiting for administrative approval.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SYSTEM ALERT MODAL POP-UP -->
    @if(session('success') || session('error'))
    <div class="modal fade" id="systemAlertModal" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-body text-center p-4">
                    @if(session('success'))
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style="width: 64px; height: 64px;">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-main);">Successful</h5>
                        <p class="small mb-4 fw-medium" style="color: var(--text-muted);">{{ session('success') }}</p>
                    @endif

                    @if(session('error'))
                        <div class="mb-3">
                            <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle" style="width: 64px; height: 64px;">
                                <i class="fas fa-exclamation-circle fa-2x"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2" style="color: var(--text-main);">Error</h5>
                        <p class="small mb-4 fw-medium" style="color: var(--text-muted);">{{ session('error') }}</p>
                    @endif
                    
                    <button type="button" class="btn btn-light w-100 rounded-pill py-2 fw-semibold shadow-sm" data-bs-dismiss="modal">Dismiss</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @include('admin.components.footer')

    @if(session('success') || session('error'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var systemAlertModal = new bootstrap.Modal(document.getElementById('systemAlertModal'));
            systemAlertModal.show();
        });
    </script>
    @endif

</body>
</html>