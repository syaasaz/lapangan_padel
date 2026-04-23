<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LapZone</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #f5f7fa;
            --surface: #ffffff;
            --surface-soft: #f8fafc;
            --text: #1f2937;
            --muted: #6b7280;
            --line: #e5e7eb;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success-bg: #dcfce7;
            --success-text: #166534;
            --warning-bg: #fef3c7;
            --warning-text: #92400e;
            --danger-bg: #fee2e2;
            --danger-text: #b91c1c;
        }

        body {
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .app-shell {
            padding: 24px 0 40px;
        }

        .app-navbar {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 1rem 1.25rem;
        }

        .brand-mark {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            overflow: hidden;
        }

        .brand-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .navbar-brand {
            color: var(--text) !important;
            font-weight: 700;
        }

        .page-header,
        .content-card,
        .stats-card,
        .detail-card,
        .form-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
        }

        .page-header,
        .content-card,
        .detail-card,
        .form-card {
            padding: 1.5rem;
        }

        .stats-card {
            padding: 1.25rem;
            height: 100%;
        }

        .page-title {
            font-size: 1.9rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-subtitle,
        .text-muted-soft {
            color: var(--muted);
        }

        .section-label {
            font-size: 0.85rem;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .stats-value {
            font-size: 1.9rem;
            font-weight: 700;
            line-height: 1.1;
        }

        .btn {
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-light {
            border: 1px solid var(--line);
            background: var(--surface);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background: var(--warning-bg);
            color: var(--warning-text);
        }

        .status-confirmed,
        .status-finished {
            background: var(--success-bg);
            color: var(--success-text);
        }

        .status-cancelled {
            background: var(--danger-bg);
            color: var(--danger-text);
        }

        .table-clean {
            margin-bottom: 0;
        }

        .table-clean thead th {
            font-size: 0.82rem;
            color: var(--muted);
            font-weight: 600;
            border-bottom: 1px solid var(--line);
            white-space: nowrap;
        }

        .table-clean td {
            vertical-align: middle;
            border-color: var(--line);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .detail-item {
            background: var(--surface-soft);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 1rem;
        }

        .detail-label {
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 0.35rem;
        }

        .detail-value {
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border-color: #d1d5db;
            padding: 0.75rem 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.12);
        }

        .page-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .alert {
            border-radius: 12px;
        }

        @media (max-width: 767.98px) {
            .page-title {
                font-size: 1.5rem;
            }

            .page-header,
            .content-card,
            .detail-card,
            .form-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container app-shell">
        <nav class="navbar navbar-expand-lg app-navbar mb-4">
            <div class="container-fluid px-0">
                <a class="navbar-brand d-flex align-items-center gap-3" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
                    <span class="brand-mark">
                        <img src="{{ asset('images/padel.png') }}" alt="Logo LapZone" class="brand-logo">
                    </span>
                    <span>LapZone</span>
                </a>
                <div class="navbar-nav ms-auto align-items-center gap-2">
                    @auth
                        <span class="navbar-text text-muted me-2">Halo, {{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>

        @if (session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
