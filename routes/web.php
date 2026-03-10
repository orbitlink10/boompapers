<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('loadOrders')) {
    function loadOrders(): array
    {
        $file = storage_path('app/orders.json');
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $data = json_decode($json, true);
            if (!is_array($data)) {
                return [];
            }

            // Keep only orders that belong to a client account.
            $orders = collect($data)
                ->filter(function ($order) {
                    if (!is_array($order)) {
                        return false;
                    }

                    return trim((string) ($order['customer_email'] ?? '')) !== '';
                })
                ->values()
                ->all();

            return normalizeOrderDeadlines($orders);
        }
        return [];
    }
}

if (!function_exists('saveOrders')) {
    function saveOrders(array $orders): void
    {
        $file = storage_path('app/orders.json');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        file_put_contents($file, json_encode(array_values($orders)));
    }
}

if (!function_exists('loadWriters')) {
    function loadWriters(): array
    {
        $file = storage_path('app/writers.json');
        if (!file_exists($file)) {
            return [];
        }

        $json = file_get_contents($file);
        $data = json_decode($json, true);

        return is_array($data) ? array_values($data) : [];
    }
}

if (!function_exists('saveWriters')) {
    function saveWriters(array $writers): void
    {
        $file = storage_path('app/writers.json');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }

        file_put_contents($file, json_encode(array_values($writers)));
    }
}

if (!function_exists('findWriterByEmail')) {
    function findWriterByEmail(array $writers, ?string $email): ?array
    {
        $email = strtolower(trim((string) $email));
        if ($email === '') {
            return null;
        }

        foreach ($writers as $writer) {
            if (!is_array($writer)) {
                continue;
            }
            $writerEmail = strtolower(trim((string) ($writer['email'] ?? '')));
            if ($writerEmail === $email) {
                return $writer;
            }
        }

        return null;
    }
}

if (!function_exists('durationLabelFromSeconds')) {
    function durationLabelFromSeconds(int $seconds): string
    {
        $seconds = max(0, $seconds);
        $totalMinutes = (int) floor($seconds / 60);
        $days = (int) floor($totalMinutes / 1440);
        $hours = (int) floor(($totalMinutes % 1440) / 60);
        $minutes = $totalMinutes % 60;

        if ($days > 0) {
            return $days . ' Day' . ($days === 1 ? '' : 's') . ' ' . $hours . 'h';
        }

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }
}

if (!function_exists('remainingDeadlineLabelFor')) {
    function remainingDeadlineLabelFor(?string $dueAt, string $fallback = 'N/A'): string
    {
        $dueAtTs = strtotime((string) ($dueAt ?? ''));
        if ($dueAtTs === false || $dueAtTs <= 0) {
            return $fallback;
        }

        $diffSeconds = $dueAtTs - time();
        if ($diffSeconds <= 0) {
            return 'Expired';
        }

        return durationLabelFromSeconds($diffSeconds);
    }
}

if (!function_exists('deadlineSecondsFor')) {
    function deadlineSecondsFor(?string $deadline): ?int
    {
        $label = trim((string) $deadline);
        if ($label === '') {
            return null;
        }

        if (!preg_match('/^(\d+)\s*(hour|hours|day|days)$/i', $label, $matches)) {
            return null;
        }

        $amount = (int) $matches[1];
        $unit = strtolower($matches[2]);
        if ($amount <= 0) {
            return null;
        }

        if (in_array($unit, ['day', 'days'], true)) {
            return $amount * 24 * 60 * 60;
        }

        return $amount * 60 * 60;
    }
}

if (!function_exists('writerDueAtForOrder')) {
    function writerDueAtForOrder(array $order): ?string
    {
        $baseDueTs = strtotime((string) ($order['due_at'] ?? ''));
        if ($baseDueTs === false || $baseDueTs <= 0) {
            $createdAtTs = strtotime((string) ($order['created_at'] ?? ''));
            if ($createdAtTs === false || $createdAtTs <= 0) {
                $createdAtTs = time();
            }

            $seconds = deadlineSecondsFor($order['deadline'] ?? null);
            if ($seconds === null) {
                return null;
            }
            $baseDueTs = $createdAtTs + $seconds;
        }

        $writerDueTs = $baseDueTs - (4 * 60 * 60);

        return date(DATE_ATOM, $writerDueTs);
    }
}

if (!function_exists('normalizeOrderDeadlines')) {
    function normalizeOrderDeadlines(array $orders): array
    {
        $normalized = [];
        $changed = false;

        foreach ($orders as $order) {
            if (!is_array($order)) {
                continue;
            }

            $item = $order;

            $createdAtTs = strtotime((string) ($item['created_at'] ?? ''));
            if ($createdAtTs === false || $createdAtTs <= 0) {
                $createdAtTs = time();
                $item['created_at'] = date(DATE_ATOM, $createdAtTs);
                $changed = true;
            }

            $dueAtTs = strtotime((string) ($item['due_at'] ?? ''));
            if ($dueAtTs === false || $dueAtTs <= 0) {
                $seconds = deadlineSecondsFor($item['deadline'] ?? null);
                if ($seconds !== null) {
                    $item['due_at'] = date(DATE_ATOM, $createdAtTs + $seconds);
                    $changed = true;
                }
            }

            $normalized[] = $item;
        }

        if ($changed) {
            saveOrders($normalized);
        }

        return $normalized;
    }
}

