<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        // Vérifier si on a des produits, sinon on en crée quelques-uns
        if (Product::count() === 0) {
            Product::factory()->count(10)->create();
        }

        // Créer 5 promotions actives
        Promotion::factory()->count(5)->create();

        // Créer 3 promotions expirées pour tester les filtres
        Promotion::factory()->expired()->count(3)->create();

        $this->command->info('Promotions générées avec succès !');
    }
}
