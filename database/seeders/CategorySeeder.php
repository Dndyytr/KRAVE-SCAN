<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();

        foreach ($branches as $branch) {
            Category::create(['name' => 'Coffee', 'branch_id' => $branch->id]);
            Category::create(['name' => 'Non-Coffee', 'branch_id' => $branch->id]);
            Category::create(['name' => 'Food', 'branch_id' => $branch->id]);
            Category::create(['name' => 'Dessert', 'branch_id' => $branch->id]);
        }
    }
}
