<?php

namespace App\Http\Controllers;

use App\Models\DeliveryZone;
use App\Models\DeliveryArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeliveryZoneController extends Controller
{
    /**
     * Afficher la liste des zones de livraison
     */
    public function index()
    {
        $zones = DeliveryZone::with('areas')->orderBy('name')->get();
        return view('admin.tarifs.index', compact('zones'));
    }

    /**
     * Créer une nouvelle zone
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:delivery_zones,name',
            'description' => 'nullable|string',
            'base_token_price' => 'required|numeric|min:0',
            'areas' => 'nullable|array',
            'areas.*' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $zone = DeliveryZone::create([
            'name' => $request->name,
            'description' => $request->description,
            'base_token_price' => $request->base_token_price,
        ]);

        if ($request->has('areas')) {
            foreach ($request->areas as $areaName) {
                DeliveryArea::create([
                    'name' => trim($areaName),
                    'delivery_zone_id' => $zone->id,
                ]);
            }
        }

        return response()->json([
            'message' => 'Zone créée avec succès',
            'zone' => $zone->load('areas')
        ], 201);
    }

    /**
     * Mettre à jour une zone
     */
    public function update(Request $request, DeliveryZone $zone)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:delivery_zones,name,' . $zone->id,
            'description' => 'nullable|string',
            'base_token_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $zone->update([
            'name' => $request->name,
            'description' => $request->description,
            'base_token_price' => $request->base_token_price,
        ]);

        return response()->json([
            'message' => 'Zone mise à jour avec succès',
            'zone' => $zone
        ]);
    }

    /**
     * Supprimer une zone
     */
    public function destroy(DeliveryZone $zone)
    {
        // Vérifier si la zone est utilisée dans des livraisons ou transactions
        if ($zone->deliveries()->exists() || $zone->tokenTransactions()->exists()) {
            return response()->json([
                'message' => 'Impossible de supprimer cette zone car elle est associée à des livraisons ou transactions.'
            ], 403);
        }

        $zone->areas()->delete();
        $zone->delete();

        return response()->json(['message' => 'Zone supprimée avec succès']);
    }

    /**
     * Ajouter un quartier à une zone
     */
    public function storeArea(Request $request, DeliveryZone $zone)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:delivery_areas,name,NULL,id,delivery_zone_id,' . $zone->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $area = DeliveryArea::create([
            'name' => $request->name,
            'delivery_zone_id' => $zone->id,
        ]);

        return response()->json([
            'message' => 'Quartier ajouté avec succès',
            'area' => $area
        ], 201);
    }

    /**
     * Mettre à jour un quartier
     */
    public function updateArea(Request $request, DeliveryArea $area)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:delivery_areas,name,' . $area->id . ',id,delivery_zone_id,' . $area->delivery_zone_id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $area->update(['name' => $request->name]);

        return response()->json([
            'message' => 'Quartier mis à jour avec succès',
            'area' => $area
        ]);
    }

    /**
     * Supprimer un quartier
     */
    public function destroyArea(DeliveryArea $area)
    {
        $area->delete();
        return response()->json(['message' => 'Quartier supprimé avec succès']);
    }
}