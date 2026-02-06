<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;

class DashboardController extends Controller
{
    /**
     * Récupérer les stats pour le dashboard
     */
    public function index()
    {
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total');
        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $deliveredOrders = Order::where('status', 'delivered')->count();

        $totalProducts = Product::count();
        $totalUsers = User::count();

        return response()->json([
            'success' => true,
            'message' => 'Données du dashboard récupérées avec succès',
            'data' => [
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'pending_orders' => $pendingOrders,
                'processing_orders' => $processingOrders,
                'delivered_orders' => $deliveredOrders,
                'total_products' => $totalProducts,
                'total_users' => $totalUsers
            ]
        ]);
    }
}
