<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Super Admin Dashboard - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            padding: 0;
        }
        .sidebar-brand {
            padding: 20px;
            color: white;
            font-size: 1.2rem;
            font-weight: 700;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }
        .sidebar-brand small { font-size: .7rem; opacity: .7; display: block; }
        .sidebar-nav { padding: 15px 0; }
        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.75);
            padding: 10px 20px;
            border-radius: 0;
            transition: all .2s;
        }
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            color: white;
            background: rgba(255,255,255,.1);
        }
        .sidebar-nav .nav-link i { width: 20px; }
        .stat-card { border: none; border-radius: 12px; overflow: hidden; }
        .stat-card .card-body { padding: 1.5rem; }
        .stat-icon {
            width: 50px; height: 50px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
        }
        .badge-pending   { background: #fff3cd; color: #856404; }
        .badge-approved  { background: #d1e7dd; color: #0a3622; }
        .badge-rejected  { background: #f8d7da; color: #58151c; }
        .badge-suspended { background: #e2e3e5; color: #41464b; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-auto col-md-2 px-0 sidebar">
            <div class="sidebar-brand">
                <i class="bi bi-box-seam me-2"></i>DN
                <small>Super Admin Panel</small>
            </div>
            <nav class="sidebar-nav">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('super-admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('super-admin.shops.index') }}">
                            <i class="bi bi-shop me-2"></i>Shops
                            @if($pendingShops > 0)
                                <span class="badge bg-warning ms-1">{{ $pendingShops }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mt-auto p-3" style="position: absolute; bottom: 0; width: 100%;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light w-100">
                        <i class="bi bi-box-arrow-left me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col ps-0">
            <!-- Top Bar -->
            <div class="bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 fw-bold">Dashboard</h5>
                    <small class="text-muted">Welcome back, {{ auth()->user()->name }}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger">Super Admin</span>
                    <span class="text-muted small">{{ auth()->user()->email }}</span>
                </div>
            </div>

            <div class="p-4">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Shop Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-shop"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold">{{ $totalShops }}</div>
                                    <div class="text-muted small">Total Shops</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold">{{ $pendingShops }}</div>
                                    <div class="text-muted small">Pending Approval</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold">{{ $approvedShops }}</div>
                                    <div class="text-muted small">Approved</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card stat-card shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="stat-icon bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div>
                                    <div class="fs-4 fw-bold">{{ $totalUsers }}</div>
                                    <div class="text-muted small">Total Users</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Shops -->
                @if($recentPendingShops->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-warning bg-opacity-10 d-flex align-items-center justify-content-between">
                        <span class="fw-semibold"><i class="bi bi-clock-history me-2 text-warning"></i>Pending Approvals</span>
                        <a href="{{ route('super-admin.shops.index') }}" class="btn btn-sm btn-warning">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Shop Name</th>
                                        <th>Owner</th>
                                        <th>City</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPendingShops as $shop)
                                    <tr>
                                        <td>
                                            <strong>{{ $shop->name }}</strong>
                                        </td>
                                        <td>{{ $shop->owner?->name ?? '—' }}</td>
                                        <td>{{ $shop->city ?? '—' }}</td>
                                        <td>{{ $shop->created_at->diffForHumans() }}</td>
                                        <td>
                                            <form method="POST" action="{{ route('super-admin.shops.approve', $shop) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('super-admin.shops.reject', $shop) }}" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="bi bi-x-lg"></i> Reject
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>No shops pending approval.
                </div>
                @endif

                <!-- All Recent Shops -->
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span class="fw-semibold"><i class="bi bi-shop me-2"></i>Recent Shops</span>
                        <a href="{{ route('super-admin.shops.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Shop Name</th>
                                        <th>Owner</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentShops as $shop)
                                    <tr>
                                        <td><strong>{{ $shop->name }}</strong></td>
                                        <td>{{ $shop->owner?->name ?? '—' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $shop->status }}">
                                                {{ ucfirst($shop->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $shop->created_at->format('d M Y') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">No shops registered yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!-- /p-4 -->
        </div><!-- /col -->
    </div><!-- /row -->
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
