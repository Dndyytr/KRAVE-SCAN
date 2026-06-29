<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Category;
use App\Services\BranchContext;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('menus');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $branchId = app(BranchContext::class)->getBranchId() ?: Branch::first()?->id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                }),
            ],
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', __('Kategori berhasil ditambahkan.'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $branchId = app(BranchContext::class)->getBranchId() ?: Branch::first()?->id;

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })->ignore($category->id),
            ],
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', __('Kategori berhasil diperbarui.'));
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->menus()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', __('Kategori tidak dapat dihapus karena masih memiliki menu masakan yang terhubung.'));
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', __('Kategori berhasil dihapus.'));
    }
}
