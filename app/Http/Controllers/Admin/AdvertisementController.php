<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Advertisement;
use Illuminate\Support\Facades\Storage;

class AdvertisementController extends Controller
{
    /**
     * Lister toutes les publicités
     */
    public function index()
    {
        $ads = Advertisement::orderBy('start_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Publicités récupérées',
            'data' => $ads
        ]);
    }

    /**
     * Ajouter une publicité
     */
public function store(Request $request)
{
    try {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:image,video,interstitial',
            'media' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:50000', // 50MB max pour les vidéos
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
        ]);

        if ($request->hasFile('media')) {
            // Sauvegarde dans storage/app/public/ads
            $file = $request->file('media');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('ads', $fileName, 'public');

            $ad = Advertisement::create([
                'title' => $request->title,
                'type' => $request->type,
                'media_url' => asset('storage/' . $path),
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
            ]);

            return response()->json([
                'success' => true,
                'data' => $ad
            ], 201);
        }
    } catch (\Exception $e) {
        // Cela te permettra de voir l'erreur réelle dans les logs Laravel
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
} /**
     * Mettre à jour une publicité
     */
    public function update(Request $request, $id)
    {
        $ad = Advertisement::find($id);
        if (!$ad) {
            return response()->json([
                'success' => false,
                'message' => 'Publicité introuvable',
            ], 404);
        }

      $request->validate([
    'title' => 'sometimes|string|max:255',
    'type' => 'sometimes|in:image,video,interstitial',
    'media' => 'required|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:50000', // 50MB max pour les vidéos
    'start_at' => 'sometimes|date',
    'end_at' => 'nullable|date|after:start_at',
]);

        if ($request->hasFile('media')) {
            // Supprimer l'ancien fichier
            if ($ad->media_url) {
                $oldPath = str_replace('/storage/', 'public/', $ad->media_url);
                Storage::delete($oldPath);
            }
            $path = $request->file('media')->store('public/ads');
            $ad->media_url = Storage::url($path);
        }

        $ad->fill($request->only(['title', 'type', 'start_at', 'end_at']));
        $ad->save();

        return response()->json([
            'success' => true,
            'message' => 'Publicité mise à jour',
            'data' => $ad
        ]);
    }

    /**
     * Supprimer une publicité
     */
    public function destroy($id)
    {
        $ad = Advertisement::find($id);
        if (!$ad) {
            return response()->json([
                'success' => false,
                'message' => 'Publicité introuvable',
            ], 404);
        }

        // Supprimer le fichier
        if ($ad->media_url) {
            $oldPath = str_replace('/storage/', 'public/', $ad->media_url);
            Storage::delete($oldPath);
        }

        $ad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Publicité supprimée',
        ]);
    }
    // Dans AdvertisementController.php
public function getActiveAds()
{
    $ads = Advertisement::active()->get(); // Utilise ton scopeActive
    return response()->json(['success' => true, 'data' => $ads]);
}
}
