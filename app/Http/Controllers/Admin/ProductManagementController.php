<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
class ProductManagementController extends Controller
{
    /* ============================
      LISTE DES PRODUITS
    ============================ */
  public function index(Request $request)
{
    $query = Product::with(['category', 'images'])->orderByDesc('created_at');

    // Si une catégorie est passée en paramètre URL (?category_id=1)
    if ($request->has('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    $products = $query->paginate(10);

    return response()->json([
        'success' => true,
        'data' => $products,
    ]);
}
public function store(Request $request)
{
    // 1. Validation rigoureuse
    $validated = $request->validate([
        'name'          => 'required|string|max:255',
        'price'         => 'required|numeric|min:0',
        'category_id'   => 'required|exists:categories,id',
        'description'   => 'nullable|string',
        'images'        => 'required|array|min:1', // Au moins une image à la création
        'images.*'      => 'image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max par image
    ]);

    try {
        // Utilisation d'une transaction pour éviter de créer un produit sans images en cas d'erreur
        return DB::transaction(function () use ($request) {

            // 2. Création du produit et génération du Slug
            $product = Product::create([
                'name'        => $request->name,
                'slug'        => Str::slug($request->name) . '-' . time(), // Ajout d'un timestamp pour l'unicité
                'price'       => $request->price,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'is_active'   => true,
            ]);

            // 3. Gestion des images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    // Stockage physique sur le disque public
                    $path = $imageFile->store('products', 'public');

                    // La première image du tableau devient l'image principale (is_main = true)
                    $product->images()->create([
                        'url'     => $path,
                        'alt'     => $product->name,
                        'is_main' => ($index === 0),
                    ]);
                }
            }

            // 4. Retourner le produit avec ses images chargées
            return response()->json([
                'success' => true,
                'message' => 'Produit créé avec succès',
                'data'    => $product->load('images')
            ], 201);
        });

    } catch (\Exception $e) {
        // En cas d'erreur, on peut logguer et retourner un message d'erreur
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la création : ' . $e->getMessage()
        ], 500);
    }
}
   public function update(Request $request, $id)
{
    $product = Product::findOrFail($id);

    $validated = $request->validate([
        'name'          => 'sometimes|required|string|max:255',
        'price'         => 'sometimes|required|numeric',
        'category_id'   => 'sometimes|required|exists:categories,id',
        'description'   => 'nullable|string',
        'deleted_images'=> 'nullable|array', // IDs des images à supprimer
        'images'        => 'nullable|array',  // Nouveaux fichiers
        'images.*'      => 'file|image|mimes:jpg,jpeg,png,webp|max:5120'
    ]);

    try {
        // 1. Mise à jour des infos de base
        $product->update($request->only(['name', 'description', 'price', 'category_id']));

        // 2. Supprimer les images demandées
        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $imageId) {
                $image = $product->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->url);
                    $image->delete();
                }
            }
        }

        // 3. Ajouter les nouvelles images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');

                // Si le produit n'a plus d'images, la première devient la principale
                $isMain = ($product->images()->count() === 0 && $index === 0);

                $product->images()->create([
                    'url'     => $path,
                    'is_main' => $isMain,
                    'alt'     => $product->name
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $product->load('images')
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /* ============================
      GESTION DES IMAGES
    ============================ */
    // Dans ProductManagementController.php
    private function handleImages(Request $request, Product $product): void
    {
        // C'est ici qu'on vérifie si les fichiers sont bien là
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            foreach ($files as $index => $image) {
                if ($image->isValid()) {
                    // Stockage
                    $path = $image->store('products', 'public');

                    $product->images()->create([
                        'url' => $path,
                        'is_main' => ($index === 0 && $product->images()->count() === 0),
                        'alt' => $product->name,
                    ]);
                }
            }
        }
    }

    /* ============================
      SUPPRESSION
    ============================ */
    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->url);
            $img->delete();
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produit supprimé',
        ]);
    }
}
