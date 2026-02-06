<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    /**
     * Rechercher des produits
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
        ]);

        $query = Product::query();

        // Recherche par nom
        $query->where('name', 'like', '%' . $request->query . '%');

        // Filtrer par catégorie
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrer par prix
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        $products = $query->with('images')->paginate(20);

        return response()->json([
            'success' => true,
            'message' => 'Résultats de recherche récupérés avec succès',
            'data' => $products,
        ]);
    }
}
