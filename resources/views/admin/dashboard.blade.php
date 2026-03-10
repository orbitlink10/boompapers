<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Writing Script</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1d7bff;
            --primary-strong: #1466de;
            --primary-soft: #eaf3ff;
            --dark: #14141a;
            --muted: #5c5c6a;
            --card: #ffffff;
            --border: #e6e8f0;
            --bg: #f7f8fb;
            --shadow: 0 16px 36px rgba(23, 44, 85, 0.08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background:
                radial-gradient(circle at 90% 8%, rgba(29, 123, 255, 0.12), rgba(29, 123, 255, 0) 28%),
                var(--bg);
            color: var(--dark);
            min-height: 100vh;
        }
        a { color: inherit; text-decoration: none; }
        .layout {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 24px 22px;
            display: flex;
            flex-direction: column;
            gap: 22px;
            box-shadow: 8px 0 30px rgba(0, 0, 0, 0.04);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            font-size: 22px;
        }
        .brand .mark {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), #25c7ff);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 16px;
            box-shadow: 0 14px 28px rgba(29, 123, 255, 0.32);
        }
        .badge-label {
            background: var(--primary-soft);
            padding: 10px 12px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 13px;
            color: var(--primary-strong);
        }
        .nav-group { display: grid; gap: 10px; }
        .nav-title {
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            color: #2d2d35;
            transition: background .12s ease, color .12s ease, transform .12s ease;
        }
        .nav-link.active,
        .nav-link:hover {
            background: var(--primary-soft);
            color: var(--primary-strong);
            transform: translateX(1px);
        }
        .nav-count {
            margin-left: auto;
            background: #f2f6ff;
            border-radius: 10px;
            padding: 4px 9px;
            font-size: 12px;
            font-weight: 800;
            color: #2f4168;
        }
        .content {
            padding: 24px 28px 42px;
            display: grid;
            gap: 18px;
            background: transparent;
        }
        .topbar {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 16px;
            justify-content: space-between;
            box-shadow: var(--shadow);
        }
        .topline { display: grid; gap: 2px; }
        .eyebrow { font-size: 13px; color: var(--muted); font-weight: 700; }
        .page-title { font-size: 36px; line-height: 1.05; font-weight: 800; letter-spacing: -0.4px; }
        .topbar .actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .btn {
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 12px 18px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            background: #fff;
            color: #2b2b34;
            transition: transform .12s ease, box-shadow .12s ease, border .12s ease;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }
        .btn:not(.btn-primary) { border-color: var(--border); }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #25c7ff);
            color: #fff;
            box-shadow: 0 14px 30px rgba(29, 123, 255, 0.3);
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 18px;
            box-shadow: var(--shadow);
            display: grid;
            gap: 8px;
            transition: transform .14s ease, box-shadow .14s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 36px rgba(23, 44, 85, 0.14);
        }
        .card .label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 17px;
            color: #2c2c36;
        }
        .label-icon {
            width: 28px;
            height: 28px;
            border-radius: 9px;
            background: var(--primary-soft);
            color: var(--primary-strong);
            border: 1px solid #d8e7ff;
            display: inline-grid;
            place-items: center;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.2px;
        }
        .card .count { font-size: 46px; line-height: 1; font-weight: 800; letter-spacing: -0.4px; }
        .card small { color: var(--muted); font-size: 14px; }
        .card .trend {
            margin-left: auto;
            color: var(--primary);
            font-size: 13px;
            font-weight: 800;
        }
        .chart-placeholder {
            background: linear-gradient(130deg, rgba(29, 123, 255, 0.12), rgba(37, 199, 255, 0.05) 38%, #fff 100%);
            border-radius: 20px;
            border: 1px dashed rgba(29, 123, 255, 0.3);
            height: 180px;
            display: grid;
            place-items: center;
            color: #45506a;
            font-weight: 700;
            font-size: 14px;
        }
        .table-card {
            padding: 0;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead { background: #f3f8ff; }
        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
        }
        th { font-weight: 800; color: #3a3a45; }
        tbody tr:hover { background: #f7fbff; }
        .status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 13px;
        }
        .status.pending { background: #fff4e5; color: #d66b00; }
        .status.available { background: #eef4ff; color: #4164a8; }
        .status.assigned { background: #e8f5ff; color: #0c7bca; }
        .status.editing { background: #f0f2ff; color: #3c4ad9; }
        .status.completed { background: #e9f8ee; color: #1a9b52; }
        .status.revision { background: #fff0f2; color: #d62d50; }
        .status.approved { background: #ecf8f3; color: #157f56; }
        .status.cancelled { background: #fff0f0; color: #cc3a3a; }
        .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: #edf3ff;
            border: 1px solid #d9e7ff;
            display: grid; place-items: center;
            font-weight: 800; color: #334568;
        }
        @media (max-width: 1100px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { flex-direction: row; flex-wrap: wrap; align-items: center; }
            .nav-group { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); width: 100%; }
            .content { padding: 24px 20px 40px; }
            .page-title { font-size: 28px; }
            .card .count { font-size: 36px; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="mark">AD</div>
            <span>Admin</span>
        </div>
        <div class="badge-label">SMS - 1</div>

        <div class="nav-group">
            <div class="nav-title">Main</div>
            <a class="nav-link active" href="{{ route('admin.dashboard') }}"><span>Dashboard</span></a>
            <a class="nav-link" href="{{ route('order.create') }}"><span>Add Order</span></a>
            <a class="nav-link" href="{{ route('admin.orders') }}"><span>Orders</span><span class="nav-count">{{ $navCounts['orders'] ?? 0 }}</span></a>
            <a class="nav-link" href="{{ route('admin.courses') }}"><span>Courses</span><span class="nav-count">{{ $navCounts['courses'] ?? 0 }}</span></a>
        </div>

        <div class="nav-group">
            <div class="nav-title">Manage Users</div>
            <a class="nav-link" href="{{ route('admin.clients') }}"><span>Clients</span><span class="nav-count">{{ $navCounts['clients'] ?? 0 }}</span></a>
            <a class="nav-link" href="{{ route('admin.writers') }}"><span>Writers</span><span class="nav-count">{{ $navCounts['writers'] ?? 0 }}</span></a>
            <a class="nav-link" href="{{ route('admin.orders') }}"><span>Financial</span></a>
        </div>

        <div class="nav-group">
            <div class="nav-title">Configs</div>
            <a class="nav-link" href="{{ route('admin.orders') }}"><span>Mass Email</span></a>
            <a class="nav-link" href="{{ route('admin.settings') }}"><span>Settings</span></a>
            <a class="nav-link" href="{{ route('admin.orders') }}"><span>Configs</span></a>
        </div>
    </aside>

    <main class="content">
        @php
            $orders = $orders ?? [];
            $count = function($status) use ($orders) { return collect($orders)->where('status', $status)->count(); };
        @endphp
        <div class="topbar">
            <div class="topline">
                <div class="eyebrow">Welcome back</div>
                <div class="page-title">{{ session('admin_name', 'Admin') }}</div>
            </div>
            <div class="actions">
                <a class="btn" href="{{ route('admin.orders') }}">Invoices</a>
                <a class="btn btn-primary" href="{{ route('order.create') }}">New Order</a>
            </div>
        </div>

        <section class="summary-grid">
            @php $statuses = [
                ['key'=>'pending','code'=>'PD','label'=>'Pending','sub'=>'Awaiting writer'],
                ['key'=>'available','code'=>'AV','label'=>'Available','sub'=>'Open to claim'],
                ['key'=>'assigned','code'=>'AS','label'=>'Assigned','sub'=>'In progress'],
                ['key'=>'editing','code'=>'ED','label'=>'Editing','sub'=>'QA review'],
                ['key'=>'completed','code'=>'CO','label'=>'Completed','sub'=>'Delivered'],
                ['key'=>'revision','code'=>'RV','label'=>'Revision','sub'=>'Awaiting fixes'],
                ['key'=>'approved','code'=>'AP','label'=>'Approved','sub'=>'Paid'],
                ['key'=>'cancelled','code'=>'CN','label'=>'Cancelled','sub'=>'Refunded'],
            ]; @endphp
            @foreach($statuses as $s)
                <a class="card" href="{{ route('admin.orders', ['status' => $s['key']]) }}" style="text-decoration:none; color:inherit; cursor:pointer;">
                    <div class="label"><span class="label-icon">{{ $s['code'] }}</span> {{ $s['label'] }}</div>
                    <div class="count">{{ $count($s['key']) }} orders</div>
                    <div style="display:flex; align-items:center; gap:10px;"><small>{{ $s['sub'] }}</small><span class="trend">Open</span></div>
                </a>
            @endforeach
        </section>

        <div class="card chart-placeholder">
            Performance chart placeholder (connect to your analytics or reporting data)
        </div>

        <div class="card table-card">
            <table>
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Client</th>
                    <th>Writer</th>
                    <th>Topic</th>
                    <th>Due</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order['id'] }}</td>
                        <td><span class="avatar">{{ strtoupper(substr($order['title'] ?? 'O',0,1)) }}</span> {{ $order['customer_name'] ?? 'Customer' }}</td>
                        <td>{{ $order['writer_name'] ?? 'Unassigned' }}</td>
                        <td>{{ $order['title'] ?? 'Untitled' }}</td>
                        <td>{{ $order['deadline'] ?? '48 Hours' }}</td>
                        <td><span class="status {{ $order['status'] ?? 'pending' }}">{{ ucfirst($order['status'] ?? 'pending') }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; padding:16px; color:#6b6b7a;">No orders yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
