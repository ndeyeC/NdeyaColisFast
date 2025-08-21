<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class TokenPriceController extends Controller
{
    public function index()
    {
        $zones = DeliveryZone::all();
        return view('admin.token_prices.index', compact('zones'));
    }

    public function edit($id)
    {
        $zone = DeliveryZone::findOrFail($id);
        return view('admin.token_prices.edit', compact('zone'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'base_token_price' => 'required|integer|min:0',
        ]);

        $zone = DeliveryZone::findOrFail($id);
        $zone->update([
            'base_token_price' => $request->base_token_price,
        ]);

        return redirect()->route('admin.token-prices.index')->with('success', 'Prix mis à jour avec succès.');
    }
}
