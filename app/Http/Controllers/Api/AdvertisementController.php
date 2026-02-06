<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;

class AdvertisementController extends Controller
{
    /**
     * Récupérer toutes les publicités actives
     */
public function index() {
    $ads = Advertisement::where('start_at', '<=', now())
        ->where(function($q) {
            $q->whereNull('end_at')->orWhere('end_at', '>=', now());
        })
        ->get();
    return response()->json(['success' => true, 'data' => $ads]);
}
}
