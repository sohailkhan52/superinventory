<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shops - Super Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .sidebar { min-height: 100vh; background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%); }
        .sidebar-brand { padding: 20px; color: white; font-size: 1.2rem; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,.15); }
        .sidebar-brand small { font-size: .7rem; opacity: .7; display: block; }
        .sidebar-nav .nav-link { color: rgba(255,255,255,.75); padding: 10px 20px; }
        .sidebar-nav .nav-link:hover, .sidebar-nav .nav-link.active { color: white; background: rgba(255,255,255,.1); }
        .badge-pending   { background: #fff3cd; color: #856404; padding: 4px 10px; border-radius: 20px; font-size: .8rem; }
        .badge-approved  { background: #d1e7dd; color: #0a3622; padding: 4px 10px; border-radius: 20px; font-size: .8rem; }
        .badge-rejected  { background: #f8d7da; color: #58151c; padding: 4px 10px; border-radius: 20px; font-size: .8rem; }
        .badge-suspended { background: #e2e3e5; color: #41464b; padding: 4px 10px; border-radius: 20px; font-size: .8rem; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-auto col-md-2 px-0 sidebar">
            <div class="sidebar-brand">
                <i class="bi bi-box-seam me-2"></i>DN
                <small>Super Admin Panel</small>
            </div>
            <nav class="sidebar-nav py-2">
                <ul class="nav flex-column">
                    <li><a class="nav-link" href="{{ route('super-admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                    <li><a class="nav-link active" href="{{ route('super-admin.shops.index') }}"><i class="bi bi-shop me-2"></i>Shops</a></li>
                </ul>
            </nav>
        </div>

        <div class="col ps-0">
            <div class="bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 fw-bold">All Shops</h5>
                    <small class="text-muted">Manage shop registrations</small>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf
                    <button class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-left me-1"></i>Logout</button>
                </form>
            </div>

            <div class="p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Shop Name</th>
                                        <th>Owner</th>
                                        <th>City</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($shops as $shop)
                                    <tr>
                                        <td>{{ $shop->id }}</td>
                                        <td><strong>{{ $shop->name }}</strong></td>
                                        <td>{{ $shop->owner?->name ?? '—' }}<br><small class="text-muted">{{ $shop->owner?->email }}</small></td>
                                        <td>{{ $shop->city ?? '—' }}</td>
                                        <td>{{ $shop->phone ?? '—' }}</td>
                                        <td><span class="badge-{{ $shop->status }}">{{ ucfirst($shop->status) }}</span></td>
                                        <td>{{ $shop->created_at->format('d M Y') }}</td>
                                        <td>
                                            @if($shop->status === 'pending')
                                                <form method="POST" action="{{ route('super-admin.shops.approve', $shop) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                                                </form>
                                                <form method="POST" action="{{ route('super-admin.shops.reject', $shop) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-danger" title="Reject"><i class="bi bi-x-lg"></i></button>
                                                </form>
                                            @elseif($shop->status === 'approved')
                                                <form method="POST" action="{{ route('super-admin.shops.suspend', $shop) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-warning" title="Suspend"><i class="bi bi-pause-circle"></i></button>
                                                </form>
                                            @elseif($shop->status === 'suspended' || $shop->status === 'rejected')
                                                <form method="POST" action="{{ route('super-admin.shops.approve', $shop) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success" title="Re-approve"><i class="bi bi-arrow-clockwise"></i></button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No shops registered yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">{{ $shops->links() }}</div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
