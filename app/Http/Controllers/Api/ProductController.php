<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Lister les produits avec filtres optionnels
     */
    public function index(Request $request)
    {
        try {
            $query = Product::query()->with(['category', 'images', 'promotions' => function($q) {
                $q->where('start_at', '<=', now())->where('end_at', '>=', now());
            }]);

            // Recherche textuelle
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                          $categoryQuery->where('name', 'like', '%' . $searchTerm . '%');
                      });
                });
            }

            // Filtre par catégorie
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            // Filtre par prix
            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Filtre promotions actives
            if ($request->boolean('onSale')) {
                $query->whereHas('promotions', function ($q) {
                    $q->where('start_at', '<=', now())
                      ->where('end_at', '>=', now());
                });
            }

            // Filtre nouveautés (derniers 30 jours)
            if ($request->boolean('new')) {
                $query->where('created_at', '>=', now()->subDays(30));
            }

            // Tri
            $sort = $request->get('sort', 'newest');
            switch ($sort) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'rating':
                    $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'desc');
                    break;
                case 'popular':
                    $query->withCount('orders')->orderBy('orders_count', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }

            // Limite ou pagination
            $limit = $request->get('limit', 15);
            if ($request->boolean('paginate')) {
                return response()->json($query->paginate($limit));
            }

            return response()->json([
                'success' => true,
                'data' => $query->limit($limit)->get()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur serveur',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher les produits actuellement en promotion
     */
    public function onSale()
    {
        try {
            $products = Product::whereHas('promotions', function ($query) {
                $query->where('start_at', '<=', now())
                      ->where('end_at', '>=', now());
            })
            ->with(['promotions' => function($q) {
                $q->where('start_at', '<=', now())->where('end_at', '>=', now());
            }, 'category', 'images'])
            ->orderByDesc(function($query) {
                $query->selectRaw('(price - sale_price) / price * 100')
                      ->from('promotions')
                      ->whereColumn('product_id', 'products.id')
                      ->where('start_at', '<=', now())
                      ->where('end_at', '>=', now())
                      ->limit(1);
            })
            ->limit(12)
            ->get();

            return response()->json([
                'success' => true,
                'message' => 'Produits en promotion récupérés',
                'data' => $products
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher un produit spécifique avec ses détails complets
     */
    public function show($id)
    {
        try {
            $product = Product::with([
                'category',
                'images',
                'reviews.user',
                'promotions' => function($q) {
                    $q->where('start_at', '<=', now())
                      ->where('end_at', '>=', now())
                      ->orderByDesc('discount_percentage');
                }
            ])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->find($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit introuvable'
                ], 404);
            }

            // Ajouter le prix final (après promotion)
            $product->final_price = $product->getCurrentPrice();

            return response()->json([
                'success' => true,
                'data' => $product
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erreur lors de la récupération du produit'
            ], 500);
        }
    }

    /**
     * Recherche rapide pour l'autocomplete
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');

            if (empty($query) || strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $results = Product::where('name', 'like', '%' . $query . '%')
                ->orWhere('description', 'like', '%' . $query . '%')
                ->with(['category', 'images'])
                ->limit(8)
                ->get()
                ->map(function($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image' => $product->image_url,
                        'category' => $product->category->name,
                        'price' => $product->price,
                        'final_price' => $product->getCurrentPrice(),
                        'has_discount' => $product->hasActivePromotion(),
                        'url' => "/product/{$product->id}"
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer des produits suggérés (même catégorie)
     */
    public function related($id)
    {
        try {
            $product = Product::findOrFail($id);

            $related = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->with(['category', 'images', 'promotions' => function($q) {
                    $q->where('start_at', '<=', now())->where('end_at', '>=', now());
                }])
                ->limit(6)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $related
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
