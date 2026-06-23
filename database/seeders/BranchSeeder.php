<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Branch::create([
            'name' => 'Krave Scan Jakarta',
            'code' => 'JKT-01',
            'address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'phone' => '021-12345678',
        ]);

        Branch::create([
            'name' => 'Krave Scan Bandung',
            'code' => 'BDG-01',
            'address' => 'Jl. Dago No. 10, Bandung',
            'phone' => '022-87654321',
        ]);
    }
}
