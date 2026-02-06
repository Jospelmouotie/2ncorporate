<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ProductImage;
use App\Models\Product;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
{
    // Liste de mots clés pour avoir des images de produits cohérentes
    $keywords = ['fashion', 'electronics', 'watch', 'shoes', 'iphone', 'laptop'];
    $keyword = $this->faker->randomElement($keywords);

    return [
        'product_id' => Product::factory(),
        // Utilisation de source.unsplash.com pour des images réelles
        'url' => "https://images.unsplash.com/photo-" . $this->faker->randomElement([
            '1523275335684-37898b6baf30', // Watch
            '1505740420928-5e560c06d30e', // Headphones
            '1542291026-7eec264c27ff', // Shoes
            '1526170315870-ef039c0350a4', // Camera
            '1491553895911-0055eca6402d', // Sneakers
        ]) . "?auto=format&fit=crop&w=800&q=80",
        'alt' => $this->faker->sentence(3),
    ];
}
}