if (!function_exists('ordersForCustomer')) {
    function ordersForCustomer(array $orders, ?string $email): array
    {
        $email = trim((string) $email);
        if ($email === '') {
            return [];
        }

        return collect($orders)
            ->filter(function ($order) use ($email) {
                $orderEmail = trim((string) ($order['customer_email'] ?? ''));
                return $orderEmail !== '' && strcasecmp($orderEmail, $email) === 0;
            })
            ->values()
            ->all();
    }
}

if (!function_exists('storeOrderFiles')) {
    function storeOrderFiles(array $files, int $orderId): void
    {
        if (empty($files)) {
            return;
        }

        $stored = session('order_files', []);
        $dir = storage_path('app/uploads');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            $name = uniqid('order_' . $orderId . '_', true) . '_' . $file->getClientOriginalName();
            $file->move($dir, $name);
            $stored[] = [
                'order_id' => $orderId,
                'name' => $file->getClientOriginalName(),
                'path' => $name,
                'date' => now()->toDateTimeString(),
            ];
        }

        session(['order_files' => $stored]);
    }
}

if (!function_exists('pricingLevels')) {
    function pricingLevels(): array
    {
        return ['High School', 'College', 'Masters', 'PhD'];
    }
}

if (!function_exists('pricingDeadlines')) {
    function pricingDeadlines(): array
    {
        return ['8 Hours', '24 Hours', '48 Hours', '3 Days', '5 Days', '7 Days', '14 Days'];
    }
}

if (!function_exists('defaultPricingMatrix')) {
    function defaultPricingMatrix(): array
    {
        return [
            'High School' => [
                '8 Hours' => 29.6,
                '24 Hours' => 25.6,
                '48 Hours' => 19.6,
                '3 Days' => 17.6,
                '5 Days' => 15.6,
                '7 Days' => 14.6,
                '14 Days' => 12.6,
            ],
            'College' => [
                '8 Hours' => 32.6,
                '24 Hours' => 28.6,
                '48 Hours' => 21.6,
                '3 Days' => 19.6,
                '5 Days' => 17.6,
                '7 Days' => 16.6,
                '14 Days' => 14.6,
            ],
            'Masters' => [
                '8 Hours' => 36.6,
                '24 Hours' => 32.6,
                '48 Hours' => 25.6,
                '3 Days' => 23.6,
                '5 Days' => 21.6,
                '7 Days' => 20.6,
                '14 Days' => 18.6,
            ],
            'PhD' => [
                '8 Hours' => 40.6,
                '24 Hours' => 36.6,
                '48 Hours' => 29.6,
                '3 Days' => 27.6,
                '5 Days' => 25.6,
                '7 Days' => 24.6,
                '14 Days' => 22.6,
            ],
        ];
    }
}

if (!function_exists('loadPricing')) {
    function loadPricing(): array
    {
        $defaults = defaultPricingMatrix();
        $file = storage_path('app/pricing.json');
        if (!file_exists($file)) {
            return $defaults;
        }

        $json = file_get_contents($file);
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return $defaults;
        }

        $pricing = [];
        foreach (pricingLevels() as $level) {
            foreach (pricingDeadlines() as $deadline) {
                $value = $decoded[$level][$deadline] ?? $defaults[$level][$deadline];
                $pricing[$level][$deadline] = is_numeric($value) ? round((float) $value, 2) : $defaults[$level][$deadline];
            }
        }

        return $pricing;
    }
}

if (!function_exists('savePricing')) {
    function savePricing(array $pricing): void
    {
        $file = storage_path('app/pricing.json');
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }
        file_put_contents($file, json_encode($pricing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

if (!function_exists('pricePerPageFor')) {
    function pricePerPageFor(?string $level, ?string $deadline): float
    {
        $pricing = loadPricing();
        $level = trim((string) ($level ?? 'College'));
        $deadline = trim((string) ($deadline ?? '48 Hours'));

        if (isset($pricing[$level][$deadline])) {
            return (float) $pricing[$level][$deadline];
        }

        foreach ($pricing as $savedLevel => $rows) {
            if (strcasecmp($savedLevel, $level) !== 0) {
                continue;
            }
            foreach ($rows as $savedDeadline => $value) {
                if (strcasecmp($savedDeadline, $deadline) === 0) {
                    return (float) $value;
                }
            }
        }

        return (float) ($pricing['College']['48 Hours'] ?? 21.6);
    }
}

if (!function_exists('categoryMultiplierFor')) {
    function categoryMultiplierFor(?string $category): float
    {
        $multipliers = [
            'standard' => 1.0,
            'premium' => 1.15,
            'platinum' => 1.30,
        ];

        $key = strtolower(trim((string) ($category ?? 'standard')));
        return $multipliers[$key] ?? 1.0;
    }
}

Route::get('/', function () {
    return view('welcome');
});

Route::get('/writers', function () {
    $writers = [
        ['name' => 'Alice Writer', 'specialty' => 'Business, Management', 'rating' => '4.9', 'orders' => 312],
        ['name' => 'Brian Smith', 'specialty' => 'Nursing, Healthcare', 'rating' => '4.8', 'orders' => 284],
        ['name' => 'Carol Johnson', 'specialty' => 'Technology, IT', 'rating' => '4.9', 'orders' => 355],
        ['name' => 'David Lee', 'specialty' => 'Literature, History', 'rating' => '4.7', 'orders' => 241],
        ['name' => 'Eva Brown', 'specialty' => 'Economics, Finance', 'rating' => '4.8', 'orders' => 298],
    ];

    return view('writers', ['writers' => $writers]);
})->name('writers.index');

Route::get('/order', function () {
    return view('order');
})->name('order');

Route::get('/order/create', function () {
    return view('order-form', [
        'pricing' => loadPricing(),
    ]);
})->name('order.create');

Route::post('/customer/register', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'name' => 'required|string|max:255',
        'password' => 'required|string|min:6',
        'phone_country' => 'nullable|string|max:50',
        'phone_number' => 'nullable|string|max:30',
    ]);

    session([
        'customer_logged_in' => true,
        'customer_email' => $data['email'],
        'customer_name' => $data['name'],
    ]);

    return redirect()->route('customer.dashboard');
})->name('customer.register');

