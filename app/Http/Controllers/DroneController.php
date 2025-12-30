<?php

namespace App\Http\Controllers;

use App\Repositories\DroneRepository;
use App\Services\DroneService;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Sager Drone Tracking API",
 *     description="API for tracking and managing drones via MQTT",
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * @OA\Tag(
 *     name="Drones",
 *     description="API endpoints for drone management"
 * )
 */

class DroneController extends Controller
{
    public function __construct(
        private DroneService $droneService,
        private DroneRepository $droneRepository
    ) {}

    /**
     * @OA\Get(
     *     path="/api/drones",
     *     tags={"Drones"},
     *     summary="Get all drones",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="serial",
     *         in="query",
     *         description="Filter by serial number (partial match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(response=200, description="List of drones")
     * )
     */
    public function index(Request $request)
    {
        $filters = $request->only(['serial']);
        $perPage = $request->input('per_page', 15);

        $drones = $this->droneRepository->all($filters, $perPage);

        return response()->json($drones);
    }

    /**
     * @OA\Get(
     *     path="/api/drones/online",
     *     tags={"Drones"},
     *     summary="Get all online drones",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of online drones with current location"
     *     )
     * )
     */
    public function online()
    {
        $drones = $this->droneRepository->getOnline();

        return response()->json([
            'data' => $drones,
            'count' => $drones->count(),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/drones/nearby",
     *     tags={"Drones"},
     *     summary="Get drones within specified radius",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number", format="float", example=31.9783)
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number", format="float", example=35.8309)
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Radius in kilometers",
     *         required=false,
     *         @OA\Schema(type="number", format="float", default=5)
     *     ),
     *     @OA\Response(response=200, description="List of nearby drones"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function nearby(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'sometimes|numeric|min:0.1|max:100',
        ]);

        $drones = $this->droneRepository->getNearby(
            $validated['latitude'],
            $validated['longitude'],
            $validated['radius'] ?? 5
        );

        return response()->json([
            'data' => $drones,
            'count' => $drones->count(),
            'search_params' => [
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'radius_km' => $validated['radius'] ?? 5,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/drones/{serial}/flight-path",
     *     tags={"Drones"},
     *     summary="Get drone flight path as GeoJSON",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="serial",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Flight path in GeoJSON format"),
     *     @OA\Response(response=404, description="Drone not found")
     * )
     */
    public function flightPath(string $serial)
    {
        $geoJson = $this->droneService->getFlightPathGeoJson($serial);

        if (!$geoJson) {
            return response()->json([
                'message' => 'Drone not found or no flight history available',
            ], 404);
        }

        return response()->json($geoJson);
    }

    /**
     * @OA\Get(
     *     path="/api/drones/dangerous",
     *     tags={"Drones"},
     *     summary="Get all dangerous drones with reasons",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of dangerous drones with classification reasons"
     *     )
     * )
     */
    public function dangerous()
    {
        $drones = $this->droneService->getDangerousDronesWithReasons();

        return response()->json([
            'data' => $drones,
            'count' => $drones->count(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/drones/{serial}/mark-safe",
     *     tags={"Drones"},
     *     summary="Mark a drone as safe (Admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="serial",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Drone marked as safe"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Drone not found")
     * )
     */
    public function markSafe(string $serial)
    {
        $result = $this->droneService->markDroneAsSafe($serial);

        if (!$result) {
            return response()->json([
                'message' => 'Drone not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Drone marked as safe',
            'serial' => $serial,
        ]);
    }
}
