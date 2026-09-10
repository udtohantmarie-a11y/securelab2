<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <style>
        /* 🟢 MODERN EXECUTIVE ANALYTICS ENHANCEMENTS */
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
        .dashboard-card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 16px 35px rgba(0,0,0,0.08); 
        }

        /* Gradient Accents */
        .bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%); color: white; }
        .bg-gradient-danger { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); color: white; }
        .bg-gradient-success { background: linear-gradient(135deg, #20c997 0%, #198754 100%); color: white; }
        .bg-gradient-info { background: linear-gradient(135deg, #0dcaf0 0%, #055160 100%); color: white; }

        /* Faint Background Icons (Watermarks) */
        .watermark-icon { 
            position: absolute; 
            right: -15px; 
            bottom: -20px; 
            font-size: 130px; 
            opacity: 0.03; 
            transform: rotate(-15deg); 
            pointer-events: none; 
            color: var(--text-main, #000);
        }

        /* Icon Wrappers */
        .stat-icon-wrapper { 
            width: 55px; 
            height: 55px; 
            border-radius: 16px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 24px; 
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        /* Inner Shadows and Surface Containers */
        .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.03); }

        .surface-box {
            background-color: var(--bg-subtle, #f8fafc) !important;
            border: 1px solid var(--border-color, rgba(0,0,0,0.06)) !important;
        }

        .icon-circle-badge {
            background-color: var(--bg-surface, #fff);
            border: 1px solid var(--border-color, rgba(0,0,0,0.06));
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            <div class="mb-4">
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">Usage Analytics</h4>
                <p class="text-muted small mb-0 mt-1">Visual overview of system operations and laboratory access trends.</p>
            </div>

            @php
                // Pag-process ng data mula sa controller papunta sa format na maiintindihan ng Chart.js
                $labels = [];
                $data = [];
                $totalActions = 0;
                $failedAttempts = 0;

                foreach($stats as $stat) {
                    $label = ucfirst(str_replace('_', ' ', $stat->action));
                    if(empty($label)) $label = 'System / Enrollment';

                    $labels[] = $label;
                    $data[] = $stat->total;
                    $totalActions += $stat->total;

                    if($stat->action == 'attempt_failed') {
                        $failedAttempts += $stat->total;
                    }
                }
            @endphp

            <div class="row mb-4 g-3 g-md-4">
                <div class="col-12 col-md-6">
                    <div class="dashboard-card h-100 p-3 p-md-4">
                        <i class="fas fa-chart-pie watermark-icon"></i>
                        <div class="d-flex align-items-center">
                            <div class="stat-icon-wrapper bg-gradient-primary me-3 me-md-4">
                                <i class="fas fa-fingerprint"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Total Logged Actions</h6>
                                <h2 class="fw-bolder mb-0 text-dark">{{ number_format($totalActions) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="dashboard-card h-100 p-3 p-md-4">
                        <i class="fas fa-shield-alt watermark-icon text-danger"></i>
                        <div class="d-flex align-items-center">
                            <div class="stat-icon-wrapper bg-gradient-danger me-3 me-md-4">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Failed Access Attempts</h6>
                                <h2 class="fw-bolder mb-0 text-dark">{{ number_format($failedAttempts) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 g-md-4">
                <div class="col-12 col-lg-8 mb-4">
                    <div class="dashboard-card p-3 p-md-4 h-100">
                        <i class="fas fa-chart-bar watermark-icon"></i>
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-color: var(--border-color, rgba(0,0,0,0.08)) !important;">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line text-primary me-2"></i> Access Distribution</h5>
                        </div>
                        <div style="height: 350px; position: relative;">
                            <canvas id="actionsChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-4 mb-4">
                    <div class="dashboard-card p-3 p-md-4 h-100">
                        <i class="fas fa-list watermark-icon"></i>
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom" style="border-color: var(--border-color, rgba(0,0,0,0.08)) !important;">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-clipboard-list text-primary me-2"></i> Summary Breakdown</h5>
                        </div>
                        
                        <div class="mt-3">
                            @forelse($stats as $stat)
                                @php
                                    $actionName = ucfirst(str_replace('_', ' ', $stat->action));
                                    if(empty($actionName)) $actionName = 'Registration / System';
                                    
                                    // Custom colors for specific actions
                                    $iconColor = 'text-primary';
                                    if(str_contains(strtolower($actionName), 'failed')) $iconColor = 'text-danger';
                                    if(str_contains(strtolower($actionName), 'unlock')) $iconColor = 'text-success';
                                    if(str_contains(strtolower($actionName), 'lock') && !str_contains(strtolower($actionName), 'unlock')) $iconColor = 'text-secondary';
                                @endphp
                                <div class="surface-box p-3 rounded-4 mb-3 d-flex justify-content-between align-items-center shadow-inner">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle-badge p-2 rounded-circle shadow-sm me-3 text-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-circle {{ $iconColor }} mt-1" style="font-size: 10px;"></i>
                                        </div>
                                        <span class="text-dark fw-bold small">{{ $actionName }}</span>
                                    </div>
                                    <span class="badge surface-box text-dark border shadow-sm rounded-pill px-3 py-2 fw-bolder">{{ number_format($stat->total) }}</span>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="stat-icon-wrapper bg-secondary bg-opacity-10 text-secondary mx-auto mb-3" style="width: 70px; height: 70px; font-size: 30px; opacity: 0.5;">
                                        <i class="fas fa-folder-open"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No Data Available</h6>
                                    <p class="text-muted small mb-0">No system actions have been recorded yet.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('admin.components.footer')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ctx = document.getElementById('actionsChart');
            
            if (ctx) {
                const chartContext = ctx.getContext('2d');

                // Kunin ang data mula sa PHP
                const labels = {!! json_encode($labels) !!};
                const dataPoints = {!! json_encode($data) !!};

                // SecureLab Custom Palette
                const bgColors = [
                    'rgba(13, 110, 253, 0.85)',  // Primary Blue
                    'rgba(32, 201, 151, 0.85)',  // Success Teal
                    'rgba(255, 65, 108, 0.85)',  // Danger Red
                    'rgba(255, 193, 7, 0.85)',   // Warning Yellow
                    'rgba(13, 202, 240, 0.85)',  // Info Cyan
                    'rgba(108, 117, 125, 0.85)'  // Secondary Gray
                ];
                
                const borderColors = [
                    '#0d6efd',
                    '#20c997',
                    '#ff416c',
                    '#ffc107',
                    '#0dcaf0',
                    '#6c757d'
                ];

                new Chart(chartContext, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Actions',
                            data: dataPoints,
                            backgroundColor: bgColors,
                            borderColor: borderColors,
                            borderWidth: 2,
                            borderRadius: 10, // Modern rounded bars
                            barPercentage: 0.55 // Makes the bars slightly slimmer and sleeker
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: { 
                                padding: 12, 
                                cornerRadius: 10,
                                backgroundColor: 'rgba(10, 37, 64, 0.9)',
                                titleFont: { family: 'Plus Jakarta Sans', size: 14, weight: '700' },
                                bodyFont: { family: 'Plus Jakarta Sans', size: 13, weight: '600' }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { 
                                    precision: 0,
                                    font: { family: 'Plus Jakarta Sans', weight: '600' }
                                },
                                grid: { 
                                    borderDash: [5, 5],
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                border: { display: false }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { family: 'Plus Jakarta Sans', weight: '600' } },
                                border: { display: false }
                            }
                        },
                        animation: {
                            y: {
                                duration: 1500,
                                easing: 'easeOutQuart'
                            }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>