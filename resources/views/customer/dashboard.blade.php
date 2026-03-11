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
            grid-template-columns: 220px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: linear-gradient(180deg, #0f2a54 0%, #18447f 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            padding: 18px 12px;
            display: grid;
            gap: 12px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 800;
            color: #f1f7ff;
        }
        .brand .icon {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: grid;
            place-items: center;
            color: #dce8ff;
            font-size: 17px;
        }
        .menu-list {
            display: grid;
            gap: 6px;
            margin-top: 2px;
        }
        .menu-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 6px;
            border-radius: 10px;
            text-decoration: none;
            transition: background .12s ease, color .12s ease, transform .12s ease;
            color: #d6e3fb;
        }
        .menu-link:hover, .menu-link.active {
            background: rgba(255, 255, 255, 0.08);
            color: #f4f8ff;
            transform: translateX(2px);
        }
        .menu-icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            border: 1px solid rgba(186, 203, 232, 0.35);
            background: rgba(140, 167, 214, 0.2);
            color: #dce8ff;
            display: grid;
            place-items: center;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.4px;
            flex: 0 0 auto;
        }
        .menu-text {
            font-size: 15px;
            line-height: 1.2;
            font-weight: 700;
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
            .sidebar { padding: 14px 10px; }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand"><span class="icon">◎</span><span>MyAccount</span></div>
        <nav class="menu-list">
            <a class="menu-link" href="{{ route('order.create') }}">
                <span class="menu-icon">NO</span>
                <span class="menu-text">New Order</span>
            </a>
            <a class="menu-link active" href="{{ route('customer.dashboard') }}">
                <span class="menu-icon">OR</span>
                <span class="menu-text">Orders</span>
            </a>
            <a class="menu-link" href="#">
                <span class="menu-icon">WA</span>
                <span class="menu-text">Wallet</span>
            </a>
            <a class="menu-link" href="#">
                <span class="menu-icon">CO</span>
                <span class="menu-text">Courses</span>
            </a>
            <a class="menu-link" href="#">
                <span class="menu-icon">TW</span>
                <span class="menu-text">Top Writers</span>
            </a>
            <a class="menu-link" href="#">
                <span class="menu-icon">PR</span>
                <span class="menu-text">Profile</span>
            </a>
            <a class="menu-link" href="{{ route('customer.logout') }}">
                <span class="menu-icon">LO</span>
                <span class="menu-text">Logout</span>
            </a>
        </nav>
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
