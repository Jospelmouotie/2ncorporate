<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class WishlistController extends Controller
{
    /**
     * Identifie l'utilisateur (Connecté ou Invité)
     */
    private function getIdentifier(Request $request)
    {
        if ($request->user()) {
            return ['user_id' => $request->user()->id];
        }

        // Pour les invités, on utilise un identifiant stocké dans les cookies
        // ou on en génère un nouveau
        $guestId = $request->cookie('guest_id') ?: $request->header('X-Guest-ID') ?: Str::uuid();

        return ['guest_id' => $guestId];
    }

    public function index(Request $request)
    {
        $id = $this->getIdentifier($request);

        $wishlist = Wishlist::where($id)
            ->with('products')
            ->first();

        return response()->json([
            'success' => true,
            'data' => $wishlist ? $wishlist->products : []
        ]);
    }

    public function add(Request $request)
{
    try {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        // Logique d'identification
        $user = $request->user();
        $guestId = $request->cookie('guest_id');

        if (!$user && !$guestId) {
            // Si pas de user et pas de cookie, on peut en créer un
            $guestId = (string) \Illuminate\Support\Str::uuid();
        }

        // Trouver ou créer la wishlist
        $wishlist = Wishlist::firstOrCreate(
            $user ? ['user_id' => $user->id] : ['guest_id' => $guestId]
        );

        // Attach sans doublon (syncWithoutDetaching est plus propre)
        $wishlist->products()->syncWithoutDetaching([$validated['product_id']]);

        return response()->json([
            'success' => true,
            'message' => 'Ajouté aux favoris',
        ])->cookie('guest_id', $guestId, 43200); // Expire dans 30 jours

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur interne: ' . $e->getMessage()
        ], 500);
    }
}

    public function remove(Request $request, $productId)
    {
        $id = $this->getIdentifier($request);
        $wishlist = Wishlist::where($id)->first();

        if ($wishlist) {
            $wishlist->products()->detach($productId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Produit retiré de la wishlist',
        ]);
    }
}