Route::post('/customer/login', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    // For demo purposes we accept any credentials; in production verify against DB.
    session([
        'customer_logged_in' => true,
        'customer_email' => $data['email'],
        'customer_name' => strstr($data['email'], '@', true) ?: 'Customer',
    ]);

    return redirect()->route('customer.dashboard');
})->name('customer.login');

Route::get('/customer/logout', function () {
    session()->forget(['customer_logged_in', 'customer_email', 'customer_name']);
    return redirect()->route('order', ['tab' => 'existing']);
})->name('customer.logout');

Route::get('/writer', function (\Illuminate\Http\Request $request) {
    if (session('writer_logged_in')) {
        return redirect()->route('writer.dashboard');
    }

    return view('writer.auth', [
        'tab' => $request->query('tab', 'new'),
    ]);
})->name('writer.auth');

Route::post('/writer/register', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'password' => 'required|string|min:6',
        'tab' => 'nullable|string',
    ]);

    $writers = loadWriters();
    if (findWriterByEmail($writers, $data['email'])) {
        return back()
            ->withErrors(['email' => 'A writer account with this email already exists.'])
            ->withInput();
    }

    $id = (collect($writers)->max('id') ?? 0) + 1;
    $writers[] = [
        'id' => $id,
        'name' => $data['name'],
        'email' => strtolower(trim($data['email'])),
        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        'created_at' => now()->toIso8601String(),
    ];
    saveWriters($writers);

    session([
        'writer_logged_in' => true,
        'writer_id' => $id,
        'writer_name' => $data['name'],
        'writer_email' => strtolower(trim($data['email'])),
    ]);

    return redirect()->route('writer.dashboard');
})->name('writer.register');

Route::post('/writer/login', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'tab' => 'nullable|string',
    ]);

    $writer = findWriterByEmail(loadWriters(), $data['email']);
    $passwordHash = (string) ($writer['password'] ?? '');
    $isValid = $writer && $passwordHash !== '' && password_verify($data['password'], $passwordHash);
    if (!$isValid) {
        return back()
            ->withErrors(['credentials' => 'Invalid email or password.'])
            ->withInput();
    }

    session([
        'writer_logged_in' => true,
        'writer_id' => $writer['id'] ?? null,
        'writer_name' => $writer['name'] ?? 'Writer',
        'writer_email' => $writer['email'] ?? strtolower(trim($data['email'])),
    ]);

    return redirect()->route('writer.dashboard');
})->name('writer.login');

Route::get('/writer/logout', function () {
    session()->forget(['writer_logged_in', 'writer_id', 'writer_name', 'writer_email']);
    return redirect()->route('writer.auth', ['tab' => 'existing']);
})->name('writer.logout');

