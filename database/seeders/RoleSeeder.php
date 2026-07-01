<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Permissions (idempotently)
        $permissions = [
            'access_admin' => 'Akses Halaman Admin',
            'access_cashier' => 'Akses Halaman Kasir',
            'access_kitchen' => 'Akses Halaman Dapur',
            'manage_users' => 'Kelola Pengguna',
            'manage_roles' => 'Kelola Peran & Akses',
            'manage_stocks' => 'Kelola Stok Bahan',
            'manage_automations' => 'Kelola Otomatisasi',
            'view_activity_logs' => 'Lihat Log Aktivitas',
            'manage_menus' => 'Kelola Menu Hidangan',
            'manage_categories' => 'Kelola Kategori Menu',
            'view_all_orders' => 'Lihat Semua Pesanan (Admin)',
            'view_all_transactions' => 'Lihat Semua Transaksi (Admin)',
            'view_reports' => 'Lihat Laporan Penjualan & Performa',
        ];

        $permissionModels = [];
        foreach ($permissions as $name => $label) {
            $permissionModels[$name] = Permission::firstOrCreate(
                ['name' => $name],
                ['label' => $label]
            );
        }

        // 2. Seed Roles (idempotently)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);
        $kitchenRole = Role::firstOrCreate(['name' => 'kitchen']);

        // 3. Clear existing relations to prevent duplicates
        DB::table('permission_role')->delete();

        // 4. Attach Permissions to Roles
        // Admin gets all permissions
        $adminRole->permissions()->attach(array_values(array_map(fn ($p) => $p->id, $permissionModels)));

        // Cashier gets cashier panel access, and menu/category management
        $cashierRole->permissions()->attach([
            $permissionModels['access_cashier']->id,
            $permissionModels['manage_menus']->id,
            $permissionModels['manage_categories']->id,
        ]);

        // Kitchen gets kitchen panel access
        $kitchenRole->permissions()->attach([
            $permissionModels['access_kitchen']->id,
        ]);
    }
}
