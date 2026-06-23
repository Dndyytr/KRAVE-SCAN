<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admins Group
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/menus', function () {
            return 'Daftar Menu Admin (Stub)';
        })->name('admin.menus');

        Route::get('/stocks', function () {
            return 'Stok Barang Admin (Stub)';
        })->name('admin.stocks');

        Route::get('/users', function () {
            return 'Kelola Pengguna/Cabang (Stub)';
        })->name('admin.users');

        Route::get('/reports', function () {
            return 'Laporan Penjualan (Stub)';
        })->name('admin.reports');
    });

    // Cashiers Group
    Route::prefix('cashier')->middleware('role:cashier')->group(function () {
        Route::get('/orders', [CashierController::class, 'orders'])->name('cashier.orders');
        Route::get('/orders/{order}', [CashierController::class, 'showOrder'])->name('cashier.orders.show');
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
    Route::get('/cart', [CustomerController::class, 'cart'])->name('customer.cart');
    Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('customer.cart.add');
    Route::post('/cart/update', [CustomerController::class, 'updateCart'])->name('customer.cart.update');
    Route::post('/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::get('/order/{order}', [CustomerController::class, 'orderStatus'])->name('customer.order.status');
});

require __DIR__.'/auth.php';
