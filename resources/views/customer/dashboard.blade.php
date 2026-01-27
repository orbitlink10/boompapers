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
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 24px;
            display: grid;
            gap: 26px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 24px;
            font-weight: 800;
        }
        .brand .icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: #0f5951;
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 22px;
        }
        .nav-group { display: grid; gap: 8px; }
        .nav-title { font-size: 12px; letter-spacing: 0.4px; text-transform: uppercase; color: var(--muted); font-weight: 800; }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 12px;
            color: #2f3236;
            font-weight: 800;
            text-decoration: none;
            transition: background .12s ease, color .12s ease;
        }
        .nav-link.active, .nav-link:hover { background: #ecf3f1; color: var(--green); }
        .badge {
            margin-left: auto;
            background: #0f5951;
            color: #fff;
            border-radius: 10px;
            padding: 4px 9px;
            font-size: 12px;
            font-weight: 800;
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
            .sidebar { grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); }
        }
    </style>
</head>
<body>
<div class="layout">
    <aside class="sidebar">
        <div class="brand"><span class="icon">◎</span><span>MyAccount</span></div>
        <div class="nav-group">
            <div class="nav-title">Main Menu</div>
            <a class="nav-link" href="/order/create">New Order</a>
            <a class="nav-link active" href="/customer/dashboard">Orders <span class="badge">1</span></a>
            <a class="nav-link" href="#">Wallet <span class="badge">$0</span></a>
        </div>
        <div class="nav-group">
            <div class="nav-title">Listing</div>
            <a class="nav-link" href="#">Courses <span class="badge">0</span></a>
            <a class="nav-link" href="#">Top Writers</a>
        </div>
        <div class="nav-group">
            <div class="nav-title">Account</div>
            <a class="nav-link" href="#">Profile</a>
            <a class="nav-link" href="/customer/logout">Logout</a>
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
            $orders = session('orders', []);
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
