<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        // On définit le chemin où Faker va enregistrer l'image
        $path = storage_path('app/public/categories');

        // Créer le dossier s'il n'existe pas encore
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            // Faker génère l'image physiquement dans le dossier et renvoie le nom du fichier
            'image' => 'categories/' . $this->faker->image($path, 640, 480, null, false),
        ];
    }
}
