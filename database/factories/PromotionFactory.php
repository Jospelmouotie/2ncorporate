<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PromotionFactory extends Factory
{
    protected $model = Promotion::class;

    public function definition(): array
    {
        // On récupère un produit aléatoire
        $product = Product::inRandomOrder()->first() ?? Product::factory()->create();

        // On définit un prix promo (environ 20% de moins que le prix original)
        $salePrice = $product->price * 0.8;

        return [
            'product_id' => $product->id,
            'sale_price' => $salePrice,
            'start_at'   => now()->subDays(rand(0, 5)), // A commencé récemment
            'end_at'     => now()->addDays(rand(5, 15)), // Finit plus tard
        ];
    }

    /**
     * État pour une promotion déjà terminée
     */
    public function expired()
    {
        return $this->state(fn (array $attributes) => [
            'start_at' => now()->subDays(20),
            'end_at'   => now()->subDays(1),
        ]);
    }
}
