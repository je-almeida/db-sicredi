<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DB Sicredi Demo</title>
</head>
<style>
body {
    background: #f5f7f2;
}
.navbar-sicredi {
    background: linear-gradient(90deg, #6CBF43 60%, #005029 100%) !important;
    box-shadow: 0 2px 8px #0001;
}
.navbar-sicredi .navbar-brand, .navbar-sicredi .nav-link, .navbar-sicredi .dropdown-toggle {
    color: #fff !important;
    font-weight: 600;
}
.navbar-sicredi .nav-link.active, .navbar-sicredi .nav-link:focus {
    color: #005029 !important;
    background: #e6f4e6;
    border-radius: 4px;
}
.btn-success, .btn-primary, .btn-info, .btn-secondary, .btn-warning, .btn-danger {
    background: #6CBF43 !important;
    border: none !important;
    color: #fff !important;
}
.btn-success:hover, .btn-primary:hover, .btn-info:hover, .btn-secondary:hover, .btn-warning:hover, .btn-danger:hover {
    background: #005029 !important;
    color: #fff !important;
}
.table thead {
    background: #e6f4e6;
}
.modal-header {
    background: #6CBF43;
    color: #fff;
}
@media (max-width: 991.98px) {
    .navbar-sicredi .navbar-collapse {
        background: #6CBF43;
        border-radius: 0 0 10px 10px;
        margin-top: 8px;
    }
    .navbar-sicredi .nav-link {
        color: #fff !important;
        padding-left: 1.5rem;
    }
}
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-sicredi mb-4 shadow">
        <div class="container">
            <a class="navbar-brand fw-bold text-white" href="/">DB Sicredi Demo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/customers">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/operations">Operações</a>
                    </li>
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name ?? Auth::user()->email }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item" type="submit">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    @yield('content')
</body>
</html>
