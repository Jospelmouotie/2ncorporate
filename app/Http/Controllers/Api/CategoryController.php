<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Lister toutes les catégories
     */
    public function index()
    {
        $categories = Category::with('products')->get();

        return response()->json([
            'success' => true,
            'message' => 'Catégories récupérées avec succès',
            'data' => $categories
        ]);
    }

    /**
     * Afficher une catégorie précise
     */
    public function show($id)
    {
        $category = Category::with('products')->find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie introuvable',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Catégorie récupérée avec succès',
            'data' => $category
        ]);
    }

    /**
     * Créer une catégorie (Admin)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catégorie créée avec succès',
            'data' => $category
        ]);
    }

    /**
     * Mettre à jour une catégorie (Admin)
     */
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie introuvable',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|unique:categories,slug,' . $category->id,
        ]);

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Catégorie mise à jour avec succès',
            'data' => $category
        ]);
    }

    /**
     * Supprimer une catégorie (Admin)
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Catégorie introuvable',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catégorie supprimée avec succès',
        ]);
    }
}
