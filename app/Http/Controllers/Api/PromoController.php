<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PromoController extends Controller
{
    /**
     * Lister toutes les promotions
     */
 public function index()
{
    // On récupère les promos actives avec les produits
    $promos = Promotion::with('product')
        ->where('end_at', '>', now()) // Uniquement celles pas encore finies
        ->get();

    return response()->json([
        'success' => true,
        'data' => $promos
    ]);
}
    /**
     * Afficher une promotion spécifique
     */
    public function show($id)
    {
        $promo = Promotion::find($id);

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion introuvable',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Promotion récupérée avec succès',
            'data' => $promo,
        ]);
    }

    /**
     * Créer une promotion (Admin)
     */
   public function store(Request $request)
{
    $validated = $request->validate([
        'product_id' => 'required|exists:products,id',
        'sale_price' => 'required|numeric|min:1',
        'start_at'   => 'required|date',
        'end_at'     => 'required|date|after:start_at',
    ]);

    $promotion = Promotion::create($validated);

    // Mettre à jour le produit automatiquement
    $product = Product::find($request->product_id);
    $product->update([
        'is_on_sale' => true,
        'sale_price' => $request->sale_price // On stocke le prix promo direct sur le produit aussi
    ]);

    return response()->json(['success' => true, 'data' => $promotion->load('product')], 201);
}

    /**
     * Mettre à jour une promotion (Admin)
     */
    public function update(Request $request, $id)
    {
        $promo = Promotion::find($id);

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion introuvable',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => ['sometimes', 'string', Rule::unique('promotions')->ignore($promo->id)],
            'discount_type' => ['sometimes', Rule::in(['percent', 'fixed'])],
            'discount_value' => 'sometimes|numeric|min:0',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'sometimes|date|after:starts_at',
            'active' => 'sometimes|boolean'
        ]);

        $promo->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Promotion mise à jour avec succès',
            'data' => $promo,
        ]);
    }

    /**
     * Supprimer une promotion (Admin)
     */
    public function destroy($id)
    {
        $promo = Promotion::find($id);

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promotion introuvable',
            ], 404);
        }

        $promo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promotion supprimée avec succès',
        ]);
    }

    /**
     * Vérifier un code promo pour le panier
     */
    public function check(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $promo = Promotion::where('code', $request->code)
            ->where('active', true)
            ->where('starts_at', '<=', Carbon::now())
            ->where('ends_at', '>=', Carbon::now())
            ->first();

        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Code promo invalide ou expiré',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Code promo appliqué avec succès',
            'data' => $promo,
        ]);
    }
}
