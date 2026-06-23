<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query()->with(['role', 'branch']);

        // Apply search filter (name or email)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply branch filter (only relevant for Super Admin since Branch Admin is scoped)
        if (auth()->user()->branch_id === null && $request->filled('branch_id')) {
            $query->where('branch_id', $request->input('branch_id'));
        }

        // Apply role filter
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->input('role_id'));
        }

        // Apply status filter (active/inactive)
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $users = $query->orderBy('name')->paginate(10)->withQueryString();
        $branches = auth()->user()->branch_id === null ? Branch::orderBy('name')->get() : collect();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'branches', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $branches = auth()->user()->branch_id === null ? Branch::orderBy('name')->get() : collect();

        return view('admin.users.create', compact('roles', 'branches'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,id',
        ];

        // Only Super Admin can select a branch
        if (auth()->user()->branch_id === null) {
            $rules['branch_id'] = 'nullable|exists:branches,id';
        }

        $validated = $request->validate($rules);

        // For Branch Admin, enforce their own branch
        if (auth()->user()->branch_id !== null) {
            $validated['branch_id'] = auth()->user()->branch_id;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true; // active by default

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', __('Staf berhasil ditambahkan.'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Safety check (in case global scope is bypassed or not working)
        if (auth()->user()->branch_id !== null && $user->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke staf cabang lain.');
        }

        $roles = Role::orderBy('name')->get();
        $branches = auth()->user()->branch_id === null ? Branch::orderBy('name')->get() : collect();

        return view('admin.users.edit', compact('user', 'roles', 'branches'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // Safety check
        if (auth()->user()->branch_id !== null && $user->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke staf cabang lain.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ];

        // Logged in user editing themselves cannot change their own role or branch or status
        $isEditingSelf = $user->id === auth()->id();

        if (! $isEditingSelf) {
            $rules['role_id'] = 'required|exists:roles,id';
            if (auth()->user()->branch_id === null) {
                $rules['branch_id'] = 'nullable|exists:branches,id';
            }
        }

        $validated = $request->validate($rules);

        // For Branch Admin, lock the branch
        if (auth()->user()->branch_id !== null && ! $isEditingSelf) {
            $validated['branch_id'] = auth()->user()->branch_id;
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', __('Profil staf berhasil diperbarui.'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        // Safety check
        if (auth()->user()->branch_id !== null && $user->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke staf cabang lain.');
        }

        // Prevent self deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', __('Anda tidak dapat menghapus akun Anda sendiri.'));
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', __('Staf berhasil dihapus.'));
    }

    /**
     * Toggle the active status of the user (suspend/unsuspend).
     */
    public function toggleActive(User $user)
    {
        // Safety check
        if (auth()->user()->branch_id !== null && $user->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Anda tidak memiliki akses ke staf cabang lain.');
        }

        // Prevent self suspend
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', __('Anda tidak dapat menonaktifkan akun Anda sendiri.'));
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusMessage = $user->is_active ? __('Akun staf berhasil diaktifkan.') : __('Akun staf berhasil dinonaktifkan.');

        return redirect()->route('admin.users.index')->with('success', $statusMessage);
    }
}
