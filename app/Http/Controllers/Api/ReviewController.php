<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Product;

class ReviewController extends Controller
{
    /**
     * Lister les avis d’un produit
     */
    public function index($productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit introuvable',
            ], 404);
        }

        $reviews = $product->reviews()->with('user')->get();

        return response()->json([
            'success' => true,
            'message' => 'Avis récupérés avec succès',
            'data' => $reviews,
        ]);
    }

    /**
     * Ajouter un avis
     */
    public function store(Request $request, $productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit introuvable',
            ], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $review = $product->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Avis ajouté avec succès',
            'data' => $review,
        ]);
    }

    /**
     * Mettre à jour un avis
     */
    public function update(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review || $review->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Avis introuvable ou accès refusé',
            ], 403);
        }

        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'sometimes|string|max:1000',
        ]);

        $review->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Avis mis à jour avec succès',
            'data' => $review,
        ]);
    }

    /**
     * Supprimer un avis
     */
    public function destroy(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review || $review->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Avis introuvable ou accès refusé',
            ], 403);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Avis supprimé avec succès',
        ]);
    }
}