Route::get('/writer/dashboard', function (\Illuminate\Http\Request $request) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $writerId = (int) (session('writer_id') ?? 0);
    $writerName = trim((string) (session('writer_name') ?? ''));
    $writerEmail = strtolower(trim((string) (session('writer_email') ?? '')));
    $menu = strtolower(trim((string) $request->query('menu', 'available')));
    if (!in_array($menu, ['available', 'assigned', 'revision', 'completed'], true)) {
        $menu = 'available';
    }

    $activeAssignedStatuses = ['assigned', 'inprogress', 'editing'];
    $completedStatuses = ['completed', 'approved'];
    $statusOptions = [
        'assigned' => 'Assigned',
        'inprogress' => 'In Progress',
        'editing' => 'Editing',
        'revision' => 'Revision',
        'completed' => 'Completed',
        'approved' => 'Approved',
    ];

    $ordersCollection = collect(loadOrders());

    $isMine = function (array $order) use ($writerId, $writerName, $writerEmail) {
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));
        $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));
        return $writerId > 0 && $assignedId === $writerId
            || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0)
            || ($writerEmail !== '' && $assignedEmail !== '' && strcasecmp($assignedEmail, $writerEmail) === 0);
    };

    $isAvailable = function (array $order) {
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));
        $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));

        return $assignedId <= 0
            && $assignedName === ''
            && $assignedEmail === ''
            && in_array($status, ['pending', 'available'], true);
    };

    $matchesMenu = function (array $order, string $selectedMenu) use ($isMine, $isAvailable, $activeAssignedStatuses, $completedStatuses) {
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));

        if ($selectedMenu === 'available') {
            return $isAvailable($order);
        }

        if (!$isMine($order)) {
            return false;
        }

        if ($selectedMenu === 'assigned') {
            return in_array($status, $activeAssignedStatuses, true);
        }

        if ($selectedMenu === 'revision') {
            return $status === 'revision';
        }

        return in_array($status, $completedStatuses, true);
    };

    $menuItems = [
        [
            'key' => 'available',
            'short' => 'AV',
            'label' => 'Available',
            'description' => 'Open orders ready to take.',
            'count' => $ordersCollection->filter(fn ($order) => is_array($order) && $matchesMenu($order, 'available'))->count(),
        ],
        [
            'key' => 'assigned',
            'short' => 'AS',
            'label' => 'Assigned',
            'description' => 'Orders currently on your desk.',
            'count' => $ordersCollection->filter(fn ($order) => is_array($order) && $matchesMenu($order, 'assigned'))->count(),
        ],
        [
            'key' => 'revision',
            'short' => 'RV',
            'label' => 'Revision',
            'description' => 'Orders waiting for changes.',
            'count' => $ordersCollection->filter(fn ($order) => is_array($order) && $matchesMenu($order, 'revision'))->count(),
        ],
        [
            'key' => 'completed',
            'short' => 'CM',
            'label' => 'Completed',
            'description' => 'Finished work and approvals.',
            'count' => $ordersCollection->filter(fn ($order) => is_array($order) && $matchesMenu($order, 'completed'))->count(),
        ],
    ];

    $activeMenu = collect($menuItems)->firstWhere('key', $menu) ?? $menuItems[0];

    $orders = $ordersCollection
        ->filter(function ($order) use ($menu, $matchesMenu) {
            if (!is_array($order)) {
                return false;
            }

            return $matchesMenu($order, $menu);
        })
        ->sortByDesc(fn ($order) => (int) ($order['id'] ?? 0))
        ->map(function (array $order) use ($writerId, $writerName, $writerEmail, $statusOptions, $isAvailable) {
            $writerDueAt = writerDueAtForOrder($order);
            $postedDeadlineSeconds = deadlineSecondsFor($order['deadline'] ?? null);
            $adjustedSeconds = $postedDeadlineSeconds !== null ? max(0, $postedDeadlineSeconds - (4 * 60 * 60)) : null;
            $fallbackDeadline = $adjustedSeconds !== null
                ? durationLabelFromSeconds($adjustedSeconds)
                : (string) ($order['deadline'] ?? 'N/A');

            $status = strtolower(trim((string) ($order['status'] ?? 'pending')));
            $orderWriterId = (int) ($order['writer_id'] ?? 0);
            $orderWriterName = trim((string) ($order['writer_name'] ?? ''));
            $orderWriterEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));

            $isAssignedToCurrent = $writerId > 0 && $orderWriterId === $writerId
                || ($writerName !== '' && $orderWriterName !== '' && strcasecmp($orderWriterName, $writerName) === 0)
                || ($writerEmail !== '' && $orderWriterEmail !== '' && strcasecmp($orderWriterEmail, $writerEmail) === 0);
            $isTakeable = $isAvailable($order);
            $canViewOrderStatus = $isAssignedToCurrent || $isTakeable;

            $files = collect(session('order_files', []))
                ->where('order_id', (int) ($order['id'] ?? 0))
                ->values()
                ->all();

            return [
                'id' => $order['id'] ?? null,
                'title' => $order['title'] ?? 'Untitled',
                'subject' => $order['subject'] ?? 'Other',
                'type' => $order['type'] ?? 'Essay',
                'pages' => (int) ($order['pages'] ?? 1),
                'status' => $canViewOrderStatus ? $status : 'private',
                'client_name' => $order['customer_name'] ?? 'Client',
                'client_email' => $order['customer_email'] ?? 'client@example.com',
                'assigned_writer' => $isAssignedToCurrent
                    ? ($orderWriterName !== '' ? $orderWriterName : 'Unassigned')
                    : ($isTakeable ? 'Unassigned' : 'Private'),
                'writer_due_at' => $writerDueAt,
                'writer_deadline' => remainingDeadlineLabelFor($writerDueAt, $fallbackDeadline),
                'writer_deadline_fallback' => $fallbackDeadline,
                'is_assigned_to_current' => $isAssignedToCurrent,
                'can_take' => $isTakeable,
                'status_options' => $statusOptions,
                'status_label' => !$canViewOrderStatus
                    ? 'Private'
                    : ($status === 'inprogress' ? 'In Progress' : ucfirst($status)),
                'files_count' => count($files),
            ];
        })
        ->values()
        ->all();

    return view('writer.dashboard', [
        'orders' => $orders,
        'menu' => $menu,
        'menuItems' => $menuItems,
        'activeMenu' => $activeMenu,
    ]);
})->name('writer.dashboard');

