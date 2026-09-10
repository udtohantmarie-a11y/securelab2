@php
    $canExport = in_array(Auth::user()->role ?? '', ['Admin', 'Dean']);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    {{-- DataTables for Professional Export --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.bootstrap5.min.css">
    {{-- 🟢 ADDED: DataTables Responsive CSS para sa Mobile View --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css">
    
    <style>
        /* ====================================================
           SCREEN VIEW STYLES (Modern Executive Security Theme)
           ==================================================== */
        .threat-kpi-card {
            border-radius: 16px;
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.04);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .threat-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.08);
        }
        .pulse-dot-red {
            display: inline-block; 
            width: 8px; 
            height: 8px; 
            border-radius: 50%; 
            background: #ef4444;
            box-shadow: 0 0 0 rgba(239, 68, 68, 0.4); 
            animation: pulse-red-kpi 2s infinite;
        }
        @keyframes pulse-red-kpi { 0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); } 70% { box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); } 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); } }

        .alert-card-executive {
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

        .severity-critical-row { border-left: 4px solid #ef4444 !important; }
        .severity-warning-row { border-left: 4px solid #f59e0b !important; }
        .severity-info-row { border-left: 4px solid #06b6d4 !important; }

        #alertsTable thead th { 
            background-color: var(--bg-subtle); 
            border-bottom: 1px solid var(--border-color); 
            text-transform: uppercase; 
            font-size: 0.72rem; 
            letter-spacing: 0.75px; 
            font-weight: 700;
            color: var(--text-muted); 
            padding: 14px 12px;
        }

        #alertsTable tbody tr {
            transition: background-color 0.15s ease;
            border-bottom: 1px solid var(--border-color);
        }
        #alertsTable tbody tr:hover {
            background-color: var(--bg-subtle);
        }
        #alertsTable tbody td {
            padding: 14px 12px;
            color: var(--text-main);
            vertical-align: middle;
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

        .pill-critical {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.25);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .pill-warning {
            background: rgba(245, 158, 11, 0.12);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.25);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .pill-info {
            background: rgba(6, 182, 212, 0.12);
            color: #06b6d4;
            border: 1px solid rgba(6, 182, 212, 0.25);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-active-badge {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50px;
            animation: pulse-red 2s infinite;
        }
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
            70% { box-shadow: 0 0 0 6px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        .status-resolved-badge {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 50px;
        }

        #syncing-alert {
            display: none;
            font-size: 0.8rem;
            cursor: pointer;
            animation: pulse-red 2s infinite;
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
            border-color: #ef4444;
            outline: none;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            font-size: 0.82rem;
            color: var(--text-muted);
            padding-top: 14px;
        }
        
        #officialAlertsPrint { display: none; }

        @if($canExport)
        /* ====================================================
           PRINT VIEW STYLES - OFFICIAL SCHOOL TRANSACTION LOG BOOK
           (BSIS Computer Laboratory Incident & Intrusion Alerts)
           ==================================================== */
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
            .sidebar, .navbar, .mobile-header, .filter-toolbar, .alert-card-executive, 
            .dt-buttons, footer, .footer, #syncing-alert, .dataTables_wrapper,
            .main-content > .container-fluid > *:not(#officialAlertsPrint) {
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
            #officialAlertsPrint {
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
            <!-- 🖨️ OFFICIAL SCHOOL TRANSACTION LOG BOOK PRINT LAYOUT (Security Incident & Intrusion Alerts) -->
            <div id="officialAlertsPrint">
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
                    <div class="logbook-subtitle small mb-2">SECURITY INCIDENT & INTRUSION ALERTS AUDIT LOG SHEET</div>
                </div>

                <!-- Official Grid Table (Matching Alerts Table Columns) -->
                <table class="logbook-table">
                    <thead>
                        <tr>
                            <th style="width: 17%;">TRIGGERED AT</th>
                            <th style="width: 18%;">LABORATORY</th>
                            <th style="width: 18%;">INCIDENT TYPE</th>
                            <th style="width: 12%;">SEVERITY</th>
                            <th style="width: 23%;">DESCRIPTION</th>
                            <th style="width: 12%;">STATUS</th>
                        </tr>
                    </thead>
                    <tbody id="alertsPrintTbody">
                        @forelse($allAlerts as $alert)
                            <tr>
                                <td class="text-center">{{ \Carbon\Carbon::parse($alert->triggered_at)->format('Y-m-d H:i:s') }}</td>
                                <td>BSIS Computer Laboratory</td>
                                <td class="text-center"><strong>{{ strtoupper(str_replace('_', ' ', $alert->alert_type)) }}</strong></td>
                                <td class="text-center"><strong>{{ strtoupper($alert->severity) }}</strong></td>
                                <td>{{ $alert->description }}</td>
                                <td class="text-center">{{ $alert->is_resolved ? 'RESOLVED' : 'ACTIVE THREAT' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">No security incident alerts recorded.</td>
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

            {{-- 🟢 RESPONSIVE FIX: flex-column on mobile, flex-row on desktop --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 rounded-2 fw-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">
                            <i class="fas fa-shield-virus me-1"></i> THREAT INTELLIGENCE FEED
                        </span>
                        <span class="text-muted small">•</span>
                        <span class="text-muted small fw-medium">Live Audit Log</span>
                    </div>
                    <h4 class="fw-bold mb-1" style="color: var(--text-main); letter-spacing: -0.5px;">
                        Security Incident & Intrusion Alerts
                        <span id="syncing-alert" class="ms-2 badge rounded-pill fw-bold" style="background: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);" onclick="window.location.reload();">
                            <i class="fas fa-shield-alt fa-fade me-1"></i> Threat Detected! Click to Reload
                        </span>
                    </h4>
                    <p class="small mb-0" style="color: var(--text-muted);">Real-time monitoring of unauthorized access attempts, door forced events, and hardware alerts.</p>
                </div>
                <div id="alertExport" class="w-100 w-md-auto overflow-auto">
                    @if(!$canExport)
                        <span class="badge rounded-pill px-3 py-2 text-muted" style="background: var(--bg-subtle); border: 1px solid var(--border-color); font-size: 0.78rem;">
                            <i class="fas fa-lock me-1"></i> View-Only Mode
                        </span>
                    @endif
                </div>
            </div>

            <!-- 🟢 THREAT INTELLIGENCE SUMMARY STRIP -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="threat-kpi-card p-3 h-100" style="border-top: 3.5px solid #3b82f6;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-muted text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">Total Incidents</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 34px; height: 34px; background: rgba(59, 130, 246, 0.12); font-size: 13px;">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ $allAlerts->count() }}</h3>
                        <small class="text-muted" style="font-size: 11px;">All logged alerts</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    @php $activeCount = $allAlerts->where('is_resolved', 0)->count(); @endphp
                    <div class="threat-kpi-card p-3 h-100" style="border-top: 3.5px solid #ef4444;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-danger text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">Active Threats</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 34px; height: 34px; background: rgba(239, 68, 68, 0.12); font-size: 13px;">
                                <i class="fas fa-radiation"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-danger d-flex align-items-center gap-2" style="letter-spacing: -0.5px;">
                            {{ $activeCount }}
                            @if($activeCount > 0)
                                <span class="pulse-dot-red"></span>
                            @endif
                        </h3>
                        <small class="text-muted" style="font-size: 11px;">{{ $activeCount > 0 ? 'Requires attention' : 'All clear' }}</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="threat-kpi-card p-3 h-100" style="border-top: 3.5px solid #f43f5e;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-muted text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">Critical Breaches</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-danger" style="width: 34px; height: 34px; background: rgba(244, 63, 94, 0.12); font-size: 13px;">
                                <i class="fas fa-skull-crossbones"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">{{ $allAlerts->where('severity', 'critical')->count() }}</h3>
                        <small class="text-muted" style="font-size: 11px;">Forced door / tampering</small>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="threat-kpi-card p-3 h-100" style="border-top: 3.5px solid #10b981;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-muted text-uppercase" style="font-size: 10.5px; letter-spacing: 0.5px;">Resolved & Cleared</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-success" style="width: 34px; height: 34px; background: rgba(16, 185, 129, 0.12); font-size: 13px;">
                                <i class="fas fa-check-double"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-0 text-success" style="letter-spacing: -0.5px;">{{ $allAlerts->where('is_resolved', 1)->count() }}</h3>
                        <small class="text-muted" style="font-size: 11px;">Audited & safe</small>
                    </div>
                </div>
            </div>

            {{-- Date Filter Section --}}
            <div class="filter-toolbar mb-4 date-filter-section shadow-sm">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25);">
                        <i class="fas fa-filter me-1"></i> Threat Audit Filter
                    </span>

                    <div class="d-flex align-items-center gap-2">
                        <label for="minDate" class="small fw-bold mb-0" style="color: var(--text-muted); font-size: 0.75rem;">FROM:</label>
                        <input type="date" id="minDate" class="form-control form-control-sm rounded-pill px-3" style="max-width: 150px; background: var(--bg-subtle); border: 1px solid var(--border-color); color: var(--text-main);">
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <label for="maxDate" class="small fw-bold mb-0" style="color: var(--text-muted); font-size: 0.75rem;">TO:</label>
                        <input type="date" id="maxDate" class="form-control form-control-sm rounded-pill px-3" style="max-width: 150px; background: var(--bg-subtle); border: 1px solid var(--border-color); color: var(--text-main);">
                    </div>
                    
                    <button id="clearDateFilter" class="btn btn-sm rounded-pill px-3 fw-semibold ms-auto" style="background: var(--bg-subtle); border: 1px solid var(--border-color); color: var(--text-muted); transition: all 0.2s ease;">
                        <i class="fas fa-history me-1"></i> Reset Range
                    </button>
                </div>
            </div>

            <div class="card alert-card-executive">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table id="alertsTable" class="table table-hover align-middle mb-0 w-100 dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th class="ps-4 text-nowrap">Triggered At</th>
                                    <th class="text-nowrap">Laboratory</th>
                                    <th class="text-nowrap">Type</th>
                                    <th class="text-nowrap">Severity</th>
                                    <th class="text-nowrap">Description</th>
                                    <th class="pe-4 text-nowrap text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allAlerts as $alert)
                                    @php
                                        $rowSeverityClass = match($alert->severity) {
                                            'critical' => 'severity-critical-row',
                                            'warning' => 'severity-warning-row',
                                            default => 'severity-info-row'
                                        };
                                        $typePillClass = match($alert->alert_type) {
                                            'door_forced_open' => 'pill-critical',
                                            'invalid_passcode', 'door_left_open' => 'pill-warning',
                                            default => 'pill-info'
                                        };
                                    @endphp
                                    <tr class="{{ $rowSeverityClass }}"
                                        data-print-timestamp="{{ \Carbon\Carbon::parse($alert->triggered_at)->format('Y-m-d H:i:s') }}"
                                        data-print-lab="BSIS Computer Laboratory"
                                        data-print-type="{{ strtoupper(str_replace('_', ' ', $alert->alert_type)) }}"
                                        data-print-severity="{{ strtoupper($alert->severity) }}"
                                        data-print-desc="{{ e($alert->description) }}"
                                        data-print-status="{{ $alert->is_resolved ? 'RESOLVED' : 'ACTIVE THREAT' }}"
                                    >
                                        <td class="ps-4 text-nowrap">
                                            <span class="timestamp-chip">
                                                {{ \Carbon\Carbon::parse($alert->triggered_at)->format('Y-m-d H:i:s') }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            <span class="small fw-semibold" style="color: var(--text-main);">BSIS Computer Laboratory</span>
                                        </td>
                                        <td class="text-nowrap">
                                            <span class="{{ $typePillClass }}">
                                                <i class="fas fa-bell"></i>
                                                <span>{{ strtoupper(str_replace('_', ' ', $alert->alert_type)) }}</span>
                                            </span>
                                        </td>
                                        <td class="text-nowrap">
                                            @if($alert->severity === 'critical')
                                                <strong class="text-danger small"><i class="fas fa-skull-crossbones me-1"></i>CRITICAL</strong>
                                            @elseif($alert->severity === 'warning')
                                                <strong class="text-warning small"><i class="fas fa-exclamation-triangle me-1"></i>WARNING</strong>
                                            @else
                                                <strong class="text-info small"><i class="fas fa-info-circle me-1"></i>INFO</strong>
                                            @endif
                                        </td>
                                        <td class="small" style="color: var(--text-muted); max-width: 320px;">{{ $alert->description }}</td>
                                        <td class="pe-4 text-center text-nowrap">
                                            @if($alert->is_resolved)
                                                <span class="status-resolved-badge">
                                                    <i class="fas fa-check-circle me-1"></i>RESOLVED
                                                </span>
                                            @else
                                                <span class="status-active-badge">
                                                    <i class="fas fa-radiation me-1"></i>ACTIVE THREAT
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

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
            var table = $('#alertsTable').DataTable({
                responsive: true,
                "order": [[ 0, "desc" ]],
                "pageLength": 10,
                "language": {
                    "search": "Filter Alerts:",
                    "emptyTable": "No records found in the database." 
                },
                @if($canExport)
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: '', 
                        className: 'btn btn-success btn-sm rounded-pill px-3 me-2 mb-2 mb-md-0',
                        text: '<i class="fas fa-file-excel me-1"></i> Excel',
                        exportOptions: { stripHtml: true },
                        messageTop: 'Republic of the Philippines\nTALIBON POLYTECHNIC COLLEGE\nProvince of Bohol • Municipality of Talibon\nBACHELOR OF SCIENCE IN INFORMATION SYSTEMS (BSIS)\nBSIS Computer Laboratory • San Isidro, Talibon, Bohol\n\nSCHOOL TRANSACTION LOG BOOK\nSECURITY INCIDENT & INTRUSION ALERTS AUDIT LOG SHEET\nAs of ' + currentDateTime,
                        messageBottom: '\n\n\nTPC-BSIS-LOG Form-1\nCertified by: ' + userName + '\n' + userRole + '\nBachelor of Science in Information Systems - TPC'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: '',
                        className: 'btn btn-danger btn-sm rounded-pill px-3 me-2 mb-2 mb-md-0',
                        text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: { stripHtml: true },
                        customize: function (doc) {
                            doc.content[0].table.widths = Array(doc.content[0].table.body[0].length + 1).join('*').split('');
                            doc.styles.tableHeader.fillColor = '#f2f2f2';
                            doc.styles.tableHeader.color = '#000000';
                            doc.styles.tableHeader.alignment = 'left';

                            // Header for PDF
                            doc.content.unshift({
                                alignment: 'center',
                                margin: [0, 0, 0, 15],
                                text: [
                                    { text: 'Republic of the Philippines\n', fontSize: 10, color: '#333333' },
                                    { text: 'TALIBON POLYTECHNIC COLLEGE\n', fontSize: 13, bold: true },
                                    { text: 'Province of Bohol • Municipality of Talibon\n', fontSize: 8.5, color: '#555555' },
                                    { text: 'BACHELOR OF SCIENCE IN INFORMATION SYSTEMS (BSIS)\nBSIS Computer Laboratory • San Isidro, Talibon, Bohol\n\n', fontSize: 9, bold: true, color: '#222222' },
                                    { text: 'SCHOOL TRANSACTION LOG BOOK\n', fontSize: 13, bold: true },
                                    { text: 'SECURITY INCIDENT & INTRUSION ALERTS AUDIT LOG SHEET • As of ' + currentDateTime + '\n\n', fontSize: 9, color: '#444444' }
                                ]
                            });

                            // Signature / Footer for PDF
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
                            syncAlertsPrint();
                            window.print();
                        }
                    }
                ]
                @else
                buttons: []
                @endif
            });
            
            @if($canExport)
            table.buttons().container().appendTo('#alertExport');

            // 🟢 SYNCHRONIZE PRINT SHEET WITH CURRENTLY FILTERED ROWS (Matching Alerts Table Columns)
            function syncAlertsPrint() {
                var printTbody = $('#alertsPrintTbody');
                if (!printTbody.length) return;
                printTbody.empty();

                var rows = table.rows({ search: 'applied' }).nodes();

                if (rows.length === 0) {
                    printTbody.append('<tr><td colspan="6" style="text-align:center; padding: 25px; font-style: italic; color: #555;">No security incident alerts recorded for the selected period.</td></tr>');
                } else {
                    $(rows).each(function() {
                        var timestampVal = $(this).attr('data-print-timestamp') || '';
                        var labVal = $(this).attr('data-print-lab') || 'BSIS Computer Laboratory';
                        var typeVal = $(this).attr('data-print-type') || '';
                        var severityVal = $(this).attr('data-print-severity') || '';
                        var descVal = $(this).attr('data-print-desc') || '';
                        var statusVal = $(this).attr('data-print-status') || '';

                        var trHtml = '<tr>' +
                            '<td style="text-align: center;">' + timestampVal + '</td>' +
                            '<td>' + labVal + '</td>' +
                            '<td style="text-align: center;"><strong>' + typeVal + '</strong></td>' +
                            '<td style="text-align: center;"><strong>' + severityVal + '</strong></td>' +
                            '<td>' + descVal + '</td>' +
                            '<td style="text-align: center;">' + statusVal + '</td>' +
                        '</tr>';
                        printTbody.append(trHtml);
                    });
                }

                // Add ruled blank lines if rows < 16 to simulate authentic physical logbook page
                var rowCount = rows.length;
                if (rowCount > 0 && rowCount < 16) {
                    var blanks = Math.min(16 - rowCount, 8);
                    for (var b = 0; b < blanks; b++) {
                        printTbody.append('<tr class="blank-log-row"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>');
                    }
                }
            }

            window.addEventListener('beforeprint', function() {
                syncAlertsPrint();
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
                $firstAlert = $allAlerts->first();
                $initialAlertId = 0;
                
                if ($firstAlert) {
                    $initialAlertId = $firstAlert->alert_id ?? $firstAlert->notif_id ?? $firstAlert->log_id ?? $firstAlert->id ?? 0;
                }
            @endphp
            let currentLatestAlertId = {{ $initialAlertId }};
            
            setInterval(function() {
                fetch('/api/latest-alert-id')
                    .then(response => response.json())
                    .then(data => {
                        if (data.latest_id > currentLatestAlertId) {
                            // 🟢 FIX: Just show the clickable badge, prevent forced reload
                            $('#syncing-alert').fadeIn();
                        }
                    })
                    .catch(error => console.error('Alert polling error:', error));
            }, 3000); 
        });
    </script>
</body>
</html>