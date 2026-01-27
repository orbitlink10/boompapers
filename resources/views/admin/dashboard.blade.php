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
            --accent: #f25c3c;
            --accent-soft: #ffe7df;
            --dark: #1c1c28;
            --muted: #6b6b7a;
            --card: #ffffff;
            --border: #ececf3;
            --bg: linear-gradient(180deg, #fff6f2 0%, #f8f9fb 100%);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
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
            gap: 28px;
            box-shadow: 8px 0 30px rgba(0,0,0,0.04);
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
            background: var(--accent);
            color: #fff;
            display: grid;
            place-items: center;
            font-size: 20px;
            box-shadow: 0 14px 28px rgba(242,92,60,0.35);
        }
        .badge-label {
            background: var(--accent-soft);
            padding: 12px 14px;
            border-radius: 14px;
            font-weight: 700;
            color: var(--accent);
        }
        .nav-group { display: grid; gap: 10px; }
        .nav-title { font-size: 12px; font-weight: 700; color: var(--muted); letter-spacing: 0.4px; text-transform: uppercase; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 12px;
            font-weight: 700;
            color: #2d2d35;
            transition: background .12s ease, color .12s ease;
        }
        .nav-link.active,
        .nav-link:hover { background: #fff2ec; color: var(--accent); }
        .nav-count {
            margin-left: auto;
            background: #f3f4f8;
            border-radius: 10px;
            padding: 4px 9px;
            font-size: 12px;
            font-weight: 800;
            color: #3a3a45;
        }
        .content {
            padding: 28px 32px 48px;
            display: grid;
            gap: 24px;
            background: var(--bg);
        }
        .topbar {
            display: flex;
            align-items: center;
            gap: 16px;
            justify-content: space-between;
        }
        .topbar .actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .btn {
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 12px 16px;
            font-weight: 800;
            cursor: pointer;
            background: #fff;
            color: #2b2b34;
            transition: transform .12s ease, box-shadow .12s ease, border .12s ease;
        }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 12px 24px rgba(0,0,0,0.06); }
        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 14px 30px rgba(242,92,60,0.35);
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
        }
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px 18px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.04);
            display: grid;
            gap: 8px;
        }
        .card .label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: #2c2c36;
        }
        .card .count { font-size: 24px; font-weight: 800; }
        .card small { color: var(--muted); }
        .card .trend {
            margin-left: auto;
            color: var(--accent);
            font-size: 13px;
            font-weight: 800;
        }
        .chart-placeholder {
            background: linear-gradient(120deg, rgba(242,92,60,0.12), rgba(255,255,255,0.9));
            border-radius: 20px;
            border: 1px dashed rgba(242,92,60,0.35);
            height: 240px;
            display: grid;
            place-items: center;
            color: var(--muted);
            font-weight: 700;
        }
        .table-card {
            padding: 0;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead { background: #fff7f3; }
        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
        }
        th { font-weight: 800; color: #3a3a45; }
        tbody tr:hover { background: #fdf8f6; }
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
        .status.assigned { background: #e8f5ff; color: #0c7bca; }
        .status.completed { background: #e9f8ee; color: #1a9b52; }
        .status.revision { background: #fff0f2; color: #d62d50; }
        .avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: #f2f4f8;
            display: grid; place-items: center;
            font-weight: 800; color: #4a4a55;
        }
        @media (max-width: 1100px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { flex-direction: row; flex-wrap: wrap; align-items: center; }
            .nav-group { grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); width: 100%; }
            .content { padding: 24px 20px 40px; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="mark">✦</div>
            <span>Admin</span>
        </div>
        <div class="badge-label">SMS - 1</div>

        <div class="nav-group">
            <div class="nav-title">Main</div>
            <a class="nav-link active" href="#"><span>Dashboard</span></a>
            <a class="nav-link" href="#"><span>Add Order</span></a>
            <a class="nav-link" href="#"><span>Orders</span><span class="nav-count">20</span></a>
            <a class="nav-link" href="#"><span>Courses</span><span class="nav-count">6</span></a>
        </div>

        <div class="nav-group">
            <div class="nav-title">Manage Users</div>
            <a class="nav-link" href="#"><span>Clients</span><span class="nav-count">9</span></a>
            <a class="nav-link" href="#"><span>Writers</span><span class="nav-count">3</span></a>
            <a class="nav-link" href="#"><span>Financial</span></a>
        </div>

        <div class="nav-group">
            <div class="nav-title">Configs</div>
            <a class="nav-link" href="#"><span>Mass Email</span></a>
            <a class="nav-link" href="#"><span>Configs</span></a>
        </div>
    </aside>

    <main class="content">
        @php
            $orders = $orders ?? session('orders', []);
            $count = function($status) use ($orders) { return collect($orders)->where('status', $status)->count(); };
        @endphp
        <div class="topbar">
            <div>
                <div style="font-size:14px; color:var(--muted); font-weight:700;">Welcome back</div>
                <div style="font-size:24px; font-weight:800;">Francis Kamau</div>
            </div>
            <div class="actions">
                <a class="btn" href="{{ route('admin.orders') }}">Invoices</a>
                <a class="btn btn-primary" href="{{ route('order.create') }}">New Order</a>
            </div>
        </div>

        <section class="summary-grid">
            @php $statuses = [
                ['key'=>'pending','icon'=>'📑','label'=>'Pending','sub'=>'Awaiting writer'],
                ['key'=>'available','icon'=>'🎧','label'=>'Available','sub'=>'Open to claim'],
                ['key'=>'assigned','icon'=>'💼','label'=>'Assigned','sub'=>'In progress'],
                ['key'=>'editing','icon'=>'🖥','label'=>'Editing','sub'=>'QA review'],
                ['key'=>'completed','icon'=>'🏁','label'=>'Completed','sub'=>'Delivered'],
                ['key'=>'revision','icon'=>'📅','label'=>'Revision','sub'=>'Awaiting fixes'],
                ['key'=>'approved','icon'=>'📢','label'=>'Approved','sub'=>'Paid'],
                ['key'=>'cancelled','icon'=>'↩','label'=>'Cancelled','sub'=>'Refunded'],
            ]; @endphp
            @foreach($statuses as $s)
                <a class="card" href="{{ route('admin.orders', ['status' => $s['key']]) }}" style="text-decoration:none; color:inherit; cursor:pointer;">
                    <div class="label"><span>{{ $s['icon'] }}</span> {{ $s['label'] }}</div>
                    <div class="count">{{ $count($s['key']) }} orders</div>
                    <div style="display:flex; align-items:center; gap:10px;"><small>{{ $s['sub'] }}</small><span class="trend">↗</span></div>
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