Route::post('/writer/orders/{id}/claim', function (\Illuminate\Http\Request $request, $id) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $orders = loadOrders();
    $targetId = (int) $id;
    $writerId = (int) (session('writer_id') ?? 0);
    $writerName = trim((string) (session('writer_name') ?? ''));
    $writerEmail = strtolower(trim((string) (session('writer_email') ?? '')));
    $found = false;
    $updated = false;

    foreach ($orders as &$order) {
        if (!is_array($order) || (int) ($order['id'] ?? 0) !== $targetId) {
            continue;
        }

        $found = true;
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));
        $assignedEmail = strtolower(trim((string) ($order['writer_email'] ?? '')));
        $status = strtolower(trim((string) ($order['status'] ?? 'pending')));

        if (($assignedId > 0 && $writerId > 0 && $assignedId !== $writerId)
            || ($assignedName !== '' && $writerName !== '' && strcasecmp($assignedName, $writerName) !== 0)
            || ($assignedEmail !== '' && $writerEmail !== '' && strcasecmp($assignedEmail, $writerEmail) !== 0)) {
            return back()->with('error', 'This order is already assigned.');
        }

        if ($assignedId === $writerId || ($assignedName !== '' && strcasecmp($assignedName, $writerName) === 0)) {
            return back()->with('status', 'This order is already assigned to you.');
        }

        if (!in_array($status, ['pending', 'available'], true)) {
            return back()->with('error', 'Only available orders can be claimed right now.');
        }

        $noticeTime = now()->toIso8601String();
        $displayWriter = $writerName !== '' ? $writerName : 'A writer';
        $order['writer_id'] = $writerId;
        $order['writer_name'] = $writerName;
        $order['writer_email'] = $writerEmail;
        $order['status'] = 'assigned';
        $order['writer_taken_at'] = $noticeTime;
        $order['client_notice'] = $displayWriter . ' has taken your order. Work is now in progress.';
        $order['client_notice_at'] = $noticeTime;
        $updated = true;
        break;
    }

    if (!$found) {
        return back()->with('error', 'Order not found.');
    }

    if (!$updated) {
        return back()->with('error', 'Could not claim this order.');
    }

    saveOrders($orders);
    return redirect()->route('writer.dashboard', ['menu' => 'assigned'])
        ->with('status', 'Order taken successfully. Client notified immediately.');
})->name('writer.order.claim');

Route::post('/writer/orders/{id}/status', function (\Illuminate\Http\Request $request, $id) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $data = $request->validate([
        'status' => 'required|string|max:50',
    ]);

    $allowedStatuses = ['assigned', 'inprogress', 'editing', 'revision', 'completed', 'approved'];
    $newStatus = strtolower(trim((string) $data['status']));
    if (!in_array($newStatus, $allowedStatuses, true)) {
        return back()->with('error', 'Invalid status selected.');
    }

    $orders = loadOrders();
    $writerId = (int) (session('writer_id') ?? 0);
    $writerName = trim((string) (session('writer_name') ?? ''));
    $targetId = (int) $id;
    $found = false;

    foreach ($orders as &$order) {
        if (!is_array($order) || (int) ($order['id'] ?? 0) !== $targetId) {
            continue;
        }

        $found = true;
        $assignedId = (int) ($order['writer_id'] ?? 0);
        $assignedName = trim((string) ($order['writer_name'] ?? ''));

        if (!($assignedId === $writerId || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0))) {
            return back()->with('error', 'You can only update orders assigned to you.');
        }

        $displayWriter = $assignedName !== '' ? $assignedName : ($writerName !== '' ? $writerName : 'Your writer');
        $noticeTime = now()->toIso8601String();
        $order['status'] = $newStatus;
        $order['writer_last_update_at'] = $noticeTime;

        if ($newStatus === 'revision') {
            $order['client_notice'] = $displayWriter . ' moved your order to revision.';
        } elseif (in_array($newStatus, ['completed', 'approved'], true)) {
            $order['client_notice'] = $displayWriter . ' marked your order as completed.';
        } else {
            $order['client_notice'] = $displayWriter . ' updated your order status to ' . ($newStatus === 'inprogress' ? 'in progress' : $newStatus) . '.';
        }
        $order['client_notice_at'] = $noticeTime;
        break;
    }

    if (!$found) {
        return back()->with('error', 'Order not found.');
    }

    saveOrders($orders);
    $targetMenu = 'assigned';
    if ($newStatus === 'revision') {
        $targetMenu = 'revision';
    } elseif (in_array($newStatus, ['completed', 'approved'], true)) {
        $targetMenu = 'completed';
    }

    return redirect()->route('writer.dashboard', ['menu' => $targetMenu])
        ->with('status', 'Order status updated.');
})->name('writer.order.status');

