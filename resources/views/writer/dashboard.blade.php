<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writer Dashboard | BoomPapers</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue: #1d7bff;
            --dark: #14141a;
            --muted: #5c5c6a;
            --bg: #f7f8fb;
            --card: #ffffff;
            --border: #e6e8f0;
            --green: #0f5951;
            --danger: #c53030;
            --warn: #c27a00;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Manrope', system-ui, -apple-system, sans-serif;
            background:
                radial-gradient(circle at 88% 10%, rgba(29, 123, 255, 0.12), rgba(29, 123, 255, 0) 30%),
                var(--bg);
            color: var(--dark);
        }
        .topbar {
            background: rgba(255,255,255,0.9);
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .topbar-inner {
            max-width: 1240px;
            margin: 0 auto;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 20px;
            font-weight: 800;
        }
        .mark {
            width: 36px;
            height: 36px;
            border-radius: 11px;
            background: linear-gradient(135deg, #1d7bff, #25c7ff);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
        }
        .actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn {
            text-decoration: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 800;
            font-size: 14px;
            border: 1px solid transparent;
            color: #1f2b40;
            background: #fff;
        }
        .btn-ghost { border-color: var(--border); }
        .btn-primary {
            background: linear-gradient(135deg, #1d7bff, #25c7ff);
            color: #fff;
            box-shadow: 0 10px 22px rgba(29,123,255,0.28);
        }
        main {
            max-width: 1240px;
            margin: 0 auto;
            padding: 20px 18px 36px;
            display: grid;
            gap: 14px;
        }
        .intro {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
        }
        .intro h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.3px;
        }
        .intro p {
            margin: 6px 0 0;
            color: var(--muted);
            font-weight: 600;
        }
        .note {
            background: #eef5ff;
            border: 1px solid #d5e6ff;
            color: #2255a4;
            border-radius: 10px;
            padding: 8px 10px;
            font-weight: 700;
            font-size: 13px;
            white-space: nowrap;
        }
        .table-wrap {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead { background: #f4f8ff; }
        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }
        th {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.35px;
            color: #42526e;
            font-weight: 800;
        }
        tr:hover { background: #f9fbff; }
        .order-id {
            color: var(--green);
            font-weight: 900;
            text-decoration: none;
        }
        .title {
            font-size: 24px;
            font-weight: 800;
            line-height: 1.3;
            color: #1f2d44;
            letter-spacing: -0.2px;
        }
        .meta {
            margin-top: 6px;
            color: var(--muted);
            font-size: 13px;
            font-weight: 700;
        }
        .status {
            display: inline-flex;
            align-items: center;
            padding: 6px 9px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 800;
            text-transform: capitalize;
        }
        .status.pending { background: #fff3d9; color: #c27a00; }
        .status.assigned { background: #e8f5ff; color: #0b6fb8; }
        .status.available { background: #e7f8ee; color: #1f9b55; }
        .status.inprogress { background: #e8f5ff; color: #0b6fb8; }
        .status.completed { background: #e7f8ee; color: #1f9b55; }
        .status.revision { background: #fff0f2; color: #d62d50; }
        .status.editing { background: #f0f2ff; color: #3c4ad9; }
        .status.approved { background: #eaf6ff; color: #1f6fb5; }
        .status.cancelled { background: #fde9e9; color: #c53030; }
        .deadline-live {
            font-weight: 800;
            color: #1f2b40;
            white-space: nowrap;
        }
        .deadline-live.deadline-urgent { color: var(--warn); }
        .deadline-live.deadline-expired { color: var(--danger); }
        .empty {
            text-align: center;
            padding: 20px;
            color: var(--muted);
            font-weight: 800;
        }
        .scope-switch {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }
        .scope-link {
            text-decoration: none;
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 7px 12px;
            color: #2a3346;
            background: #fff;
            font-weight: 800;
            font-size: 13px;
        }
        .scope-link.active {
            border-color: #1d7bff;
            color: #1d7bff;
            background: #eff6ff;
        }
        .flash {
            margin: 0;
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 800;
            font-size: 13px;
        }
        .flash.success {
            background: #eafaf0;
            border: 1px solid #bbecce;
            color: #0f7a45;
        }
        .flash.error {
            background: #fde9e9;
            border: 1px solid #ffcaca;
            color: #bd2130;
        }
        .order-actions {
            display: grid;
            gap: 9px;
        }
        .order-actions form {
            display: grid;
            gap: 8px;
            margin: 0;
        }
        .order-actions select {
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 8px 10px;
            font-family: inherit;
            font-weight: 700;
            color: #1f2b40;
            background: #fff;
        }
        .btn-success {
            background: linear-gradient(135deg, #13b66c, #0f9f5a);
            color: #fff;
        }
        .btn-light {
            background: #f2f7ff;
            border-color: #d7e7ff;
        }
        @media (max-width: 1050px) {
            .intro {
                flex-direction: column;
                align-items: flex-start;
            }
            .title { font-size: 18px; }
            .scope-switch { width: 100%; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <div class="brand"><span class="mark">WR</span><span>Writer Dashboard</span></div>
            <div class="actions">
                <a class="btn btn-ghost" href="{{ route('writers.index') }}">Public Writers Page</a>
                <a class="btn btn-primary" href="{{ route('writer.logout') }}">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <section class="intro">
            <div>
                <h1>{{ session('writer_name', 'Writer') }}</h1>
                <p>All client-posted orders are shown here. Price is hidden from writer accounts.</p>
            </div>
            <div class="note">Writer deadline = Client deadline minus 4 hours</div>
        </section>

        @if(session('status'))
            <p class="flash success">{{ session('status') }}</p>
        @endif
        @if(session('error'))
            <p class="flash error">{{ session('error') }}</p>
        @endif
        @if(session('uploaded'))
            <p class="flash success">{{ session('uploaded') }}</p>
        @endif

        <section class="table-wrap">
            <div style="padding: 14px 16px; border-bottom: 1px solid var(--border); display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                <div class="scope-switch">
                    <a class="scope-link {{ ($scope ?? 'mine') === 'mine' ? 'active' : '' }}" href="{{ route('writer.dashboard', ['scope' => 'mine']) }}">
                        My Orders ({{ $counts['mine'] ?? 0 }})
                    </a>
                    <a class="scope-link {{ ($scope ?? '') === 'available' ? 'active' : '' }}" href="{{ route('writer.dashboard', ['scope' => 'available']) }}">
                        Available ({{ $counts['available'] ?? 0 }})
                    </a>
                    <a class="scope-link {{ ($scope ?? '') === 'all' ? 'active' : '' }}" href="{{ route('writer.dashboard', ['scope' => 'all']) }}">
                        All ({{ $counts['all'] ?? 0 }})
                    </a>
                </div>
                <div style="font-weight: 800; color:var(--muted);">Filter by scope to focus your workspace.</div>
            </div>
        </section>

        <section class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Pages</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Assigned</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td><span class="order-id">#{{ $order['id'] }}</span></td>
                            <td>
                                <div class="title">{{ $order['title'] }}</div>
                                <div class="meta">{{ $order['subject'] }}</div>
                            </td>
                            <td>
                                <div>{{ $order['client_name'] }}</div>
                                <div class="meta">{{ $order['client_email'] }}</div>
                            </td>
                            <td>{{ $order['type'] }}</td>
                            <td>{{ $order['pages'] }}</td>
                            <td>
                                <span class="deadline-live" data-deadline="{{ $order['writer_due_at'] ?? '' }}" data-fallback="{{ $order['writer_deadline_fallback'] }}">
                                    {{ $order['writer_deadline'] }}
                                </span>
                             </td>
                             <td><span class="status {{ $order['status'] }}">{{ $order['status'] }}</span></td>
                             <td>{{ $order['assigned_writer'] }}</td>
                             <td>
                                 <div class="order-actions">
                                    @if($order['can_claim'])
                                        <form action="{{ route('writer.order.claim', ['id' => $order['id']]) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-success" type="submit">Claim Order</button>
                                        </form>
                                    @elseif($order['is_assigned_to_current'])
                                        <form action="{{ route('writer.order.status', ['id' => $order['id']]) }}" method="POST">
                                            @csrf
                                            <select name="status" aria-label="Update order status">
                                                @foreach($order['status_options'] as $key => $value)
                                                    <option value="{{ $key }}" {{ ($order['status'] === $key) ? 'selected' : '' }}>{{ $value }}</option>
                                                @endforeach
                                            </select>
                                            <button class="btn" type="submit">Update Status</button>
                                        </form>
                                        <form action="{{ route('writer.order.files', ['id' => $order['id']]) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <input type="file" name="files[]" multiple>
                                            <button class="btn btn-light" type="submit">Upload Files</button>
                                        </form>
                                        @if(($order['files_count'] ?? 0) > 0)
                                            <span style="font-size:12px; font-weight:800; color:#1f6fb5">{{ $order['files_count'] }} file(s)</span>
                                        @endif
                                    @else
                                        <span style="color: var(--muted); font-weight: 800; font-size: 13px;">Read only</span>
                                    @endif
                                 </div>
                             </td>
                         </tr>
                    @empty
                        <tr><td colspan="9" class="empty">No client-posted orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>

    <script>
        const deadlineCells = document.querySelectorAll('.deadline-live');

        function formatRemaining(diffMs) {
            const totalMinutes = Math.max(Math.floor(diffMs / 60000), 0);
            const days = Math.floor(totalMinutes / 1440);
            const hours = Math.floor((totalMinutes % 1440) / 60);
            const minutes = totalMinutes % 60;

            if (days > 0) {
                return days + ' Day' + (days === 1 ? '' : 's') + ' ' + hours + 'h';
            }
            if (hours > 0) {
                return hours + 'h ' + minutes + 'm';
            }
            return minutes + 'm';
        }

        function refreshDeadlines() {
            deadlineCells.forEach(cell => {
                const rawDueAt = cell.dataset.deadline || '';
                const fallback = cell.dataset.fallback || 'N/A';
                const dueAtMs = Date.parse(rawDueAt);

                if (!Number.isFinite(dueAtMs)) {
                    cell.textContent = fallback;
                    cell.classList.remove('deadline-urgent', 'deadline-expired');
                    return;
                }

                const diffMs = dueAtMs - Date.now();
                cell.title = 'Writer due ' + new Date(dueAtMs).toLocaleString();

                if (diffMs <= 0) {
                    cell.textContent = 'Expired';
                    cell.classList.add('deadline-expired');
                    cell.classList.remove('deadline-urgent');
                    return;
                }

                cell.textContent = formatRemaining(diffMs);
                cell.classList.remove('deadline-expired');
                if (diffMs <= 60 * 60 * 1000) {
                    cell.classList.add('deadline-urgent');
                } else {
                    cell.classList.remove('deadline-urgent');
                }
            });
        }

        refreshDeadlines();
        setInterval(refreshDeadlines, 30000);
    </script>
</body>
</html>
