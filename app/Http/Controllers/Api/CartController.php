<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    private function getIdentifier(Request $request)
    {
        if ($request->user()) {
            return ['user_id' => $request->user()->id];
        }
        // Utilise le cookie ou un header personnalisé envoyé par Vue
        $guestId = $request->cookie('guest_id') ?: $request->header('X-Guest-ID');

        if (!$guestId) {
            $guestId = Str::uuid(); // Génère si inexistant
        }

        return ['guest_id' => $guestId];
    }

    public function index(Request $request) {
        $id = $this->getIdentifier($request);

        $cart = Cart::where($id)
            ->with('items.product.images') // Charge aussi les images
            ->first();

        return response()->json([
            'success' => true,
            'data' => $cart ?: ['items' => []],
            'guest_id' => $id['guest_id'] ?? null
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $id = $this->getIdentifier($request);
        $cart = Cart::firstOrCreate($id);

        $item = $cart->items()->where('product_id', $request->product_id)->first();

        if ($item) {
            // Si on envoie une quantité spécifique (depuis le panier), on remplace
            // Sinon (depuis le shop), on incrémente
            if ($request->has('override_quantity')) {
                $item->update(['quantity' => $request->quantity]);
            } else {
                $item->increment('quantity', $request->quantity);
            }
        } else {
            $cart->items()->create([
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Panier mis à jour'])
                         ->cookie('guest_id', $id['guest_id'] ?? '', 43200);
    }

    public function remove(Request $request, $productId)
    {
        $id = $this->getIdentifier($request);
        $cart = Cart::where($id)->first();

        if ($cart) {
            $cart->items()->where('product_id', $productId)->delete();
        }

        return response()->json(['success' => true, 'message' => 'Retiré']);
    }
}
