<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromoManagemetnController extends Controller
{
    // Récupérer la liste avec les infos des produits
    public function index()
    {
        $promos = Promotion::with('product')->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $promos,
        ]);
    }

    // Créer la promotion
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'sale_price' => 'required|numeric|min:0',
            'start_at'   => 'required|date',
            'end_at'     => 'required|date|after:start_at',
        ]);

        $promotion = Promotion::create($validated);

        // Charger le produit avant de renvoyer la réponse
        $promotion->load('product');

        return response()->json([
            'success' => true,
            'data' => $promotion
        ], 201);
    }

    public function destroy($id)
    {
        $promo = Promotion::findOrFail($id);
        $promo->delete();

        return response()->json(['success' => true]);
    }
}
