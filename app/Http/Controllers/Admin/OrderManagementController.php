<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderManagementController extends Controller
{
    /**
     * Lister toutes les commandes
     */
    public function index(Request $request)
    {
        // Possibilité de filtrer par statut
        $status = $request->query('status');

        $orders = Order::with('items.product', 'user')
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Liste des commandes récupérée avec succès',
            'data' => $orders
        ]);
    }

    /**
     * Détail d'une commande
     */
    public function show($id)
    {
        $order = Order::with('items.product', 'user')->find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Commande introuvable',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Détail de la commande',
            'data' => $order
        ]);
    }

    /**
     * Mettre à jour le statut d'une commande
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Commande introuvable',
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->status = $validated['status'];
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Statut de la commande mis à jour',
            'data' => $order
        ]);
    }

    /**
     * Supprimer ou annuler une commande
     */
    public function destroy($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Commande introuvable',
            ], 404);
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Commande supprimée avec succès',
        ]);
    }
}
