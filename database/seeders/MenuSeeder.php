<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::all();

        foreach ($branches as $branch) {
            $coffee = Category::where('branch_id', $branch->id)->where('name', 'Coffee')->first();
            $nonCoffee = Category::where('branch_id', $branch->id)->where('name', 'Non-Coffee')->first();
            $food = Category::where('branch_id', $branch->id)->where('name', 'Food')->first();
            $dessert = Category::where('branch_id', $branch->id)->where('name', 'Dessert')->first();

            if (! $coffee || ! $nonCoffee || ! $food || ! $dessert) {
                continue;
            }

            // Coffee
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $coffee->id,
                'name' => 'Espresso',
                'description' => 'Single shot espresso yang pekat dan harum dari biji kopi pilihan.',
                'price' => 18000,
                'image_path' => null,
                'is_active' => true,
            ]);
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $coffee->id,
                'name' => 'Americano',
                'description' => 'Espresso shot dengan tambahan air panas, cita rasa klasik kopi hitam.',
                'price' => 22000,
                'image_path' => null,
                'is_active' => true,
            ]);
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $coffee->id,
                'name' => 'Caffe Latte',
                'description' => 'Espresso lembut berpadu dengan susu hangat dan busa susu tipis.',
                'price' => 28000,
                'image_path' => null,
                'is_active' => true,
            ]);
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $coffee->id,
                'name' => 'Caramel Macchiato',
                'description' => 'Kombinasi espresso, susu, vanila, dan saus karamel di atasnya.',
                'price' => 32000,
                'image_path' => null,
                'is_active' => true,
            ]);

            // Non-Coffee
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $nonCoffee->id,
                'name' => 'Matcha Latte',
                'description' => 'Teh hijau Jepang premium yang diseduh dengan susu segar hangat.',
                'price' => 30000,
                'image_path' => null,
                'is_active' => true,
            ]);
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $nonCoffee->id,
                'name' => 'Chocolate Milk',
                'description' => 'Cokelat hitam premium dipadukan dengan susu segar yang legit.',
                'price' => 26000,
                'image_path' => null,
                'is_active' => true,
            ]);
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $nonCoffee->id,
                'name' => 'Iced Lemon Tea',
                'description' => 'Teh segar dingin dengan perasan buah lemon asli.',
                'price' => 18000,
                'image_path' => null,
                'is_active' => true,
            ]);

            // Food
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $food->id,
                'name' => 'Nasi Goreng Kampung',
                'description' => 'Nasi goreng khas nusantara dengan telur mata sapi, ayam suwir, dan kerupuk.',
                'price' => 35000,
                'image_path' => null,
                'is_active' => true,
            ]);
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $food->id,
                'name' => 'Chicken Katsu Curry',
                'description' => 'Ayam katsu renyah disajikan dengan nasi hangat dan saus kari khas Jepang.',
                'price' => 42000,
                'image_path' => null,
                'is_active' => true,
            ]);
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $food->id,
                'name' => 'Spaghetti Bolognese',
                'description' => 'Pasta al dente dengan saus daging sapi bolognese yang melimpah dan taburan keju.',
                'price' => 38000,
                'image_path' => null,
                'is_active' => true,
            ]);

            // Dessert
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $dessert->id,
                'name' => 'Croissant Plain',
                'description' => 'Pastry Prancis mentega yang berlapis, renyah di luar dan lembut di dalam.',
                'price' => 20000,
                'image_path' => null,
                'is_active' => true,
            ]);
            Menu::create([
                'branch_id' => $branch->id,
                'category_id' => $dessert->id,
                'name' => 'Chocolate Fudge Cake',
                'description' => 'Kue cokelat panggang berlapis cokelat fudge yang pekat dan manis.',
                'price' => 28000,
                'image_path' => null,
                'is_active' => true,
            ]);
        }
    }
}
