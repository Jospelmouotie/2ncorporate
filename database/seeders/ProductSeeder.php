<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {
          Product::factory(50)
        ->hasImages(rand(2, 4)) // Utilise la relation 'images' définie dans ton modèle Product
        ->create();
}
        }
    }

