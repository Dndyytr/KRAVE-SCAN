<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $cashierRole = Role::where('name', 'cashier')->first();

        $jktBranch = Branch::where('code', 'JKT-01')->first();
        $bdgBranch = Branch::where('code', 'BDG-01')->first();

        // Super Admin (No branch constraint)
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@kravescan.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'branch_id' => null,
        ]);

        // Jakarta Branch Admin & Cashier
        User::create([
            'name' => 'Admin Jakarta',
            'email' => 'admin.jkt@kravescan.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'branch_id' => $jktBranch->id,
        ]);

        User::create([
            'name' => 'Cashier Jakarta',
            'email' => 'cashier.jkt@kravescan.com',
            'password' => Hash::make('password'),
            'role_id' => $cashierRole->id,
            'branch_id' => $jktBranch->id,
        ]);

        // Bandung Branch Admin & Cashier
        User::create([
            'name' => 'Admin Bandung',
            'email' => 'admin.bdg@kravescan.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'branch_id' => $bdgBranch->id,
        ]);

        User::create([
            'name' => 'Cashier Bandung',
            'email' => 'cashier.bdg@kravescan.com',
            'password' => Hash::make('password'),
            'role_id' => $cashierRole->id,
            'branch_id' => $bdgBranch->id,
        ]);
    }
}
