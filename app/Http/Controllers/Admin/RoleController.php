<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => strtolower($request->input('name')),
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->input('permissions'));
        }

        return redirect()->route('admin.roles.index')->with('success', __('Peran berhasil dibuat.'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $isDefaultRole = in_array($role->name, ['admin', 'cashier', 'kitchen']);

        $rules = [
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        if (! $isDefaultRole) {
            $rules['name'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($role->id),
            ];
        }

        $request->validate($rules);

        $name = $isDefaultRole ? $role->name : strtolower($request->input('name'));

        $role->update([
            'name' => $name,
        ]);

        $role->permissions()->sync($request->input('permissions', []));

        return redirect()->route('admin.roles.index')->with('success', __('Peran berhasil diperbarui.'));
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        if (in_array($role->name, ['admin', 'cashier', 'kitchen'])) {
            return redirect()->route('admin.roles.index')->with('error', __('Peran bawaan sistem tidak dapat dihapus.'));
        }

        // Check if role has active users
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')->with('error', __('Peran tidak dapat dihapus karena masih digunakan oleh pengguna aktif.'));
        }

        $role->delete();

        return redirect()->route('admin.roles.index')->with('success', __('Peran berhasil dihapus.'));
    }
}