Route::post('/writer/orders/{id}/files', function (\Illuminate\Http\Request $request, $id) {
    if (!session('writer_logged_in')) {
        return redirect()->route('writer.auth', ['tab' => 'existing']);
    }

    $orders = loadOrders();
    $order = collect($orders)->firstWhere('id', (int) $id);
    if (!$order || !is_array($order)) {
        return redirect()->route('writer.dashboard')->with('error', 'Order not found.');
    }

    $writerId = (int) (session('writer_id') ?? 0);
    $writerName = trim((string) (session('writer_name') ?? ''));
    $assignedId = (int) ($order['writer_id'] ?? 0);
    $assignedName = trim((string) ($order['writer_name'] ?? ''));

    if (!($assignedId === $writerId || ($writerName !== '' && $assignedName !== '' && strcasecmp($assignedName, $writerName) === 0))) {
        return back()->with('error', 'You can only upload files to orders assigned to you.');
    }

    $request->validate([
        'files' => 'nullable|array',
        'files.*' => 'file|max:5120',
    ]);

    storeOrderFiles($request->file('files', []), (int) $id);
    return back()->with('uploaded', 'Files uploaded successfully.');
})->name('writer.order.files');

Route::get('/customer/dashboard', function () {
    if (!session('customer_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }
    $orders = loadOrders();
    $customerOrders = ordersForCustomer($orders, session('customer_email'));

    return view('customer.dashboard', ['orders' => $customerOrders]);
})->name('customer.dashboard');

Route::post('/order/submit', function (\Illuminate\Http\Request $request) {
    if (!session('customer_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }

    $data = $request->validate([
        'title' => 'nullable|string|max:255',
        'type' => 'nullable|string|max:100',
        'level' => 'nullable|string|max:50',
        'format' => 'nullable|string|max:50',
        'spacing' => 'nullable|string|max:20',
        'deadline' => 'nullable|string|max:50',
        'category' => 'nullable|string|max:50',
        'subject' => 'nullable|string|max:100',
        'instructions' => 'nullable|string',
        'pages' => 'nullable|integer|min:1',
        'sources' => 'nullable|integer|min:0',
        'slides' => 'nullable|integer|min:0',
        'charts' => 'nullable|integer|min:0',
        'vip_support' => 'nullable|boolean',
        'draft_outline' => 'nullable|boolean',
        'files' => 'nullable|array',
        'files.*' => 'file|max:5120',
    ]);

    $orders = loadOrders();
    $id = (collect($orders)->max('id') ?? 802) + 1;
    $pages = $data['pages'] ?? 1;
    $selectedLevel = $data['level'] ?? 'College';
    $selectedDeadline = $data['deadline'] ?? '48 Hours';
    $selectedCategory = $data['category'] ?? 'Standard';
    $pricePerPage = pricePerPageFor($selectedLevel, $selectedDeadline);
    $categoryMultiplier = categoryMultiplierFor($selectedCategory);
    $createdAt = now();
    $deadlineSeconds = deadlineSecondsFor($selectedDeadline);
    $dueAt = $deadlineSeconds !== null
        ? $createdAt->copy()->addSeconds($deadlineSeconds)->toIso8601String()
        : null;
    $vipSupport = $request->boolean('vip_support');
    $draftOutline = $request->boolean('draft_outline');
    $extrasCost = ($vipSupport ? 25 : 0) + ($draftOutline ? 20 : 0);
    $baseCost = $pages * $pricePerPage;
    $totalCost = round(($baseCost * $categoryMultiplier) + $extrasCost, 2);
    $orders[] = [
        'id' => $id,
        'title' => $data['title'] ?? 'Untitled Paper',
        'pages' => $pages,
        'cost' => $totalCost,
        'status' => 'pending',
        'deadline' => $selectedDeadline,
        'level' => $selectedLevel,
        'type' => $data['type'] ?? 'Essay',
        'format' => $data['format'] ?? 'APA',
        'spacing' => $data['spacing'] ?? 'Double',
        'category' => $selectedCategory,
        'subject' => $data['subject'] ?? 'Other',
        'sources' => $data['sources'] ?? 0,
        'slides' => $data['slides'] ?? 0,
        'charts' => $data['charts'] ?? 0,
        'vip_support' => $vipSupport,
        'draft_outline' => $draftOutline,
        'created_at' => $createdAt->toIso8601String(),
        'due_at' => $dueAt,
        'customer_email' => session('customer_email', 'customer'),
        'customer_name' => session('customer_name', 'Customer'),
    ];
    saveOrders($orders);
    storeOrderFiles($request->file('files', []), $id);

    return redirect()->route('customer.dashboard');
})->name('order.submit');

Route::get('/customer/orders/{id}', function ($id) {
    if (!session('customer_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }
    $orders = ordersForCustomer(loadOrders(), session('customer_email'));
    $order = collect($orders)->firstWhere('id', (int)$id);
    if (!$order) {
        return redirect()->route('customer.dashboard');
    }
    $files = collect(session('order_files', []))->where('order_id', (int)$id)->values()->all();
    return view('customer.order', [
        'order' => $order,
        'files' => $files,
        'orderCount' => count($orders),
    ]);
})->name('customer.order.show');

Route::post('/customer/orders/{id}/files', function (\Illuminate\Http\Request $request, $id) {
    if (!session('customer_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }
    $orders = ordersForCustomer(loadOrders(), session('customer_email'));
    $order = collect($orders)->firstWhere('id', (int)$id);
    if (!$order) {
        return redirect()->route('customer.dashboard');
    }

    $request->validate([
        'files' => 'nullable|array',
        'files.*' => 'file|max:5120', // 5MB each for demo
    ]);

    storeOrderFiles($request->file('files', []), (int) $id);

    return back()->with('uploaded', 'Files uploaded successfully.');
})->name('customer.order.files');

Route::get('/admin/login', function () {
    return view('admin.login');
})->name('admin.login');

Route::post('/admin/login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $email = $request->input('email');
    $password = $request->input('password');

    if ($email === 'admin@demo.com' && $password === 'admin123') {
        session([
            'admin_logged_in' => true,
            'admin_name' => 'Admin',
            'admin_email' => $email,
        ]);
        return redirect('/admin');
    }

    return back()->withErrors([
        'credentials' => 'Invalid email or password.',
    ])->withInput();
})->name('admin.login.submit');

Route::get('/admin/logout', function () {
    session()->forget(['admin_logged_in', 'admin_name', 'admin_email']);
    return redirect()->route('admin.login');
})->name('admin.logout');

Route::get('/admin', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }
    $orders = loadOrders();

    $ordersCollection = collect($orders);
    $distinctSubjects = $ordersCollection
        ->map(fn ($order) => trim((string) ($order['subject'] ?? '')))
        ->filter()
        ->map(fn ($subject) => strtolower($subject))
        ->unique()
        ->count();
    $distinctClients = $ordersCollection
        ->map(fn ($order) => trim((string) ($order['customer_email'] ?? '')))
        ->filter()
        ->map(fn ($email) => strtolower($email))
        ->unique()
        ->count();
    $distinctWriters = $ordersCollection
        ->map(fn ($order) => trim((string) ($order['writer_name'] ?? '')))
        ->filter()
        ->map(fn ($writer) => strtolower($writer))
        ->unique()
        ->count();

    return view('admin.dashboard', [
        'orders' => $orders,
        'navCounts' => [
            'orders' => $ordersCollection->count(),
            'courses' => $distinctSubjects,
            'clients' => $distinctClients,
            'writers' => $distinctWriters,
        ],
    ]);
})->name('admin.dashboard');

Route::get('/admin/orders', function (\Illuminate\Http\Request $request) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }
    $status = $request->query('status');
    $orders = collect(loadOrders());
    $allOrders = $orders;
    if ($status) {
        $orders = $orders->where('status', $status);
    }
    $orders = $orders->values()->all();
    $registeredWriters = collect(loadWriters())
        ->map(function ($writer) {
            $name = trim((string) ($writer['name'] ?? ''));
            if ($name === '') {
                return null;
            }

            return [
                'id' => is_numeric($writer['id'] ?? null) ? (int) $writer['id'] : null,
                'name' => $name,
            ];
        })
        ->filter()
        ->values();

    $maxWriterId = (int) ($registeredWriters->max('id') ?? 0);
    $registeredByKey = $registeredWriters->keyBy(
        fn ($writer) => strtolower(trim((string) $writer['name']))
    );

    $historicalNames = $allOrders
        ->map(fn ($order) => trim((string) ($order['writer_name'] ?? '')))
        ->filter()
        ->groupBy(fn ($name) => strtolower($name))
        ->values()
        ->map(fn ($rows) => $rows->first());

    foreach ($historicalNames as $historicalName) {
        $key = strtolower(trim((string) $historicalName));
        if ($key === '' || $registeredByKey->has($key)) {
            continue;
        }
        $maxWriterId++;
        $registeredWriters->push([
            'id' => $maxWriterId,
            'name' => $historicalName,
        ]);
        $registeredByKey->put($key, true);
    }

    $writers = $registeredWriters
        ->values()
        ->all();
    return view('admin.orders', [
        'orders' => $orders,
        'status' => $status,
        'writers' => $writers,
    ]);
})->name('admin.orders');

Route::post('/admin/orders/{id}/assign', function (\Illuminate\Http\Request $request, $id) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }
    $data = $request->validate([
        'writer_id' => 'required',
        'writer_name' => 'required|string|max:255',
        'status' => 'nullable|string|max:50',
    ]);
    $orders = loadOrders();
    foreach ($orders as &$order) {
        if ($order['id'] === (int)$id) {
            $order['writer_id'] = $data['writer_id'];
            $order['writer_name'] = $data['writer_name'];
            $order['status'] = $data['status'] ?? 'assigned';
            break;
        }
    }
    saveOrders($orders);
    return back()->with('assigned', 'Order assigned successfully.');
})->name('admin.orders.assign');

Route::delete('/admin/orders/{id}', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $targetId = (int) $id;
    $orders = loadOrders();
    $exists = collect($orders)->contains(
        fn ($order) => (int) ($order['id'] ?? 0) === $targetId
    );

    if (!$exists) {
        return back()->with('deleted', 'Order not found.');
    }

    $remainingOrders = collect($orders)
        ->reject(fn ($order) => (int) ($order['id'] ?? 0) === $targetId)
        ->values()
        ->all();

    saveOrders($remainingOrders);

    $storedFiles = collect(session('order_files', []));
    $filesToDelete = $storedFiles->filter(
        fn ($file) => (int) ($file['order_id'] ?? 0) === $targetId
    );
    $keptFiles = $storedFiles->reject(
        fn ($file) => (int) ($file['order_id'] ?? 0) === $targetId
    )->values()->all();

    foreach ($filesToDelete as $file) {
        $name = basename((string) ($file['path'] ?? ''));
        if ($name === '') {
            continue;
        }
        $fullPath = storage_path('app/uploads/' . $name);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    session(['order_files' => $keptFiles]);

    return back()->with('deleted', 'Order deleted successfully.');
})->name('admin.orders.delete');

Route::get('/admin/orders/{id}', function ($id) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }
    $orders = loadOrders();
    $order = collect($orders)->firstWhere('id', (int)$id);
    if (!$order) {
        return redirect()->route('admin.orders');
    }
    $files = collect(session('order_files', []))->where('order_id', (int)$id)->values()->all();
    return view('admin.order-show', ['order' => $order, 'files' => $files]);
})->name('admin.order.show');

