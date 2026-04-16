<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — EDU-ID SaaS</title>
    <meta name="description" content="EDU-ID SaaS - Smart School Identity & Attendance Management System">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --bg-body: #f8f7f4;
            --bg-white: #ffffff;
            --bg-cream: #fdf8f0;
            --bg-cream-hover: #faf3e8;
            --border: #eee9df;
            --border-hover: #d4af37;
            --text-primary: #1a1a2e;
            --text-secondary: #555770;
            --text-muted: #9a9cb0;
            --gold: #d4af37;
            --gold-light: #f0d060;
            --gold-dark: #b8941e;
            --gold-bg: rgba(212, 175, 55, 0.08);
            --gold-bg-hover: rgba(212, 175, 55, 0.14);
            --gold-gradient: linear-gradient(135deg, #d4af37, #e8c84a, #d4af37);
            --success: #27ae60;
            --success-bg: rgba(39, 174, 96, 0.1);
            --warning: #e67e22;
            --warning-bg: rgba(230, 126, 34, 0.1);
            --danger: #e74c3c;
            --danger-bg: rgba(231, 76, 60, 0.1);
            --info: #3498db;
            --info-bg: rgba(52, 152, 219, 0.1);
            --purple: #8e44ad;
            --purple-bg: rgba(142, 68, 173, 0.1);
            --sidebar-width: 260px;
            --header-height: 68px;
            --radius: 14px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
            --shadow: 0 2px 12px rgba(0,0,0,0.06);
            --shadow-lg: 0 8px 30px rgba(0,0,0,0.08);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: #0f0f14;
            border-right: 1px solid rgba(212, 175, 55, 0.1);
            z-index: 100;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 20px rgba(0,0,0,0.2);
        }

        .sidebar-brand {
            padding: 22px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-brand .logo-icon {
            width: 44px;
            height: 44px;
            background: var(--gold-gradient);
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #1a1a2e;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .sidebar-brand h1 {
            font-size: 19px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .sidebar-brand span {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            font-weight: 500;
        }

        .sidebar-nav {
            flex: 1;
            padding: 18px 14px;
            overflow-y: auto;
        }

        .nav-section {
            margin-bottom: 26px;
        }

        .nav-section-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            color: rgba(255,255,255,0.25);
            padding: 0 12px;
            margin-bottom: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: 11px;
            color: rgba(255,255,255,0.55);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            margin-bottom: 3px;
        }

        .nav-link:hover {
            background: rgba(212, 175, 55, 0.08);
            color: #d4af37;
        }

        .nav-link.active {
            background: rgba(212, 175, 55, 0.12);
            color: #f0d060;
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 20px;
            background: var(--gold-gradient);
            border-radius: 0 4px 4px 0;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 11px;
            background: rgba(255,255,255,0.04);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--gold-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 800;
            color: #1a1a2e;
        }

        .user-info {
            flex: 1;
        }

        .user-info .name {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
        }

        .user-info .role {
            font-size: 11px;
            color: rgba(255,255,255,0.35);
            text-transform: capitalize;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .header {
            height: var(--header-height);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-left h2 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: var(--text-primary);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background: var(--gold-gradient);
            color: #1a1a2e;
            box-shadow: 0 4px 14px rgba(212, 175, 55, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }

        .btn-ghost {
            background: var(--bg-white);
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }

        .btn-ghost:hover {
            border-color: var(--gold);
            color: var(--gold-dark);
            background: var(--gold-bg);
        }

        .btn-sm {
            padding: 7px 14px;
            font-size: 12px;
        }

        .content {
            padding: 28px 32px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: var(--shadow-sm);
        }

        .stat-card:hover {
            border-color: var(--gold);
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 14px;
        }

        .stat-icon.gold { background: var(--gold-bg); color: var(--gold-dark); }
        .stat-icon.green { background: var(--success-bg); color: var(--success); }
        .stat-icon.blue { background: var(--info-bg); color: var(--info); }
        .stat-icon.amber { background: var(--warning-bg); color: var(--warning); }
        .stat-icon.purple { background: var(--purple-bg); color: var(--purple); }
        .stat-icon.red { background: var(--danger-bg); color: var(--danger); }
        .stat-icon.teal { background: rgba(22, 160, 133, 0.1); color: #16a085; }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 3px;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* Cards */
        .card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--bg-cream);
        }

        .card-header h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .card-body {
            padding: 22px;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            text-align: left;
            padding: 13px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            background: var(--bg-cream);
        }

        .data-table td {
            padding: 14px 16px;
            font-size: 13px;
            border-bottom: 1px solid #f4f0e8;
            color: var(--text-secondary);
        }

        .data-table tbody tr {
            transition: background 0.15s;
        }

        .data-table tbody tr:hover {
            background: var(--gold-bg);
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 11px;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-warning { background: var(--warning-bg); color: var(--warning); }
        .badge-danger { background: var(--danger-bg); color: var(--danger); }
        .badge-info { background: var(--info-bg); color: var(--info); }
        .badge-purple { background: var(--purple-bg); color: var(--purple); }
        .badge-gold { background: var(--gold-bg); color: var(--gold-dark); }

        /* Grid layouts */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }

        /* Forms */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            background: var(--bg-white);
            border: 2px solid var(--border);
            border-radius: 11px;
            color: var(--text-primary);
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%239a9cb0' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 36px;
        }

        /* Search Bar */
        .search-bar {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .search-input-wrap {
            position: relative;
        }

        .search-input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        .search-input-wrap input {
            padding-left: 40px;
            min-width: 280px;
        }

        /* Pagination */
        .pagination-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 22px;
            border-top: 1px solid var(--border);
            background: var(--bg-cream);
        }

        .pagination-info {
            font-size: 13px;
            color: var(--text-muted);
        }

        .pagination-links {
            display: flex;
            gap: 4px;
        }

        .pagination-links a, .pagination-links span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .pagination-links a {
            color: var(--text-secondary);
            border: 1px solid var(--border);
            background: var(--bg-white);
        }

        .pagination-links a:hover {
            border-color: var(--gold);
            color: var(--gold-dark);
            background: var(--gold-bg);
        }

        .pagination-links .active {
            background: var(--gold);
            color: #1a1a2e;
            border: none;
            font-weight: 700;
        }

        /* Alert */
        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: var(--success-bg);
            border: 1px solid rgba(39, 174, 96, 0.2);
            color: var(--success);
        }

        .alert-error {
            background: var(--danger-bg);
            border: 1px solid rgba(231, 76, 60, 0.2);
            color: var(--danger);
        }

        /* Chart */
        .chart-bars {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            height: 150px;
            padding-top: 20px;
        }

        .chart-bar-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .chart-bar {
            width: 100%;
            max-width: 40px;
            border-radius: 8px 8px 0 0;
            background: var(--gold-gradient);
            min-height: 10px;
            transition: all 0.5s ease;
            opacity: 0.75;
        }

        .chart-bar:hover {
            opacity: 1;
            transform: scaleY(1.05);
            transform-origin: bottom;
        }

        .chart-bar-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .chart-bar-value {
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 700;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--border);
            margin-bottom: 16px;
        }

        .empty-state h4 {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 13px;
            color: var(--text-muted);
        }

        /* Plan Cards */
        .plan-card {
            background: var(--bg-white);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 30px 26px;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: var(--shadow-sm);
        }

        .plan-card:hover {
            border-color: var(--gold);
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(212, 175, 55, 0.12);
        }

        .plan-card.featured {
            border-color: var(--gold);
            background: linear-gradient(180deg, var(--bg-cream) 0%, var(--bg-white) 100%);
        }

        .plan-card.featured::before {
            content: 'POPULAR';
            position: absolute;
            top: -1px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gold-gradient);
            color: #1a1a2e;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            padding: 5px 18px;
            border-radius: 0 0 10px 10px;
        }

        .plan-name {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .plan-price {
            font-size: 38px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 4px;
            color: var(--text-primary);
        }

        .plan-price span {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .plan-features {
            list-style: none;
            padding: 20px 0;
            text-align: left;
        }

        .plan-features li {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 0;
            font-size: 13px;
            color: var(--text-secondary);
        }

        .plan-features li i { color: var(--success); font-size: 14px; }
        .plan-features li i.fa-xmark { color: var(--text-muted); }

        /* Animations */
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade { animation: slideDown 0.3s ease; }

        @media (max-width: 1024px) {
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; }
            .content { padding: 20px 16px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <h1>EDU-ID</h1>
                <span>SaaS Platform</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Management</div>
                <a href="{{ route('schools') }}" class="nav-link {{ request()->routeIs('schools*') ? 'active' : '' }}">
                    <i class="fas fa-building-columns"></i> Schools
                </a>
                <a href="{{ route('students') }}" class="nav-link {{ request()->routeIs('students*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i> Students
                </a>
                <a href="{{ route('users') }}" class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Users
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">ID & QR</div>
                <a href="{{ route('qrcodes') }}" class="nav-link {{ request()->routeIs('qrcodes*') ? 'active' : '' }}">
                    <i class="fas fa-qrcode"></i> QR Codes
                </a>
                <a href="{{ route('idcards') }}" class="nav-link {{ request()->routeIs('idcards*') ? 'active' : '' }}">
                    <i class="fas fa-id-badge"></i> ID Cards
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Tracking</div>
                <a href="{{ route('scanner') }}" class="nav-link {{ request()->routeIs('scanner*') ? 'active' : '' }}">
                    <i class="fas fa-camera"></i> Scanner
                </a>
                <a href="{{ route('attendance') }}" class="nav-link {{ request()->routeIs('attendance*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-check"></i> Attendance
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Billing</div>
                <a href="{{ route('plans') }}" class="nav-link {{ request()->routeIs('plans*') ? 'active' : '' }}">
                    <i class="fas fa-gem"></i> Plans
                </a>
                <a href="{{ route('subscriptions') }}" class="nav-link {{ request()->routeIs('subscriptions*') ? 'active' : '' }}">
                    <i class="fas fa-crown"></i> Subscriptions
                </a>
                <a href="{{ route('payments') }}" class="nav-link {{ request()->routeIs('payments*') ? 'active' : '' }}">
                    <i class="fas fa-indian-rupee-sign"></i> Payments
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="user-card">
                <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                <div class="user-info">
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="role">{{ str_replace('_', ' ', Auth::user()->role) }}</div>
                </div>
                <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:rgba(255,255,255,0.3);cursor:pointer;font-size:14px;padding:4px;" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main -->
    <main class="main-content">
        <header class="header">
            <div class="header-left">
                <h2>@yield('page-title', 'Dashboard')</h2>
            </div>
            <div class="header-right">
                @yield('header-actions')
            </div>
        </header>

        <div class="content animate-fade">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @yield('scripts')
</body>
</html>
