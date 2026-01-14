<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdvertisementFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['image', 'video', 'interstitial']);

        // Simulation d'URL de médias
        $mediaUrl = ($type === 'video')
            ? 'https://www.w3schools.com/html/mov_bbb.mp4' // Vidéo de test
            : 'https://picsum.photos/seed/' . rand(1, 1000) . '/1200/600'; // Image aléatoire

        return [
            'title' => $this->faker->sentence(3),
            'type' => $type,
            'media_url' => $mediaUrl,
            'start_at' => now()->subDays(rand(1, 10)), // Commence il y a quelques jours
            'end_at' => now()->addDays(rand(5, 30)),   // Finit dans le futur
        ];
    }

    /**
     * État pour une publicité expirée
     */
    public function expired()
    {
        return $this->state(fn (array $attributes) => [
            'start_at' => now()->subDays(20),
            'end_at' => now()->subDays(1),
        ]);
    }

    /**
     * État pour une publicité future (pas encore active)
     */
    public function future()
    {
        return $this->state(fn (array $attributes) => [
            'start_at' => now()->addDays(10),
            'end_at' => now()->addDays(20),
        ]);
    }
}