Route::get('/admin/courses', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $orders = collect(loadOrders());
    $courses = $orders
        ->map(function ($order) {
            return [
                'subject' => trim((string) ($order['subject'] ?? '')),
                'writer' => trim((string) ($order['writer_name'] ?? '')),
            ];
        })
        ->filter(fn ($row) => $row['subject'] !== '')
        ->groupBy(fn ($row) => strtolower($row['subject']))
        ->values()
        ->map(function ($rows, $index) {
            $subject = $rows->first()['subject'];
            $activeWriters = collect($rows)
                ->map(fn ($row) => strtolower($row['writer']))
                ->filter()
                ->unique()
                ->count();

            return [
                'id' => $index + 1,
                'name' => $subject,
                'active_writers' => $activeWriters,
            ];
        })
        ->all();

    return view('admin.courses', ['courses' => $courses]);
})->name('admin.courses');

Route::get('/admin/clients', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $orders = loadOrders();
    $clients = collect($orders)
        ->groupBy(fn ($order) => trim((string) ($order['customer_email'] ?? '')))
        ->filter(fn ($rows, $email) => $email !== '')
        ->map(function ($rows, $email) {
            $first = $rows->first();
            return [
                'name' => $first['customer_name'] ?? (strstr((string) $email, '@', true) ?: 'Client'),
                'email' => $email,
                'orders' => $rows->count(),
                'spent' => round($rows->sum(fn ($row) => (float) ($row['cost'] ?? 0)), 2),
            ];
        })
        ->values()
        ->all();

    return view('admin.clients', ['clients' => $clients]);
})->name('admin.clients');

