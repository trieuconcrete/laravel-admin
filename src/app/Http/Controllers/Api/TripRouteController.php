<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TripRoute;

class TripRouteController extends Controller
{
    /**
     * Suggest origin or destination names based on query.
     * Query params: field=origin|destination, q=search string
     */
    public function suggest(Request $request)
    {
        $field = $request->get('field', 'origin');
        $q = $request->get('q', '');

        if (!in_array($field, ['origin', 'destination'])) {
            return response()->json([], 400);
        }

        $column = $field === 'origin' ? 'origin_name' : 'destination_name';

        $results = TripRoute::select($column . ' as name', 'price')
            ->where($column, 'like', "%{$q}%")
            ->distinct()
            ->limit(12)
            ->get();

        return response()->json($results);
    }

    /**
     * Given an origin name, return possible destinations and the stored origin address.
     * Query params: origin=...
     */
    public function destinationsByOrigin(Request $request)
    {
        $origin = $request->get('origin');
        if (!$origin) {
            return response()->json([], 400);
        }

        $destinations = TripRoute::where('origin_name', $origin)
            ->select('destination_name as name', 'tons', 'price')
            ->distinct()
            ->get();

        return response()->json([
            'destinations' => $destinations,
        ]);
    }
}
