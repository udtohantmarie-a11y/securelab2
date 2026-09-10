<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <!-- 🟢 ADDED: DataTables CSS for the Advanced Table -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <style>
        /* 🟢 MODERN EXECUTIVE REPORTS ENHANCEMENTS */
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

        .bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%); color: white; }

        .watermark-icon { 
            position: absolute; 
            right: -15px; 
            bottom: -20px; 
            font-size: 130px; 
            opacity: 0.02; 
            transform: rotate(-15deg); 
            pointer-events: none; 
            color: var(--text-main, #000);
        }

        .control-btn { transition: all 0.25s ease; font-weight: 700; letter-spacing: 0.5px; }
        .control-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(13, 110, 253, 0.18); }

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
        .table-custom tbody tr:hover { background-color: rgba(13, 110, 253, 0.03); }
        .table-custom td { border-bottom: 1px solid var(--border-color, #f1f5f9); padding: 14px 12px; color: var(--text-main, #1e293b); }

        .surface-box {
            background-color: var(--bg-subtle, #f8fafc) !important;
            border: 1px solid var(--border-color, rgba(0,0,0,0.06)) !important;
        }

        .signature-line {
            border-bottom: 1.5px solid #000;
            width: 80%;
            margin: 40px auto 8px auto;
        }

        /* 🖨️ STRICT PRINT STYLES FOR OFFICIAL HARDCOPY AUDIT */
        @media print {
            body * { visibility: hidden; }
            #printableDocument, #printableDocument * { visibility: visible; }
            #printableDocument { position: absolute; left: 0; top: 0; width: 100%; margin: 0; padding: 0; }
            
            .no-print, .dataTables_filter, .dataTables_info, .dataTables_paginate, .dataTables_length, .btn-action-view, .watermark-icon { 
                display: none !important; 
            }
            
            @page { margin: 1cm; size: landscape; }
            body { background-color: white !important; color: #000 !important; font-family: "Times New Roman", Arial, sans-serif !important; }
            
            .dashboard-card { box-shadow: none !important; border: none !important; padding: 0 !important; margin: 0 !important; border-radius: 0 !important; background: white !important; }
            
            table { border-collapse: collapse !important; width: 100% !important; margin-top: 15px !important; }
            table, th, td { border: 1px solid #000 !important; }
            th, td { padding: 6px 8px !important; text-align: left !important; font-size: 11px !important; color: #000 !important; }
            th { background-color: #e9ecef !important; -webkit-print-color-adjust: exact; color-adjust: exact; }
            
            .badge { background: transparent !important; color: #000 !important; border: none !important; padding: 0 !important; font-weight: normal !important; font-size: 11px !important; }
            .text-muted, .text-primary, .text-success, .text-danger, .text-warning, .text-info, .text-secondary, .text-dark { color: #000 !important; }
            .d-flex.align-items-center { display: block !important; }
            img.rounded-circle { display: none !important; } 

            .print-signatures {
                display: flex !important;
                page-break-inside: avoid;
                margin-top: 40px;
            }
        }
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 no-print">
                <div>
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">System Access Reports</h4>
                    <p class="text-muted small mb-0 mt-1">Comprehensive record of all laboratory access, biometric scans, and overrides.</p>
                </div>
                
                <div class="d-flex gap-2">
                    <button id="btnPrintReport" class="btn bg-gradient-primary text-white rounded-pill px-4 py-2 shadow-sm fw-bold control-btn">
                        <i class="fas fa-print me-2"></i> Print Official Report
                    </button>
                </div>
            </div>

            <!-- 🟢 ADVANCED FILTER TOOLBAR (No-Print) -->
            <div class="dashboard-card p-3 p-md-4 mb-4 no-print border-start border-4 border-primary">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-3 gap-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-filter text-primary me-2"></i>Filter & Search Records</h6>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3 quick-filter" data-range="today">Today</button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3 quick-filter" data-range="month">This Month</button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill fw-bold px-3 quick-filter" data-range="all">All Time</button>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Date From</label>
                        <input type="date" id="filterDateFrom" class="form-control py-2 shadow-inner border-0" style="border-radius: 12px; background-color: var(--bg-subtle, #f8fafc) !important; color: var(--text-main, #1e293b) !important; border: 1px solid var(--border-color, #e2e8f0) !important;">
                    </div>
                    <div class="col-12 col-sm-6 col-md-3">
                        <label class="form-label small fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Date To</label>
                        <input type="date" id="filterDateTo" class="form-control py-2 shadow-inner border-0" style="border-radius: 12px; background-color: var(--bg-subtle, #f8fafc) !important; color: var(--text-main, #1e293b) !important; border: 1px solid var(--border-color, #e2e8f0) !important;">
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="form-label small fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Action</label>
                        <select id="filterAction" class="form-select py-2 shadow-inner border-0" style="border-radius: 12px; background-color: var(--bg-subtle, #f8fafc) !important; color: var(--text-main, #1e293b) !important; border: 1px solid var(--border-color, #e2e8f0) !important;">
                            <option value="">All Actions</option>
                            <option value="Unlocked">Unlocked</option>
                            <option value="Locked">Locked</option>
                            <option value="Failed Attempt">Failed Attempt</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-md-2">
                        <label class="form-label small fw-bold text-muted mb-1" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Method</label>
                        <select id="filterMethod" class="form-select py-2 shadow-inner border-0" style="border-radius: 12px; background-color: var(--bg-subtle, #f8fafc) !important; color: var(--text-main, #1e293b) !important; border: 1px solid var(--border-color, #e2e8f0) !important;">
                            <option value="">All Methods</option>
                            <option value="Biometric">Biometric (Fingerprint)</option>
                            <option value="Keypad PIN">Keypad PIN</option>
                            <option value="Web Dashboard">Web Dashboard</option>
                            <option value="Exit Button">Exit Button</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                        <button type="button" id="btnResetFilters" class="btn btn-outline-secondary w-100 rounded-pill fw-bold py-2 shadow-sm control-btn">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- 🟢 OFFICIAL REPORT DOCUMENT -->
            <div class="dashboard-card p-4" id="printableDocument">
                <i class="fas fa-file-invoice watermark-icon"></i>
                <div class="card-body p-0">
                    
                    <div class="d-none d-print-block text-center mb-4 pb-2 border-bottom border-dark">
                        <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                            <img src="{{ asset('assets/img/tpc-logo.jpg') }}" alt="TPC Logo" style="width: 55px; height: 55px; object-fit: contain; border-radius: 50%;" onerror="this.style.display='none';">
                            <img src="{{ asset('assets/img/bsis-logo.jpg') }}" alt="BSIS Logo" style="width: 55px; height: 55px; object-fit: contain; border-radius: 50%;" onerror="this.style.display='none';">
                        </div>
                        <div style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Republic of the Philippines</div>
                        <h4 class="fw-bold mb-0" style="color: black !important; font-size: 17px; letter-spacing: 0.5px;">{{ cache('set_institution_name', 'Talibon Polytechnic College') }}</h4>
                        <div style="font-size: 11px; color: #222;">Province of Bohol • Municipality of Talibon</div>
                        <div style="font-size: 12px; font-weight: 700; color: #111;">BACHELOR OF SCIENCE IN INFORMATION SYSTEMS (BSIS)</div>
                        <div style="font-size: 11px; color: #333;">San Isidro, Talibon, Bohol</div>
                        <h3 class="fw-bold text-uppercase mt-2 mb-1" style="font-size: 18px; letter-spacing: 1px;">SCHOOL TRANSACTION LOG BOOK</h3>
                        <div class="fw-semibold small text-uppercase" style="font-size: 11px;">BSIS Official Laboratory Audit & Access History Report</div>
                        <div class="small fw-semibold mt-1" style="font-size: 11px;">Coverage: <span id="printCoverageText">All Time Records</span> • As of {{ now()->format('F d, Y h:i A') }}</div>
                    </div>

                    <div class="table-responsive position-relative" style="z-index: 2;">
                        <table class="table table-custom align-middle mb-0 table-borderless" id="reportsTable">
                            <thead>
                                <tr>
                                    <th class="ps-3 border-0 rounded-start">Date & Time</th>
                                    <th class="border-0">Personnel Name</th>
                                    <th class="text-center border-0">Role</th>
                                    <th class="border-0">Laboratory</th>
                                    <th class="border-0">Action</th>
                                    <th class="border-0">Method</th>
                                    <th class="text-center border-0 rounded-end no-print">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $log)
                                    <tr>
                                        <!-- 🟢 DITO NATIN KUKUNIN ANG TIMESTAMP SA JS -->
                                        <td class="ps-3" data-order="{{ \Carbon\Carbon::parse($log->logged_at)->timestamp }}">
                                            <div class="fw-bold text-dark" style="font-size: 13px;">{{ \Carbon\Carbon::parse($log->logged_at)->format('M d, Y') }}</div>
                                            <div class="text-muted" style="font-size: 11px;">{{ \Carbon\Carbon::parse($log->logged_at)->format('h:i:s A') }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                @if(!empty($log->profile_photo))
                                                    <img src="{{ asset('storage/' . $log->profile_photo) }}" class="rounded-circle no-print shadow-sm" width="32" height="32" style="object-fit: cover;" alt="Avatar" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($log->user_name) }}&background=0d6efd&color=fff&bold=true';">
                                                @else
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($log->user_name) }}&background=0d6efd&color=fff&bold=true" class="rounded-circle no-print shadow-sm" width="32" height="32" alt="Avatar">
                                                @endif
                                                <span class="fw-bold text-dark" style="font-size: 13px;">{{ $log->user_name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-3 py-1 rounded-pill" style="font-size: 10px;">{{ $log->user_role }}</span>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-bold" style="font-size: 12px;">{{ $log->room_name }}</span>
                                            <small class="d-block text-muted" style="font-size: 10px;">{{ $log->room_code }}</small>
                                        </td>
                                        <td>
                                            @if(Str::contains($log->action, 'unlock'))
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3 py-1 fw-bold" style="font-size: 10px;">
                                                    <i class="fas fa-unlock me-1 no-print"></i> Unlocked
                                                </span>
                                            @elseif(Str::contains($log->action, 'lock'))
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3 py-1 fw-bold" style="font-size: 10px;">
                                                    <i class="fas fa-lock me-1 no-print"></i> Locked
                                                </span>
                                            @elseif(Str::contains($log->action, 'attempt_failed'))
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3 py-1 fw-bold" style="font-size: 10px;">
                                                    <i class="fas fa-times-circle me-1 no-print"></i> Failed Attempt
                                                </span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3 py-1 fw-bold" style="font-size: 10px;">
                                                    {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($log->method === 'fingerprint')
                                                <span class="text-muted fw-bold" style="font-size: 11px;"><i class="fas fa-fingerprint me-1 text-primary no-print"></i> Biometric</span>
                                            @elseif($log->method === 'hardware_keypad')
                                                <span class="text-muted fw-bold" style="font-size: 11px;"><i class="fas fa-th me-1 text-warning no-print"></i> Keypad PIN</span>
                                            @elseif($log->method === 'remote_pwa')
                                                <span class="text-muted fw-bold" style="font-size: 11px;"><i class="fas fa-mobile-alt me-1 text-info no-print"></i> Web Dashboard</span>
                                            @elseif($log->method === 'emergency_button')
                                                <span class="text-muted fw-bold" style="font-size: 11px;"><i class="fas fa-sign-out-alt me-1 text-danger no-print"></i> Exit Button</span>
                                            @else
                                                <span class="text-muted fw-bold text-capitalize" style="font-size: 11px;"><i class="fas fa-cog me-1 no-print"></i> {{ str_replace('_', ' ', $log->method) ?: 'System Action' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center no-print">
                                            <button type="button" class="btn btn-light btn-sm rounded-circle shadow-sm border btn-action-view" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#logModal_{{ $log->log_id }}"
                                                    title="View Full Details">
                                                <i class="fas fa-eye text-primary"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25 no-print"></i>
                                                <h6 class="fw-bold">No Records Found</h6>
                                                <p class="small mb-0">The system has not recorded any access logs yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- 🟢 OFFICIAL SIGNATURES SECTION (Prints & Hardcopy) -->
                    <div class="print-signatures mt-5 pt-3" style="display: none; width: 100%;">
                        <div style="display: flex; justify-content: space-between; text-align: center; width: 100%;">
                            <div style="flex: 1; padding: 0 10px;">
                                <p class="mb-0 small text-muted">Prepared By:</p>
                                <div class="signature-line"></div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">{{ Auth::user()->full_name }}</h6>
                                <small class="text-muted">{{ Auth::user()->role }} / Operator</small>
                            </div>
                            <div style="flex: 1; padding: 0 10px;">
                                <p class="mb-0 small text-muted">Verified & Checked By:</p>
                                <div class="signature-line"></div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">Laboratory Custodian</h6>
                                <small class="text-muted">BSIS Department In-Charge</small>
                            </div>
                            <div style="flex: 1; padding: 0 10px;">
                                <p class="mb-0 small text-muted">Noted & Approved By:</p>
                                <div class="signature-line"></div>
                                <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">College Dean</h6>
                                <small class="text-muted">College Administration</small>
                            </div>
                        </div>
                    </div>

                    <!-- 🟢 OFFICIAL LOGBOOK FOOTER (Prints & Hardcopy) -->
                    <div class="d-none d-print-block mt-4 pt-3 border-top border-dark">
                        <div class="d-flex justify-content-between align-items-end">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('assets/img/tpc-logo.jpg') }}" alt="Seal" style="width: 32px; height: 32px; object-fit: contain; border-radius: 50%;" onerror="this.style.display='none';">
                                <img src="{{ asset('assets/img/bsis-logo.jpg') }}" alt="BSIS Seal" style="width: 32px; height: 32px; object-fit: contain; border-radius: 50%;" onerror="this.style.display='none';">
                                <div style="font-size: 10px; line-height: 1.35; color: #111; text-align: left;">
                                    <div><strong>Bachelor of Science in Information Systems</strong></div>
                                    <div>Brgy. San Isidro, Talibon, Bohol 6325 • Tel: (038) 515-0051</div>
                                    <div style="text-decoration: underline; color: #0d6efd;">info@tpc.edu.ph</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold" style="font-size: 13px; letter-spacing: 0.5px;">TPC-BSIS-LOG Form-1</div>
                                <div class="small text-muted" style="font-size: 9.5px;">Certified Official BSIS Record • Printed {{ now()->format('F d, Y h:i A') }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- 🟢 LOG DETAILS MODALS (No-Print) -->
    @foreach($reports as $log)
    <div class="modal fade no-print" id="logModal_{{ $log->log_id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; background-color: var(--bg-surface, #fff); border: 1px solid var(--border-color, rgba(0,0,0,0.08)) !important;">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold text-dark"><i class="fas fa-info-circle text-primary me-2"></i>Log Entry #{{ $log->log_id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="surface-box p-3 rounded-4 mb-3">
                        <div class="row g-2 small">
                            <div class="col-6">
                                <span class="text-muted d-block">Personnel:</span>
                                <strong class="text-dark">{{ $log->user_name }} ({{ $log->user_role }})</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Exact Timestamp:</span>
                                <strong class="text-dark">{{ \Carbon\Carbon::parse($log->logged_at)->format('Y-m-d h:i:s A') }}</strong>
                            </div>
                            <div class="col-6 mt-3">
                                <span class="text-muted d-block">Room:</span>
                                <strong class="text-dark">{{ $log->room_name }} ({{ $log->room_code }})</strong>
                            </div>
                            <div class="col-6 mt-3">
                                <span class="text-muted d-block">Door State After:</span>
                                <strong class="text-capitalize {{ $log->door_state_after === 'unlocked' ? 'text-success' : 'text-danger' }}">{{ $log->door_state_after }}</strong>
                            </div>
                            <div class="col-6 mt-3">
                                <span class="text-muted d-block">Method Used:</span>
                                <strong class="text-capitalize">{{ str_replace('_', ' ', $log->method) }}</strong>
                            </div>
                            <div class="col-6 mt-3">
                                <span class="text-muted d-block">Device Code:</span>
                                <strong class="text-dark">{{ $log->device_code ?? 'N/A' }}</strong>
                            </div>
                            <div class="col-12 mt-3">
                                <span class="text-muted d-block">Client IP Address:</span>
                                <code>{{ $log->ip_address ?? 'Local/Hardware' }}</code>
                            </div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="small fw-bold text-muted mb-1">System Notes / Description:</label>
                        <div class="p-3 surface-box rounded-3 text-muted small fw-semibold">
                            {{ $log->notes ?: 'No additional notes logged.' }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm w-100" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @include('admin.components.footer')

    <!-- 🟢 jQuery and DataTables Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- DATATABLES & ADVANCED FILTER SCRIPT -->
    <script>
        $(document).ready(function() {
            var table = $('#reportsTable').DataTable({
                "order": [[ 0, "desc" ]],
                "pageLength": 25,
                "language": {
                    "search": "Quick Keyword Search:"
                }
            });

            function formatDateString(dateStr) {
                if (!dateStr) return '';
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                // Fix timezone issue when parsing simple YYYY-MM-DD
                let dateParts = dateStr.split('-');
                let d = new Date(dateParts[0], dateParts[1] - 1, dateParts[2]);
                return d.toLocaleDateString('en-US', options);
            }

            function updateCoverageText() {
                let fromVal = $('#filterDateFrom').val();
                let toVal = $('#filterDateTo').val();
                let text = "All Time Records";

                if (fromVal && toVal) {
                    if (fromVal === toVal) {
                        text = formatDateString(fromVal);
                    } else {
                        let datePartsFrom = fromVal.split('-');
                        let d1 = new Date(datePartsFrom[0], datePartsFrom[1] - 1, datePartsFrom[2]);
                        
                        let datePartsTo = toVal.split('-');
                        let d2 = new Date(datePartsTo[0], datePartsTo[1] - 1, datePartsTo[2]);
                        
                        let lastDayOfMonth = new Date(d1.getFullYear(), d1.getMonth() + 1, 0).getDate();

                        if (d1.getDate() === 1 && d2.getDate() === lastDayOfMonth && d1.getMonth() === d2.getMonth() && d1.getFullYear() === d2.getFullYear()) {
                            text = d1.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
                        } else {
                            text = "From " + formatDateString(fromVal) + " to " + formatDateString(toVal);
                        }
                    }
                } else if (fromVal) {
                    text = "Starting from " + formatDateString(fromVal);
                } else if (toVal) {
                    text = "Up until " + formatDateString(toVal);
                }

                $('#printCoverageText').text(text);
            }

            // 🟢 CORRECTED FILTER LOGIC (Using UNIX Timestamp from HTML)
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex, rowData, counter) {
                    var dateFrom = $('#filterDateFrom').val();
                    var dateTo = $('#filterDateTo').val();
                    var actionVal = $('#filterAction').val().toLowerCase();
                    var methodVal = $('#filterMethod').val().toLowerCase();

                    var rowAction = data[4].toLowerCase();
                    var rowMethod = data[5].toLowerCase();

                    if (actionVal && !rowAction.includes(actionVal)) return false;
                    if (methodVal && !rowMethod.includes(methodVal)) return false;

                    if (dateFrom || dateTo) {
                        // Kukunin ang totoong Data Order attribute sa Table Row
                        var tr = settings.aoData[dataIndex].nTr;
                        var timestampStr = $(tr).find('td:eq(0)').attr('data-order');
                        
                        if (timestampStr) {
                            var rowTimestamp = parseInt(timestampStr) * 1000; // UNIX to JS ms

                            if (dateFrom) {
                                let fromParts = dateFrom.split('-');
                                let fromDate = new Date(fromParts[0], fromParts[1] - 1, fromParts[2], 0, 0, 0);
                                if (rowTimestamp < fromDate.getTime()) return false;
                            }
                            if (dateTo) {
                                let toParts = dateTo.split('-');
                                let toDate = new Date(toParts[0], toParts[1] - 1, toParts[2], 23, 59, 59);
                                if (rowTimestamp > toDate.getTime()) return false;
                            }
                        }
                    }

                    return true;
                }
            );

            // Quick Filter Buttons Logic
            $('.quick-filter').on('click', function() {
                let range = $(this).data('range');
                let today = new Date();
                let yyyy = today.getFullYear();
                let mm = String(today.getMonth() + 1).padStart(2, '0');
                let dd = String(today.getDate()).padStart(2, '0');

                if (range === 'today') {
                    let todayStr = `${yyyy}-${mm}-${dd}`;
                    $('#filterDateFrom').val(todayStr);
                    $('#filterDateTo').val(todayStr);
                } else if (range === 'month') {
                    let firstDay = `${yyyy}-${mm}-01`;
                    let lastDay = new Date(yyyy, today.getMonth() + 1, 0);
                    let lastDayStr = `${yyyy}-${mm}-${String(lastDay.getDate()).padStart(2, '0')}`;
                    $('#filterDateFrom').val(firstDay);
                    $('#filterDateTo').val(lastDayStr);
                } else {
                    $('#filterDateFrom').val('');
                    $('#filterDateTo').val('');
                }

                updateCoverageText();
                table.draw();
            });

            // Trigger filtering on input change
            $('#filterDateFrom, #filterDateTo, #filterAction, #filterMethod').on('change', function() {
                updateCoverageText();
                table.draw();
            });

            // Reset button
            $('#btnResetFilters').on('click', function() {
                $('#filterDateFrom').val('');
                $('#filterDateTo').val('');
                $('#filterAction').val('');
                $('#filterMethod').val('');
                updateCoverageText();
                table.search('').draw();
            });

            // 🖨️ PRINT BUTTON LOGIC
            $('#btnPrintReport').on('click', function() {
                var currentLength = table.page.len(); 
                table.page.len(-1).draw(); // Show all filtered records
                
                setTimeout(function() {
                    window.print();
                    table.page.len(currentLength).draw(); // Restore pagination
                }, 500);
            });
        });
    </script>
</body>
</html>