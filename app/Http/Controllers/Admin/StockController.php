<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockItem;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the stock items.
     */
    public function index(Request $request)
    {
        $query = StockItem::query();

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'low') {
                $query->whereColumn('quantity', '<=', 'minimum_quantity');
            } elseif ($status === 'safe') {
                $query->whereColumn('quantity', '>', 'minimum_quantity');
            }
        }

        $stocks = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.stocks.index', compact('stocks'));
    }

    /**
     * Show the form for creating a new stock item.
     */
    public function create()
    {
        return view('admin.stocks.create');
    }

    /**
     * Store a newly created stock item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'minimum_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
        ]);

        StockItem::create($validated);

        return redirect()->route('admin.stocks.index')->with('success', __('Stok barang berhasil ditambahkan.'));
    }

    /**
     * Show the form for editing the specified stock item.
     */
    public function edit(StockItem $stock)
    {
        return view('admin.stocks.edit', compact('stock'));
    }

    /**
     * Update the specified stock item in storage.
     */
    public function update(Request $request, StockItem $stock)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'minimum_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
        ]);

        $stock->update($validated);

        return redirect()->route('admin.stocks.index')->with('success', __('Stok barang berhasil diperbarui.'));
    }

    /**
     * Remove the specified stock item from storage.
     */
    public function destroy(StockItem $stock)
    {
        $stock->delete();

        return redirect()->route('admin.stocks.index')->with('success', __('Stok barang berhasil dihapus.'));
    }
}
