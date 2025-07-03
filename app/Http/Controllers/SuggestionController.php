<?php

use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SuggestionController extends Controller
{
    public function getSuggestedCities(Request $request)
    {
        $input = $request->input('adresse'); 
        $regions = Zone::all()->pluck('region_depart')->unique();

        $suggestions = $regions->filter(function ($region) use ($input) {
            return Str::contains(Str::lower($region), Str::lower($input)); 
        });

        return response()->json($suggestions); 
    }
}
