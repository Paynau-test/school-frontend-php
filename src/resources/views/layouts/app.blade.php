<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'School - Calificaciones')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; }
        .navbar { background: #2c3e50 !important; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .score-input { max-width: 100px; text-align: center; font-weight: 600; }
        .score-input:focus { border-color: #3498db; box-shadow: 0 0 0 .2rem rgba(52,152,219,.25); }
        .badge-saved { background: #27ae60; }
        .badge-pending { background: #95a5a6; }
    </style>
</head>
<body>
    @if(session('jwt_token'))
    <nav class="navbar navbar-dark mb-4">
        <div class="container">
            <span class="navbar-brand fw-bold">School · Calificaciones</span>
            <div class="d-flex align-items-center gap-3">
                <span class="text-light small">{{ session('user.name', session('user.email', '')) }}</span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button class="btn btn-outline-light btn-sm">Salir</button>
                </form>
            </div>
        </div>
    </nav>
    @endif

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
