<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CategoryManagementController extends Controller
{
    /**
     * Lister toutaes les catégories
     */
    public function index()
    {
        $categories = Category::with('products')->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des catégories récupérée',
            'data' => $categories
        ]);
    }

    /**
     * Créer une catégorie
     */
   public function store(Request $request)
{
 
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'required|string|unique:categories,slug',

'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB max
        'description' => 'nullable|string',
    ]);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('categories', 'public');
        $validated['image'] = $path;
    }

    $category = Category::create($validated);

    return response()->json(['success' => true, 'data' => $category], 201);
}
    /**
     * Afficher une catégorie spécifique
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
     * Mettre à jour une catégorie
     */
public function update(Request $request, $id)
{
    $category = Category::findOrFail($id);

    // Debug pour voir ce que Laravel reçoit réellement (Optionnel)
     \Log::info($request->all());

    $validated = $request->validate([
        'name' => 'sometimes|string|max:255',
        // Validation du slug unique en ignorant l'ID actuel
        'slug' => [
            'sometimes',
            'string',
            \Illuminate\Validation\Rule::unique('categories')->ignore($category->id)
        ],
       // Dans store() et update()
'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240', // 10MB max
        'description' => 'nullable|string',
    ]);

    if ($request->hasFile('image')) {
        // Suppression de l'ancienne image
        if ($category->image) {
            \Storage::disk('public')->delete($category->image);
        }
        // Stockage
        $path = $request->file('image')->store('categories', 'public');
        $category->image = $path;
    }

    $category->name = $request->input('name', $category->name);
    $category->slug = $request->input('slug', $category->slug);
    $category->description = $request->input('description', $category->description);

    $category->save();

    return response()->json([
        'success' => true,
        'data' => $category->fresh() // fresh() pour avoir l'image_url à jour
    ]);
}

    /**
     * Supprimer une catégorie
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
