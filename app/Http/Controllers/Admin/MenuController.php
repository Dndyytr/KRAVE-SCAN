<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\StockItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the menus.
     */
    public function index(Request $request)
    {
        $query = Menu::with('category');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $menus = $query->orderBy('category_id')->orderBy('name')->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.menus.index', compact('menus', 'categories'));
    }

    /**
     * Show the form for creating a new menu.
     */
    public function create()
    {
        $categories = Category::all();
        $stocks = StockItem::orderBy('name')->get();

        return view('admin.menus.create', compact('categories', 'stocks'));
    }

    /**
     * Store a newly created menu in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock_item_id' => 'nullable|exists:stock_items,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $validated;
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $data['image_path'] = 'storage/'.$path;
        }

        Menu::create($data);

        return redirect()->route('admin.menus.index')->with('success', __('Menu berhasil ditambahkan.'));
    }

    /**
     * Show the form for editing the specified menu.
     */
    public function edit(Menu $menu)
    {
        $categories = Category::all();
        $stocks = StockItem::orderBy('name')->get();

        return view('admin.menus.edit', compact('menu', 'categories', 'stocks'));
    }

    /**
     * Update the specified menu in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'stock_item_id' => 'nullable|exists:stock_items,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $validated;
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menu->image_path) {
                $relativeOldPath = str_replace('storage/', '', $menu->image_path);
                Storage::disk('public')->delete($relativeOldPath);
            }

            $path = $request->file('image')->store('menus', 'public');
            $data['image_path'] = 'storage/'.$path;
        }

        $menu->update($data);

        return redirect()->route('admin.menus.index')->with('success', __('Menu berhasil diperbarui.'));
    }

    /**
     * Remove the specified menu from storage.
     */
    public function destroy(Menu $menu)
    {
        if ($menu->image_path) {
            $relativeOldPath = str_replace('storage/', '', $menu->image_path);
            Storage::disk('public')->delete($relativeOldPath);
        }

        $menu->delete();

        return redirect()->route('admin.menus.index')->with('success', __('Menu berhasil dihapus.'));
    }

    /**
     * Toggle the active status of the specified menu.
     */
    public function toggleActive(Request $request, Menu $menu)
    {
        $menu->is_active = ! $menu->is_active;
        $menu->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'is_active' => $menu->is_active,
                'message' => __('Status menu berhasil diperbarui.'),
            ]);
        }

        return redirect()->route('admin.menus.index')->with('success', __('Status menu berhasil diperbarui.'));
    }
}
