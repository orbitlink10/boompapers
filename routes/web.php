<?php

use Illuminate\Support\Facades\Route;

if (!function_exists('loadOrders')) {
    function loadOrders(): array
    {
        $file = storage_path('app/orders.json');
        if (file_exists($file)) {
            $json = file_get_contents($file);
            $data = json_decode($json, true);
            return is_array($data) ? $data : [];
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
    return view('order-form');
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
    if (empty($orders) && session()->has('orders')) {
        $orders = session('orders');
        saveOrders($orders);
    }
    $orders = collect($orders)->where('customer_email', session('customer_email'))->values()->all();
    return view('customer.dashboard', ['orders' => $orders]);
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
    ]);

    $orders = loadOrders();
    $id = (collect($orders)->max('id') ?? 802) + 1;
    $pages = $data['pages'] ?? 1;
    $pricePerPage = 21.6;
    $orders[] = [
        'id' => $id,
        'title' => $data['title'] ?? 'Untitled Paper',
        'pages' => $pages,
        'cost' => round($pages * $pricePerPage, 2),
        'status' => 'pending',
        'deadline' => $data['deadline'] ?? '48 Hours',
        'level' => $data['level'] ?? 'College',
        'type' => $data['type'] ?? 'Essay',
        'format' => $data['format'] ?? 'APA',
        'spacing' => $data['spacing'] ?? 'Double',
        'category' => $data['category'] ?? 'Standard',
        'subject' => $data['subject'] ?? 'Other',
        'sources' => $data['sources'] ?? 0,
        'slides' => $data['slides'] ?? 0,
        'charts' => $data['charts'] ?? 0,
        'customer_email' => session('customer_email', 'customer'),
        'customer_name' => session('customer_name', 'Customer'),
    ];
    saveOrders($orders);
    session(['orders' => $orders]); // keep for customer view convenience

    return redirect()->route('customer.dashboard');
})->name('order.submit');

Route::get('/customer/orders/{id}', function ($id) {
    if (!session('customer_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }
    $orders = loadOrders();
    $order = collect($orders)->where('customer_email', session('customer_email'))->firstWhere('id', (int)$id);
    if (!$order) {
        return redirect()->route('customer.dashboard');
    }
    $files = collect(session('order_files', []))->where('order_id', (int)$id)->values()->all();
    return view('customer.order', ['order' => $order, 'files' => $files]);
})->name('customer.order.show');

Route::post('/customer/orders/{id}/files', function (\Illuminate\Http\Request $request, $id) {
    if (!session('customer_logged_in')) {
        return redirect()->route('order', ['tab' => 'existing']);
    }
    $orders = loadOrders();
    $order = collect($orders)->where('customer_email', session('customer_email'))->firstWhere('id', (int)$id);
    if (!$order) {
        return redirect()->route('customer.dashboard');
    }

    $request->validate([
        'files.*' => 'file|max:5120', // 5MB each for demo
    ]);

    $stored = session('order_files', []);
    $dir = storage_path('app/uploads');
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    foreach ($request->file('files', []) as $file) {
        $name = time() . '_' . $file->getClientOriginalName();
        $file->move($dir, $name);
        $stored[] = [
            'order_id' => (int)$id,
            'name' => $file->getClientOriginalName(),
            'path' => $name,
            'date' => now()->toDateTimeString(),
        ];
    }
    session(['order_files' => $stored]);

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
    if (empty($orders) && session()->has('orders')) {
        $orders = session('orders');
        saveOrders($orders);
    }
    return view('admin.dashboard', ['orders' => $orders]);
})->name('admin.dashboard');

Route::get('/admin/orders', function (\Illuminate\Http\Request $request) {
    if (!session('admin_logged_in')) {
        return redirect()->route('admin.login');
    }
    $status = $request->query('status');
    $orders = loadOrders();
    if (empty($orders) && session()->has('orders')) {
        $orders = session('orders');
        saveOrders($orders);
    }
    $orders = collect($orders);
    if ($status) {
        $orders = $orders->where('status', $status);
    }
    $orders = $orders->values()->all();
    $writers = [
        ['id' => 1, 'name' => 'Alice Writer'],
        ['id' => 2, 'name' => 'Brian Smith'],
        ['id' => 3, 'name' => 'Carol Johnson'],
    ];
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
    session(['orders' => $orders]); // keep session copy in sync
    return back()->with('assigned', 'Order assigned successfully.');
})->name('admin.orders.assign');

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
