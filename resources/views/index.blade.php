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

        html {
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        body { 
            background-color: var(--surface-bg); 
            font-family: 'Plus Jakarta Sans', 'Poppins', sans-serif; 
            color: var(--text-main);
            scroll-behavior: smooth; 
            overflow-x: hidden !important;
            width: 100% !important;
            max-width: 100% !important;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }

        /* 🟢 DYNAMIC BACKGROUND GLOW */
        .bg-animation-container {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            overflow: hidden;
            pointer-events: none;
            background: linear-gradient(180deg, #f8fafc 0%, #edf2f7 100%);
        }

        .glowing-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.22;
            animation: floatOrb 22s infinite alternate ease-in-out;
            pointer-events: none;
            max-width: 90vw;
            max-height: 90vw;
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

        /* 🟢 MOBILE RESPONSIVE GUARDS */
        @media (max-width: 991px) {
            /* Prevent AOS transforms from causing horizontal overflow */
            [data-aos] {
                transform: none !important;
                opacity: 1 !important;
                transition: none !important;
            }
            .hero-title { font-size: 2.25rem; }
            .hero-section { padding: 35px 0 40px; }

            /* Modern Mobile Navbar Drawer Card */
            .navbar-collapse {
                background: #ffffff;
                border-radius: 20px;
                padding: 16px 18px;
                margin-top: 12px;
                box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
                border: 1px solid rgba(226, 232, 240, 0.9);
            }
            .navbar-nav {
                width: 100%;
                text-align: left;
                align-items: flex-start !important;
            }
            .navbar-nav .nav-item {
                width: 100%;
            }
            .navbar-nav .nav-link {
                width: 100%;
                padding: 10px 14px !important;
                border-radius: 10px;
            }
            .navbar-portal-actions {
                flex-direction: column !important;
                width: 100% !important;
                gap: 8px !important;
                margin-top: 12px !important;
            }
            .navbar-portal-actions .btn {
                width: 100% !important;
                justify-content: center;
                padding: 12px !important;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                width: 32px;
                height: 32px;
            }
            .navbar-brand-title {
                font-size: 0.98rem !important;
            }
            .navbar-brand-subtitle {
                font-size: 0.62rem !important;
            }
            .hero-title { font-size: 1.85rem !important; line-height: 1.25; }
            .hero-section { padding: 25px 0 35px; }
            .hero-badge-pill { font-size: 0.72rem; padding: 5px 12px; white-space: normal; text-align: center; }
            
            .hero-action-btns {
                flex-direction: column !important;
                width: 100% !important;
                gap: 10px !important;
            }
            .hero-action-btns .btn {
                width: 100% !important;
                text-align: center;
                justify-content: center;
                padding: 14px 20px !important;
                font-size: 0.95rem !important;
            }

            .trust-markers {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 10px !important;
            }

            .section-heading { font-size: 1.65rem !important; }
            .persona-card, .feature-card, .defense-layer-card { padding: 20px 16px !important; }
            .stat-card { padding: 16px 10px !important; }
            .stat-number { font-size: 1.6rem !important; }
            .modal-content { border-radius: 20px !important; }
            .modal-dialog { margin: 12px auto; max-width: 95vw !important; }
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

        /* 🟢 CYBER-PHYSICAL INTERACTIVE SIMULATOR (OPTION 3) */
        .sim-shell {
            background: linear-gradient(165deg, #091322 0%, #0c1c33 50%, #060e1a 100%);
            border-radius: 26px;
            border: 1px solid rgba(56, 189, 248, 0.28);
            box-shadow: 0 25px 50px -12px rgba(2, 6, 23, 0.6), 0 0 40px rgba(13, 110, 253, 0.15);
            overflow: hidden;
            position: relative;
            transition: all 0.35s ease;
        }
        .sim-shell::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.9), transparent);
            z-index: 10;
        }

        .sim-hud-header {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 16px 22px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sim-node-badge {
            background: rgba(13, 110, 253, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
            border-radius: 30px;
            padding: 4px 12px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .sim-signal-pill {
            background: rgba(34, 197, 94, 0.12);
            color: #4ade80;
            border: 1px solid rgba(74, 222, 128, 0.3);
            border-radius: 30px;
            padding: 4px 10px;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .sim-sound-toggle {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #e2e8f0;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .sim-sound-toggle:hover {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* Simulator Stage Visualizer */
        .sim-stage {
            padding: 22px 22px 18px;
            background: radial-gradient(circle at 50% 30%, rgba(13, 110, 253, 0.12) 0%, transparent 70%);
            position: relative;
        }

        .sim-visual-core {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            background: rgba(15, 23, 42, 0.65);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 18px 20px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
        }

        .sim-visual-core.state-armed {
            border-color: rgba(34, 197, 94, 0.35);
            box-shadow: inset 0 0 25px rgba(34, 197, 94, 0.08);
        }
        .sim-visual-core.state-scanning {
            border-color: rgba(56, 189, 248, 0.6);
            box-shadow: inset 0 0 35px rgba(56, 189, 248, 0.2), 0 0 20px rgba(56, 189, 248, 0.25);
        }
        .sim-visual-core.state-unlocked {
            border-color: rgba(13, 202, 240, 0.6);
            box-shadow: inset 0 0 35px rgba(13, 202, 240, 0.18), 0 0 20px rgba(13, 202, 240, 0.3);
        }
        .sim-visual-core.state-alarm {
            border-color: rgba(239, 68, 68, 0.8);
            box-shadow: inset 0 0 45px rgba(239, 68, 68, 0.35), 0 0 30px rgba(239, 68, 68, 0.4);
            animation: redStrobe 0.8s infinite alternate ease-in-out;
        }
        @keyframes redStrobe {
            0% { background: rgba(239, 68, 68, 0.12); }
            100% { background: rgba(239, 68, 68, 0.32); }
        }

        .sim-avatar-orb {
            width: 68px; height: 68px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 28px;
            position: relative;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }
        .sim-avatar-orb.state-armed {
            background: rgba(34, 197, 94, 0.18);
            color: #4ade80;
            border: 2px solid #22c55e;
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.4);
        }
        .sim-avatar-orb.state-scanning {
            background: rgba(56, 189, 248, 0.2);
            color: #38bdf8;
            border: 2px solid #38bdf8;
            box-shadow: 0 0 25px rgba(56, 189, 248, 0.6);
        }
        .sim-avatar-orb.state-unlocked {
            background: rgba(13, 202, 240, 0.2);
            color: #0dcaf0;
            border: 2px solid #0dcaf0;
            box-shadow: 0 0 25px rgba(13, 202, 240, 0.6);
        }
        .sim-avatar-orb.state-alarm {
            background: rgba(239, 68, 68, 0.25);
            color: #ef4444;
            border: 2px solid #ef4444;
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.8);
            animation: pulseAlarm 0.7s infinite alternate;
        }
        @keyframes pulseAlarm {
            0% { transform: scale(0.95); }
            100% { transform: scale(1.08); }
        }

        /* Laser Sweep Animation for Fingerprint */
        .sim-laser-sweep {
            position: absolute;
            left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, transparent, #38bdf8, #ffffff, #38bdf8, transparent);
            box-shadow: 0 0 10px #38bdf8, 0 0 20px #0ea5e9;
            opacity: 0;
            pointer-events: none;
            z-index: 5;
        }
        .sim-visual-core.is-scanning .sim-laser-sweep {
            opacity: 1;
            animation: sweepScan 1.1s infinite alternate ease-in-out;
        }
        @keyframes sweepScan {
            0% { top: 10%; }
            100% { top: 90%; }
        }

        /* Deadbolt Bolt Indicator Bar */
        .deadbolt-cylinder {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        /* Simulator Action Buttons */
        .sim-btn-deck {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 7px;
            margin-top: 14px;
        }
        @media (max-width: 768px) {
            .sim-btn-deck {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 576px) {
            .sim-hud-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 12px !important;
                padding: 12px 14px !important;
            }
            .sim-hud-header > div:last-child {
                width: 100% !important;
                flex-direction: row !important;
                justify-content: space-between !important;
                align-items: center !important;
            }
            .sim-stage {
                padding: 14px 12px !important;
            }
            .sim-visual-core {
                padding: 14px 12px !important;
                gap: 12px !important;
            }
            .sim-avatar-orb {
                width: 52px !important;
                height: 52px !important;
                font-size: 22px !important;
            }
            #simActionTitle {
                font-size: 1rem !important;
            }
            .sim-btn-deck {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 6px !important;
            }
            .sim-btn-deck .sim-btn-key:last-child {
                grid-column: span 2 !important;
            }
            .sim-vitals-row {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 6px !important;
            }
        }
        .sim-btn-key {
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            padding: 10px 6px;
            border-radius: 12px;
            font-size: 0.73rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            text-align: center;
            user-select: none;
        }
        .sim-btn-key:hover {
            background: rgba(13, 110, 253, 0.25);
            border-color: rgba(56, 189, 248, 0.5);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
        }
        .sim-btn-key:active {
            transform: translateY(0);
        }
        .sim-btn-key.active-action {
            background: rgba(13, 110, 253, 0.4);
            border-color: #38bdf8;
            color: #ffffff;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.5);
        }
        .sim-btn-key.btn-tamper:hover {
            background: rgba(239, 68, 68, 0.25);
            border-color: rgba(239, 68, 68, 0.6);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        /* Telemetry Row in Simulator */
        .sim-vitals-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            margin-top: 14px;
        }
        @media (max-width: 768px) {
            .sim-vitals-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .sim-vital-box {
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 11px;
            padding: 8px 10px;
            text-align: center;
        }
        .sim-vital-lbl {
            font-size: 0.63rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 2px;
            display: block;
        }
        .sim-vital-val {
            font-size: 0.74rem;
            font-weight: 800;
            color: #f1f5f9;
        }

        /* Dynamic Live Audit Console */
        .sim-audit-console {
            background: rgba(10, 15, 29, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 14px;
            padding: 10px 14px;
            margin-top: 14px;
            max-height: 125px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(56, 189, 248, 0.3) transparent;
        }
        .sim-audit-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 5px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            font-size: 0.72rem;
            animation: slideInAudit 0.35s ease-out;
        }
        .sim-audit-item:last-child {
            border-bottom: none;
        }
        @keyframes slideInAudit {
            0% { opacity: 0; transform: translateY(-8px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* 4-Layer Defense Grid */
        .defense-layer-card {
            background: #ffffff;
            border-radius: 22px;
            border: 1.5px solid #e2e8f0;
            padding: 28px 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.35s ease;
            height: 100%;
        }
        .defense-layer-card:hover {
            transform: translateY(-6px);
            border-color: #38bdf8;
            box-shadow: 0 18px 36px rgba(13, 110, 253, 0.1);
        }
        .defense-layer-num {
            position: absolute;
            top: 14px;
            right: 18px;
            font-size: 2.2rem;
            font-weight: 900;
            color: rgba(226, 232, 240, 0.7);
            line-height: 1;
            user-select: none;
        }
        .defense-layer-card:hover .defense-layer-num {
            color: rgba(13, 110, 253, 0.15);
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
                    <span class="fw-bold text-dark navbar-brand-title" style="font-size: 1.1rem; line-height: 1.1; letter-spacing: -0.3px;">SECURELAB</span>
                    <small class="text-muted fw-semibold navbar-brand-subtitle" style="font-size: 0.72rem; letter-spacing: 0.5px;">BSIS COMPUTER LABORATORY</small>
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
                <div class="d-flex align-items-center gap-2 ms-lg-3 mt-2 mt-lg-0 navbar-portal-actions">
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
                <div class="row align-items-center g-4 g-lg-5">
                    
                    <!-- Left Hero Content -->
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                        
                        <!-- Institution Badge -->
                        <div class="hero-badge-pill mb-3">
                            <span class="pulse-indicator"></span>
                            <span>Talibon Polytechnic College • BSIS Department</span>
                        </div>

                        <!-- Main Title -->
                        <h1 class="hero-title mb-3">
                            Smart Biometric <br class="d-none d-md-inline">
                            <span class="highlight-text">Access & Security</span> <br class="d-none d-md-inline">
                            Laboratory Portal
                        </h1>

                        <!-- Interactive Sandbox Indicator -->
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 rounded-sm-pill mb-3 shadow-sm text-wrap" style="max-width: 100%; background: rgba(13, 110, 253, 0.08); border: 1.5px dashed rgba(13, 110, 253, 0.35);">
                            <span class="spinner-grow spinner-grow-sm text-primary flex-shrink-0" style="width: 8px; height: 8px;"></span>
                            <span class="small fw-bold text-primary">Interactive IoT Sandbox: Test hardware actions on the simulator! 👉</span>
                        </div>

                        <p class="text-muted lead fw-normal mb-4" style="font-size: 1.05rem; line-height: 1.65;">
                            An IoT-powered smart door lock and laboratory monitoring system engineered for authorized 
                            <strong>College Deans</strong>, <strong>Faculty & Staff</strong>, and <strong>System Administrators</strong>. 
                            Combining optical biometric authentication with FIDO2 cryptographic passkeys and real-time incident telemetry.
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
                        <div class="d-flex flex-wrap gap-3 hero-action-btns">
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
                        <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4 mt-4 pt-2 text-muted small fw-semibold trust-markers">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success fs-6"></i>
                                <span>500 DPI Biometrics</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success fs-6"></i>
                                <span>FIDO2 Passkeys</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-check-circle text-success fs-6"></i>
                                <span>Instant Audit Logs</span>
                            </div>
                        </div>

                    </div>

                    <!-- Right Hero Visual Showcase: Interactive Cyber-Physical Laboratory Simulator -->
                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000">
                        <div class="sim-shell">
                            
                            <!-- HUD Top Bar -->
                            <div class="sim-hud-header d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                        <span class="sim-node-badge">
                                            <i class="fas fa-microchip"></i> ESP8266 Live Node
                                        </span>
                                        <span class="sim-signal-pill">
                                            <i class="fas fa-wifi"></i> -58 dBm • 99%
                                        </span>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-white" style="letter-spacing: -0.2px;">BSIS Laboratory Room 101</h6>
                                    <small class="text-white-50" style="font-size: 0.72rem;">Hardware Hub • Solenoid 12V + Optical Biometrics</small>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-2">
                                    <span id="simStateBadge" class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold" style="font-size: 0.72rem;">
                                        <i class="fas fa-shield-alt me-1"></i> ARMED & LOCKED
                                    </span>
                                    <button id="simSoundToggle" class="sim-sound-toggle" title="Toggle audio effects">
                                        <i id="simSoundIcon" class="fas fa-volume-up text-info"></i> <span id="simSoundLabel">Audio: ON</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Visual Chamber Stage -->
                            <div class="sim-stage">
                                <!-- Main Holographic Status Core -->
                                <div id="simVisualCore" class="sim-visual-core state-armed">
                                    <!-- Laser Sweep Line -->
                                    <div class="sim-laser-sweep"></div>

                                    <!-- Left: Avatar Icon Orb -->
                                    <div id="simAvatarOrb" class="sim-avatar-orb state-armed">
                                        <i id="simIcon" class="fas fa-lock"></i>
                                    </div>

                                    <!-- Middle: Status Descriptions & Solenoid Status -->
                                    <div class="flex-grow-1 ms-2">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <span id="simStateLabel" class="text-uppercase fw-bold text-success" style="font-size: 0.7rem; letter-spacing: 0.8px;">
                                                SECURE STANDBY
                                            </span>
                                            <span id="simDeadboltStatus" class="deadbolt-cylinder bg-dark text-success border border-success border-opacity-25">
                                                <i class="fas fa-bolt"></i> <span>SOLENOID: ENGAGED</span>
                                            </span>
                                        </div>
                                        <h5 id="simActionTitle" class="fw-bold mb-1 text-white" style="font-size: 1.15rem;">
                                            LABORATORY SECURED
                                        </h5>
                                        <p id="simActionDesc" class="text-white-50 small mb-0" style="font-size: 0.76rem;">
                                            12V Fail-Secure Deadbolt Locked • Optical Sensor Standby
                                        </p>

                                        <!-- Auto-Relock Countdown Progress Bar -->
                                        <div id="simCountdownWrap" class="progress mt-2 d-none" style="height: 5px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                                            <div id="simCountdownBar" class="progress-bar bg-info progress-bar-striped progress-bar-animated" style="width: 100%; transition: width 0.1s linear;"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Interactive Hardware Action Deck (5 Action Keys) -->
                                <div class="sim-btn-deck">
                                    <button id="btnSimFingerprint" class="sim-btn-key" title="Simulate Authorized Biometric Scan">
                                        <i class="fas fa-fingerprint text-info fs-5"></i>
                                        <span>Scan Fingerprint</span>
                                    </button>

                                    <button id="btnSimPin" class="sim-btn-key" title="Simulate 4-Digit Keypad Passcode Entry">
                                        <i class="fas fa-keyboard text-primary fs-5"></i>
                                        <span>Enter PIN</span>
                                    </button>

                                    <button id="btnSimDoorbell" class="sim-btn-key" title="Simulate Doorbell Chime Push">
                                        <i class="fas fa-bell text-warning fs-5"></i>
                                        <span>Ring Doorbell</span>
                                    </button>

                                    <button id="btnSimTamper" class="sim-btn-key btn-tamper" title="Simulate Forced Entry / Vibration Alarm">
                                        <i class="fas fa-radiation-alt text-danger fs-5"></i>
                                        <span id="btnTamperLabel">Force Tamper</span>
                                    </button>

                                    <button id="btnSimReset" class="sim-btn-key" title="Reset Simulator to Clean Armed Standby">
                                        <i class="fas fa-shield-alt text-success fs-5"></i>
                                        <span>Reset / Re-Arm</span>
                                    </button>
                                </div>

                                <!-- Live Hardware Telemetry Strip -->
                                <div class="sim-vitals-row">
                                    <div class="sim-vital-box">
                                        <span class="sim-vital-lbl"><i class="fas fa-plug text-primary me-1"></i>Solenoid 12V</span>
                                        <span id="telSolenoid" class="sim-vital-val text-success">ENGAGED</span>
                                    </div>
                                    <div class="sim-vital-box">
                                        <span class="sim-vital-lbl"><i class="fas fa-fingerprint text-info me-1"></i>Biometrics</span>
                                        <span id="telBiometric" class="sim-vital-val text-info">500 DPI ACTIVE</span>
                                    </div>
                                    <div class="sim-vital-box">
                                        <span class="sim-vital-lbl"><i class="fas fa-hashtag text-warning me-1"></i>Passcode</span>
                                        <span id="telPin" class="sim-vital-val text-light">4-DIGIT READY</span>
                                    </div>
                                    <div class="sim-vital-box">
                                        <span class="sim-vital-lbl"><i class="fas fa-volume-up text-danger me-1"></i>Siren Alarm</span>
                                        <span id="telAlarm" class="sim-vital-val text-muted">STANDBY</span>
                                    </div>
                                </div>

                                <!-- Dynamic Live Audit Stream -->
                                <div class="sim-audit-console">
                                    <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom border-secondary border-opacity-25">
                                        <small class="fw-bold text-info text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                                            <i class="fas fa-terminal me-1"></i> Real-Time Telemetry & Audit Stream
                                        </small>
                                        <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25" style="font-size: 0.64rem;">
                                            <span class="spinner-grow spinner-grow-sm me-1" style="width: 5px; height: 5px;"></span> Streaming
                                        </span>
                                    </div>
                                    <div id="simAuditFeed">
                                        <div class="sim-audit-item text-white-50">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-25">10:42 AM</span>
                                                <span class="text-white fw-semibold">Faculty Prof. Santos</span>
                                                <span class="text-muted d-none d-sm-inline">• Biometric Scan Verified</span>
                                            </div>
                                            <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25">Access Granted</span>
                                        </div>
                                        <div class="sim-audit-item text-white-50">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-secondary bg-opacity-20 text-secondary border border-secondary border-opacity-25">09:15 AM</span>
                                                <span class="text-white fw-semibold">ESP8266 Hub Diagnostic</span>
                                                <span class="text-muted d-none d-sm-inline">• Optical Lens Nominal</span>
                                            </div>
                                            <span class="badge bg-info bg-opacity-20 text-info border border-info border-opacity-25">System Armed</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Personnel Portal Quick Link -->
                                <div class="mt-3 text-center">
                                    <button class="btn btn-outline-info w-100 rounded-pill py-2 fw-bold small text-white border-opacity-50" data-bs-toggle="modal" data-bs-target="#loginModal">
                                        <i class="fas fa-user-shield me-2 text-info"></i> Sign In to Authorized Personnel Portal <i class="fas fa-arrow-right ms-1"></i>
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

        <!-- 🟢 4-LAYER CYBER-PHYSICAL DEFENSE ARCHITECTURE -->
        <section id="features" class="py-5 bg-white border-top">
            <div class="container py-4 text-center">
                
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">
                    Multi-Tier Cyber-Physical Security
                </span>
                <h2 class="section-heading mt-2">4-Layer Integrated Defense Architecture</h2>
                <p class="text-muted mb-5 mx-auto" style="max-width: 680px;">
                    Engineered to replace vulnerable physical keys with an automated, tamper-resistant access ecosystem combining hardware robotics and cryptographic web defense.
                </p>

                <div class="row g-4 text-start">
                    
                    <!-- Layer 1 -->
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="defense-layer-card shadow-sm">
                            <span class="defense-layer-num">01</span>
                            <div class="stat-icon-wrapper bg-primary bg-opacity-10 text-primary mb-3">
                                <i class="fas fa-lock"></i>
                            </div>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 fw-bold mb-2" style="font-size: 0.7rem;">Physical Layer</span>
                            <h5 class="fw-bold mb-2 text-dark">12V Solenoid Deadbolt</h5>
                            <p class="text-muted small mb-0 lh-base">
                                Fail-secure electromagnetic lock with heavy-duty holding force. Magnetic reed switch sensors instantly detect door opening and automatically re-engage upon closing.
                            </p>
                        </div>
                    </div>

                    <!-- Layer 2 -->
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="defense-layer-card shadow-sm">
                            <span class="defense-layer-num">02</span>
                            <div class="stat-icon-wrapper bg-success bg-opacity-10 text-success mb-3">
                                <i class="fas fa-fingerprint"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 fw-bold mb-2" style="font-size: 0.7rem;">Biometric Layer</span>
                            <h5 class="fw-bold mb-2 text-dark">500 DPI Optical Sensor</h5>
                            <p class="text-muted small mb-0 lh-base">
                                High-precision optical prism comparison in &lt; 0.8s with false-acceptance rate &lt; 0.001%. Local secure flash template storage prevents biometric spoofing attacks.
                            </p>
                        </div>
                    </div>

                    <!-- Layer 3 -->
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="defense-layer-card shadow-sm">
                            <span class="defense-layer-num">03</span>
                            <div class="stat-icon-wrapper bg-info bg-opacity-10 text-info mb-3">
                                <i class="fas fa-key"></i>
                            </div>
                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 py-1 fw-bold mb-2" style="font-size: 0.7rem;">Identity Layer</span>
                            <h5 class="fw-bold mb-2 text-dark">FIDO2 WebAuthn Passkeys</h5>
                            <p class="text-muted small mb-0 lh-base">
                                Zero-password cryptographic authentication for Dean, Faculty, and Admin portals using device-bound biometric sensors (Touch ID, Windows Hello, Face ID).
                            </p>
                        </div>
                    </div>

                    <!-- Layer 4 -->
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="defense-layer-card shadow-sm">
                            <span class="defense-layer-num">04</span>
                            <div class="stat-icon-wrapper bg-danger bg-opacity-10 text-danger mb-3">
                                <i class="fas fa-satellite-dish"></i>
                            </div>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 fw-bold mb-2" style="font-size: 0.7rem;">Telemetry Layer</span>
                            <h5 class="fw-bold mb-2 text-dark">Telemetry & Web Push</h5>
                            <p class="text-muted small mb-0 lh-base">
                                NodeMCU ESP8266 continuous heartbeat telemetry. Immediately broadcasts tamper vibration alerts, doorbell chimes, and audit trails directly via Web Push.
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
        AOS.init({ duration: 800, once: true, offset: 20, disable: 'mobile' });

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

    <!-- 🟢 INTERACTIVE LIVE LABORATORY SIMULATOR ENGINE (OPTION 3) -->
    <script>
        (function() {
            // 1. Audio Synthesizer & Audio Asset Engine
            class SimulatorAudioEngine {
                constructor() {
                    this.ctx = null;
                    this.muted = false;
                    this.unlockAudio = new Audio('{{ asset("assets/sounds/notification.mp3") }}');
                    this.alarmAudio = new Audio('{{ asset("assets/sounds/alarm.mp3") }}');
                    this.alarmAudio.loop = true;
                }

                init() {
                    if (!this.ctx) {
                        const AudioCtx = window.AudioContext || window.webkitAudioContext;
                        if (AudioCtx) this.ctx = new AudioCtx();
                    }
                    if (this.ctx && this.ctx.state === 'suspended') {
                        this.ctx.resume();
                    }
                }

                toggleMute() {
                    this.muted = !this.muted;
                    if (this.muted) {
                        this.stopAlarm();
                    }
                    return this.muted;
                }

                playTone(freq, type, duration, delay = 0, gainVal = 0.1) {
                    if (this.muted) return;
                    this.init();
                    if (!this.ctx) return;

                    setTimeout(() => {
                        try {
                            const osc = this.ctx.createOscillator();
                            const gain = this.ctx.createGain();
                            osc.type = type;
                            osc.frequency.setValueAtTime(freq, this.ctx.currentTime);
                            gain.gain.setValueAtTime(gainVal, this.ctx.currentTime);
                            gain.gain.exponentialRampToValueAtTime(0.001, this.ctx.currentTime + duration);
                            osc.connect(gain);
                            gain.connect(this.ctx.destination);
                            osc.start();
                            osc.stop(this.ctx.currentTime + duration);
                        } catch(e) {}
                    }, delay);
                }

                playScanBeep() {
                    if (this.muted) return;
                    this.playTone(880, 'sine', 0.08, 0, 0.12);
                    this.playTone(1320, 'sine', 0.12, 100, 0.12);
                }

                playKeypadBeep() {
                    if (this.muted) return;
                    this.playTone(941, 'triangle', 0.06, 0, 0.15);
                }

                playUnlockChime() {
                    if (this.muted) return;
                    try {
                        this.unlockAudio.currentTime = 0;
                        const p = this.unlockAudio.play();
                        if (p && p.catch) {
                            p.catch(() => {
                                // Synthesizer musical chime fallback (C5 -> E5 -> G5 -> C6)
                                this.playTone(523.25, 'sine', 0.14, 0, 0.12);
                                this.playTone(659.25, 'sine', 0.14, 110, 0.12);
                                this.playTone(783.99, 'sine', 0.18, 220, 0.12);
                                this.playTone(1046.50, 'sine', 0.28, 330, 0.15);
                            });
                        }
                    } catch(e) {}
                }

                playDoorbell() {
                    if (this.muted) return;
                    try {
                        this.unlockAudio.currentTime = 0;
                        const p = this.unlockAudio.play();
                        if (p && p.catch) {
                            p.catch(() => {
                                // Ding-dong chime (E5 -> C5)
                                this.playTone(659.25, 'sine', 0.28, 0, 0.18);
                                this.playTone(523.25, 'sine', 0.45, 260, 0.18);
                            });
                        }
                    } catch(e) {}
                }

                playAlarmSiren() {
                    if (this.muted) return;
                    try {
                        this.alarmAudio.currentTime = 0;
                        const p = this.alarmAudio.play();
                        if (p && p.catch) {
                            p.catch(() => {
                                // Synthesizer siren oscillations fallback
                                this.playTone(480, 'sawtooth', 0.25, 0, 0.12);
                                this.playTone(850, 'sawtooth', 0.25, 250, 0.12);
                                this.playTone(480, 'sawtooth', 0.25, 500, 0.12);
                                this.playTone(850, 'sawtooth', 0.25, 750, 0.12);
                            });
                        }
                    } catch(e) {}
                }

                stopAlarm() {
                    try {
                        this.alarmAudio.pause();
                        this.alarmAudio.currentTime = 0;
                    } catch(e) {}
                }

                playMechanicalClick() {
                    if (this.muted) return;
                    this.playTone(180, 'triangle', 0.06, 0, 0.15);
                    this.playTone(90, 'sine', 0.10, 50, 0.2);
                }
            }

            const audio = new SimulatorAudioEngine();

            // 2. DOM Elements Cache
            const dom = {
                soundToggle: document.getElementById('simSoundToggle'),
                soundIcon: document.getElementById('simSoundIcon'),
                soundLabel: document.getElementById('simSoundLabel'),
                visualCore: document.getElementById('simVisualCore'),
                avatarOrb: document.getElementById('simAvatarOrb'),
                simIcon: document.getElementById('simIcon'),
                stateBadge: document.getElementById('simStateBadge'),
                stateLabel: document.getElementById('simStateLabel'),
                deadboltStatus: document.getElementById('simDeadboltStatus'),
                actionTitle: document.getElementById('simActionTitle'),
                actionDesc: document.getElementById('simActionDesc'),
                countdownWrap: document.getElementById('simCountdownWrap'),
                countdownBar: document.getElementById('simCountdownBar'),
                btnFp: document.getElementById('btnSimFingerprint'),
                btnPin: document.getElementById('btnSimPin'),
                btnBell: document.getElementById('btnSimDoorbell'),
                btnTamper: document.getElementById('btnSimTamper'),
                btnTamperLabel: document.getElementById('btnTamperLabel'),
                btnReset: document.getElementById('btnSimReset'),
                telSolenoid: document.getElementById('telSolenoid'),
                telBiometric: document.getElementById('telBiometric'),
                telPin: document.getElementById('telPin'),
                telAlarm: document.getElementById('telAlarm'),
                auditFeed: document.getElementById('simAuditFeed')
            };

            if (!dom.visualCore) return;

            let currentState = 'ARMED'; // 'ARMED', 'SCANNING', 'UNLOCKED', 'ALARM', 'DOORBELL'
            let activeTimer = null;
            let countdownInterval = null;

            // 3. Audio Toggle
            if (dom.soundToggle) {
                dom.soundToggle.addEventListener('click', function() {
                    const isMuted = audio.toggleMute();
                    if (isMuted) {
                        dom.soundIcon.className = 'fas fa-volume-mute text-secondary';
                        dom.soundLabel.textContent = 'Muted';
                        dom.soundToggle.style.opacity = '0.7';
                    } else {
                        dom.soundIcon.className = 'fas fa-volume-up text-info';
                        dom.soundLabel.textContent = 'Audio: ON';
                        dom.soundToggle.style.opacity = '1';
                    }
                });
            }

            // 4. Audit Log Helper
            function addAuditLog(title, subtitle, badgeClass, badgeText) {
                if (!dom.auditFeed) return;
                const now = new Date();
                let timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                const item = document.createElement('div');
                item.className = 'sim-audit-item text-white-50';
                item.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary bg-opacity-25 text-white-50 border border-white border-opacity-10">${timeStr}</span>
                        <span class="text-white fw-semibold">${title}</span>
                        <span class="text-muted d-none d-sm-inline">• ${subtitle}</span>
                    </div>
                    <span class="badge ${badgeClass}">${badgeText}</span>
                `;

                dom.auditFeed.insertBefore(item, dom.auditFeed.firstChild);

                // Keep stream clean (max 6 items)
                while (dom.auditFeed.children.length > 6) {
                    dom.auditFeed.removeChild(dom.auditFeed.lastChild);
                }
            }

            function clearAllTimers() {
                if (activeTimer) clearTimeout(activeTimer);
                if (countdownInterval) clearInterval(countdownInterval);
                dom.visualCore.classList.remove('is-scanning');
                if (dom.countdownWrap) dom.countdownWrap.classList.add('d-none');
            }

            // 5. Reset to Armed Standby
            function resetToArmed(silent = false) {
                clearAllTimers();
                currentState = 'ARMED';
                audio.stopAlarm();
                if (!silent) audio.playMechanicalClick();

                // Core Visuals
                dom.visualCore.className = 'sim-visual-core state-armed';
                dom.avatarOrb.className = 'sim-avatar-orb state-armed';
                dom.simIcon.className = 'fas fa-lock';

                // Badges & Labels
                dom.stateBadge.className = 'badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-1 rounded-pill fw-bold';
                dom.stateBadge.innerHTML = '<i class="fas fa-shield-alt me-1"></i> ARMED & LOCKED';

                dom.stateLabel.className = 'text-uppercase fw-bold text-success';
                dom.stateLabel.textContent = 'SECURE STANDBY';

                dom.deadboltStatus.className = 'deadbolt-cylinder bg-dark text-success border border-success border-opacity-25';
                dom.deadboltStatus.innerHTML = '<i class="fas fa-bolt"></i> <span>SOLENOID: ENGAGED</span>';

                dom.actionTitle.textContent = 'LABORATORY SECURED';
                dom.actionDesc.textContent = '12V Fail-Secure Deadbolt Locked • Optical Sensor Standby';

                if (dom.btnTamperLabel) dom.btnTamperLabel.textContent = 'Force Tamper';

                // Telemetry
                dom.telSolenoid.textContent = 'ENGAGED';
                dom.telSolenoid.className = 'sim-vital-val text-success';

                dom.telBiometric.textContent = '500 DPI ACTIVE';
                dom.telBiometric.className = 'sim-vital-val text-info';

                dom.telPin.textContent = '4-DIGIT READY';
                dom.telPin.className = 'sim-vital-val text-light';

                dom.telAlarm.textContent = 'STANDBY';
                dom.telAlarm.className = 'sim-vital-val text-muted';
            }

            // 6. Action 1: Fingerprint Scan Simulation
            function simulateFingerprint() {
                clearAllTimers();
                audio.stopAlarm();
                currentState = 'SCANNING';

                // Visual Scanning State
                dom.visualCore.className = 'sim-visual-core state-scanning is-scanning';
                dom.avatarOrb.className = 'sim-avatar-orb state-scanning';
                dom.simIcon.className = 'fas fa-fingerprint';

                dom.stateBadge.className = 'badge bg-info-subtle text-info border border-info border-opacity-25 px-3 py-1 rounded-pill fw-bold';
                dom.stateBadge.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> SCANNING...';

                dom.stateLabel.className = 'text-uppercase fw-bold text-info';
                dom.stateLabel.textContent = 'BIOMETRIC SCAN';

                dom.actionTitle.textContent = 'SCANNING FINGERPRINT...';
                dom.actionDesc.textContent = 'Optical prism active • Matching minutiae points against flash memory';

                dom.telBiometric.textContent = 'MATCHING...';
                dom.telBiometric.className = 'sim-vital-val text-warning';

                audio.playScanBeep();

                // Match Success after 750ms
                activeTimer = setTimeout(() => {
                    currentState = 'UNLOCKED';
                    dom.visualCore.classList.remove('is-scanning');
                    dom.visualCore.className = 'sim-visual-core state-unlocked';
                    dom.avatarOrb.className = 'sim-avatar-orb state-unlocked';
                    dom.simIcon.className = 'fas fa-lock-open';

                    dom.stateBadge.className = 'badge bg-info text-dark px-3 py-1 rounded-pill fw-bold shadow-sm';
                    dom.stateBadge.innerHTML = '<i class="fas fa-lock-open me-1"></i> UNLOCKED';

                    dom.stateLabel.className = 'text-uppercase fw-bold text-info';
                    dom.stateLabel.textContent = 'ACCESS GRANTED';

                    dom.deadboltStatus.className = 'deadbolt-cylinder bg-dark text-info border border-info border-opacity-25';
                    dom.deadboltStatus.innerHTML = '<i class="fas fa-bolt"></i> <span>SOLENOID: RELEASED</span>';

                    dom.actionTitle.textContent = 'ACCESS GRANTED: PROF. SANTOS';
                    dom.actionDesc.textContent = 'BSIS Faculty Verified • Actuating 12V Solenoid • Auto-relock armed';

                    dom.telSolenoid.textContent = 'RELEASED (12V)';
                    dom.telSolenoid.className = 'sim-vital-val text-info';

                    dom.telBiometric.textContent = 'MATCH #04 (99.2%)';
                    dom.telBiometric.className = 'sim-vital-val text-success';

                    audio.playUnlockChime();
                    addAuditLog('Faculty Prof. Santos', 'Biometric Scan Verified • Room 101 Unlocked', 'bg-success bg-opacity-20 text-success border border-success border-opacity-25', 'Access Granted');

                    // Start 5-Second Auto-Relock Countdown
                    startRelockCountdown(5000);
                }, 750);
            }

            // 7. Action 2: Keypad PIN Simulation
            function simulatePin() {
                clearAllTimers();
                audio.stopAlarm();
                currentState = 'PIN';

                dom.visualCore.className = 'sim-visual-core state-scanning';
                dom.avatarOrb.className = 'sim-avatar-orb state-scanning';
                dom.simIcon.className = 'fas fa-keyboard';

                dom.stateBadge.className = 'badge bg-primary-subtle text-primary border border-primary border-opacity-25 px-3 py-1 rounded-pill fw-bold';
                dom.stateBadge.innerHTML = '<i class="fas fa-calculator me-1"></i> ENTERING PIN';

                dom.stateLabel.className = 'text-uppercase fw-bold text-primary';
                dom.stateLabel.textContent = 'KEYPAD AUTHENTICATION';

                dom.actionTitle.textContent = 'PIN: [ ● ● ● ● ]';
                dom.actionDesc.textContent = 'Verifying 4-digit room entry passcode for BSIS Room 101...';

                dom.telPin.textContent = 'VERIFYING...';
                dom.telPin.className = 'sim-vital-val text-warning';

                // Play rapid keystroke beeps
                audio.playKeypadBeep();
                setTimeout(() => audio.playKeypadBeep(), 100);
                setTimeout(() => audio.playKeypadBeep(), 200);
                setTimeout(() => audio.playKeypadBeep(), 300);

                activeTimer = setTimeout(() => {
                    currentState = 'UNLOCKED';
                    dom.visualCore.className = 'sim-visual-core state-unlocked';
                    dom.avatarOrb.className = 'sim-avatar-orb state-unlocked';
                    dom.simIcon.className = 'fas fa-door-open';

                    dom.stateBadge.className = 'badge bg-info text-dark px-3 py-1 rounded-pill fw-bold shadow-sm';
                    dom.stateBadge.innerHTML = '<i class="fas fa-lock-open me-1"></i> UNLOCKED';

                    dom.stateLabel.className = 'text-uppercase fw-bold text-info';
                    dom.stateLabel.textContent = 'PIN VERIFIED';

                    dom.deadboltStatus.className = 'deadbolt-cylinder bg-dark text-info border border-info border-opacity-25';
                    dom.deadboltStatus.innerHTML = '<i class="fas fa-bolt"></i> <span>SOLENOID: RELEASED</span>';

                    dom.actionTitle.textContent = 'ACCESS GRANTED: ROOM PASSCODE';
                    dom.actionDesc.textContent = 'Authorized 4-Digit Matrix Keypad Entry • Solenoid Power Disengaged';

                    dom.telSolenoid.textContent = 'RELEASED (12V)';
                    dom.telSolenoid.className = 'sim-vital-val text-info';

                    dom.telPin.textContent = 'AUTHENTICATED';
                    dom.telPin.className = 'sim-vital-val text-success';

                    audio.playUnlockChime();
                    addAuditLog('Keypad Passcode', 'Room 101 PIN Verified • Solenoid Actuated', 'bg-info bg-opacity-20 text-info border border-info border-opacity-25', 'Passcode OK');

                    startRelockCountdown(5000);
                }, 600);
            }

            // 8. Action 3: Doorbell Chime Simulation
            function simulateDoorbell() {
                if (currentState === 'ALARM') return;
                audio.playDoorbell();

                dom.stateBadge.className = 'badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold shadow-sm';
                dom.stateBadge.innerHTML = '<i class="fas fa-bell me-1"></i> DOORBELL CHIME';

                dom.stateLabel.className = 'text-uppercase fw-bold text-warning';
                dom.stateLabel.textContent = 'VISITOR AT DOOR';

                dom.actionTitle.textContent = '🔔 DOORBELL CHIME BROADCAST';
                dom.actionDesc.textContent = 'Visitor pressed external push-button • Push notification dispatched';

                addAuditLog('Doorbell Push-Button', 'Visitor at Room 101 • Push Notification Alert Sent', 'bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25', 'Doorbell Rang');

                setTimeout(() => {
                    if (currentState === 'ARMED') {
                        resetToArmed(true);
                    }
                }, 3000);
            }

            // 9. Action 4: Tamper / Forced Entry Alarm Simulation
            function simulateTamper() {
                if (currentState === 'ALARM') {
                    // Clicking again silences siren
                    resetToArmed();
                    addAuditLog('Security Admin', 'Alarm Silenced & Sensor Reset to Standby', 'bg-success bg-opacity-20 text-success border border-success border-opacity-25', 'Silenced');
                    return;
                }

                clearAllTimers();
                currentState = 'ALARM';

                dom.visualCore.className = 'sim-visual-core state-alarm';
                dom.avatarOrb.className = 'sim-avatar-orb state-alarm';
                dom.simIcon.className = 'fas fa-radiation-alt text-danger';

                dom.stateBadge.className = 'badge bg-danger text-white px-3 py-1 rounded-pill fw-bold shadow-sm';
                dom.stateBadge.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> CRITICAL ALARM';

                dom.stateLabel.className = 'text-uppercase fw-bold text-danger';
                dom.stateLabel.textContent = 'SECURITY BREACH DETECTED';

                dom.deadboltStatus.className = 'deadbolt-cylinder bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50';
                dom.deadboltStatus.innerHTML = '<i class="fas fa-lock"></i> <span>FAIL-SECURE LOCKDOWN</span>';

                dom.actionTitle.textContent = '🚨 FORCED ENTRY / TAMPER ALERT!';
                dom.actionDesc.textContent = 'Vibration / door breach sensor tripped • 105dB local siren active • Web Push alert dispatched';

                if (dom.btnTamperLabel) dom.btnTamperLabel.textContent = 'Silence Siren';

                dom.telSolenoid.textContent = 'LOCKDOWN';
                dom.telSolenoid.className = 'sim-vital-val text-danger';

                dom.telAlarm.textContent = 'ACTIVE (105dB)';
                dom.telAlarm.className = 'sim-vital-val text-danger fw-bold';

                audio.playAlarmSiren();
                addAuditLog('SECURITY BREACH', 'Forced Tamper Vibration Tripped • 105dB Siren Active', 'bg-danger bg-opacity-25 text-danger border border-danger border-opacity-50', 'Alarm Fired');
            }

            // 10. Auto-Relock Countdown
            function startRelockCountdown(ms) {
                if (!dom.countdownWrap || !dom.countdownBar) return;
                dom.countdownWrap.classList.remove('d-none');
                dom.countdownBar.style.width = '100%';

                const startTime = Date.now();
                countdownInterval = setInterval(() => {
                    const elapsed = Date.now() - startTime;
                    const remainingPercent = Math.max(0, 100 - (elapsed / ms) * 100);
                    dom.countdownBar.style.width = remainingPercent + '%';

                    if (elapsed >= ms) {
                        clearInterval(countdownInterval);
                        dom.countdownWrap.classList.add('d-none');
                        resetToArmed();
                        addAuditLog('Magnetic Reed Switch', 'Door Closed • Solenoid Re-Armed • Room Secured', 'bg-success bg-opacity-20 text-success border border-success border-opacity-25', 'Re-Locked');
                    }
                }, 50);
            }

            // 11. Event Listeners
            if (dom.btnFp) dom.btnFp.addEventListener('click', simulateFingerprint);
            if (dom.btnPin) dom.btnPin.addEventListener('click', simulatePin);
            if (dom.btnBell) dom.btnBell.addEventListener('click', simulateDoorbell);
            if (dom.btnTamper) dom.btnTamper.addEventListener('click', simulateTamper);
            if (dom.btnReset) dom.btnReset.addEventListener('click', () => {
                resetToArmed();
                addAuditLog('System Diagnostics', 'Manual Operator Reset • All Peripherals Nominal', 'bg-info bg-opacity-20 text-info border border-info border-opacity-25', 'Re-Armed');
            });

            // 12. Idle Heartbeat (every 30s)
            setInterval(() => {
                if (currentState === 'ARMED' && Math.random() > 0.4) {
                    addAuditLog('ESP8266 Live Node', 'Telemetry Ping Nominal • WiFi: -58 dBm • Solenoid: HIGH', 'bg-secondary bg-opacity-20 text-white-50 border border-secondary border-opacity-25', 'Heartbeat OK');
                }
            }, 30000);

        })();
    </script>
</body>
</html>