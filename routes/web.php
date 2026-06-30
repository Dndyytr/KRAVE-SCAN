<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AutomationController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::redirect('/', '/login');

Route::get('/run-migration', function () {
    try {
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $output = Artisan::output();

        return "<pre>Database migrated and seeded successfully!\n\nCommand Output:\n".e($output).'</pre>';
    } catch (Throwable $e) {
        return "<pre>Error running migrations:\n".e($e->getMessage())."\n\nStack Trace:\n".e($e->getTraceAsString()).'</pre>';
    }
})->withoutMiddleware('web');

Route::get('/test-s3', function () {
    try {
        $disk = Storage::disk('public');
        $driver = config('filesystems.disks.public.driver');

        $testFileName = 'test_connection_'.time().'.txt';
        $disk->put($testFileName, 'KraveScan connection test');

        $exists = $disk->exists($testFileName);
        $url = $disk->url($testFileName);

        if ($exists) {
            $disk->delete($testFileName);
        }

        return response()->json([
            'status' => 'success',
            'driver' => $driver,
            'test_file_written' => $exists,
            'resolved_url' => $url,
            'config' => [
                'driver' => config('filesystems.disks.public.driver'),
                'bucket' => config('filesystems.disks.public.bucket'),
                'region' => config('filesystems.disks.public.region'),
                'endpoint' => config('filesystems.disks.public.endpoint'),
                'use_path_style' => config('filesystems.disks.public.use_path_style_endpoint'),
                'key_configured' => ! empty(config('filesystems.disks.public.key')),
                'secret_configured' => ! empty(config('filesystems.disks.public.secret')),
            ],
        ]);
    } catch (Throwable $e) {
        return response()->json([
            'status' => 'failed',
            'driver' => config('filesystems.disks.public.driver'),
            'error_message' => $e->getMessage(),
            'error_class' => get_class($e),
            'trace' => substr($e->getTraceAsString(), 0, 1000),
            'config' => [
                'driver' => config('filesystems.disks.public.driver'),
                'bucket' => config('filesystems.disks.public.bucket'),
                'region' => config('filesystems.disks.public.region'),
                'endpoint' => config('filesystems.disks.public.endpoint'),
                'use_path_style' => config('filesystems.disks.public.use_path_style_endpoint'),
                'key_configured' => ! empty(config('filesystems.disks.public.key')),
                'secret_configured' => ! empty(config('filesystems.disks.public.secret')),
            ],
        ], 500);
    }
})->withoutMiddleware('web');

// Locale Switcher
Route::get('/locale/{lang}', function (string $lang) {
    if (in_array($lang, ['id', 'en'])) {
        session()->put('locale', $lang);
    }

    return redirect()->back();
})->name('locale.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'branch.staff'])
    ->name('dashboard');

Route::middleware(['auth', 'branch.staff'])->group(function () {
    // Notifications API
    Route::get('/api/notifications', [NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/api/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-as-read');
    Route::post('/api/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-as-read');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admins Group
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::post('/switch-branch', [DashboardController::class, 'switchBranch'])->name('admin.switch-branch');
        Route::patch('/menus/{menu}/toggle-active', [MenuController::class, 'toggleActive'])->name('admin.menus.toggle-active');
        Route::resource('menus', MenuController::class)->names('admin.menus');
        Route::resource('categories', CategoryController::class)->names('admin.categories');

        Route::resource('stocks', StockController::class)->names('admin.stocks');
        Route::resource('orders', OrderController::class)->only(['index', 'show'])->names('admin.orders');
        Route::resource('transactions', TransactionController::class)->only(['index'])->names('admin.transactions');

        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');
        Route::resource('users', UserController::class)->names('admin.users');

        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('admin.reports.sales');
        Route::get('/reports/menus', [ReportController::class, 'menus'])->name('admin.reports.menus');
        Route::get('/reports/payments', [ReportController::class, 'payments'])->name('admin.reports.payments');
        Route::get('/automations', [AutomationController::class, 'index'])->name('admin.automations.index');
        Route::get('/automations/rules', [AutomationController::class, 'indexRules'])->name('admin.automations.rules.index');
        Route::get('/automations/rules/create', [AutomationController::class, 'createRule'])->name('admin.automations.rules.create');
        Route::post('/automations/rules', [AutomationController::class, 'storeRule'])->name('admin.automations.rules.store');
        Route::get('/automations/rules/{rule}/edit', [AutomationController::class, 'editRule'])->name('admin.automations.rules.edit');
        Route::put('/automations/rules/{rule}', [AutomationController::class, 'updateRule'])->name('admin.automations.rules.update');
        Route::delete('/automations/rules/{rule}', [AutomationController::class, 'destroyRule'])->name('admin.automations.rules.destroy');
        Route::patch('/automations/rules/{rule}/toggle', [AutomationController::class, 'toggleRule'])->name('admin.automations.rules.toggle');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity-logs.index');
    });

    // Cashiers Group
    Route::prefix('cashier')->middleware('role:cashier')->group(function () {
        Route::get('/orders', [CashierController::class, 'orders'])->name('cashier.orders');
        Route::get('/orders/{order}', [CashierController::class, 'showOrder'])->name('cashier.orders.show');
        Route::patch('/orders/{order}/status', [CashierController::class, 'updateStatus'])->name('cashier.orders.update-status');
        Route::post('/orders/{order}/payment', [CashierController::class, 'processPayment'])->name('cashier.orders.payment');
        Route::get('/receipts/{receipt}', [CashierController::class, 'showReceipt'])->name('cashier.receipts.show');
    });
});

// Customers Group (Tanpa Autentikasi / Sesi Meja)
Route::prefix('c/{branch_code}')->middleware('branch.customer')->group(function () {
    Route::get('/', function ($branch_code) {
        $tableNumber = session('table_number', 1);

        return redirect()->route('customer.menu', ['branch_code' => $branch_code, 'table_number' => $tableNumber]);
    });

    Route::get('/table/{table_number}', [CustomerController::class, 'menu'])->name('customer.menu');
    Route::post('/menu/identify', [CustomerController::class, 'identifyMenu'])->name('customer.menu.identify');
    Route::get('/cart', [CustomerController::class, 'cart'])->name('customer.cart');
    Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('customer.cart.add');
    Route::post('/cart/update', [CustomerController::class, 'updateCart'])->name('customer.cart.update');
    Route::post('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::get('/order/{order}', [CustomerController::class, 'orderStatus'])->name('customer.order.status');
});

require __DIR__.'/auth.php';
