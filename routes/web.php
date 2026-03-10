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
            return collect($data)
                ->filter(function ($order) {
                    if (!is_array($order)) {
                        return false;
                    }

                    return trim((string) ($order['customer_email'] ?? '')) !== '';
                })
                ->values()
                ->all();
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
    $writers = $allOrders
        ->map(fn ($order) => trim((string) ($order['writer_name'] ?? '')))
        ->filter()
        ->groupBy(fn ($name) => strtolower($name))
        ->values()
        ->map(function ($rows, $index) {
            return [
                'id' => $index + 1,
                'name' => $rows->first(),
            ];
        })
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
    $writers = collect($orders)
        ->map(fn ($order) => trim((string) ($order['writer_name'] ?? '')))
        ->filter()
        ->groupBy(fn ($name) => strtolower($name))
        ->values()
        ->map(function ($rows, $index) use ($orders) {
        $writerName = $rows->first();
        $assigned = collect($orders)->filter(
            fn ($order) => strcasecmp(trim((string) ($order['writer_name'] ?? '')), $writerName) === 0
        );
        return [
            'id' => $index + 1,
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
