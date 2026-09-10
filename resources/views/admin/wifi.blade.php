<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.components.header')
    <style>
        /* 🟢 MODERN UI ENHANCEMENTS */
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        /* 🟢 MODERN EXECUTIVE UI ENHANCEMENTS */
        body { 
            background-color: var(--bg-body, #f4f7f6); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--text-main, #1e293b);
        }
        
        .dashboard-card { 
            border-radius: 20px; 
            border: none; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.03); 
            border-radius: 24px; 
            border: 1px solid var(--border-color, rgba(0,0,0,0.06)) !important; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); 
            position: relative; 
            overflow: hidden; 
            background: #fff; 
            background: var(--bg-surface, #fff); 
        }
        
        /* Gradient Accents */
        .bg-gradient-primary { background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%); color: white; }
        
        /* Faint Background Icons (Watermarks) */
        .watermark-icon { 
            position: absolute; 
            right: -15px; 
            bottom: -20px; 
            font-size: 150px; 
            opacity: 0.03; 
            transform: rotate(-15deg); 
            pointer-events: none; 
            color: var(--text-main, #000);
        }

        /* Form Inputs */
        .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.03); }
        .input-group-text { background-color: #f8f9fa; border: none; }
        .form-control:focus { box-shadow: none; border-color: #0d6efd; background-color: #fff !important; }
        .input-group-text { background-color: var(--bg-subtle, #f8fafc) !important; border: none; }
        .input-group-container {
            border-radius: 14px; 
            overflow: hidden; 
            border: 1.5px solid var(--border-color, #e2e8f0);
            transition: all 0.25s ease;
        }
        .input-group-container:focus-within {
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.12);
        }
        .form-control {
            background-color: var(--bg-subtle, #f8fafc) !important;
            color: var(--text-main, #1e293b) !important;
        }
        .form-control:focus { 
            box-shadow: none; 
            border-color: transparent; 
            background-color: var(--bg-surface, #fff) !important; 
        }
        
        /* Button */
        .sync-btn {
            transition: all 0.3s ease;
            transition: all 0.25s ease;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .sync-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(13, 110, 253, 0.2);
            box-shadow: 0 10px 22px rgba(13, 110, 253, 0.22);
        }
    </style>
</head>
<body>

    @include('admin.components.sidebar')

    <div class="main-content">
        @include('admin.components.navbar')

        <div class="container-fluid px-0">
            <!-- Page Header -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2.5 py-1 rounded-2 fw-bold" style="font-size: 10.5px; letter-spacing: 0.5px;">
                            <i class="fas fa-wifi me-1"></i> IOT NETWORK SYNCHRONIZER
                        </span>
                        <span class="text-muted small">•</span>
                        <span class="text-muted small fw-medium">ESP8266 2.4 GHz Node</span>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.5px;">Hardware WiFi Configuration</h4>
                    <p class="text-muted small mb-0 mt-1">Update the main school network credentials for all IoT door locks.</p>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 fw-semibold" style="border-radius: 15px; background-color: rgba(16, 185, 129, 0.12); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25);">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 fw-semibold" style="border-radius: 15px; background-color: rgba(239, 68, 68, 0.12); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.25);">
                    <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Configuration Card -->
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                    <div class="dashboard-card p-3 p-sm-4 p-md-5 mt-2" style="border-top: 3.5px solid #00d2ff !important;">
                        <i class="fas fa-wifi watermark-icon text-primary"></i>
                        
                        <div class="text-center mb-4 pb-3 border-bottom" style="border-color: var(--border-color) !important;">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; font-size: 30px;">
                                <i class="fas fa-broadcast-tower"></i>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">Network Synchronization</h5>
                            <p class="text-muted small px-3 mb-0">Connect your door hardware to the school's main WiFi network. These credentials will be securely transmitted and saved to the locks' SD cards.</p>
                        </div>

                        {{-- 🟢 FIXED: Added autocomplete="off" to form --}}
                        <form action="{{ route('hardware.wifi.update') }}" method="POST" autocomplete="off">
                            @csrf
                            
                            <!-- Network Selection (SSID) -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.8px;">Network Name (SSID)</label>
                                <div class="input-group input-group-container shadow-inner">
                                    <span class="input-group-text px-3"><i class="fas fa-satellite-dish text-primary"></i></span>
                                    {{-- 🟢 FIXED: Added autocomplete="off" --}}
                                    <input type="text" name="ssid" class="form-control form-control-lg border-0 fw-semibold" placeholder="e.g. TPC_Campus_WiFi" autocomplete="off" required>
                                </div>
                            </div>

                            <!-- Password Input -->
                            <div class="mb-4 pb-2">
                                <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.8px;">WiFi Password</label>
                                <div class="input-group input-group-container shadow-inner">
                                    <span class="input-group-text px-3"><i class="fas fa-lock text-primary"></i></span>
                                    {{-- 🟢 FIXED: Added autocomplete="new-password" and data-lpignore="true" --}}
                                    <input type="password" name="password" id="wifiPassword" class="form-control form-control-lg border-0 fw-semibold" placeholder="Enter network password" autocomplete="new-password" data-lpignore="true" required>
                                    <span class="input-group-text px-3" style="cursor: pointer;" onclick="toggleWifiPassword()">
                                        <i class="fas fa-eye text-muted" id="wifiEyeIcon"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Warning Notice -->
                            <div class="alert alert-warning border-0 small py-3 px-4 fw-semibold shadow-sm mb-4 d-flex align-items-center" style="border-radius: 16px; background-color: rgba(255, 193, 7, 0.12); color: #b45309; border: 1px solid rgba(255, 193, 7, 0.2) !important;">
                                <i class="fas fa-exclamation-triangle fs-4 me-3 text-warning"></i> 
                                <div>
                                    Ensure the hardware locks are currently <strong>online</strong> to receive this update instantly over MQTT. Offline devices will not receive these credentials.
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn bg-gradient-primary text-white w-100 py-3 rounded-pill sync-btn shadow-sm">
                                <i class="fas fa-sync-alt me-2"></i> Sync Credentials to Hardware
                            </button>
                        </form>
                        
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    @include('admin.components.footer')

    <!-- Scripts -->
    <script>
        // Toggle Password Visibility
        function toggleWifiPassword() {
            let pwdInput = document.getElementById("wifiPassword");
            let eyeIcon = document.getElementById("wifiEyeIcon");
            
            if (pwdInput.type === "password") {
                pwdInput.type = "text";
                eyeIcon.classList.remove("fa-eye");
                eyeIcon.classList.add("fa-eye-slash");
                eyeIcon.classList.add("text-primary");
                eyeIcon.classList.remove("text-muted");
            } else {
                pwdInput.type = "password";
                eyeIcon.classList.remove("fa-eye-slash");
                eyeIcon.classList.add("fa-eye");
                eyeIcon.classList.remove("text-primary");
                eyeIcon.classList.add("text-muted");
            }
        }
    </script>
</body>
</html>