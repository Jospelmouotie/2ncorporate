<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use Illuminate\Database\Seeder;

class AdvertisementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer 5 publicités actuellement actives
        Advertisement::factory()->count(5)->create([
            'title' => 'PUB ACTIVE - ' . fake()->word(),
        ]);

        // Créer 2 publicités expirées (pour tester ton scopeActive)
        Advertisement::factory()->expired()->count(2)->create([
            'title' => 'PUB EXPIRÉE',
        ]);

        // Créer 2 publicités prévues pour le futur
        Advertisement::factory()->future()->count(2)->create([
            'title' => 'PUB FUTURE',
        ]);

        // Créer une publicité permanente (sans date de fin)
        Advertisement::factory()->create([
            'title' => 'PUB PERMANENTE',
            'start_at' => now()->subYear(),
            'end_at' => null,
        ]);
    }
}
