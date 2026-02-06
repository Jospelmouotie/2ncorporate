<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string|max:500',
            'payment_method'   => 'required|string|in:stripe,paypal,cash',
            'cart_id'          => 'required|exists:carts,id',
        ]);

        $user = $request->user();
        $cart = Cart::with('items.product')
            ->where('id', $request->cart_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Panier vide'], 400);
        }

        DB::beginTransaction();
        try {
            // Calcul du total sécurisé côté serveur
            $total = $cart->items->sum(fn($item) => $item->quantity * ($item->product->final_price ?? $item->product->price));

            $order = Order::create([
                'user_id'          => $user->id,
                'shipping_address' => $request->shipping_address,
                'billing_address'  => $request->shipping_address, // Simplifié
                'total'            => $total,
                'payment_method'   => $request->payment_method,
                'status'           => 'pending',
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->final_price ?? $item->product->price,
                ]);
            }

            // Vider le panier après commande réussie
            $cart->items()->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Commande créée', 'data' => $order], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
