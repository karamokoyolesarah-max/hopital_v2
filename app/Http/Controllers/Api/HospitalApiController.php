<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;

class HospitalApiController extends Controller
{
    public function getNearestHospitals(Request $request)
    {
        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric',
            'radius' => 'nullable|numeric|max:50'
        ]);

        $userLat = $validated['lat'];
        $userLon = $validated['lon'];
        $radius = $validated['radius'] ?? 10;

        $hospitals = Hospital::where('is_active', true)
            ->selectRaw("
                *,
                (
                    6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )
                ) AS distance
            ", [$userLat, $userLon, $userLat])
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc')
            ->limit(5)
            ->get()
            ->map(function($hospital) {
                return [
                    'id' => $hospital->id,
                    'name' => $hospital->name,
                    'address' => $hospital->address,
                    'phone' => $hospital->phone,
                    'email' => $hospital->email,
                    'latitude' => $hospital->latitude,
                    'longitude' => $hospital->longitude,
                    'distance' => round($hospital->distance, 2)
                ];
            });

        return response()->json($hospitals);
    }

    public function getAllHospitals()
    {
        $hospitals = Hospital::where('is_active', true)
            ->select(['id', 'name', 'address', 'phone', 'latitude', 'longitude'])
            ->get();

        return response()->json($hospitals);
    }
}