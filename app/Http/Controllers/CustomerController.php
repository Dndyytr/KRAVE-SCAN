<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\AIImageSearchLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Services\AiImageRecognitionService;
use App\Services\BranchContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display the menu browsing page.
     */
    public function menu(Request $request, $branch_code, $table_number)
    {
        // Save table number to session
        session(['table_number' => $table_number]);

        $branch = app(BranchContext::class)->getBranch();

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
        $request->validate([
            'menu_id' => 'required|exists:menus,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $menuId = $request->input('menu_id');
        $quantity = $request->input('quantity', 1);

        $menu = Menu::where('is_active', true)->findOrFail($menuId);

        $cart = session()->get('cart', []);

        if (isset($cart[$menuId])) {
            $cart[$menuId]['quantity'] += $quantity;
        } else {
            $cart[$menuId] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => (float) $menu->price,
                'quantity' => $quantity,
                'image_path' => $menu->image_path,
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
            'menu_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $menuId = $request->input('menu_id');
        $quantity = $request->input('quantity');

        $cart = session()->get('cart', []);

        if (isset($cart[$menuId])) {
            if ($quantity <= 0) {
                unset($cart[$menuId]);
                $itemSubtotal = 0;
            } else {
                $cart[$menuId]['quantity'] = $quantity;
                $itemSubtotal = $cart[$menuId]['price'] * $quantity;
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

        DB::beginTransaction();

        try {
            // Note: ScopedToBranch trait will auto-set branch_id during creation
            $order = Order::create([
                'table_number' => $table,
                'status' => 'pending',
                'total_amount' => 0, // Placeholder, updated below
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
                ]);
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            event(new OrderCreated($order));

            // Save order ID to session for status page link in layout navigation
            session(['latest_order_id' => $order->id]);

            // Clear the session cart
            session()->forget('cart');

            return redirect()->route('customer.order.status', [
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
    public function orderStatus(Request $request, $branch_code, Order $order)
    {
        $branch = app(BranchContext::class)->getBranch();

        // Ensure the order belongs to the resolved branch context
        if ($branch && $order->branch_id !== $branch->id) {
            abort(404, __('Order not found in this branch.'));
        }

        // Eager load items and menus
        $order->load('orderItems.menu');

        return view('customers.status', [
            'branch_code' => $branch_code,
            'branch' => $branch ? $branch->name : strtoupper($branch_code),
            'order' => $order,
        ]);
    }

    /**
     * Identify a menu item from an uploaded image.
     */
    public function identifyMenu(Request $request, $branch_code, AiImageRecognitionService $aiService)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('Validasi gagal.'),
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

                    return response()->json([
                        'success' => true,
                        'menu_id' => $menu->id,
                        'menu_name' => $menu->name,
                        'confidence' => $result['confidence'],
                        'image_url' => asset('storage/'.$path),
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
}
