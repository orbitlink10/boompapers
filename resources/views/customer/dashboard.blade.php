<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --green: #0f5951;
            --accent: #f8b23d;
            --muted: #6c737a;
            --border: #e5e8ed;
            --bg: #f7f8fb;
            --card: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: #1d1f22;
        }
        .layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background:#fff;
            border-right:1px solid var(--border);
            padding:24px;
            display:flex;
            flex-direction:column;
            gap:18px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
            color: #1d1f22;
        }
        .brand .icon {
            width:44px;
            height:44px;
            border-radius:14px;
            background:#0f5951;
            display: grid;
            place-items: center;
            color:#fff;
            font-size:22px;
        }
        .nav-group {
            display:flex;
            flex-direction:column;
            gap:8px;
        }
        .nav-title {
            font-size:12px;
            letter-spacing:0.4px;
            text-transform:uppercase;
            color:var(--muted);
            font-weight:800;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            text-decoration: none;
            color:#2f3236;
            font-weight:800;
            transition: background .12s ease, color .12s ease;
        }
        .nav-link.active,
        .nav-link:hover {
            background:#ecf3f1;
            color:var(--green);
        }
        .badge {
            margin-left:auto;
            background:#0f5951;
            color:#fff;
            border-radius: 10px;
            padding:4px 9px;
            font-size:12px;
            font-weight:800;
        }
        .content {
            padding: 24px 28px 40px;
            display: grid;
            gap: 18px;
        }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .topbar .user {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
        }
        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #ecf3f1;
            display: grid;
            place-items: center;
            color: var(--green);
            font-weight: 900;
        }
        .chips {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
        }
        .chip {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            box-shadow: 0 12px 28px rgba(0,0,0,0.04);
            color: inherit;
            text-decoration: none;
            transition: transform .12s ease, border-color .12s ease, box-shadow .12s ease;
        }
        .chip:hover {
            transform: translateY(-1px);
            border-color: rgba(15, 89, 81, 0.3);
            box-shadow: 0 16px 30px rgba(17, 42, 72, 0.08);
        }
        .chip.active {
            border-color: rgba(15, 89, 81, 0.45);
            box-shadow: 0 0 0 1px rgba(15, 89, 81, 0.1), 0 16px 30px rgba(17, 42, 72, 0.08);
        }
        .chip .count { margin-left: auto; color: var(--green); }
        .filter-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 12px 28px rgba(0,0,0,0.04);
        }
        .filter-banner a {
            color: var(--green);
            font-weight: 800;
            text-decoration: none;
        }
        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(17, 42, 72, 0.06);
        }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #fff8f0; }
        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
        }
        th { font-weight: 800; color: #2c2f33; }
        tr:hover { background: #f9fbfc; }
        .status {
            padding: 6px 10px;
            border-radius: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status.pending { background: #fff3d9; color: #c27a00; }
        .status.inprogress { background: #e9f6ff; color: #0b6fb8; }
        .status.completed { background: #e7f8ee; color: #1f9b55; }
        .search-bar {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 6px;
        }
        .search-bar input {
            flex: 1;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-weight: 700;
        }
        .search-bar button {
            padding: 12px 16px;
            border-radius: 10px;
            border: none;
            background: var(--green);
            color: #fff;
            font-weight: 900;
            cursor: pointer;
        }
        @media (max-width: 1000px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { gap:14px; padding:18px; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand"><span class="icon">◎</span><span>MyAccount</span></div>
        <div class="nav-group">
            <div class="nav-title">Main Menu</div>
            <a class="nav-link" href="{{ route('order.create') }}">New Order</a>
            <a class="nav-link {{ ($statusFilter ?? 'all') === 'assigned' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'assigned']) }}">Assigned</a>
            <a class="nav-link {{ ($statusFilter ?? 'all') === 'completed' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'completed']) }}">Completed</a>
            <a class="nav-link {{ ($statusFilter ?? 'all') === 'revision' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'revision']) }}">Revision</a>
            <a class="nav-link {{ ($statusFilter ?? 'all') === 'approved' ? 'active' : '' }}" href="{{ route('customer.dashboard', ['status' => 'approved']) }}">Approved</a>
        </div>
        <div class="nav-group">
            <div class="nav-title">Listing</div>
            <a class="nav-link" href="#">Courses</a>
            <a class="nav-link" href="#">Top Writers</a>
        </div>
        <div class="nav-group">
            <div class="nav-title">Account</div>
            <a class="nav-link" href="#">Profile</a>
            <a class="nav-link" href="{{ route('customer.logout') }}">Logout</a>
        </div>
    </aside>

    <main class="content">
        <div class="topbar">
            <div>
                <div style="font-size:14px; color:var(--muted); font-weight:700;">Welcome back</div>
                <div style="font-size:26px; font-weight:900;">{{ session('customer_name','Customer') }}</div>
            </div>
            <div class="user">
                <div class="avatar">{{ strtoupper(substr(session('customer_name','C'),0,1)) }}</div>
                <div>
                    <div style="font-weight:900;">{{ session('customer_name','Customer') }}</div>
                    <div style="color:var(--muted); font-weight:700; font-size:13px;">{{ session('customer_email','you@example.com') }}</div>
                </div>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" placeholder="Search orders">
            <button>Search</button>
        </div>

        @php
            $orders = $orders ?? [];
            $statusCards = $statusCards ?? [];
            $statusFilter = $statusFilter ?? 'all';
            $selectedStatusLabel = $selectedStatusLabel ?? null;
        @endphp
        <div class="chips">
            @foreach($statusCards as $card)
                <a
                    class="chip {{ !empty($card['active']) ? 'active' : '' }}"
                    href="{{ $card['url'] }}"
                    title="{{ ($card['count'] ?? 0) === 1 ? 'Open this order' : 'Show '.$card['label'].' orders' }}"
                >
                    <span>{{ $card['label'] }}</span>
                    <span class="count">({{ $card['count'] ?? 0 }})</span>
                </a>
            @endforeach
        </div>

        @if($statusFilter !== 'all' && $selectedStatusLabel)
            <div class="filter-banner">
                <div style="font-weight:800;">Showing {{ $selectedStatusLabel }} orders</div>
                <a href="{{ route('customer.dashboard') }}">Show all orders</a>
            </div>
        @endif

        <div class="table-card">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Deadline</th>
                    <th>Cost</th>
                    <th>Writer</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td><a href="{{ route('customer.order.show', ['id' => $order['id']]) }}" style="color:var(--green); font-weight:900; text-decoration:none;">#{{ $order['id'] }}</a></td>
                        <td>
                            <a href="{{ route('customer.order.show', ['id' => $order['id']]) }}" style="color:#2c2f33; font-weight:800; text-decoration:none;">
                                {{ $order['title'] }}
                            </a>
                            <br><small style="color:var(--muted); font-weight:700;">{{ $order['pages'] }} Pg(s)</small>
                            @if(!empty($order['client_notice']))
                                <div style="margin-top:8px; padding:8px 10px; border-radius:10px; background:#edf7ff; color:#145da0; font-size:12px; font-weight:800;">
                                    {{ $order['client_notice'] }}
                                </div>
                            @endif
                        </td>
                        <td>{{ $order['deadline'] ?? '48 Hours' }}</td>
                        <td>USD {{ number_format($order['cost'] ?? 0, 2) }}</td>
                        <td>{{ $order['writer_name'] ?? 'Unassigned' }}</td>
                        <td><span class="status {{ $order['status'] }}">{{ ucwords(str_replace('inprogress', 'in progress', $order['status'] ?? 'pending')) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; padding:20px; color:var(--muted); font-weight:800;">{{ $statusFilter === 'all' ? 'No orders yet' : 'No orders in this status' }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
