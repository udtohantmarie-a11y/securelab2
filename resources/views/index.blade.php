<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- 🟢 FIX: Prevent Browser from Caching Stale CSRF Tokens --}}
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>IoT Smart Lock | BSIS Computer Laboratory</title>

    <!-- Light Theme Color -->
    <meta name="theme-color" content="#f4f7f6">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0d6efd;
            --primary-dark: #0a2540;
            --accent-green: #20c997;
            --surface-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
        }

        body { 
            background-color: var(--surface-bg); 
            font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif; 
            color: var(--text-main);
            scroll-behavior: smooth; 
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* 🟢 DYNAMIC BACKGROUND GLOW */
        .bg-animation-container {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: -1;
            overflow: hidden;
            background: linear-gradient(180deg, #f8fafc 0%, #edf2f7 100%);
        }

        .glowing-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.22;
            animation: floatOrb 22s infinite alternate ease-in-out;
        }

        .orb-1 { width: 500px; height: 500px; background: #0d6efd; top: -120px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 580px; height: 580px; background: #20c997; bottom: -150px; right: -100px; animation-delay: -6s; }
        .orb-3 { width: 400px; height: 400px; background: #6366f1; top: 35%; left: 55%; transform: translate(-50%, -50%); animation-delay: -12s; }

        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(70px, 90px) scale(1.08); }
            100% { transform: translate(-50px, 50px) scale(0.92); }
        }

        /* 🟢 NAVBAR */
        .navbar { 
            padding: 14px 0; 
            background: rgba(255, 255, 255, 0.92) !important; 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: all 0.3s;
        }

        .nav-link { 
            font-weight: 600; 
            font-size: 0.88rem; 
            letter-spacing: 0.3px; 
            color: #475569 !important;
            padding: 8px 16px !important;
            border-radius: 12px;
            transition: all 0.25s ease;
        }
        .nav-link:hover { 
            color: var(--primary-color) !important; 
            background-color: rgba(13, 110, 253, 0.06);
        }

        .navbar-brand img {
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        /* 🟢 HERO SECTION */
        .hero-section {
            padding: 60px 0 50px;
            position: relative;
        }

        .hero-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(13, 110, 253, 0.08);
            color: #0d6efd;
            border: 1px solid rgba(13, 110, 253, 0.2);
            padding: 6px 16px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .pulse-indicator {
            width: 8px; height: 8px;
            border-radius: 50%;
            background-color: #20c997;
            box-shadow: 0 0 0 0 rgba(32, 201, 151, 0.7);
            animation: pulseGreen 1.8s infinite;
        }

        @keyframes pulseGreen {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(32, 201, 151, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 8px rgba(32, 201, 151, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(32, 201, 151, 0); }
        }

        .hero-title {
            font-size: 3.1rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -1.2px;
            color: #0a2540;
        }

        .hero-title .highlight-text {
            background: linear-gradient(135deg, #0d6efd 0%, #20c997 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @media (max-width: 991px) {
            .hero-title { font-size: 2.25rem; }
            .hero-section { padding: 40px 0 45px; }
        }

        /* 🟢 LIVE HARDWARE SHOWCASE CARD */
        .preview-hardware-card {
            border-radius: 24px;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 25px 50px -12px rgba(10, 37, 64, 0.12);
            overflow: hidden;
            position: relative;
        }

        .preview-hardware-header {
            background: linear-gradient(135deg, #0a2540 0%, #0d6efd 100%);
            padding: 22px 26px;
            color: #ffffff;
        }

        .vital-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 12px 14px;
            transition: all 0.25s ease;
        }
        .vital-pill:hover {
            border-color: #cbd5e1;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }

        /* 🟢 ROLE PILL BADGES */
        .role-badge-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .role-dean { background: rgba(111, 66, 193, 0.1); color: #6f42c1; border: 1px solid rgba(111, 66, 193, 0.25); }
        .role-faculty { background: rgba(32, 201, 151, 0.1); color: #198754; border: 1px solid rgba(32, 201, 151, 0.25); }
        .role-admin { background: rgba(13, 110, 253, 0.1); color: #0d6efd; border: 1px solid rgba(13, 110, 253, 0.25); }

        /* 🟢 ROLE PERSONA CARDS */
        .persona-card {
            height: 100%;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 30px 24px;
            transition: all 0.3s ease;
            position: relative;
        }
        .persona-card:hover {
            transform: translateY(-6px);
            border-color: rgba(13, 110, 253, 0.3);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.06);
        }

        .persona-icon-box {
            width: 60px; height: 60px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
            margin-bottom: 20px;
        }

        /* 🟢 STAT CARDS */
        .stat-card {
            padding: 24px 18px;
            text-align: center;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            border-color: #93c5fd;
            box-shadow: 0 12px 24px rgba(13, 110, 253, 0.08);
        }

        .stat-number {
            font-size: 2.1rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.1;
        }

        /* 🟢 FEATURE CARDS */
        .feature-card {
            height: 100%;
            padding: 32px 26px;
            border-radius: 22px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            transition: all 0.35s ease;
        }
        .feature-card:hover {
            transform: translateY(-6px);
            border-color: #60a5fa;
            box-shadow: 0 20px 35px rgba(13, 110, 253, 0.08);
        }

        .stat-icon-wrapper {
            width: 60px; height: 60px;
            border-radius: 16px; 
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px; font-size: 26px;
            transition: all 0.3s ease;
        }
        .feature-card:hover .stat-icon-wrapper {
            transform: scale(1.08) rotate(3deg);
        }

        /* 🟢 WORKFLOW */
        .step-bubble {
            width: 60px; height: 60px;
            border-radius: 18px;
            background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%);
            color: #ffffff;
            font-size: 1.3rem;
            font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 10px 20px rgba(13, 110, 253, 0.22);
            transition: all 0.3s;
        }
        .workflow-step:hover .step-bubble {
            transform: scale(1.12);
            box-shadow: 0 15px 30px rgba(13, 110, 253, 0.35);
        }

        /* 🟢 BUTTONS */
        .btn-portal-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a2540 100%);
            color: #ffffff !important;
            border: none;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.92rem;
            letter-spacing: 0.3px;
            box-shadow: 0 8px 18px rgba(13, 110, 253, 0.22);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-portal-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(13, 110, 253, 0.32);
            color: #ffffff;
        }

        .btn-portal-secondary {
            background: #ffffff;
            color: #0a2540 !important;
            border: 1.5px solid #cbd5e1;
            padding: 12px 26px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.92rem;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-portal-secondary:hover {
            background: #f8fafc;
            border-color: #0d6efd;
            color: #0d6efd !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.05);
        }

        /* 🟢 MODAL STYLES */
        .modal-content { 
            background-color: #fff; 
            border-radius: 26px; 
            border: none; 
            overflow: hidden; 
            box-shadow: 0 25px 60px rgba(10, 37, 64, 0.22); 
        }

        .modal-side-banner {
            background: linear-gradient(150deg, #0a2540 0%, #0d6efd 65%, #20c997 100%);
            position: relative;
            overflow: hidden;
        }

        .form-control, .input-group-text, .pwd-toggle { 
            background-color: #f8fafc !important; 
            color: #1e293b !important; 
            border: 1.5px solid #e2e8f0 !important; 
            transition: all 0.25s; 
        }
        .form-control:focus {
            background-color: #ffffff !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.12) !important;
        }
        .pwd-toggle { 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 0 16px; 
        }
        .pwd-toggle:hover i { color: #0d6efd !important; }

        .watermark-icon { 
            position: absolute; right: -25px; bottom: -25px; 
            font-size: 130px; opacity: 0.03; transform: rotate(-15deg); 
            pointer-events: none; color: #0d6efd;
        }

        .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.03) !important; }

        .section-heading {
            font-size: 2.2rem;
            font-weight: 800;
            color: #0a2540;
            letter-spacing: -0.8px;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- 🟢 BACKGROUND ANIMATION LAYER -->
    <div class="bg-animation-container">
        <div class="glowing-orb orb-1"></div>
        <div class="glowing-orb orb-2"></div>
        <div class="glowing-orb orb-3"></div>
    </div>

    <!-- 🟢 INSTITUTIONAL NAVBAR -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <!-- Institutional Identity & Logos -->
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="{{ asset('assets/img/tpc-logo.jpg') }}" alt="TPC Logo" width="38" height="38" class="rounded-circle object-fit-cover bg-white shadow-sm" onerror="this.src='https://ui-avatars.com/api/?name=TPC&background=fff&color=007613'">
                <img src="{{ asset('assets/img/bsis-logo.jpg') }}" alt="BSIS Logo" width="38" height="38" class="rounded-circle object-fit-cover bg-white shadow-sm" onerror="this.src='https://ui-avatars.com/api/?name=BSIS&background=0d6efd&color=fff'">
                <div class="ms-1 d-flex flex-column">
                    <span class="fw-bold text-dark" style="font-size: 1.1rem; line-height: 1.1; letter-spacing: -0.3px;">SECURELAB</span>
                    <small class="text-muted fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">BSIS COMPUTER LABORATORY</small>
                </div>
            </a>

            <button class="navbar-toggler border-0 shadow-none p-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars fs-4 text-dark"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-1 my-3 my-lg-0">
                    <li class="nav-item"><a class="nav-link" href="#roles"><i class="fas fa-users-cog me-1 text-primary"></i>Who Can Access</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features"><i class="fas fa-shield-alt me-1 text-primary"></i>Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#process"><i class="fas fa-microchip me-1 text-primary"></i>How It Works</a></li>
                </ul>

                <!-- 🟢 INCLUSIVE PORTAL ACTIONS (Dean, Faculty, Staff & Admin) -->
                <div class="d-flex align-items-center gap-2 ms-lg-3 mt-2 mt-lg-0">
                    <button class="btn btn-portal-secondary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#registerModal">
                        <i class="fas fa-user-plus me-1 text-muted"></i>Request Access
                    </button>
                    <button class="btn btn-portal-primary btn-sm px-4" data-bs-toggle="modal" data-bs-target="#loginModal">
                        <i class="fas fa-door-open me-2"></i>Personnel Portal
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- 🟢 MAIN CONTENT WRAPPER -->
    <div class="flex-grow-1">

        <!-- 🟢 HERO SECTION: 2-COLUMN MODERN PRESENTATION -->
        <header class="hero-section">
            <div class="container">
                <div class="row align-items-center g-5">
                    
                    <!-- Left Hero Content -->
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                        
                        <!-- Institution Badge -->
                        <div class="hero-badge-pill mb-3">
                            <span class="pulse-indicator"></span>
                            <span>Talibon Polytechnic College • BSIS Department</span>
                        </div>

                        <!-- Main Title -->
                        <h1 class="hero-title mb-3">
                            Smart Biometric <br>
                            <span class="highlight-text">Access & Security</span> <br>
                            Laboratory Portal
                        </h1>

                        <p class="text-muted lead fw-normal mb-4" style="font-size: 1.05rem; line-height: 1.65;">
                            An IoT-powered smart door lock and laboratory monitoring system engineered for authorized 
                            <strong>College Deans</strong>, <strong>Faculty & Staff</strong>, and <strong>System Administrators</strong>. 
                            Seamlessly combining biometric authentication with real-time audit logging.
                        </p>

                        <!-- Stakeholder Roles Indicator -->
                        <div class="d-flex flex-wrap gap-2 align-items-center mb-4 pb-1">
                            <span class="small fw-bold text-muted text-uppercase me-2" style="font-size: 0.72rem; letter-spacing: 0.8px;">Authorized Roles:</span>
                            <span class="role-badge-item role-dean shadow-sm">
                                <i class="fas fa-user-tie"></i> College Dean
                            </span>
                            <span class="role-badge-item role-faculty shadow-sm">
                                <i class="fas fa-chalkboard-teacher"></i> Faculty & Staff
                            </span>
                            <span class="role-badge-item role-admin shadow-sm">
                                <i class="fas fa-shield-alt"></i> Laboratory Admin
                            </span>
                        </div>

                        <!-- CTA Actions -->
                        <div class="d-flex flex-wrap gap-3">
                            <button class="btn btn-portal-primary px-4 py-3" data-bs-toggle="modal" data-bs-target="#loginModal">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In to Portal
                            </button>
                            <button class="btn btn-portal-secondary px-4 py-3" data-bs-toggle="modal" data-bs-target="#registerModal">
                                <i class="fas fa-user-plus me-2"></i>Request Personnel Access
                            </button>
                            <button id="installApp" class="btn btn-outline-success rounded-pill px-4 py-3 d-none fw-bold shadow-sm">
                                <i class="fas fa-mobile-alt me-2"></i>Install PWA App
                            </button>
                        </div>

                        <!-- Trust Markers -->
                        <div class="d-flex align-items-center gap-4 mt-4 pt-2 text-muted small fw-semibold">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success fs-6"></i>
                                <span>Biometric Fingerprint</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success fs-6"></i>
                                <span>Keypad PIN Fallback</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success fs-6"></i>
                                <span>Instant Audit Logs</span>
                            </div>
                        </div>

                    </div>

                    <!-- Right Hero Visual Showcase: Interactive Live Hardware Status Card -->
                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                        <div class="preview-hardware-card">
                            
                            <!-- Header Bar -->
                            <div class="preview-hardware-header d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-white bg-opacity-20 rounded-pill px-2 py-1 small text-white fw-bold">
                                            <i class="fas fa-satellite-dish me-1"></i> ESP8266 Live Node
                                        </span>
                                        <span class="badge bg-success rounded-pill px-2 py-1 small text-white fw-bold">
                                            <span class="spinner-grow spinner-grow-sm me-1" style="width: 7px; height: 7px;"></span> Online
                                        </span>
                                    </div>
                                    <h5 class="fw-bold mb-0 text-white">BSIS Computer Laboratory</h5>
                                    <small class="opacity-75" style="font-size: 0.78rem;">Room 101 • Dedicated NodeMCU Smart Lock</small>
                                </div>
                                <div class="bg-white bg-opacity-10 p-3 rounded-circle border border-white border-opacity-25">
                                    <i class="fas fa-lock fs-3 text-white"></i>
                                </div>
                            </div>

                            <!-- Interactive Card Body -->
                            <div class="p-4 bg-white">
                                
                                <!-- Primary Door Lock Status Widget -->
                                <div class="d-flex align-items-center justify-content-between p-3 rounded-4 mb-3" style="background: linear-gradient(135deg, rgba(32, 201, 151, 0.08) 0%, rgba(13, 110, 253, 0.05) 100%); border: 1.5px solid rgba(32, 201, 151, 0.25);">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                                            <i class="fas fa-shield-check fs-5"></i>
                                        </div>
                                        <div>
                                            <span class="text-uppercase fw-bold text-success" style="font-size: 0.75rem; letter-spacing: 0.5px;">Laboratory Security State</span>
                                            <h5 class="fw-bolder mb-0 text-dark">SECURED & LOCKED</h5>
                                        </div>
                                    </div>
                                    <span class="badge bg-success text-white rounded-pill px-3 py-2 fw-bold shadow-sm">
                                        <i class="fas fa-check me-1"></i> ARMED
                                    </span>
                                </div>

                                <!-- Hardware Vitals Grid -->
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="vital-pill">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <small class="text-muted fw-bold" style="font-size: 0.72rem;">BIOMETRIC SCANNER</small>
                                                <i class="fas fa-fingerprint text-primary"></i>
                                            </div>
                                            <span class="fw-bold text-dark" style="font-size: 0.88rem;">Optical Sensor Active</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="vital-pill">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <small class="text-muted fw-bold" style="font-size: 0.72rem;">KEYPAD MATRIX</small>
                                                <i class="fas fa-keyboard text-info"></i>
                                            </div>
                                            <span class="fw-bold text-dark" style="font-size: 0.88rem;">Passcode Ready</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="vital-pill">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <small class="text-muted fw-bold" style="font-size: 0.72rem;">INTRUSION GUARD</small>
                                                <i class="fas fa-bell text-danger"></i>
                                            </div>
                                            <span class="fw-bold text-dark" style="font-size: 0.88rem;">Siren Disarmed</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="vital-pill">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <small class="text-muted fw-bold" style="font-size: 0.72rem;">BACKUP POWER</small>
                                                <i class="fas fa-battery-three-quarters text-success"></i>
                                            </div>
                                            <span class="fw-bold text-dark" style="font-size: 0.88rem;">100% Standby</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Simulated Recent Audit Feed -->
                                <div class="bg-light p-3 rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="fw-bold text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                            <i class="fas fa-history text-primary me-1"></i> Recent Entry Simulation
                                        </small>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.68rem;">Live Log</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 12px;">
                                                FP
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-bold text-dark small">Faculty Personnel</p>
                                                <small class="text-muted" style="font-size: 0.7rem;">Biometric Scan • Access Granted</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 fw-bold" style="font-size: 0.7rem;">
                                            Unlocked
                                        </span>
                                    </div>
                                </div>

                                <!-- Interactive Button Inside Preview -->
                                <div class="mt-3">
                                    <button class="btn btn-outline-primary w-100 rounded-pill py-2 fw-bold small" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <i class="fas fa-user-lock me-1"></i> Access Personnel Control Panel
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- 🟢 SECTION: WHO IS SECURELAB FOR? (CLEARING UP THE ADMIN-ONLY CONFUSION) -->
        <section id="roles" class="py-5 bg-white border-top border-bottom">
            <div class="container py-4">
                
                <div class="text-center mb-5" data-aos="fade-up">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                        Inclusive Laboratory Management
                    </span>
                    <h2 class="section-heading mt-2">Designed for Every Laboratory Stakeholder</h2>
                    <p class="text-muted mx-auto" style="max-width: 620px;">
                        SecureLab is not just for system administrators. It is an institutional ecosystem built for seamless daily academic workflows.
                    </p>
                </div>

                <div class="row g-4">
                    
                    <!-- Role 1: Faculty & Instructors -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="persona-card shadow-sm h-100">
                            <div class="persona-icon-box bg-success bg-opacity-10 text-success">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-bold mb-2" style="font-size: 0.75rem;">
                                Academic Staff
                            </span>
                            <h4 class="fw-bold text-dark mb-2">Faculty & Instructors</h4>
                            <p class="text-muted small mb-4">
                                Experience hassle-free lab access without relying on manual keys. Walk in using your enrolled biometric fingerprint or your assigned room passcode.
                            </p>
                            <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check text-success"></i> Instant biometric laboratory unlocking
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check text-success"></i> Personalized assigned room controls
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check text-success"></i> Direct support messaging to admins
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Role 2: College Dean -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="persona-card shadow-sm h-100" style="border-top: 4px solid #6f42c1;">
                            <div class="persona-icon-box bg-purple bg-opacity-10 text-purple" style="background: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <span class="badge bg-purple bg-opacity-10 text-purple rounded-pill px-3 py-1 fw-bold mb-2" style="background: rgba(111, 66, 193, 0.1); color: #6f42c1; font-size: 0.75rem;">
                                Academic Head
                            </span>
                            <h4 class="fw-bold text-dark mb-2">College Dean</h4>
                            <p class="text-muted small mb-4">
                                Maintain executive oversight of laboratory security, room occupancy statuses, faculty attendance records, and high-level safety compliance.
                            </p>
                            <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check text-primary"></i> High-level laboratory occupancy views
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check text-primary"></i> Comprehensive audit log & report downloads
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check text-primary"></i> Real-time intrusion and security alerts
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Role 3: System Administrator -->
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="persona-card shadow-sm h-100">
                            <div class="persona-icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold mb-2" style="font-size: 0.75rem;">
                                Technical Custodian
                            </span>
                            <h4 class="fw-bold text-dark mb-2">Laboratory Admin</h4>
                            <p class="text-muted small mb-4">
                                Complete administrative control over device telemetry, faculty fingerprint enrollments, PIN assignments, hardware health, and emergency actions.
                            </p>
                            <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check text-primary"></i> User approval & room assignment matrix
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check text-primary"></i> NodeMCU telemetry & battery health logs
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <i class="fas fa-check text-primary"></i> Emergency override & database maintenance
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- 🟢 SYSTEM HIGHLIGHT STATS -->
        <section class="py-5">
            <div class="container py-2">
                <div class="row g-4 text-center">
                    
                    <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="100">
                        <div class="stat-card shadow-sm">
                            <div class="stat-number text-primary mb-1">Optical</div>
                            <p class="text-muted mb-0 fw-bold small text-uppercase" style="letter-spacing: 0.8px;">Biometric Sensor</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="200">
                        <div class="stat-card shadow-sm">
                            <div class="stat-number text-success mb-1">&lt; 1.0s</div>
                            <p class="text-muted mb-0 fw-bold small text-uppercase" style="letter-spacing: 0.8px;">Verification Speed</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="300">
                        <div class="stat-card shadow-sm">
                            <div class="stat-number text-dark mb-1">Dual-Key</div>
                            <p class="text-muted mb-0 fw-bold small text-uppercase" style="letter-spacing: 0.8px;">Fingerprint & PIN</p>
                        </div>
                    </div>

                    <div class="col-6 col-md-3" data-aos="zoom-in" data-aos-delay="400">
                        <div class="stat-card shadow-sm">
                            <div class="stat-number text-info mb-1">24/7</div>
                            <p class="text-muted mb-0 fw-bold small text-uppercase" style="letter-spacing: 0.8px;">Live Audit Trail</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 🟢 SYSTEM FEATURES & OBJECTIVES -->
        <section id="features" class="py-5 bg-white border-top">
            <div class="container py-4 text-center">
                
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                    Technical Architecture
                </span>
                <h2 class="section-heading mt-2">Core System Capabilities</h2>
                <p class="text-muted mb-5 mx-auto" style="max-width: 650px;">
                    Replacing traditional lock-and-key vulnerabilities with an automated, tamper-resistant access ecosystem for the Department of Information Systems.
                </p>

                <div class="row g-4 text-start">
                    
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-card shadow-sm">
                            <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-fingerprint"></i>
                            </div>
                            <h5 class="fw-bold mb-3 text-dark">Biometric Automation</h5>
                            <p class="text-muted small mb-0 lh-base">
                                High-speed fingerprint matching with automated magnetic lock actuation. When personnel close the door, deadbolt sensors automatically re-secure the room.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-card shadow-sm">
                            <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h5 class="fw-bold mb-3 text-dark">Real-Time Audit Trail</h5>
                            <p class="text-muted small mb-0 lh-base">
                                Every door interaction records the precise timestamp, authenticated personnel identity, and action method. Fully exportable for institutional reports.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-card shadow-sm">
                            <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger">
                                <i class="fas fa-bell"></i>
                            </div>
                            <h5 class="fw-bold mb-3 text-dark">Intrusion & Threat Defense</h5>
                            <p class="text-muted small mb-0 lh-base">
                                Detects forced door entry, unrecognized fingerprint attempts, or tamper events. Instantly triggers local audible sirens and cloud-synced alerts.
                            </p>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- 🟢 HARDWARE WORKFLOW TIMELINE -->
        <section id="process" class="py-5">
            <div class="container py-4 text-center">
                
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                    Authentication Flow
                </span>
                <h2 class="section-heading mt-2 mb-5">How Hardware & Web Portal Interact</h2>

                <div class="row g-4 text-center">
                    
                    <div class="col-md-4 workflow-step" data-aos="fade-up" data-aos-delay="100">
                        <div class="step-bubble">01</div>
                        <h5 class="fw-bold text-dark mb-2">Input & Verification</h5>
                        <p class="text-muted small px-3">
                            Authorized personnel scan their registered fingerprint on the optical sensor or input their allocated keypad passcode.
                        </p>
                    </div>

                    <div class="col-md-4 workflow-step" data-aos="fade-up" data-aos-delay="200">
                        <div class="step-bubble" style="background: linear-gradient(135deg, #20c997 0%, #0a2540 100%);">02</div>
                        <h5 class="fw-bold text-dark mb-2">NodeMCU Processing</h5>
                        <p class="text-muted small px-3">
                            The central controller verifies credentials against the local template memory and cloud database records securely.
                        </p>
                    </div>

                    <div class="col-md-4 workflow-step" data-aos="fade-up" data-aos-delay="300">
                        <div class="step-bubble" style="background: linear-gradient(135deg, #6366f1 0%, #0d6efd 100%);">03</div>
                        <h5 class="fw-bold text-dark mb-2">Actuation & Logging</h5>
                        <p class="text-muted small px-3">
                            Solenoid lock releases, door occupancy changes to Occupied, and an audit entry is immediately dispatched to the portal.
                        </p>
                    </div>

                </div>

            </div>
        </section>
    </div>

    <!-- 🟢 INSTITUTIONAL FOOTER -->
    <footer class="mt-auto py-4 bg-white border-top shadow-sm position-relative" style="z-index: 10;">
        <div class="container">
            <div class="row align-items-center g-3">

                <!-- Left Content: School Identity -->
                <div class="col-md-6 text-center text-md-start">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                        <img src="{{ asset('assets/img/tpc-logo.jpg') }}" alt="TPC Logo" width="40" height="40" class="rounded-circle shadow-sm border border-2 border-white" onerror="this.src='https://ui-avatars.com/api/?name=TPC&background=007613&color=fff'">
                        <div>
                            <p class="mb-0 small fw-bolder text-dark" style="letter-spacing: 0.3px;">Talibon Polytechnic College</p>
                            <p class="mb-0 text-muted fw-medium" style="font-size: 11px;">Department of Information Systems • © 2026 All rights reserved.</p>
                        </div>
                    </div>
                </div>

                <!-- Right Content: System Info & Developers Credit -->
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-1 small text-muted fw-medium">
                        <i class="fas fa-shield-alt text-primary me-1"></i> <strong class="text-dark">SecureLab IoT Smart Access Control Portal</strong>
                    </p>
                    <div class="d-inline-flex align-items-center gap-2">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">
                            <span class="spinner-grow spinner-grow-sm text-success me-1" style="width: 7px; height: 7px;" role="status"></span>
                            Portal & Hardware Online
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </footer>

    <!-- 🟢 LOGIN MODAL: INCLUSIVE FOR DEAN, FACULTY, STAFF & ADMIN -->
    <div class="modal fade" id="loginModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="row g-0">
                    
                    <!-- Left Modal Visual Banner -->
                    <div class="col-lg-5 d-none d-lg-flex modal-side-banner flex-column justify-content-between p-4 p-xl-5 text-white">
                        <div class="position-relative" style="z-index: 2;">
                            <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 fw-bold mb-3" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <i class="fas fa-shield-check me-1"></i> Authorized Portal
                            </span>
                            <h3 class="fw-bold text-white mb-2">Personnel Portal</h3>
                            <p class="small opacity-75 mb-0">
                                Single sign-on for College Dean, Faculty, Staff, and System Administrators.
                            </p>
                        </div>

                        <div class="position-relative" style="z-index: 2;">
                            <div class="d-flex flex-column gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2 small opacity-90">
                                    <i class="fas fa-check-circle text-success"></i> Biometric Passkey support
                                </div>
                                <div class="d-flex align-items-center gap-2 small opacity-90">
                                    <i class="fas fa-check-circle text-success"></i> Real-time room control
                                </div>
                                <div class="d-flex align-items-center gap-2 small opacity-90">
                                    <i class="fas fa-check-circle text-success"></i> End-to-end encrypted session
                                </div>
                            </div>
                            <div class="pt-3 border-top border-white border-opacity-25">
                                <small class="opacity-75">Talibon Polytechnic College</small>
                            </div>
                        </div>
                    </div>

                    <!-- Right Modal Form Section -->
                    <div class="col-lg-7 p-4 p-md-5 bg-white position-relative">
                        <i class="fas fa-lock watermark-icon"></i>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom position-relative" style="z-index: 2;">
                            <div>
                                <h4 class="fw-bold mb-1 text-dark">Sign In</h4>
                                <small class="text-muted">Enter your institutional credentials</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        @if(session('success') && !str_contains(session('success'), 'requested'))
                            <div class="alert alert-success small py-2 rounded-3 border-0 bg-success bg-opacity-10 text-success fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger small py-2 rounded-3 border-0 bg-danger bg-opacity-10 text-danger fw-bold shadow-sm">
                                <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                            </div>
                        @endif

                        <div id="passkeyErrorAlert" class="alert alert-danger small py-2 rounded-3 border-0 bg-danger bg-opacity-10 text-danger fw-bold shadow-sm d-none">
                            <i class="fas fa-exclamation-circle me-2"></i> <span id="passkeyErrorMessage"></span>
                        </div>

                        <form method="POST" action="{{ route('login') }}" class="position-relative" style="z-index: 2;">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.8px;">Institutional Email</label>
                                <div class="input-group overflow-hidden rounded-3 shadow-inner">
                                    <span class="input-group-text border-end-0 bg-light"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" name="email" class="form-control py-3 shadow-none border-start-0 fw-semibold" placeholder="name@tpc.edu.ph" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label small fw-bold text-muted text-uppercase m-0" style="letter-spacing: 0.8px;">Password</label>
                                    <a href="{{ route('password.request') }}" class="small fw-bold text-decoration-none text-primary">Forgot Password?</a>
                                </div>
                                <div class="input-group overflow-hidden rounded-3 shadow-inner">
                                    <span class="input-group-text border-end-0 bg-light"><i class="fas fa-key text-primary"></i></span>
                                    <input type="password" name="password" id="login_password" class="form-control py-3 shadow-none border-start-0 border-end-0 fw-semibold" placeholder="••••••••" required>
                                    <span class="input-group-text pwd-toggle border-start-0 bg-light" data-target="login_password">
                                        <i class="fas fa-eye text-muted"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4 d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input shadow-none cursor-pointer" type="checkbox" name="remember" id="rememberMe">
                                    <label class="form-check-label small fw-bold text-muted cursor-pointer" for="rememberMe" style="user-select: none;">
                                        Remember this browser
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-portal-primary w-100 py-3 rounded-pill shadow-sm mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i> Sign In to Portal
                            </button>

                            <div class="position-relative text-center my-3">
                                <hr class="border-secondary opacity-25">
                                <span class="position-absolute top-50 start-50 translate-middle px-3 small fw-bold bg-white text-muted">OR</span>
                            </div>

                            <button type="button" id="passkeyLoginBtn" class="btn btn-dark w-100 py-3 rounded-pill shadow-sm" onclick="loginWithPasskey()">
                                <i class="fas fa-fingerprint me-2"></i> Sign In with Biometric Passkey
                            </button>

                            <div class="text-center mt-4 pt-2">
                                <p class="small text-muted fw-medium mb-0">
                                    Faculty or staff without access? 
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal" class="fw-bold text-primary text-decoration-none ms-1">Request Personnel Account</a>
                                </p>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🟢 REGISTER MODAL: REQUEST PERSONNEL ACCESS -->
    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="row g-0">
                    
                    <!-- Left Modal Visual Banner -->
                    <div class="col-lg-5 d-none d-lg-flex modal-side-banner flex-column justify-content-between p-4 p-xl-5 text-white" style="background: linear-gradient(150deg, #0a2540 0%, #198754 65%, #20c997 100%);">
                        <div class="position-relative" style="z-index: 2;">
                            <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 fw-bold mb-3" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                <i class="fas fa-id-badge me-1"></i> New Personnel
                            </span>
                            <h3 class="fw-bold text-white mb-2">Request Lab Access</h3>
                            <p class="small opacity-75 mb-0">
                                Register your academic profile to request authorization from the BSIS Laboratory Custodian.
                            </p>
                        </div>

                        <div class="position-relative" style="z-index: 2;">
                            <div class="d-flex flex-column gap-2 mb-3">
                                <div class="d-flex align-items-center gap-2 small opacity-90">
                                    <i class="fas fa-shield-alt text-warning"></i> Admin verification required
                                </div>
                                <div class="d-flex align-items-center gap-2 small opacity-90">
                                    <i class="fas fa-fingerprint text-white"></i> Biometrics enrolled after approval
                                </div>
                                <div class="d-flex align-items-center gap-2 small opacity-90">
                                    <i class="fas fa-clock text-white"></i> Instant notification upon activation
                                </div>
                            </div>
                            <div class="pt-3 border-top border-white border-opacity-25">
                                <small class="opacity-75">Talibon Polytechnic College</small>
                            </div>
                        </div>
                    </div>

                    <!-- Right Modal Form Section -->
                    <div class="col-lg-7 p-4 p-md-5 bg-white position-relative">
                        <i class="fas fa-user-plus watermark-icon"></i>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom position-relative" style="z-index: 2;">
                            <div>
                                <h4 class="fw-bold mb-1 text-dark">Personnel Registration</h4>
                                <small class="text-muted">Create your academic account</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        @if(session('success') && str_contains(session('success'), 'requested'))
                            <div class="alert alert-success small py-2 rounded-3 border-0 bg-success bg-opacity-10 text-success fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            </div>
                        @endif

                        <div class="alert alert-info small py-2 rounded-3 border-0 bg-info bg-opacity-10 text-info fw-bold shadow-sm mb-4 position-relative" style="z-index: 2;">
                            <i class="fas fa-info-circle me-2"></i> Your account will be marked as <strong>Pending</strong> and requires Administrator approval before sign in.
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="position-relative" style="z-index: 2;">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.8px;">Full Name</label>
                                <div class="input-group overflow-hidden rounded-3 shadow-inner">
                                    <span class="input-group-text border-end-0 bg-light"><i class="fas fa-user text-primary"></i></span>
                                    <input type="text" name="name" class="form-control py-2 shadow-none border-start-0 fw-semibold" placeholder="e.g. Prof. Juan Dela Cruz" value="{{ old('name') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.8px;">Official Institutional Email</label>
                                <div class="input-group overflow-hidden rounded-3 shadow-inner">
                                    <span class="input-group-text border-end-0 bg-light"><i class="fas fa-envelope text-primary"></i></span>
                                    <input type="email" name="email" class="form-control py-2 shadow-none border-start-0 fw-semibold" placeholder="name@tpc.edu.ph" value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted text-uppercase m-0" style="letter-spacing: 0.8px;">Password</label>
                                <div class="input-group overflow-hidden rounded-3 shadow-inner">
                                    <span class="input-group-text border-end-0 bg-light"><i class="fas fa-key text-primary"></i></span>
                                    <input type="password" name="password" id="register_password" class="form-control py-2 shadow-none border-start-0 border-end-0 fw-semibold" placeholder="••••••••" required>
                                    <span class="input-group-text pwd-toggle border-start-0 bg-light" data-target="register_password">
                                        <i class="fas fa-eye text-muted"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase m-0" style="letter-spacing: 0.8px;">Confirm Password</label>
                                <div class="input-group overflow-hidden rounded-3 shadow-inner">
                                    <span class="input-group-text border-end-0 bg-light"><i class="fas fa-check-circle text-primary"></i></span>
                                    <input type="password" name="password_confirmation" id="register_password_confirmation" class="form-control py-2 shadow-none border-start-0 border-end-0 fw-semibold" placeholder="••••••••" required>
                                    <span class="input-group-text pwd-toggle border-start-0 bg-light" data-target="register_password_confirmation">
                                        <i class="fas fa-eye text-muted"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-portal-primary w-100 py-3 rounded-pill shadow-sm mb-3" style="background: linear-gradient(135deg, #198754 0%, #0a2540 100%);">
                                <i class="fas fa-user-check me-2"></i> Submit Access Request
                            </button>

                            <div class="text-center mt-3">
                                <p class="small text-muted fw-medium mb-0">
                                    Already an authorized personnel? 
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal" class="fw-bold text-primary text-decoration-none ms-1">Sign in here</a>
                                </p>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true, offset: 50 });

        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                window.location.reload();
            }
        });

        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            try {
                e.preventDefault();
                deferredPrompt = e;
                const installBtn = document.getElementById('installApp');
                if (installBtn) installBtn.classList.remove('d-none');
            } catch (err) {
                console.warn('Contextual handling for PWA warning prompt:', err);
            }
        });

        const installBtnElement = document.getElementById('installApp');
        if (installBtnElement) {
            installBtnElement.addEventListener('click', async () => {
                if (deferredPrompt) {
                    try {
                        await deferredPrompt.prompt();
                        deferredPrompt = null;
                    } catch (err) {
                        console.error('PWA script launch execution cancelled:', err);
                    }
                }
            });
        }

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('ServiceWorker channel opened actively:', reg.scope))
                    .catch((err) => console.warn('Extension channel message listener exception handled:', err));
            });
        }
    </script>

    <script>
        document.querySelectorAll('.pwd-toggle').forEach(toggleBtn => {
            toggleBtn.addEventListener('click', function() {
                let targetId = this.getAttribute('data-target');
                let inputField = document.getElementById(targetId);
                let icon = this.querySelector('i');

                if (inputField.type === 'password') {
                    inputField.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    icon.style.color = '#0d6efd';
                } else {
                    inputField.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    icon.style.color = '#6c757d';
                }
            });
        });

        async function loginWithPasskey() {
            const errorAlert = document.getElementById('passkeyErrorAlert');
            const errorMessage = document.getElementById('passkeyErrorMessage');
            const loginBtn = document.getElementById('passkeyLoginBtn');

            errorAlert.classList.add('d-none'); 

            if (!window.PublicKeyCredential) {
                errorMessage.textContent = 'Your device or browser does not support Passkeys.';
                errorAlert.classList.remove('d-none');
                return;
            }

            try {
                loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Authenticating...';
                loginBtn.disabled = true;

                const challenge = new Uint8Array(32);
                window.crypto.getRandomValues(challenge);

                const assertion = await navigator.credentials.get({
                    publicKey: {
                        challenge: challenge,
                        rpId: window.location.hostname, 
                        userVerification: "preferred"
                    }
                });

                const response = await fetch('{{ route("login.passkey") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ credential_id: assertion.id })
                });

                if (response.status === 419) {
                    loginBtn.innerHTML = '<i class="fas fa-sync fa-spin me-2"></i> Refreshing Session...';
                    window.location.reload();
                    return;
                }

                const data = await response.json();

                if (data.success) {
                    window.location.href = '{{ route("dashboard") }}';
                } else {
                    throw new Error(data.message || 'Passkey not recognized in our database.');
                }

            } catch (error) {
                console.error('Passkey Login Error:', error);

                if (error.name === 'NotAllowedError') {
                    errorMessage.textContent = 'Fingerprint cancelled or no passkey registered for this link.';
                } else {
                    errorMessage.textContent = error.message || 'Authentication failed.';
                }

                errorAlert.classList.remove('d-none');
                loginBtn.innerHTML = '<i class="fas fa-fingerprint me-2"></i> Sign in with Passkey';
                loginBtn.disabled = false;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                @if(str_contains(session('success'), 'requested'))
                    var myModal = new bootstrap.Modal(document.getElementById('registerModal'));
                    myModal.show();
                @else
                    var myModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    myModal.show();
                @endif
            @endif

            @if($errors->any())
                var myModal = new bootstrap.Modal(document.getElementById('loginModal'));
                myModal.show();
            @endif
        });
    </script>

    <script>
        window.addEventListener('load', function() {
            fetch('{{ route("dashboard") }}', { 
                cache: 'no-store',
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest', 
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (response.ok && response.url.includes('dashboard')) {
                    document.body.style.opacity = '0.5'; 
                    window.location.replace('{{ route("dashboard") }}');
                }
            })
            .catch(err => {
                console.log('Network Error or Offline mode:', err);
            });
        });
    </script>
</body>
</html>