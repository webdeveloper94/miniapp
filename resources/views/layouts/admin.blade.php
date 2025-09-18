<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body{background:#0b132b;color:#e0e6f8}
        .sidebar{background:#1c2541;min-height:100vh}
        .sidebar a{color:#d0defb;text-decoration:none;display:flex;gap:.5rem;align-items:center;padding:.6rem .9rem;border-radius:.4rem}
        .sidebar a.active,.sidebar a:hover{background:#3a506b;color:#fff}
        .content{padding:1.2rem}
        .card{background:#1b2a49;border-color:#243b6b;color:#e0e6f8}
        .navbar{background:#1c2541}
        .table{color:#e0e6f8}
        .table thead th{color:#a7b5e0}
        .badge-darkblue{background:#3a506b}
    </style>
</head>
<body>
<nav class="navbar navbar-dark px-3 d-md-none">
  <button class="btn btn-outline-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas" aria-controls="adminOffcanvas">
    <i class="bi bi-list"></i>
  </button>
  <span class="navbar-brand mb-0 h1">Admin Panel</span>
</nav>

<div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="adminOffcanvas" aria-labelledby="adminOffcanvasLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="adminOffcanvasLabel"><i class="bi bi-speedometer2"></i> Admin Panel</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    @include('admin.partials.sidebar')
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <aside class="d-none d-md-block col-md-3 col-lg-2 sidebar p-3">
      @include('admin.partials.sidebar')
    </aside>
    <main class="col-12 col-md-9 col-lg-10 content">
      @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
      @yield('content')
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


