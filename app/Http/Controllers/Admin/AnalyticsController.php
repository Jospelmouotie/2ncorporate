<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Ventes par jour sur les 30 derniers jours
     */
    public function salesByDay()
    {
        $sales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(*) as orders_count')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Ventes par jour récupérées',
            'data' => $sales
        ]);
    }

    /**
     * Produits les plus vendus
     */
    public function topProducts()
    {
        $products = DB::table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Top produits récupérés',
            'data' => $products
        ]);
    }

    /**
     * Commandes par statut
     */
    public function ordersByStatus()
    {
        $statuses = Order::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Commandes par statut récupérées',
            'data' => $statuses
        ]);
    }
}
