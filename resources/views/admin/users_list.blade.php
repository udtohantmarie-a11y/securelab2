<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    
    <style>
        .user-card-executive { 
            border-radius: 22px; 
            border: 1px solid var(--border-color); 
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.035); 
            background: var(--bg-surface); 
            overflow: hidden;
        }

        .bg-gradient-primary { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%) !important; color: white; }
        .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; color: white; }
        .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important; color: white; }

        #usersTable thead th { 
            font-size: 0.72rem; 
            text-transform: uppercase; 
            letter-spacing: 0.75px; 
            font-weight: 700;
            background-color: var(--bg-subtle);
            border-bottom: 1px solid var(--border-color); 
            padding: 14px 12px;
            color: var(--text-muted);
        }
        #usersTable tbody tr { transition: background-color 0.15s ease; border-bottom: 1px solid var(--border-color); }
        #usersTable tbody tr:hover { background-color: var(--bg-subtle); }
        #usersTable td { padding: 14px 12px; color: var(--text-main); vertical-align: middle; }

        .avatar-circle-sm {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: 700;
            font-size: 1rem;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
            flex-shrink: 0;
        }

        .avatar-img-table {
            width: 44px;
            height: 44px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid var(--border-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        .action-circle-btn {
            transition: all 0.2s ease;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            color: var(--text-muted);
        }
        .action-circle-btn.edit-btn:hover { 
            background: rgba(37, 99, 235, 0.1) !important; 
            color: #2563eb !important; 
            border-color: #2563eb; 
            transform: translateY(-2px); 
        }
        .action-circle-btn.delete-btn:hover { 
            background: rgba(239, 68, 68, 0.1) !important; 
            color: #ef4444 !important; 
            border-color: #ef4444; 
            transform: translateY(-2px); 
        }

        .role-pill {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            display: inline-block;
        }
        .role-dean {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }
        .role-admin {
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
            border: 1px solid rgba(37, 99, 235, 0.25);
        }
        .role-staff {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--border-color);
            background: var(--bg-subtle);
            color: var(--text-main);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.85rem;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            background: var(--bg-surface);
            border-color: #2563eb;
            outline: none;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.82rem;
            color: var(--text-muted);
            padding: 14px 18px;
        }

        @media print {
            .dt-buttons, .sidebar, .navbar, .action-btns, .no-print { display: none !important; }
            .print-header { display: block !important; text-align: center; }
            body { background-color: white !important; }
            .main-content { margin: 0 !important; width: 100% !important; }
            .user-card-executive { box-shadow: none !important; border: none !important; }
        }
        .print-header { display: none; }
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            {{-- School Header for Reports (Visible on Print) --}}
            <div class="print-header mb-4">
                <h3 class="fw-bold">TALIBON POLYTECHNIC COLLEGE</h3>
                <h5>San Isidro, Talibon, Bohol</h5>
                <hr>
                <h4 class="text-uppercase mt-3">Registered Users Directory</h4>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--text-main); letter-spacing: -0.5px;">User Database & Personnel</h4>
                    <p class="small mb-0" style="color: var(--text-muted);">Manage system accounts, access privilege levels, and administrative credentials.</p>
                </div>
                
                <div class="d-flex flex-wrap align-items-center gap-2 no-print">
                    <button type="button" class="btn bg-gradient-success text-white rounded-pill px-4 py-2 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus me-2"></i> Add User
                    </button>
                    <div id="userTableActions"></div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 fw-semibold d-flex align-items-center" style="border-radius: 14px; background: rgba(16, 185, 129, 0.12); color: #10b981;">
                    <i class="fas fa-check-circle me-2 fs-5"></i>
                    <span>{{ session('success') }}</span>
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

            <div class="card user-card-executive mb-4">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover align-middle mb-0 w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th class="ps-4">User Profile</th>
                                <th>Email Address</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Status</th>
                                <th>Registered At</th>
                                <th class="action-btns text-center pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @if($user->profile_photo)
                                                <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="{{ $user->full_name }}" class="avatar-img-table me-3">
                                            @else
                                                <div class="avatar-circle-sm me-3">
                                                    {{ strtoupper(substr($user->full_name, 0, 1)) }}
                                                </div>
                                            @endif
                                            
                                            <div>
                                                <span class="fw-bold d-block mb-0" style="color: var(--text-main);">{{ $user->full_name }}</span>
                                                <small style="color: var(--text-muted); font-size: 11px;">ID: #{{ str_pad($user->user_id, 4, '0', STR_PAD_LEFT) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold small" style="color: var(--text-muted);"><i class="fas fa-envelope me-1 opacity-50"></i> {{ $user->email }}</span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $roleClass = match($user->role) {
                                                'Dean' => 'role-dean',
                                                'Admin' => 'role-admin',
                                                default => 'role-staff'
                                            };
                                        @endphp
                                        <span class="role-pill {{ $roleClass }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($user->is_active)
                                            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 0.72rem;">
                                                <i class="fas fa-check-circle me-1"></i>ACTIVE
                                            </span>
                                        @else
                                            <span class="badge rounded-pill px-3 py-1 fw-bold" style="background: rgba(148, 163, 184, 0.12); color: #64748b; font-size: 0.72rem;">
                                                <i class="fas fa-times-circle me-1"></i>INACTIVE
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-semibold small" style="color: var(--text-main);">
                                            {{ \Carbon\Carbon::parse($user->created_at)->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td class="action-btns text-center pe-4">
                                        <div class="d-inline-flex gap-2">
                                            <button class="action-circle-btn edit-btn edit-user-btn" 
                                                    data-id="{{ $user->user_id }}"
                                                    data-name="{{ $user->full_name }}"
                                                    data-email="{{ $user->email }}"
                                                    data-role="{{ $user->role }}"
                                                    data-active="{{ $user->is_active }}"
                                                    title="Edit User">
                                                <i class="fas fa-pen" style="font-size: 12px;"></i>
                                            </button>
                                            <button class="action-circle-btn delete-btn delete-user-btn" 
                                                    data-id="{{ $user->user_id }}"
                                                    data-name="{{ $user->full_name }}"
                                                    title="Delete User">
                                                <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="stat-icon-wrapper mx-auto mb-3" style="width: 70px; height: 70px; font-size: 28px; background: var(--bg-subtle); color: var(--text-muted); border-radius: 20px;">
                                            <i class="fas fa-users-slash"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1" style="color: var(--text-main);">No Users Found</h6>
                                        <p class="small mb-0" style="color: var(--text-muted);">The database is currently empty.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: ADD NEW USER -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-header border-0 p-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-wrapper bg-gradient-success" style="width: 40px; height: 40px; font-size: 16px; border-radius: 12px;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">Add New User</h5>
                            <small style="color: var(--text-muted);">Create a personnel credential</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" placeholder="e.g. Juan Dela Cruz" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" placeholder="e.g. juan@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Temporary Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" placeholder="Minimum 8 characters" required minlength="8">
                            <small class="d-block mt-1" style="font-size: 11px; color: var(--text-muted);"><i class="fas fa-info-circle me-1"></i> The user can change this later in their profile settings.</small>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">System Role</label>
                                <select name="role" class="form-select rounded-3 fw-semibold" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" required>
                                    <option value="Staff" selected>Staff</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Dean">Dean</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Account Status</label>
                                <select name="is_active" class="form-select rounded-3 fw-semibold" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" required>
                                    <option value="1" selected class="text-success">Active</option>
                                    <option value="0" class="text-danger">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn bg-gradient-success text-white rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="fas fa-check me-1"></i> Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT USER -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-header border-0 p-4 pb-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="stat-icon-wrapper bg-gradient-primary" style="width: 40px; height: 40px; font-size: 16px; border-radius: 12px;">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" style="color: var(--text-main);">Edit User Profile</h5>
                            <small style="color: var(--text-muted);">Modify role and account status</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold" style="color: var(--text-muted);">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edit_email" class="form-control rounded-3" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">System Role</label>
                                <select name="role" id="edit_role" class="form-select rounded-3 fw-semibold" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);">
                                    <option value="Dean">Dean</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Staff">Staff</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold" style="color: var(--text-muted);">Account Status</label>
                                <select name="is_active" id="edit_is_active" class="form-select rounded-3 fw-semibold" style="background: var(--bg-subtle); border-color: var(--border-color); color: var(--text-main);">
                                    <option value="1" class="text-success">Active</option>
                                    <option value="0" class="text-danger">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold shadow-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn bg-gradient-primary text-white rounded-pill px-4 fw-semibold shadow-sm">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: DELETE CONFIRMATION -->
    <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 22px; background: var(--bg-surface); color: var(--text-main);">
                <div class="modal-body p-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mb-3" style="width: 64px; height: 64px;">
                        <i class="fas fa-trash-alt fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2" style="color: var(--text-main);">Confirm Delete</h5>
                    <p class="small mb-4 fw-medium" style="color: var(--text-muted);">Are you sure you want to remove <strong id="delete_user_name" style="color: var(--text-main);"></strong> from the system? This action cannot be undone.</p>
                    <form action="{{ route('users.delete') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="user_id" id="delete_user_id">
                        <div class="d-flex flex-column gap-2">
                            <button type="submit" class="btn btn-danger rounded-pill fw-semibold shadow-sm py-2">Yes, Delete User</button>
                            <button type="button" class="btn btn-light rounded-pill fw-semibold shadow-sm py-2" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('admin.components.footer')

    {{-- Scripts for DataTables & Export --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#usersTable').DataTable({
                "pageLength": 10,
                "language": { "search": "Search User:" },
                buttons: [
                    {
                        extend: 'pdfHtml5',
                        title: 'SecureLab_Registered_Users',
                        className: 'btn btn-danger btn-sm rounded-pill px-3 me-2 fw-semibold',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF Export',
                        exportOptions: { columns: [0, 1, 2, 3, 4] }
                    },
                    {
                        extend: 'print',
                        className: 'btn bg-gradient-primary text-white btn-sm rounded-pill px-3 fw-semibold',
                        text: '<i class="fas fa-print me-1"></i> Print Directory',
                        exportOptions: { columns: [0, 1, 2, 3, 4] }
                    }
                ]
            });
            table.buttons().container().appendTo('#userTableActions');

            // EDIT BUTTON CLICK
            $(document).on('click', '.edit-user-btn', function() {
                $('#edit_user_id').val($(this).data('id'));
                $('#edit_full_name').val($(this).data('name'));
                $('#edit_email').val($(this).data('email'));
                $('#edit_role').val($(this).data('role'));
                $('#edit_is_active').val($(this).data('active'));
                $('#editUserModal').modal('show');
            });

            // DELETE BUTTON CLICK
            $(document).on('click', '.delete-user-btn', function() {
                $('#delete_user_id').val($(this).data('id'));
                $('#delete_user_name').text($(this).data('name'));
                $('#deleteUserModal').modal('show');
            });
        });
    </script>
</body>
</html>