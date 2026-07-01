<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Events\OrderPaid;
use App\Models\AIImageSearchLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StockItem;
use App\Services\AiImageRecognitionService;
use App\Services\BranchContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display the welcome/landing page when scanning QR code.
     */
    public function welcome(Request $request, $branch_code, $table_number)
    {
        // Save table number to session
        session(['table_number' => $table_number]);

        $branch = app(BranchContext::class)->getBranch();
        $branchName = $branch ? $branch->name : strtoupper($branch_code);

        return view('customers.welcome', [
            'branch_code' => $branch_code,
            'branch' => $branchName,
            'table' => $table_number,
        ]);
    }

    /**
     * Display the menu browsing page.
     */
    public function menu(Request $request, $branch_code, $table_number)
    {
        // Save table number to session
        session(['table_number' => $table_number]);

        $branch = app(BranchContext::class)->getBranch();

        // Prevent ordering if there is an active order
        if ($branch) {
            $activeOrder = Order::where('branch_id', $branch->id)
                ->where('table_number', $table_number)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->first();

            if ($activeOrder) {
                $msg = $activeOrder->status === 'pending'
                    ? __('Anda memiliki pesanan yang belum dibayar. Selesaikan pembayaran terlebih dahulu sebelum memesan menu lain.')
                    : __('Anda memiliki pesanan yang sedang diproses. Tunggu sampai pesanan selesai dihidangkan sebelum memesan menu lain.');

                $targetRoute = $activeOrder->status === 'pending' ? 'customer.payment' : 'customer.order.status';

                return redirect()->route($targetRoute, [
                    'branch_code' => $branch_code,
                    'order' => $activeOrder->id,
                ])->with('error', $msg);
            }
        }

        $branch = app(BranchContext::class)->getBranch();

        // Restore active order tracking from DB if present for this table/branch
        if ($branch) {
            $activeOrder = Order::where('branch_id', $branch->id)
                ->where('table_number', $table_number)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->first();

            if ($activeOrder) {
                session(['latest_order_id' => $activeOrder->id]);
            } else {
                session()->forget('latest_order_id');
            }
        }

        // Fetch active categories and active menus
        $categories = Category::all();
        $menus = Menu::where('is_active', true)->with('category')->get();

        // Get initial cart count
        $cart = session()->get('cart', []);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        return view('customers.menu', [
            'branch_code' => $branch_code,
            'branch' => $branch ? $branch->name : strtoupper($branch_code),
            'table' => $table_number,
            'categories' => $categories,
            'menus' => $menus,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * Add an item to the session-based cart.
     */
    public function addToCart(Request $request, $branch_code)
    {
        $branch = app(BranchContext::class)->getBranch();
        $table = session('table_number');

        if ($branch && $table) {
            $activeOrder = Order::where('branch_id', $branch->id)
                ->where('table_number', $table)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->first();

            if ($activeOrder) {
                $msg = $activeOrder->status === 'pending'
                    ? __('Anda memiliki pesanan yang belum dibayar. Selesaikan pembayaran terlebih dahulu sebelum memesan menu lain.')
                    : __('Anda memiliki pesanan yang sedang diproses. Tunggu sampai pesanan selesai dihidangkan sebelum memesan menu lain.');

                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 403);
            }
        }

        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        $menuId = $request->input('menu_id');
        $quantity = $request->input('quantity', 1);
        $note = $request->input('note', '');

        $menu = Menu::where('is_active', true)->findOrFail($menuId);

        $cart = session()->get('cart', []);

        // Unique cart key based on menu ID and note hash
        $cartKey = $menuId.($note !== '' ? '_'.md5($note) : '');

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'cart_key' => $cartKey,
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => (float) $menu->price,
                'quantity' => $quantity,
                'image_path' => $menu->image_path,
                'note' => $note,
            ];
        }

        session()->put('cart', $cart);
        $cartCount = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true,
            'message' => __('Menu berhasil ditambahkan ke keranjang.'),
            'cart_count' => $cartCount,
        ]);
    }

    /**
     * Display the cart page.
     */
    public function cart(Request $request, $branch_code)
    {
        $branch = app(BranchContext::class)->getBranch();
        $table = session('table_number');

        if ($branch && $table) {
            $activeOrder = Order::where('branch_id', $branch->id)
                ->where('table_number', $table)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->first();

            if ($activeOrder) {
                $msg = $activeOrder->status === 'pending'
                    ? __('Anda memiliki pesanan yang belum dibayar. Selesaikan pembayaran terlebih dahulu sebelum memesan menu lain.')
                    : __('Anda memiliki pesanan yang sedang diproses. Tunggu sampai pesanan selesai dihidangkan sebelum memesan menu lain.');

                $targetRoute = $activeOrder->status === 'pending' ? 'customer.payment' : 'customer.order.status';

                return redirect()->route($targetRoute, [
                    'branch_code' => $branch_code,
                    'order' => $activeOrder->id,
                ])->with('error', $msg);
            }
        }

        $cart = session()->get('cart', []);

        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }

        $cartCount = array_sum(array_column($cart, 'quantity'));

        return view('customers.cart', [
            'branch_code' => $branch_code,
            'branch' => $branch ? $branch->name : strtoupper($branch_code),
            'table' => $table,
            'cart' => $cart,
            'cartTotal' => $cartTotal,
            'cartCount' => $cartCount,
        ]);
    }

    /**
     * Update cart item quantity or remove item.
     */
    public function updateCart(Request $request, $branch_code)
    {
        $request->validate([
            'cart_key' => 'required_without:menu_id|string',
            'menu_id' => 'required_without:cart_key|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $cartKey = $request->input('cart_key', $request->input('menu_id'));
        $quantity = $request->input('quantity');

        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            if ($quantity <= 0) {
                unset($cart[$cartKey]);
                $itemSubtotal = 0;
            } else {
                $cart[$cartKey]['quantity'] = $quantity;
                $itemSubtotal = $cart[$cartKey]['price'] * $quantity;
            }
            session()->put('cart', $cart);
        } else {
            $itemSubtotal = 0;
        }

        $cartCount = array_sum(array_column($cart, 'quantity'));

        $cartTotal = 0;
        foreach ($cart as $item) {
            $cartTotal += $item['price'] * $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => __('Keranjang berhasil diperbarui.'),
            'cart_count' => $cartCount,
            'item_subtotal' => $itemSubtotal,
            'cart_total' => $cartTotal,
            'cart_empty' => empty($cart),
        ]);
    }

    /**
     * Process checkout and save order.
     */
    public function checkout(Request $request, $branch_code)
    {
        $cart = session()->get('cart', []);
        $table = session('table_number');

        if (empty($cart)) {
            return redirect()->route('customer.cart', ['branch_code' => $branch_code])
                ->with('error', __('Keranjang belanja Anda kosong.'));
        }

        if (! $table) {
            return redirect()->route('customer.cart', ['branch_code' => $branch_code])
                ->with('error', __('Nomor meja tidak ditemukan. Silakan pindai ulang QR Code meja Anda.'));
        }

        $branch = app(BranchContext::class)->getBranch();
        if ($branch) {
            $activeOrder = Order::where('branch_id', $branch->id)
                ->where('table_number', $table)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->first();

            if ($activeOrder) {
                $msg = $activeOrder->status === 'pending'
                    ? __('Anda memiliki pesanan yang belum dibayar. Selesaikan pembayaran terlebih dahulu sebelum memesan menu lain.')
                    : __('Anda memiliki pesanan yang sedang diproses. Tunggu sampai pesanan selesai dihidangkan sebelum memesan menu lain.');

                $targetRoute = $activeOrder->status === 'pending' ? 'customer.payment' : 'customer.order.status';

                return redirect()->route($targetRoute, [
                    'branch_code' => $branch_code,
                    'order' => $activeOrder->id,
                ])->with('error', $msg);
            }
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_contact' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Note: ScopedToBranch trait will auto-set branch_id during creation
            $order = Order::create([
                'table_number' => $table,
                'status' => 'pending',
                'total_amount' => 0, // Placeholder, updated below
                'customer_name' => $request->input('customer_name'),
                'customer_contact' => $request->input('customer_contact'),
            ]);

            $totalAmount = 0;

            foreach ($cart as $item) {
                $menu = Menu::where('is_active', true)->find($item['id']);

                if (! $menu) {
                    // Skip or handle inactive/deleted menu items
                    continue;
                }

                $price = (float) $menu->price;
                $subtotal = $price * $item['quantity'];
                $totalAmount += $subtotal;

                $order->orderItems()->create([
                    'menu_id' => $menu->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal,
                    'note' => $item['note'] ?? null,
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            event(new OrderCreated($order));

            // Save order ID to session for status page link in layout navigation
            session(['latest_order_id' => $order->id]);

            // Clear the session cart
            session()->forget('cart');

            return redirect()->route('customer.payment', [
                'branch_code' => $branch_code,
                'order' => $order->id,
            ])->with('success', __('Pesanan berhasil dibuat!'));

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('customer.cart', ['branch_code' => $branch_code])
                ->with('error', __('Terjadi kesalahan saat memproses pesanan Anda: ').$e->getMessage());
        }
    }

    /**
     * Display order status.
     */
    public function orderStatus(Request $request, $branch_code, ?Order $order = null)
    {
        $branch = app(BranchContext::class)->getBranch();

        // If no order parameter, try to find the latest order from session or active table orders
        if (! $order || ! $order->exists) {
            $latestOrderId = session('latest_order_id');
            if ($latestOrderId) {
                $order = Order::find($latestOrderId);
            }
        }

        // If still no order, let's look for active orders for the table in session
        $tableNumber = session('table_number');
        if ((! $order || ! $order->exists) && $tableNumber && $branch) {
            $order = Order::where('branch_id', $branch->id)
                ->where('table_number', $tableNumber)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->first();
        }

        $activeOrders = collect();

        // If an order is resolved, check branch context and load active orders for the table
        if ($order && $order->exists) {
            if ($branch && $order->branch_id !== $branch->id) {
                abort(404, __('Order not found in this branch.'));
            }

            $activeOrders = Order::where('branch_id', $order->branch_id)
                ->where('table_number', $order->table_number)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->get();

            $order->load('orderItems.menu');
        } else {
            $order = null;
        }

        return view('customers.status', [
            'branch_code' => $branch_code,
            'branch' => $branch ? $branch->name : strtoupper($branch_code),
            'order' => $order,
            'activeOrders' => $activeOrders,
        ]);
    }

    /**
     * Display the camera-only AI menu scanner.
     */
    public function aiScan(Request $request, $branch_code)
    {
        $branch = app(BranchContext::class)->getBranch();
        $table = session('table_number', 1);

        // Prevent scanning if there is an active order
        if ($branch) {
            $activeOrder = Order::where('branch_id', $branch->id)
                ->where('table_number', $table)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->first();

            if ($activeOrder) {
                $msg = $activeOrder->status === 'pending'
                    ? __('Anda memiliki pesanan yang belum dibayar. Selesaikan pembayaran terlebih dahulu sebelum memesan menu lain.')
                    : __('Anda memiliki pesanan yang sedang diproses. Tunggu sampai pesanan selesai dihidangkan sebelum memesan menu lain.');

                $targetRoute = $activeOrder->status === 'pending' ? 'customer.payment' : 'customer.order.status';

                return redirect()->route($targetRoute, [
                    'branch_code' => $branch_code,
                    'order' => $activeOrder->id,
                ])->with('error', $msg);
            }
        }

        return view('customers.ai-scan', [
            'branch_code' => $branch_code,
            'branch' => $branch ? $branch->name : strtoupper($branch_code),
            'table' => $table,
        ]);
    }

    /**
     * Identify a menu item from an uploaded image.
     */
    public function identifyMenu(Request $request, $branch_code, AiImageRecognitionService $aiService)
    {
        $branch = app(BranchContext::class)->getBranch();
        $table = session('table_number');

        if ($branch && $table) {
            $activeOrder = Order::where('branch_id', $branch->id)
                ->where('table_number', $table)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->first();

            if ($activeOrder) {
                $msg = $activeOrder->status === 'pending'
                    ? __('Anda memiliki pesanan yang belum dibayar. Selesaikan pembayaran terlebih dahulu sebelum memesan menu lain.')
                    : __('Anda memiliki pesanan yang sedang diproses. Tunggu sampai pesanan selesai dihidangkan sebelum memesan menu lain.');

                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('Validasi gagal: ').$validator->errors()->first('image'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $path = null;
        try {
            // Store the uploaded image in the public disk under 'ai_searches'
            $path = $request->file('image')->store('ai_searches', 'public');

            // Get original filename
            $filename = $request->file('image')->getClientOriginalName();

            // Run recognition via Python AI microservice
            $result = $aiService->recognize($path, $filename);

            if ($result['success'] && ! empty($result['prediction'])) {
                // Find matching menu in our database (case-insensitive partial match)
                $menu = Menu::where('is_active', true)
                    ->where('name', 'like', '%'.$result['prediction'].'%')
                    ->first();

                if ($menu) {
                    // Log search success in database
                    AIImageSearchLog::create([
                        'image_path' => $path,
                        'matched_menu_id' => $menu->id,
                        'confidence_score' => $result['confidence'],
                    ]);

                    $similarMenus = Menu::where('is_active', true)
                        ->where('category_id', $menu->category_id)
                        ->where('id', '!=', $menu->id)
                        ->limit(4)
                        ->get()
                        ->map(fn (Menu $item) => [
                            'id' => $item->id,
                            'name' => $item->name,
                            'price' => (float) $item->price,
                            'image' => $item->image_path
                                ? (filter_var($item->image_path, FILTER_VALIDATE_URL) ? $item->image_path : asset($item->image_path))
                                : null,
                        ])
                        ->values();

                    return response()->json([
                        'success' => true,
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'confidence' => $result['confidence'],
                        'image_url' => asset('storage/'.$path),
                        'menu' => [
                            'id' => $menu->id,
                            'name' => $menu->name,
                            'description' => $menu->description,
                            'price' => (float) $menu->price,
                            'category' => $menu->category->name,
                            'image' => $menu->image_path
                                ? (filter_var($menu->image_path, FILTER_VALIDATE_URL) ? $menu->image_path : asset($menu->image_path))
                                : null,
                        ],
                        'similar_menus' => $similarMenus,
                    ]);
                }
            }

            // Log search with no match
            AIImageSearchLog::create([
                'image_path' => $path,
                'matched_menu_id' => null,
                'confidence_score' => $result['confidence'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => __('Menu tidak dikenali atau tidak aktif di cabang ini.'),
                'image_url' => asset('storage/'.$path),
            ]);

        } catch (\Exception $e) {
            Log::error('AI Menu Identification controller error: '.$e->getMessage());

            // Still log the attempt if image was stored
            if ($path) {
                AIImageSearchLog::create([
                    'image_path' => $path,
                    'matched_menu_id' => null,
                    'confidence_score' => null,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => __('Layanan AI sedang tidak tersedia. Silakan gunakan pencarian manual.'),
            ], 503);
        }
    }

    /**
     * Display customer payment page.
     */
    public function payment(Request $request, $branch_code, ?Order $order = null)
    {
        $branch = app(BranchContext::class)->getBranch();

        // Resolve order if not provided in URL
        if (! $order || ! $order->exists) {
            $latestOrderId = session('latest_order_id');
            if ($latestOrderId) {
                $order = Order::find($latestOrderId);
            }
        }

        $tableNumber = session('table_number');
        if ((! $order || ! $order->exists) && $tableNumber && $branch) {
            $order = Order::where('branch_id', $branch->id)
                ->where('table_number', $tableNumber)
                ->whereIn('status', ['pending', 'confirmed', 'in_process'])
                ->latest()
                ->first();
        }

        if ($order && $order->exists) {
            if ($branch && $order->branch_id !== $branch->id) {
                abort(404, __('Order not found in this branch.'));
            }
            $order->load('orderItems.menu');
        } else {
            $order = null;
        }

        return view('customers.payment', [
            'branch_code' => $branch_code,
            'branch' => $branch ? $branch->name : strtoupper($branch_code),
            'order' => $order,
        ]);
    }

    /**
     * Process simulated QRIS payment from customer side.
     */
    public function payQris(Request $request, $branch_code, Order $order)
    {
        $branch = app(BranchContext::class)->getBranch();
        if ($branch && $order->branch_id !== $branch->id) {
            abort(404, __('Order not found in this branch.'));
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', __('Pesanan ini sudah dibayar atau diproses.'));
        }

        DB::beginTransaction();

        try {
            // Update order status to confirmed
            $order->update(['status' => 'confirmed']);

            // Deduct stock for each order item
            $order->load('orderItems.menu');
            foreach ($order->orderItems as $item) {
                if ($item->menu && $item->menu->stock_item_id) {
                    StockItem::withoutGlobalScopes()
                        ->where('id', $item->menu->stock_item_id)
                        ->decrement('quantity', $item->quantity);
                }
            }

            // Create success Payment record
            $totalAmount = (float) $order->total_amount;
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $totalAmount,
                'method' => 'qris',
                'status' => 'success',
                'cash_received' => $totalAmount,
                'change' => 0,
            ]);

            // Trigger automation event
            event(new OrderPaid($order, $payment));

            DB::commit();

            return redirect()->route('customer.order.status', [
                'branch_code' => $branch_code,
                'order' => $order->id,
            ])->with('success', __('Pembayaran QRIS mandiri berhasil disimulasikan!'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('QRIS payment error: '.$e->getMessage());

            return redirect()->back()->with('error', __('Gagal memproses pembayaran QRIS: ').$e->getMessage());
        }
    }
}
