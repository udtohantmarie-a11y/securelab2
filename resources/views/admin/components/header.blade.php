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
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 100%);
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.38);
        transform: translateX(3px);
    }
    .sidebar .nav-link.active i {
        opacity: 1;
        color: #ffffff;
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

    /* Responsive Breakpoint */
    @media (max-width: 992px) {
        .sidebar {
            margin-left: -270px;
        }
        .sidebar.active {
            margin-left: 0;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.4);
        }
        .main-content, footer {
            margin-left: 0;
        }
        .mobile-header {
            display: flex !important;
        }
        .main-content {
            padding: 1.25rem 1rem;
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
        --bg-body: #0d131f;
        --bg-surface: #141c2e;
        --bg-subtle: #192338;
        --border-color: #24314c;
        --border-color-light: #1f2b42;
        --text-dark: #f8fafc;
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
    body.dark-mode strong {
        color: #f8fafc !important;
    }

    body.dark-mode .text-muted,
    body.dark-mode p.text-muted,
    body.dark-mode span.text-muted,
    body.dark-mode label.text-muted,
    body.dark-mode small.text-muted {
        color: #94a3b8 !important;
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
    body.dark-mode .bg-white {
        background-color: var(--bg-subtle) !important;
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

    /* Tables Dark Mode */
    body.dark-mode table,
    body.dark-mode .table-custom th,
    body.dark-mode .table-custom td {
        color: #f8fafc !important;
        border-color: var(--border-color) !important;
    }
    body.dark-mode .table-light th,
    body.dark-mode thead tr {
        background-color: var(--bg-subtle) !important;
        color: #cbd5e1 !important;
        border-bottom: 2px solid var(--border-color) !important;
    }
    body.dark-mode tbody td {
        border-bottom: 1px solid var(--border-color) !important;
    }
    body.dark-mode .table-hover tbody tr:hover,
    body.dark-mode .table-custom tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.04) !important;
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
    document.addEventListener("DOMContentLoaded", function() {
        @if(Auth::check() && Auth::user()->dark_mode)
            document.body.classList.add('dark-mode');
        @endif
    });
</script>