Route::get('/admin/writers', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $orders = loadOrders();
    $registeredWriters = collect(loadWriters())
        ->map(function ($writer) {
            $name = trim((string) ($writer['name'] ?? ''));
            if ($name === '') {
                return null;
            }

            return [
                'id' => is_numeric($writer['id'] ?? null) ? (int) $writer['id'] : null,
                'name' => $name,
            ];
        })
        ->filter()
        ->values();

    $maxWriterId = (int) ($registeredWriters->max('id') ?? 0);
    $registeredByKey = $registeredWriters->keyBy(
        fn ($writer) => strtolower(trim((string) ($writer['name'] ?? '')))
    );

    $legacyWriterNames = collect($orders)
        ->map(fn ($order) => trim((string) ($order['writer_name'] ?? '')))
        ->filter()
        ->groupBy(fn ($name) => strtolower($name))
        ->values()
        ->map(fn ($rows) => $rows->first());

    foreach ($legacyWriterNames as $legacyName) {
        $key = strtolower(trim((string) $legacyName));
        if ($key === '' || $registeredByKey->has($key)) {
            continue;
        }
        $maxWriterId++;
        $registeredWriters->push([
            'id' => $maxWriterId,
            'name' => $legacyName,
        ]);
        $registeredByKey->put($key, true);
    }

    $writers = $registeredWriters->values()->map(function ($writer) use ($orders) {
        $writerName = $writer['name'];
        $assigned = collect($orders)->filter(
            fn ($order) => strcasecmp(trim((string) ($order['writer_name'] ?? '')), $writerName) === 0
        );

        return [
            'id' => $writer['id'],
            'name' => $writerName,
            'orders' => $assigned->count(),
            'status' => $assigned->isEmpty() ? 'Available' : 'Active',
        ];
    })->all();

    return view('admin.writers', ['writers' => $writers]);
})->name('admin.writers');

Route::get('/admin/settings', function () {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    return view('admin.settings', [
        'levels' => pricingLevels(),
        'deadlines' => pricingDeadlines(),
        'pricing' => loadPricing(),
    ]);
})->name('admin.settings');

Route::post('/admin/settings', function (\Illuminate\Http\Request $request) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }

    $levels = pricingLevels();
    $deadlines = pricingDeadlines();
    $current = loadPricing();
    $submitted = $request->input('prices', []);
    $updated = [];

    foreach ($levels as $level) {
        foreach ($deadlines as $deadline) {
            $raw = $submitted[$level][$deadline] ?? $current[$level][$deadline] ?? null;

            if (!is_numeric($raw)) {
                return back()->withErrors([
                    'prices' => "Invalid price for {$level} / {$deadline}.",
                ])->withInput();
            }

            $updated[$level][$deadline] = round(max(0, (float) $raw), 2);
        }
    }

    savePricing($updated);

    return back()->with('settings_saved', 'Pricing settings updated successfully.');
})->name('admin.settings.update');
