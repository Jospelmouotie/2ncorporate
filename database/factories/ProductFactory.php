<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;
public function definition(): array
{
    $name = $this->faker->unique()->words(3, true);
    return [
        'category_id' => Category::inRandomOrder()->first()->id ?? Category::factory(),
        'name' => ucfirst($name),
        'slug' => Str::slug($name),
        'description' => $this->faker->sentences(3, true),
        'price' => $this->faker->numberBetween(5000, 500000), // Prix en FCFA
        'stock' => $this->faker->numberBetween(0, 50),
        'is_active' => true,
        'is_on_sale' => $this->faker->boolean(30),
    ];
}
}
