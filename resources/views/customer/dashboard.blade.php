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
            background: linear-gradient(180deg, #0f2a54 0%, #18447f 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            padding: 24px 16px;
            display: grid;
            gap: 18px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
            color: #f1f7ff;
        }
        .brand .icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: grid;
            place-items: center;
            color: #dce8ff;
            font-size: 22px;
        }
        .menu-list {
            display: grid;
            gap: 10px;
            margin-top: 6px;
        }
        .menu-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 8px 8px;
            border-radius: 12px;
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
            width: 38px;
            height: 38px;
            border-radius: 11px;
            border: 1px solid rgba(186, 203, 232, 0.35);
            background: rgba(140, 167, 214, 0.2);
            color: #dce8ff;
            display: grid;
            place-items: center;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: 0.4px;
            flex: 0 0 auto;
        }
        .menu-text {
            font-size: 20px;
            line-height: 1.25;
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
        }
        .chip .count { margin-left: auto; color: var(--green); }
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
            .sidebar { padding: 16px 12px; }
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
            $count = fn($status) => collect($orders)->where('status', $status)->count();
        @endphp
        <div class="chips">
            <div class="chip">Pending <span class="count">({{ $count('pending') }})</span></div>
            <div class="chip">Bidding <span class="count">({{ $count('bidding') }})</span></div>
            <div class="chip">In Progress <span class="count">({{ $count('inprogress') }})</span></div>
            <div class="chip">Editing <span class="count">({{ $count('editing') }})</span></div>
            <div class="chip">Completed <span class="count">({{ $count('completed') }})</span></div>
            <div class="chip">Revision <span class="count">({{ $count('revision') }})</span></div>
            <div class="chip">Approved <span class="count">({{ $count('approved') }})</span></div>
            <div class="chip">Cancelled <span class="count">({{ $count('cancelled') }})</span></div>
        </div>

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
                        </td>
                        <td>{{ $order['deadline'] ?? '48 Hours' }}</td>
                        <td>USD {{ number_format($order['cost'] ?? 0, 2) }}</td>
                        <td>#0</td>
                        <td><span class="status {{ $order['status'] }}">Pending</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; padding:20px; color:var(--muted); font-weight:800;">No orders yet</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
