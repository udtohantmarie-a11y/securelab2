<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">

<meta name="theme-color" content="#0a192f">
<link rel="manifest" href="{{ asset('manifest.json') }}">

<!-- Bootstrap 5.3 & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

<style>
    /* =========================================================
       🎨 MODERN DESIGN TOKENS (LIGHT MODE DEFAULT)
    ========================================================= */
    :root {
        --font-main: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        
        /* Core Brand Palette */
        --primary-blue: #1a56db;
        --primary-hover: #1347ba;
        --primary-rgb: 26, 86, 219;
        --primary-glow: rgba(26, 86, 219, 0.25);
        
        --sidebar-bg-start: #0a172e;
        --sidebar-bg-mid: #0c1c38;
        --sidebar-bg-end: #071022;
        --sidebar-active: #1e3a8a;
        --sidebar-hover: rgba(255, 255, 255, 0.08);

        /* Surface & Background */
        --bg-body: #f4f6fb;
        --bg-surface: #ffffff;
        --bg-subtle: #f8fafc;
        --border-color: #e2e8f0;
        --border-color-light: #edf2f7;

        /* Typography */
        --text-dark: #0f172a;
        --text-main: #0f172a;
        --text-body: #334155;
        --text-muted: #64748b;
        --text-subtle: #94a3b8;

        /* Accents */
        --accent-success: #10b981;
        --accent-warning: #f59e0b;
        --accent-danger: #ef4444;
        --accent-info: #06b6d4;

        /* Shadows & Radii */
        --shadow-xs: 0 1px 2px rgba(15, 23, 42, 0.05);
        --shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.02);
        --shadow-md: 0 8px 24px -4px rgba(15, 23, 42, 0.06), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
        --shadow-lg: 0 20px 40px -8px rgba(15, 23, 42, 0.1), 0 8px 16px -4px rgba(15, 23, 42, 0.04);
        --radius-sm: 10px;
        --radius-md: 14px;
        --radius-lg: 20px;
        --radius-xl: 24px;

        /* 🛡️ Cyber-Defense Command Center Palette */
        --cyber-cyan: #00d2ff;
        --cyber-cyan-glow: rgba(0, 210, 255, 0.35);
        --cyber-emerald: #10b981;
        --cyber-emerald-glow: rgba(16, 185, 129, 0.3);
        --cyber-amber: #f59e0b;
        --cyber-crimson: #ef4444;
        --cyber-purple: #8b5cf6;
        --cyber-slate: #0b1528;
        --shadow-cyber: 0 10px 25px -5px rgba(13, 110, 253, 0.12), 0 0 0 1px rgba(56, 189, 248, 0.2);
    }

    /* Global Body */
    body {
        font-family: var(--font-main);
        background-color: var(--bg-body);
        color: var(--text-body);
        overflow-x: hidden;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        transition: background-color 0.25s ease, color 0.25s ease;
    }

    /* Modern Scrollbars */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    ::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.35);
        border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(148, 163, 184, 0.6);
    }

    /* =========================================================
       📐 LAYOUT & SIDEBAR STRUCTURE
    ========================================================= */
    .sidebar {
        width: 270px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: linear-gradient(180deg, var(--sidebar-bg-start) 0%, var(--sidebar-bg-mid) 50%, var(--sidebar-bg-end) 100%);
        color: #ffffff;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1045;
        display: flex;
        flex-direction: column;
        border-right: 1px solid rgba(255, 255, 255, 0.07);
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15);
    }

    /* Desktop Sidebar Collapse Logic */
    body.sidebar-collapsed .sidebar {
        margin-left: -270px;
    }
    body.sidebar-collapsed .main-content,
    body.sidebar-collapsed footer {
        margin-left: 0;
    }

    .sidebar-content {
        flex: 1;
        overflow-y: auto;
        padding-bottom: 20px;
    }
    .sidebar-content::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar-content::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.12);
        border-radius: 10px;
    }

    .main-content {
        margin-left: 270px;
        padding: 2rem 2.25rem;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.3s ease;
        flex: 1;
        min-height: calc(100vh - 80px);
    }

    footer {
        margin-left: 270px;
        transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* =========================================================
       🔘 SIDEBAR NAVIGATION STYLES
    ========================================================= */
    .sidebar .section-label {
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: rgba(255, 255, 255, 0.45);
        margin: 18px 0 6px 20px;
        font-weight: 700;
    }

    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.72);
        padding: 10px 18px;
        border-radius: 12px;
        margin: 2px 14px;
        font-size: 0.88rem;
        font-weight: 500;
        letter-spacing: 0.1px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        text-decoration: none;
        position: relative;
    }

    .sidebar .nav-link i {
        font-size: 1rem;
        opacity: 0.85;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .sidebar .nav-link:hover {
        background-color: var(--sidebar-hover);
        color: #ffffff;
        transform: translateX(4px);
    }
    .sidebar .nav-link:hover i {
        opacity: 1;
        transform: scale(1.1);
    }

    .sidebar .nav-link.active {
        background: linear-gradient(135deg, rgba(29, 78, 216, 0.95) 0%, rgba(37, 99, 235, 0.95) 100%);
        color: #ffffff !important;
        font-weight: 700;
        box-shadow: 0 4px 18px rgba(37, 99, 235, 0.45), inset 0 0 12px rgba(56, 189, 248, 0.25);
        border-left: 3.5px solid #38bdf8;
        transform: translateX(4px);
    }
    .sidebar .nav-link.active i {
        opacity: 1;
        color: #ffffff;
    }

    /* 🛡️ CYBER-DEFENSE COMMAND CENTER UTILITIES */
    .cyber-hud-strip {
        background: linear-gradient(165deg, #091322 0%, #0d1e38 50%, #060e1a 100%);
        border-radius: 20px;
        border: 1px solid rgba(56, 189, 248, 0.3);
        box-shadow: 0 20px 40px -10px rgba(2, 6, 23, 0.5), 0 0 30px rgba(13, 110, 253, 0.15);
        color: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .cyber-hud-strip::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 2px;
        background: linear-gradient(90deg, transparent, rgba(56, 189, 248, 0.8), transparent);
    }

    .pulse-dot-cyan {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #00d2ff;
        box-shadow: 0 0 0 rgba(0, 210, 255, 0.6);
        animation: pulse-cyan 2s infinite;
    }
    @keyframes pulse-cyan {
        0% { box-shadow: 0 0 0 0 rgba(0, 210, 255, 0.7); }
        70% { box-shadow: 0 0 0 8px rgba(0, 210, 255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 210, 255, 0); }
    }

    .mono-chip {
        font-family: 'JetBrains Mono', 'Courier New', monospace;
        font-size: 0.78rem;
        font-weight: 600;
        background: var(--bg-subtle);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        padding: 3px 8px;
        border-radius: 6px;
        letter-spacing: 0.2px;
    }

    .cyber-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }
    .cyber-badge-cyan {
        background: rgba(0, 210, 255, 0.12);
        color: #00d2ff;
        border: 1px solid rgba(0, 210, 255, 0.3);
    }
    .cyber-badge-emerald {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .cyber-badge-crimson {
        background: rgba(239, 68, 68, 0.12);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .cyber-room-card {
        border-radius: 18px;
        border: 1.5px solid var(--border-color);
        background: var(--bg-surface);
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .cyber-room-card:hover {
        transform: translateY(-4px);
        border-color: #38bdf8;
        box-shadow: 0 14px 28px -4px rgba(13, 110, 253, 0.1);
    }

    /* =========================================================
       💎 CARDS & PANELS
    ========================================================= */
    .card, .dashboard-card, .settings-card, .inbox-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color-light);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s ease;
    }

    .card:hover, .dashboard-card:hover {
        box-shadow: var(--shadow-md);
        border-color: rgba(26, 86, 219, 0.15);
    }

    /* Stat Icon Modern Containers */
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: rgba(26, 86, 219, 0.08);
        color: var(--primary-blue);
        transition: transform 0.2s ease;
    }
    .stat-icon:hover {
        transform: scale(1.05);
    }

    /* =========================================================
       🛎️ NAVBAR & INTERACTIVE ICONS
    ========================================================= */
    .icon-btn {
        position: relative;
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        color: var(--text-body);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-xs);
    }

    .icon-btn:hover {
        background: #ffffff;
        border-color: rgba(26, 86, 219, 0.3);
        color: var(--primary-blue);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }

    .icon-badge {
        position: absolute;
        top: -4px;
        right: -4px;
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        min-width: 19px;
        height: 19px;
        padding: 0 4px;
        border-radius: 9999px;
        font-size: 10px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--bg-surface);
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
    }

    /* DROPDOWNS */
    .dropdown-menu {
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-lg);
        border-radius: var(--radius-md);
        background: var(--bg-surface);
        padding: 8px;
        animation: dropFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes dropFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dropdown-item {
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.85rem;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .dropdown-item:hover, .dropdown-item:focus {
        background-color: var(--bg-subtle);
        color: var(--primary-blue);
    }

    .view-all-link {
        font-size: 0.78rem;
        text-align: center;
        display: block;
        padding: 10px;
        color: var(--primary-blue);
        font-weight: 700;
        text-decoration: none;
        border-top: 1px solid var(--border-color-light);
        transition: color 0.2s ease, background 0.2s ease;
    }
    .view-all-link:hover {
        color: var(--primary-hover);
        background-color: var(--bg-subtle);
    }

    /* Mobile Header */
    .mobile-header {
        display: none;
        background: var(--bg-surface);
        padding: 12px 18px;
        border-bottom: 1px solid var(--border-color);
        position: sticky;
        top: 0;
        z-index: 1030;
        align-items: center;
        justify-content: space-between;
        backdrop-filter: blur(10px);
        box-shadow: var(--shadow-xs);
    }

    /* =========================================================
       📱 ULTRA-RESPONSIVE MOBILE BREAKPOINTS & LAYOUT
    ========================================================= */
    .sidebar-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(10, 15, 29, 0.68);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1040;
        opacity: 0;
        transition: opacity 0.25s ease;
        pointer-events: none;
    }
    .sidebar-backdrop.active {
        display: block !important;
        opacity: 1 !important;
        pointer-events: auto !important;
    }

    @media (max-width: 992px) {
        html, body {
            overflow-x: hidden !important;
            max-width: 100vw !important;
        }

        .sidebar {
            margin-left: -280px;
            width: 280px;
            z-index: 1050;
            box-shadow: none;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar.active {
            margin-left: 0 !important;
            box-shadow: 10px 0 50px rgba(0, 0, 0, 0.5) !important;
        }

        .main-content {
            margin-left: 0 !important;
            padding: 1rem 0.85rem 2rem !important;
            max-width: 100vw !important;
            overflow-x: hidden;
        }

        footer {
            margin-left: 0 !important;
            padding: 1.25rem 0.85rem !important;
        }

        /* Full-Bleed Native Mobile Topbar */
        .mobile-header {
            display: flex !important;
            margin: -1rem -0.85rem 1.25rem -0.85rem !important;
            padding: 10px 16px !important;
            border-radius: 0 !important;
            width: calc(100% + 1.7rem) !important;
            border-left: 0 !important;
            border-right: 0 !important;
            border-top: 0 !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 1030 !important;
            background: var(--bg-surface) !important;
        }
    }

    @media (max-width: 768px) {
        /* General Containers & Spacing */
        .container-fluid {
            padding-left: 0.2rem !important;
            padding-right: 0.2rem !important;
        }

        /* Card and Panel Padding */
        .card, .stat-card-modern, .threat-kpi-card, .access-kpi-card, .dashboard-card {
            border-radius: 16px !important;
            max-width: 100% !important;
        }

        /* Heading scales */
        h3, .dashboard-header-title {
            font-size: 1.35rem !important;
            line-height: 1.25 !important;
        }
        h4 {
            font-size: 1.18rem !important;
            line-height: 1.25 !important;
        }

        /* DataTables Full Responsive Overrides */
        .dataTables_wrapper {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }
        .dataTables_wrapper .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .dataTables_wrapper [class*="col-"] {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            text-align: left !important;
            float: none !important;
            margin-bottom: 0.6rem !important;
        }
        .dataTables_wrapper .dataTables_filter input {
            width: 100% !important;
            max-width: 100% !important;
            margin-left: 0 !important;
            display: block !important;
            margin-top: 4px !important;
        }
        .dataTables_wrapper .dataTables_paginate {
            text-align: center !important;
            float: none !important;
            margin-top: 0.75rem !important;
            display: flex !important;
            justify-content: center !important;
            flex-wrap: wrap !important;
            gap: 4px !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 4px 10px !important;
            font-size: 0.8rem !important;
        }

        /* Filter Toolbars */
        .filter-toolbar {
            padding: 0.85rem !important;
            border-radius: 16px !important;
        }
        .filter-toolbar .d-flex {
            gap: 0.6rem !important;
        }
        .filter-toolbar input[type="date"],
        .filter-toolbar input[type="text"],
        .filter-toolbar select {
            max-width: 100% !important;
            width: 100% !important;
        }
        .filter-toolbar .btn,
        .filter-toolbar #clearDateFilter {
            width: 100% !important;
            margin-left: 0 !important;
            text-align: center !important;
            justify-content: center !important;
        }

        /* Modals on Mobile */
        .modal-dialog {
            margin: 0.5rem auto !important;
            max-width: 95vw !important;
        }
        .modal-content {
            border-radius: 20px !important;
        }

        /* Cyber HUD Strip */
        .cyber-hud-strip {
            padding: 1rem !important;
            border-radius: 16px !important;
        }
        .cyber-hud-strip .gap-2 {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            width: 100% !important;
            gap: 6px !important;
        }
        .cyber-hud-strip .gap-2 > div {
            padding: 8px 6px !important;
        }

        /* Messenger & Chat Area on Mobile */
        .inbox-card {
            height: calc(100vh - 120px) !important;
            border-radius: 16px !important;
        }
    }

    /* Alert Badge Badges */
    .badge-critical {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* =========================================================
       🌙 ADVANCED DARK MODE SYSTEM
    ========================================================= */
    body.dark-mode {
        color-scheme: dark;
        --bg-body: #0d131f;
        --bg-surface: #141c2e;
        --bg-subtle: #192338;
        --border-color: #24314c;
        --border-color-light: #1f2b42;
        --text-dark: #f8fafc;
        --text-main: #f8fafc;
        --text-body: #cbd5e1;
        --text-muted: #94a3b8;
        --text-subtle: #64748b;
        
        background-color: var(--bg-body) !important;
        color: var(--text-body) !important;
    }

    /* Dark Mode Text Helpers */
    body.dark-mode .text-dark,
    body.dark-mode .text-black,
    body.dark-mode .text-body,
    body.dark-mode h1,
    body.dark-mode h2,
    body.dark-mode h3,
    body.dark-mode h4,
    body.dark-mode h5,
    body.dark-mode h6,
    body.dark-mode .fw-bold,
    body.dark-mode strong,
    body.dark-mode b,
    body.dark-mode [style*="color: var(--text-main)"],
    body.dark-mode [style*="color:var(--text-main)"],
    body.dark-mode [style*="color: var(--text-dark)"],
    body.dark-mode [style*="color:var(--text-dark)"] {
        color: #f8fafc !important;
    }

    body.dark-mode .text-muted,
    body.dark-mode p.text-muted,
    body.dark-mode span.text-muted,
    body.dark-mode label.text-muted,
    body.dark-mode small.text-muted,
    body.dark-mode [style*="color: var(--text-muted)"],
    body.dark-mode [style*="color:var(--text-muted)"] {
        color: #94a3b8 !important;
    }

    body.dark-mode .text-secondary {
        color: #94a3b8 !important;
    }
    body.dark-mode .text-primary {
        color: #60a5fa !important;
    }
    body.dark-mode .text-success {
        color: #34d399 !important;
    }
    body.dark-mode .text-warning {
        color: #fbbf24 !important;
    }
    body.dark-mode .text-danger {
        color: #f87171 !important;
    }
    body.dark-mode .text-info {
        color: #38bdf8 !important;
    }

    /* Containers in Dark Mode */
    body.dark-mode .card,
    body.dark-mode .dashboard-card,
    body.dark-mode .settings-card,
    body.dark-mode .inbox-card,
    body.dark-mode .mobile-header,
    body.dark-mode footer,
    body.dark-mode .dropdown-menu {
        background-color: var(--bg-surface) !important;
        border-color: var(--border-color) !important;
        color: #f8fafc !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4) !important;
    }

    body.dark-mode .bg-light,
    body.dark-mode .bg-white,
    body.dark-mode .surface-box {
        background-color: var(--bg-subtle) !important;
        border-color: var(--border-color) !important;
        color: #f8fafc !important;
    }

    body.dark-mode .icon-btn {
        background-color: var(--bg-subtle) !important;
        border-color: var(--border-color) !important;
        color: #f8fafc !important;
    }
    body.dark-mode .icon-btn:hover {
        background-color: #212d46 !important;
        color: #60a5fa !important;
    }

    body.dark-mode .border-bottom,
    body.dark-mode .border-top,
    body.dark-mode .border-start,
    body.dark-mode .border-end,
    body.dark-mode hr {
        border-color: var(--border-color) !important;
    }

    /* Form Controls Dark Mode */
    body.dark-mode .form-control,
    body.dark-mode .form-select,
    body.dark-mode .search-bar {
        background-color: var(--bg-subtle) !important;
        color: #f8fafc !important;
        border: 1px solid var(--border-color) !important;
    }
    body.dark-mode .form-control:focus,
    body.dark-mode .form-select:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
    }
    body.dark-mode .form-control::placeholder,
    body.dark-mode .search-bar::placeholder {
        color: #64748b !important;
    }
    body.dark-mode .input-group-text {
        background-color: var(--bg-subtle) !important;
        border-color: var(--border-color) !important;
        color: #94a3b8 !important;
    }

    /* Modals & Toasts Dark Mode */
    body.dark-mode .modal-content,
    body.dark-mode .toast {
        background-color: var(--bg-surface) !important;
        color: #f8fafc !important;
        border: 1px solid var(--border-color) !important;
    }
    body.dark-mode .modal-header,
    body.dark-mode .toast-header {
        border-bottom-color: var(--border-color) !important;
        background-color: var(--bg-subtle) !important;
        color: #ffffff !important;
    }

    /* =========================================================
       📊 MASTER TABLE DARK MODE STYLING
    ========================================================= */
    body.dark-mode .table,
    body.dark-mode table,
    body.dark-mode .table-custom,
    body.dark-mode .dashboard-table,
    body.dark-mode .table-pending,
    body.dark-mode #auditLogsTable,
    body.dark-mode #alertsTable,
    body.dark-mode #usersTable,
    body.dark-mode #reportsTable {
        --bs-table-color: #f8fafc !important;
        --bs-table-bg: transparent !important;
        --bs-table-border-color: #24314c !important;
        --bs-table-striped-bg: rgba(255, 255, 255, 0.02) !important;
        --bs-table-striped-color: #f8fafc !important;
        --bs-table-active-bg: rgba(255, 255, 255, 0.05) !important;
        --bs-table-active-color: #f8fafc !important;
        --bs-table-hover-bg: rgba(255, 255, 255, 0.04) !important;
        --bs-table-hover-color: #ffffff !important;
        color: #f8fafc !important;
        border-color: #24314c !important;
    }

    body.dark-mode .table > :not(caption) > * > *,
    body.dark-mode table > :not(caption) > * > * {
        background-color: transparent !important;
        border-bottom-color: #24314c !important;
        box-shadow: none !important;
        color: #f8fafc !important;
    }

    /* Table Headers */
    body.dark-mode table thead,
    body.dark-mode .table thead,
    body.dark-mode table thead tr,
    body.dark-mode .table thead tr,
    body.dark-mode table thead th,
    body.dark-mode .table thead th,
    body.dark-mode .table-custom th,
    body.dark-mode .table-light th,
    body.dark-mode #auditLogsTable thead th,
    body.dark-mode #alertsTable thead th,
    body.dark-mode #usersTable thead th,
    body.dark-mode #reportsTable thead th {
        background-color: #192338 !important;
        color: #cbd5e1 !important;
        border-bottom: 2px solid #24314c !important;
        border-top: none !important;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    /* Table Rows */
    body.dark-mode table tbody tr,
    body.dark-mode .table tbody tr,
    body.dark-mode #auditLogsTable tbody tr,
    body.dark-mode #alertsTable tbody tr,
    body.dark-mode #usersTable tbody tr {
        border-bottom: 1px solid #24314c !important;
        background-color: transparent !important;
        transition: background-color 0.15s ease;
    }

    /* Table Cells */
    body.dark-mode table tbody td,
    body.dark-mode .table tbody td,
    body.dark-mode .table-custom td,
    body.dark-mode .table-pending td,
    body.dark-mode .dashboard-table td,
    body.dark-mode #auditLogsTable tbody td,
    body.dark-mode #alertsTable tbody td,
    body.dark-mode #usersTable td,
    body.dark-mode #reportsTable tbody td {
        color: #f1f5f9 !important;
        border-bottom: 1px solid #24314c !important;
        background-color: transparent !important;
    }

    /* Table Hover States */
    body.dark-mode .table-hover tbody tr:hover,
    body.dark-mode .table-hover tbody tr:hover td,
    body.dark-mode .table-custom tbody tr:hover,
    body.dark-mode .table-custom tbody tr:hover td,
    body.dark-mode table tbody tr:hover td,
    body.dark-mode #auditLogsTable tbody tr:hover,
    body.dark-mode #auditLogsTable tbody tr:hover td,
    body.dark-mode #alertsTable tbody tr:hover,
    body.dark-mode #alertsTable tbody tr:hover td,
    body.dark-mode #usersTable tbody tr:hover,
    body.dark-mode #usersTable tbody tr:hover td {
        background-color: rgba(255, 255, 255, 0.05) !important;
        color: #ffffff !important;
    }

    /* Elements inside table cells */
    body.dark-mode table td .text-dark,
    body.dark-mode table td strong,
    body.dark-mode table td b,
    body.dark-mode table td .fw-bold,
    body.dark-mode table td h5,
    body.dark-mode table td h6,
    body.dark-mode table td a:not(.btn),
    body.dark-mode table td span:not(.badge):not(.badge-action-pill):not(.role-pill):not(.timestamp-chip):not(.role-badge-pending):not(.pill-critical):not(.pill-warning):not(.pill-info):not(.status-active-badge):not(.status-resolved-badge),
    body.dark-mode table td p,
    body.dark-mode table td div:not(.avatar-initial-sm):not(.avatar-circle-sm):not(.avatar-img-table):not(.rounded-circle):not(.stat-icon-wrapper) {
        color: #f8fafc !important;
    }

    body.dark-mode table td .text-muted,
    body.dark-mode table td small,
    body.dark-mode table td .small.fw-semibold,
    body.dark-mode table td .small.fw-medium {
        color: #94a3b8 !important;
    }

    /* Chips, Badges and Pills in Dark Mode Tables */
    body.dark-mode .timestamp-chip {
        background: #192338 !important;
        color: #93c5fd !important;
        border: 1px solid #24314c !important;
    }

    body.dark-mode .method-badge {
        background: #192338 !important;
        color: #e2e8f0 !important;
        border: 1px solid #24314c !important;
    }

    body.dark-mode .role-badge-pending {
        background: #192338 !important;
        border: 1px solid #24314c !important;
        color: #f8fafc !important;
    }

    body.dark-mode .badge.bg-secondary,
    body.dark-mode .badge.text-secondary {
        background: rgba(148, 163, 184, 0.15) !important;
        color: #cbd5e1 !important;
        border-color: rgba(148, 163, 184, 0.3) !important;
    }

    body.dark-mode .badge.bg-primary.bg-opacity-10,
    body.dark-mode .badge.text-primary {
        background: rgba(59, 130, 246, 0.2) !important;
        color: #60a5fa !important;
        border-color: rgba(96, 165, 250, 0.3) !important;
    }

    body.dark-mode .badge.bg-success.bg-opacity-10,
    body.dark-mode .badge.text-success {
        background: rgba(16, 185, 129, 0.2) !important;
        color: #34d399 !important;
        border-color: rgba(52, 211, 153, 0.3) !important;
    }

    body.dark-mode .badge.bg-warning.bg-opacity-10,
    body.dark-mode .badge.text-warning {
        background: rgba(245, 158, 11, 0.2) !important;
        color: #fbbf24 !important;
        border-color: rgba(251, 191, 36, 0.3) !important;
    }

    body.dark-mode .badge.bg-danger.bg-opacity-10,
    body.dark-mode .badge.text-danger {
        background: rgba(239, 68, 68, 0.2) !important;
        color: #f87171 !important;
        border-color: rgba(248, 113, 113, 0.3) !important;
    }

    body.dark-mode .badge.bg-info.bg-opacity-10,
    body.dark-mode .badge.text-info {
        background: rgba(6, 182, 212, 0.2) !important;
        color: #38bdf8 !important;
        border-color: rgba(56, 189, 248, 0.3) !important;
    }

    body.dark-mode .action-primary,
    body.dark-mode .role-admin {
        background: rgba(59, 130, 246, 0.2) !important;
        color: #60a5fa !important;
        border-color: rgba(96, 165, 250, 0.3) !important;
    }

    body.dark-mode .role-staff {
        background: rgba(16, 185, 129, 0.2) !important;
        color: #34d399 !important;
        border-color: rgba(52, 211, 153, 0.3) !important;
    }

    body.dark-mode .role-dean {
        background: rgba(239, 68, 68, 0.2) !important;
        color: #f87171 !important;
        border-color: rgba(248, 113, 113, 0.3) !important;
    }

    body.dark-mode .action-circle-btn {
        background: #192338 !important;
        border-color: #24314c !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .action-circle-btn:hover {
        background: #24314c !important;
    }

    body.dark-mode .table-responsive {
        border-color: #24314c !important;
    }

    body.dark-mode .card.user-card-executive,
    body.dark-mode .card.log-card-executive,
    body.dark-mode .card.alert-card-executive {
        background-color: var(--bg-surface) !important;
        border-color: var(--border-color) !important;
    }

    /* Date Inputs in Dark Mode */
    body.dark-mode input[type="date"] {
        color-scheme: dark !important;
        background-color: #192338 !important;
        color: #f8fafc !important;
        border-color: #24314c !important;
    }
    body.dark-mode #clearDateFilter {
        background: #192338 !important;
        border-color: #24314c !important;
        color: #94a3b8 !important;
    }
    body.dark-mode #clearDateFilter:hover {
        background: #24314c !important;
        color: #f8fafc !important;
    }

    /* DataTables Controls & Text in Dark Mode */
    body.dark-mode .dataTables_wrapper .dataTables_length,
    body.dark-mode .dataTables_wrapper .dataTables_filter,
    body.dark-mode .dataTables_wrapper .dataTables_info,
    body.dark-mode .dataTables_wrapper .dataTables_paginate {
        color: #94a3b8 !important;
    }

    body.dark-mode .dataTables_wrapper .dataTables_length label,
    body.dark-mode .dataTables_wrapper .dataTables_filter label {
        color: #94a3b8 !important;
    }

    body.dark-mode .dataTables_wrapper .dataTables_filter input,
    body.dark-mode .dataTables_wrapper .dataTables_length select {
        background-color: #192338 !important;
        border: 1px solid #24314c !important;
        color: #f8fafc !important;
    }

    body.dark-mode .dataTables_wrapper .dataTables_filter input:focus,
    body.dark-mode .dataTables_wrapper .dataTables_length select:focus {
        border-color: #3b82f6 !important;
        color: #ffffff !important;
        outline: none !important;
    }

    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #94a3b8 !important;
        background: transparent !important;
        border: 1px solid transparent !important;
    }

    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #192338 !important;
        color: #ffffff !important;
        border-color: #24314c !important;
    }

    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #1a56db !important;
        color: #ffffff !important;
        border-color: #1a56db !important;
    }

    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
    body.dark-mode .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
        color: #475569 !important;
    }

    body.dark-mode .dataTables_empty {
        color: #94a3b8 !important;
        background-color: transparent !important;
    }

    /* Bootstrap 5 Pagination in Dark Mode */
    body.dark-mode .pagination .page-link {
        background-color: #192338 !important;
        border-color: #24314c !important;
        color: #cbd5e1 !important;
    }
    body.dark-mode .pagination .page-item.active .page-link {
        background-color: #1a56db !important;
        border-color: #1a56db !important;
        color: #ffffff !important;
    }
    body.dark-mode .pagination .page-item.disabled .page-link {
        background-color: #141c2e !important;
        border-color: #24314c !important;
        color: #475569 !important;
    }

    /* DataTables Responsive Child Rows */
    body.dark-mode table.dataTable > tbody > tr.child {
        background-color: #141c2e !important;
    }
    body.dark-mode table.dataTable > tbody > tr.child ul.dtr-details {
        color: #f1f5f9 !important;
    }
    body.dark-mode table.dataTable > tbody > tr.child span.dtr-title {
        color: #94a3b8 !important;
        font-weight: 600 !important;
    }
    body.dark-mode table.dataTable > tbody > tr.child span.dtr-data {
        color: #f1f5f9 !important;
    }

    /* Select2 Dark Mode */
    body.dark-mode .select2-container--default .select2-selection--single {
        background-color: #192338 !important;
        border-color: #24314c !important;
        color: #f8fafc !important;
    }
    body.dark-mode .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #f8fafc !important;
    }
    body.dark-mode .select2-dropdown {
        background-color: #141c2e !important;
        border-color: #24314c !important;
        color: #f8fafc !important;
    }
    body.dark-mode .select2-container--default .select2-results__option {
        color: #cbd5e1 !important;
    }
    body.dark-mode .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #1a56db !important;
        color: #ffffff !important;
    }

    /* Chat / Inbox Dark Mode */
    body.dark-mode .contact-list {
        background-color: var(--bg-surface) !important;
        border-color: var(--border-color) !important;
    }
    body.dark-mode .chat-area {
        background-color: var(--bg-body) !important;
    }
    body.dark-mode .received .msg-bubble {
        background: var(--bg-subtle) !important;
        color: #ffffff !important;
        border: 1px solid var(--border-color) !important;
    }
    body.dark-mode .chat-input-container {
        background: var(--bg-surface) !important;
        border-color: var(--border-color) !important;
    }
    body.dark-mode .contact-item:hover {
        background: var(--bg-subtle) !important;
        color: #ffffff !important;
    }
    body.dark-mode .contact-item.active {
        background: #1e3a8a !important;
        color: #ffffff !important;
    }
    body.dark-mode .view-all-link {
        border-top-color: var(--border-color) !important;
    }
    body.dark-mode .nav-pills .nav-link {
        color: #94a3b8 !important;
    }
    body.dark-mode .nav-pills .nav-link.active {
        color: #ffffff !important;
        background-color: var(--primary-blue) !important;
    }
</style>

<script>
    (function() {
        const isDbDark = {{ (Auth::check() && Auth::user()->dark_mode) ? 'true' : 'false' }};
        const localTheme = localStorage.getItem('securelab_dark_mode');
        if (isDbDark || localTheme === 'true') {
            document.documentElement.classList.add('dark-mode');
            if (document.body) {
                document.body.classList.add('dark-mode');
            } else {
                document.addEventListener("DOMContentLoaded", function() {
                    document.body.classList.add('dark-mode');
                });
            }
        }
    })();
</script>