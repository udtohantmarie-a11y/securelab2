@php
    $canExport = in_array(Auth::user()->role ?? '', ['Admin', 'Dean']);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    {{-- DataTables Buttons Extension CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    {{-- 🟢 ADDED: DataTables Responsive CSS para sa Mobile View --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    
    <style>
        /* ====================================================
           SCREEN VIEW STYLES (Modern, Executive, SaaS Standard)
           ==================================================== */
        .log-card-executive {
            border-radius: 22px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.035);
            background: var(--bg-surface);
            overflow: hidden;
        }

        .filter-toolbar {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 14px 18px;
        }

        #auditLogsTable thead th { 
            background-color: var(--bg-subtle); 
            border-bottom: 1px solid var(--border-color); 
            text-transform: uppercase; 
            font-size: 0.72rem; 
            letter-spacing: 0.75px; 
            font-weight: 700;
            color: var(--text-muted); 
            padding: 14px 12px;
        }

        #auditLogsTable tbody tr {
            transition: background-color 0.15s ease;
            border-bottom: 1px solid var(--border-color);
        }
        #auditLogsTable tbody tr:hover {
            background-color: var(--bg-subtle);
        }
        #auditLogsTable tbody td {
            padding: 14px 12px;
            color: var(--text-main);
            vertical-align: middle;
        }

        .badge-action-pill {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 50px;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .action-unlock {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }
        .action-lock {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }
        .action-warning {
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }
        .action-primary {
            background: rgba(37, 99, 235, 0.12);
            color: #2563eb;
            border: 1px solid rgba(37, 99, 235, 0.25);
        }

        .timestamp-chip {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-main);
            background: var(--bg-subtle);
            padding: 3px 8px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .method-badge {
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.76rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        #syncing-indicator {
            display: none;
            font-size: 0.8rem;
            cursor: pointer;
            animation: pulse-border 2s infinite;
        }
        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
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
            padding-top: 14px;
        }

        #officialLogBookPrint { display: none; }

        /* ====================================================
           PRINT VIEW STYLES (Official School Log Book Standard)
           ==================================================== */
        @if($canExport)
        @media print {
            @page {
                size: portrait;
                margin: 10mm 12mm 12mm 12mm;
            }

            body {
                background-color: white !important;
                color: #000 !important;
                margin: 0 !important;
                padding: 0 !important;
                font-family: Arial, "Times New Roman", sans-serif !important;
            }

            /* Hide all web screen components */
            .sidebar, .navbar, .mobile-header, .filter-toolbar, .log-card-executive, 
            .dt-buttons, footer, .footer, #syncing-indicator, .dataTables_wrapper,
            .main-content > .container-fluid > *:not(#officialLogBookPrint) {
                display: none !important;
            }

            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .container-fluid {
                width: 100% !important;
                padding: 0 !important;
            }

            /* Reveal Log Book Print Sheet */
            #officialLogBookPrint {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                color: black !important;
            }

            .logbook-print-header {
                text-align: center;
                margin-bottom: 8px;
            }

            .logbook-print-logo {
                width: 65px;
                height: 65px;
                object-fit: contain;
                border-radius: 50%;
                display: inline-block;
            }

            .logbook-republic {
                font-size: 11.5px;
                font-weight: 600;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                margin-top: 3px;
                line-height: 1.2;
            }

            .logbook-inst-name {
                font-size: 16.5px;
                font-weight: 800;
                letter-spacing: 0.5px;
                margin-top: 2px;
                line-height: 1.2;
            }

            .logbook-sub-inst {
                font-size: 11px;
                color: #222;
                margin-top: 1px;
            }

            .logbook-dept {
                font-size: 11px;
                font-weight: 600;
                color: #111;
                margin-top: 1px;
            }

            .logbook-address {
                font-size: 11px;
                color: #333;
                margin-top: 1px;
            }

            .logbook-title {
                font-size: 17.5px;
                font-weight: 900;
                letter-spacing: 1px;
                text-transform: uppercase;
                margin-top: 10px !important;
                margin-bottom: 2px !important;
                font-family: Arial, "Times New Roman", serif !important;
            }

            .logbook-subtitle {
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.8px;
                color: #444;
            }

            /* Log Book Table Grid - Crisp double border frame & solid cell borders */
            .logbook-table {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 2px solid #000 !important;
                margin-top: 10px !important;
            }

            .logbook-table thead {
                display: table-header-group !important;
            }

            .logbook-table thead th {
                border: 1px solid #000 !important;
                border-bottom: 2px solid #000 !important;
                padding: 6px 4px !important;
                font-size: 10.5px !important;
                font-weight: 800 !important;
                text-align: center !important;
                vertical-align: middle !important;
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                text-transform: uppercase;
                color: #000 !important;
            }

            .logbook-table tbody tr {
                page-break-inside: avoid !important;
                height: 27px !important;
            }

            .logbook-table tbody td {
                border: 1px solid #000 !important;
                padding: 4px 6px !important;
                font-size: 10px !important;
                color: #000 !important;
                vertical-align: middle !important;
                line-height: 1.25;
            }

            .logbook-role-text {
                font-size: 8.5px !important;
                font-weight: normal !important;
                color: #333 !important;
            }

            .logbook-sig-line {
                font-size: 9.5px !important;
                color: #555 !important;
                letter-spacing: -1px;
            }

            .logbook-table tr.blank-log-row td {
                height: 25px !important;
            }

            /* Log Book Footer */
            .logbook-footer {
                page-break-inside: avoid !important;
                margin-top: 20px !important;
                padding-top: 10px !important;
            }

            .logbook-footer-logo {
                width: 32px;
                height: 32px;
                object-fit: contain;
                border-radius: 50%;
            }

            .logbook-form-code {
                font-family: Arial, sans-serif !important;
                font-weight: 900 !important;
                font-size: 13px !important;
                letter-spacing: 0.5px;
            }
        }
        @else
        @media print {
            body * {
                display: none !important;
            }
            body:before {
                content: "Paunawa: Naka-set sa View-Only ang account. Hindi awtorisadong mag-print o mag-generate ng report.";
                display: block !important;
                text-align: center;
                font-size: 16px;
                font-family: Arial, sans-serif;
                padding: 50px 20px;
                font-weight: bold;
                color: #dc3545;
            }
        }
        @endif
    </style>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            @if($canExport)
            <!-- 🖨️ OFFICIAL SCHOOL TRANSACTION LOG BOOK PRINT LAYOUT (Matches Reference Standard) -->
            <div id="officialLogBookPrint">
                <!-- Header Section -->
                <div class="logbook-print-header text-center mb-2">
                    <div class="d-flex justify-content-center align-items-center gap-3 mb-2">
                        <img src="{{ asset('assets/img/tpc-logo.jpg') }}" alt="TPC Logo" class="logbook-print-logo" onerror="this.style.display='none';">
                        <img src="{{ asset('assets/img/bsis-logo.jpg') }}" alt="BSIS Logo" class="logbook-print-logo" onerror="this.style.display='none';">
                    </div>
                    <div class="logbook-republic">Republic of the Philippines</div>
                    <div class="logbook-inst-name">{{ cache('set_institution_name', 'TALIBON POLYTECHNIC COLLEGE') }}</div>
                    <div class="logbook-sub-inst">Province of Bohol • Municipality of Talibon</div>
                    <div class="logbook-dept">BACHELOR OF SCIENCE IN INFORMATION SYSTEMS (BSIS)</div>
                    <div class="logbook-sub-inst">BSIS Computer Laboratory • Smart Access Control System</div>
                    <div class="logbook-address">San Isidro, Talibon, Bohol</div>
                    <h2 class="logbook-title mt-2 mb-1">SCHOOL TRANSACTION LOG BOOK</h2>
                    <div class="logbook-subtitle small mb-2">BSIS LABORATORY ACCESS & ELECTRONIC AUDIT LOG SHEET</div>
                </div>

                <!-- Official Grid Table (Matching Log Table Columns) -->
                <table class="logbook-table">
                    <thead>
                        <tr>
                            <th style="width: 16%;">TIMESTAMP</th>
                            <th style="width: 18%;">USER</th>
                            <th style="width: 17%;">LABORATORY</th>
                            <th style="width: 12%;">ACTION</th>
                            <th style="width: 13%;">METHOD</th>
                            <th style="width: 10%;">STATUS</th>
                            <th style="width: 14%;">NOTES</th>
                        </tr>
                    </thead>
                    <tbody id="logBookPrintTbody">
                        @forelse($logs as $log)
                            @if(strtolower($log->action) !== 'attempt_failed')
                                <tr>
                                    <td class="text-center">{{ \Carbon\Carbon::parse($log->logged_at)->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <strong>{{ strtoupper($log->user_name ?? 'System') }}</strong>
                                        @if(!empty($log->user_role))
                                            <br><span class="logbook-role-text">{{ $log->user_role }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->room_name ?? 'N/A' }}</td>
                                    <td class="text-center"><strong>{{ strtoupper(str_replace('_', ' ', $log->action)) }}</strong></td>
                                    <td class="text-center">{{ ucfirst(str_replace('_', ' ', $log->method ?? 'Biometric')) }}</td>
                                    <td class="text-center">AUTHORIZED</td>
                                    <td>{{ $log->notes ?? '-' }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No access logs recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Official Footer Section -->
                <div class="logbook-footer">
                    <div class="d-flex justify-content-between align-items-end">
                        <!-- Left: Address & Contact Info -->
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('assets/img/tpc-logo.jpg') }}" alt="Seal" class="logbook-footer-logo" onerror="this.style.display='none';">
                            <img src="{{ asset('assets/img/bsis-logo.jpg') }}" alt="BSIS Seal" class="logbook-footer-logo" onerror="this.style.display='none';">
                            <div class="logbook-footer-contact" style="font-size: 10px; line-height: 1.35; color: #111;">
                                <div><strong>Bachelor of Science in Information Systems</strong></div>
                                <div>Brgy. San Isidro, Talibon, Bohol 6325 • Tel: (038) 515-0051</div>
                                <div style="text-decoration: underline; color: #0d6efd;">info@tpc.edu.ph</div>
                            </div>
                        </div>

                        <!-- Right: Form Code & Certified Info -->
                        <div class="text-end">
                            <div class="logbook-form-code fw-bold">TPC-BSIS-LOG Form-1</div>
                            <div class="text-muted" style="font-size: 9.5px;">Certified Official BSIS Record • {{ date('F d, Y h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 🟢 RESPONSIVE FIX: flex-column sa mobile, flex-row sa desktop --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--text-main); letter-spacing: -0.5px;">
                        Authorized Access Logs 
                        <span id="syncing-indicator" class="ms-2 badge rounded-pill fw-bold" style="background: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);" onclick="window.location.reload();">
                            <i class="fas fa-sync-alt fa-spin me-1"></i> New Entry! Click to Reload
                        </span>
                    </h4>
                    <p class="small mb-0" style="color: var(--text-muted);">Real-time biometric and electronic laboratory access audit trail.</p>
                </div>
                @if($canExport)
                    <div id="exportButtons" class="w-100 w-md-auto overflow-auto"></div>
                @else
                    <div class="d-flex align-items-center">
                        <span class="badge rounded-pill px-3 py-2 fw-semibold border shadow-xs" style="background: rgba(108, 117, 125, 0.08); color: var(--text-muted); font-size: 0.8rem;">
                            <i class="fas fa-eye me-1 text-primary"></i> View-Only Mode
                        </span>
                    </div>
                @endif
            </div>

            {{-- Date Filter Section --}}
            <div class="filter-toolbar mb-4 date-filter-section">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                        <i class="fas fa-filter me-1"></i> Quick Filter
                    </span>
                    
                    <div class="d-flex align-items-center gap-2">
                        <label for="minDate" class="small fw-bold mb-0" style="color: var(--text-muted); font-size: 0.75rem;">FROM:</label>
                        <input type="date" id="minDate" class="form-control form-control-sm rounded-pill px-3" style="max-width: 150px; background: var(--bg-subtle); border: 1px solid var(--border-color); color: var(--text-main);">
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <label for="maxDate" class="small fw-bold mb-0" style="color: var(--text-muted); font-size: 0.75rem;">TO:</label>
                        <input type="date" id="maxDate" class="form-control form-control-sm rounded-pill px-3" style="max-width: 150px; background: var(--bg-subtle); border: 1px solid var(--border-color); color: var(--text-main);">
                    </div>
                    
                    <button id="clearDateFilter" class="btn btn-sm rounded-pill px-3 fw-semibold ms-auto" style="background: var(--bg-subtle); border: 1px solid var(--border-color); color: var(--text-muted);">
                        <i class="fas fa-history me-1"></i> Reset Range
                    </button>
                </div>
            </div>

            <div class="card log-card-executive">
                <div class="card-body p-0">
                    <table id="auditLogsTable" class="table table-hover align-middle mb-0 w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th class="ps-4 text-nowrap">Timestamp</th>
                                <th class="text-nowrap">User</th>
                                <th class="text-nowrap">Laboratory</th>
                                <th class="text-nowrap">Action</th>
                                <th class="text-nowrap">Method</th>
                                <th class="text-nowrap">Status</th>
                                <th class="pe-4 text-nowrap">Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                @if(strtolower($log->action) !== 'attempt_failed')
                                    <tr data-print-timestamp="{{ \Carbon\Carbon::parse($log->logged_at)->format('Y-m-d H:i:s') }}"
                                        data-print-user="{{ strtoupper($log->user_name ?? 'System') }}"
                                        data-print-role="{{ $log->user_role ?? '' }}"
                                        data-print-lab="{{ $log->room_name ?? 'N/A' }}"
                                        data-print-action="{{ strtoupper(str_replace('_', ' ', $log->action)) }}"
                                        data-print-method="{{ ucfirst(str_replace('_', ' ', $log->method ?? 'Biometric')) }}"
                                        data-print-status="AUTHORIZED"
                                        data-print-notes="{{ $log->notes ?? '-' }}">
                                        <td class="ps-4 text-nowrap">
                                            <span class="timestamp-chip">
                                                {{ \Carbon\Carbon::parse($log->logged_at)->format('Y-m-d H:i:s') }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            <span class="small fw-bold" style="color: var(--text-main);">{{ $log->user_name ?? 'System' }}</span>
                                            <br><small style="color: var(--text-muted); font-size: 0.72rem;">{{ $log->user_role ?? 'N/A' }}</small>
                                        </td>
                                        <td class="text-nowrap">
                                            <span class="small fw-semibold" style="color: var(--text-main);">{{ $log->room_name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-nowrap">
                                            @php
                                                $actionPill = match(strtolower($log->action)) {
                                                    'unlock', 'remote_unlock' => 'action-unlock',
                                                    'lock', 'remote_lock' => 'action-lock',
                                                    'emergency_override' => 'action-warning',
                                                    default => 'action-primary'
                                                };
                                            @endphp
                                            <span class="badge-action-pill {{ $actionPill }}">
                                                <i class="fas {{ in_array(strtolower($log->action), ['unlock', 'remote_unlock']) ? 'fa-lock-open' : 'fa-lock' }}"></i>
                                                {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            @php
                                                $methodIcon = match($log->method ?? '') {
                                                    'fingerprint' => 'fa-fingerprint text-success',
                                                    'hardware_keypad' => 'fa-keyboard text-info',
                                                    'remote_pwa' => 'fa-wifi text-primary',
                                                    'emergency_button' => 'fa-triangle-exclamation text-danger',
                                                    'auto_schedule' => 'fa-clock text-secondary',
                                                    default => 'fa-circle-question text-muted'
                                                };
                                                $methodLabel = $log->method
                                                    ? ucfirst(str_replace('_', ' ', $log->method))
                                                    : 'N/A';
                                            @endphp
                                            <span class="method-badge">
                                                <i class="fas {{ $methodIcon }}"></i>
                                                <span>{{ $methodLabel }}</span>
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            <span class="badge rounded-pill fw-bold px-3 py-1" style="background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 0.72rem;">
                                                <i class="fas fa-check-circle me-1"></i>AUTHORIZED
                                            </span>
                                        </td>
                                        <td class="pe-4">
                                            <small style="color: var(--text-muted); font-size: 0.78rem;">{{ $log->notes ?? '-' }}</small>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 small" style="color: var(--text-muted);">No log records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @include('admin.components.footer')

    {{-- DataTables and Export JS Libraries --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    {{-- 🟢 ADDED: DataTables Responsive JS --}}
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>
    
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            var now = new Date();
            var year = now.getFullYear();
            var month = String(now.getMonth() + 1).padStart(2, '0');
            var day = String(now.getDate()).padStart(2, '0');
            var todayString = year + '-' + month + '-' + day;
            var currentDateTime = '{{ date("F d, Y H:i A") }}';
            
            var userName = '{{ strtoupper(Auth::user()->full_name ?? "Administrator") }}';
            var userRole = '{{ Auth::user()->role ?? "Administrator" }}';

            $('#minDate').val(todayString);
            $('#maxDate').val(todayString);

            $.fn.dataTable.ext.search.push(
                function( settings, data, dataIndex ) {
                    var min = $('#minDate').val();
                    var max = $('#maxDate').val();
                    var dateStr = data[0]; 
                    var dateOnly = dateStr.trim().substring(0, 10);

                    if (
                        ( min === "" && max === "" ) ||
                        ( min === "" && dateOnly <= max ) ||
                        ( min <= dateOnly && max === "" ) ||
                        ( min <= dateOnly && dateOnly <= max )
                    ) {
                        return true;
                    }
                    return false;
                }
            );

            // 🟢 FIX: Added responsive: true configuration
            var table = $('#auditLogsTable').DataTable({
                responsive: true,
                "order": [[ 0, "desc" ]],
                "pageLength": 15,
                "language": {
                    "search": "Filter Logs:",
                    "emptyTable": "Walang nakitang data sa database."
                },
                @if($canExport)
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: '', 
                        className: 'btn btn-success btn-sm rounded-pill px-3 me-2 mb-2 mb-md-0',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        messageTop: 'TALIBON POLYTECHNIC COLLEGE\nSan Isidro, Talibon, Bohol\n\nAUTHORIZED ACCESS LOGS REPORT\nAs of ' + currentDateTime,
                        messageBottom: '\n\n\nGenerated by:\n' + userName + '\n' + userRole + '\nAuthorized Signature'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: '', 
                        className: 'btn btn-danger btn-sm rounded-pill px-3 me-2 mb-2 mb-md-0',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            stripHtml: true 
                        },
                        customize: function (doc) {
                            doc.content[0].table.widths = Array(doc.content[0].table.body[0].length + 1).join('*').split('');
                            doc.styles.tableHeader.fillColor = '#f2f2f2';
                            doc.styles.tableHeader.color = '#000000';
                            doc.styles.tableHeader.alignment = 'left';

                            doc.content.unshift({
                                alignment: 'center',
                                margin: [0, 0, 0, 15],
                                text: [
                                    { text: 'Republic of the Philippines\n', fontSize: 10, color: '#333333' },
                                    { text: 'TALIBON POLYTECHNIC COLLEGE\n', fontSize: 13, bold: true },
                                    { text: 'Province of Bohol • Municipality of Talibon\n', fontSize: 8.5, color: '#555555' },
                                    { text: 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS (BSIS)\nBSIS Computer Laboratory • San Isidro, Talibon, Bohol\n\n', fontSize: 9, bold: true, color: '#222222' },
                                    { text: 'SCHOOL TRANSACTION LOG BOOK\n', fontSize: 13, bold: true },
                                    { text: 'BSIS LABORATORY ACCESS AUDIT TRAIL • As of ' + currentDateTime + '\n\n', fontSize: 9, color: '#444444' }
                                ]
                            });

                            doc.content.push({
                                margin: [0, 25, 0, 0],
                                columns: [
                                    {
                                        width: '60%',
                                        text: 'Bachelor of Science in Information Systems\nBrgy. San Isidro, Talibon, Bohol 6325\n(038) 515-0051 • info@tpc.edu.ph',
                                        fontSize: 8,
                                        color: '#444444'
                                    },
                                    {
                                        width: '40%',
                                        alignment: 'right',
                                        text: 'TPC-BSIS-LOG Form-1\nCertified by: ' + userName,
                                        fontSize: 9,
                                        bold: true
                                    }
                                ]
                            });
                        }
                    },
                    {
                        text: '<i class="fas fa-print me-1"></i> Print Report',
                        className: 'btn btn-primary btn-sm rounded-pill px-3 mb-2 mb-md-0',
                        action: function ( e, dt, node, config ) {
                            syncLogBookPrint();
                            window.print();
                        }
                    }
                ]
                @else
                buttons: []
                @endif
            });

            @if($canExport)
            table.buttons().container().appendTo('#exportButtons');

            // 🟢 SYNCHRONIZE PRINT SHEET WITH CURRENTLY FILTERED ROWS (Matching Log Table Columns)
            function syncLogBookPrint() {
                var printTbody = $('#logBookPrintTbody');
                if (!printTbody.length) return;
                printTbody.empty();

                var rows = table.rows({ search: 'applied' }).nodes();

                if (rows.length === 0) {
                    printTbody.append('<tr><td colspan="7" style="text-align:center; padding: 25px; font-style: italic; color: #555;">No laboratory access transactions recorded for the selected period.</td></tr>');
                } else {
                    $(rows).each(function() {
                        var timestampVal = $(this).attr('data-print-timestamp') || '';
                        var userVal = $(this).attr('data-print-user') || '';
                        var roleVal = $(this).attr('data-print-role') || '';
                        var labVal = $(this).attr('data-print-lab') || '';
                        var actionVal = $(this).attr('data-print-action') || '';
                        var methodVal = $(this).attr('data-print-method') || '';
                        var statusVal = $(this).attr('data-print-status') || 'AUTHORIZED';
                        var notesVal = $(this).attr('data-print-notes') || '-';

                        var trHtml = '<tr>' +
                            '<td style="text-align: center;">' + timestampVal + '</td>' +
                            '<td><strong>' + userVal + '</strong>' + (roleVal ? '<br><span class="logbook-role-text">' + roleVal + '</span>' : '') + '</td>' +
                            '<td>' + labVal + '</td>' +
                            '<td style="text-align: center;"><strong>' + actionVal + '</strong></td>' +
                            '<td style="text-align: center;">' + methodVal + '</td>' +
                            '<td style="text-align: center;">' + statusVal + '</td>' +
                            '<td>' + notesVal + '</td>' +
                        '</tr>';
                        printTbody.append(trHtml);
                    });
                }

                // Add ruled blank lines if rows < 16 to simulate authentic physical logbook page
                var rowCount = rows.length;
                if (rowCount > 0 && rowCount < 16) {
                    var blanks = Math.min(16 - rowCount, 8);
                    for (var b = 0; b < blanks; b++) {
                        printTbody.append('<tr class="blank-log-row"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>');
                    }
                }
            }

            window.addEventListener('beforeprint', function() {
                syncLogBookPrint();
            });
            @endif
            table.draw();

            @if(!$canExport)
            // Prevent Ctrl+P / Cmd+P and beforeprint for Staff
            window.addEventListener('beforeprint', function(e) {
                alert('Paunawa: Naka-set sa View-Only ang account. Hindi awtorisadong mag-print o mag-generate ng report.');
            });

            window.addEventListener('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'P')) {
                    e.preventDefault();
                    alert('Paunawa: Naka-set sa View-Only ang account. Hindi awtorisadong mag-print o mag-generate ng report.');
                }
            });
            @endif

            $('#minDate, #maxDate').on('change', function () {
                table.draw();
            });

            $('#clearDateFilter').on('click', function() {
                $('#minDate').val('');
                $('#maxDate').val('');
                table.draw();
            });

            @php
                $initialLatestId = $logs->first() ? $logs->first()->log_id : 0;
            @endphp
            let currentLatestLogId = {{ $initialLatestId }};
            
            setInterval(function() {
                fetch('/api/latest-log-id')
                    .then(response => response.json())
                    .then(data => {
                        if (data.latest_id > currentLatestLogId) {
                            // 🟢 FIX: Imbes na ire-load habang nagbabasa, ipapakita lang yung button para i-click
                            $('#syncing-indicator').fadeIn();
                        }
                    })
                    .catch(error => console.error('Error fetching latest log:', error));
            }, 3000); 
        });
    </script>
</body>
</html>