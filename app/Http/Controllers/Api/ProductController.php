<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Lister les produits avec filtres optionnels
     */
   // Dans App\Http\Controllers\Api\ProductController.php

public function index(Request $request)
{
    try {
        // On commence la requête avec les relations nécessaires
        $query = Product::query()->with(['category', 'images']);

        // 1. Filtre par recherche (uniquement si non vide)
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 2. Filtre par catégorie (uniquement si c'est un ID valide)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // 3. Filtre par prix Min
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // 4. Filtre par prix Max
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 5. Filtre Promotion (pour le bouton "Tout voir" des ventes flash)
        if ($request->has('onSale') && $request->onSale == 'true') {
            $query->whereHas('promotions', function ($q) {
                $q->where('start_at', '<=', now())
                  ->where('end_at', '>=', now());
            });
        }

        // 6. Tri
        $sort = $request->get('sort', 'newest'); // 'newest' par défaut
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->latest();
                break;
        }

        // Retourne la pagination (12 produits par page)
        return response()->json($query->paginate(10));

    } catch (\Exception $e) {
        // En cas d'erreur, on retourne un message clair au lieu d'un crash 500
        return response()->json([
            'error' => 'Erreur serveur lors de la récupération des produits',
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Afficher les produits actuellement en promotion
     */
    public function onSale()
    {
        // On récupère les produits qui ont au moins une promotion active
        $products = Product::whereHas('promotions', function ($query) {
            $query->where('start_at', '<=', now())
                  ->where('end_at', '>=', now());
        })
        ->with(['promotions', 'category'])
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Produits en promotion récupérés',
            'data' => $products
        ]);
    }

    /**
     * Afficher un produit spécifique avec ses détails complets
     */
    public function show($id)
{
    try {
        // Ajout de 'images' dans le with()
        $product = Product::with([
            'category',
            'images', // <-- TRÈS IMPORTANT : pour vos vignettes orange
            'reviews.user',
            'promotions' => function($q) {
                $q->where('start_at', '<=', now())->where('end_at', '>=', now());
            }
        ])
        ->withAvg('reviews', 'rating')
        ->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produit introuvable'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Erreur lors de la récupération du produit',
            'debug' => $e->getMessage() // À retirer en production
        ], 500);
    }
}

    /**
     * Récupérer des produits suggérés (même catégorie)
     */
    public function related($id)
    {
        $product = Product::findOrFail($id);

        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'promotions'])
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $related
        ]);
    }
    
}
