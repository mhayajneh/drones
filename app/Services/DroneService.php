<?php

namespace App\Services;

use App\Models\Drone;
use App\Repositories\DroneRepository;
use App\Repositories\LocationHistoryRepository;
use App\Repositories\DangerClassificationRepository;
use App\Strategies\DangerClassificationContext;
use App\Strategies\HighAltitudeStrategy;
use App\Strategies\HighSpeedStrategy;
use App\Strategies\GeofenceStrategy;
use App\Repositories\NoFlyZoneRepository;
use Illuminate\Support\Facades\Log;

class DroneService
{
    private DangerClassificationContext $classificationContext;

    public function __construct(
        private DroneRepository $droneRepository,
        private LocationHistoryRepository $locationHistoryRepository,
        private DangerClassificationRepository $dangerClassificationRepository,
        private NoFlyZoneRepository $noFlyZoneRepository
    ) {
        $this->initializeClassificationStrategies();
    }

    private function initializeClassificationStrategies(): void
    {
        $this->classificationContext = new DangerClassificationContext();

        $this->classificationContext
            ->addStrategy(new HighAltitudeStrategy())
            ->addStrategy(new HighSpeedStrategy())
            ->addStrategy(new GeofenceStrategy($this->noFlyZoneRepository));
    }

    public function processOsdData(string $serial, array $data): Drone
    {
        // Update or create drone
        $drone = $this->droneRepository->updateOrCreate($serial, [
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'height' => $data['height'] ?? null,
            'horizontal_speed' => $data['horizontal_speed'] ?? null,
            'vertical_speed' => $data['vertical_speed'] ?? null,
            'elevation' => $data['elevation'] ?? null,
            'gear' => $data['gear'] ?? null,
            'height_limit' => $data['height_limit'] ?? null,
            'home_distance' => $data['home_distance'] ?? null,
            'is_near_area_limit' => $data['is_near_area_limit'] ?? false,
            'is_near_height_limit' => $data['is_near_height_limit'] ?? false,
            'rc_lost_action' => $data['rc_lost_action'] ?? null,
            'rid_state' => $data['rid_state'] ?? false,
            'rth_altitude' => $data['rth_altitude'] ?? null,
            'storage' => $data['storage'] ?? null,
            'total_flight_distance' => $data['total_flight_distance'] ?? null,
            'total_flight_sorties' => $data['total_flight_sorties'] ?? null,
            'total_flight_time' => $data['total_flight_time'] ?? null,
            'track_id' => $data['track_id'] ?? null,
            'wind_direction' => $data['wind_direction'] ?? null,
            'wind_speed' => $data['wind_speed'] ?? null,
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        // Record location history if coordinates are available
        if (isset($data['latitude']) && isset($data['longitude']) && isset($data['height'])) {
            $this->locationHistoryRepository->create($drone, $data);
        }

        // Classify danger status
        $this->classifyDroneStatus($drone);

        Log::info("Processed OSD data for drone {$serial}", [
            'is_dangerous' => $drone->is_dangerous,
            'is_online' => $drone->is_online,
        ]);

        return $drone->fresh();
    }

    private function classifyDroneStatus(Drone $drone): void
    {
        // Skip if marked as safe by admin
        if ($drone->is_marked_safe) {
            return;
        }

        $classification = $this->classificationContext->classify($drone);

        // Update drone danger status
        $this->droneRepository->markAsDangerous($drone, $classification['is_dangerous']);

        // Record danger classifications
        if ($classification['is_dangerous']) {
            foreach ($classification['classifications'] as $classificationData) {
                // Only create if not already recorded
                if (!$this->dangerClassificationRepository->hasUnresolvedReason(
                    $drone,
                    $classificationData['reason']
                )) {
                    $this->dangerClassificationRepository->create(
                        $drone,
                        $classificationData['reason'],
                        $classificationData['details']
                    );
                }
            }
        } else {
            // Resolve all danger classifications if drone is no longer dangerous
            $this->dangerClassificationRepository->resolveAll($drone);
        }
    }

    public function getFlightPathGeoJson(string $serial): ?array
    {
        $drone = $this->droneRepository->findBySerial($serial);

        if (!$drone) {
            return null;
        }

        $locations = $this->locationHistoryRepository->getFlightPath($drone);

        if ($locations->isEmpty()) {
            return null;
        }

        $coordinates = $locations->map(function ($location) {
            return [
                floatval($location->longitude),
                floatval($location->latitude),
                floatval($location->height),
            ];
        })->toArray();

        return [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => $coordinates,
                    ],
                    'properties' => [
                        'serial' => $drone->serial,
                        'start_time' => $locations->first()->recorded_at->toIso8601String(),
                        'end_time' => $locations->last()->recorded_at->toIso8601String(),
                        'total_distance' => $drone->total_flight_distance,
                        'total_time' => $drone->total_flight_time,
                    ],
                ],
            ],
        ];
    }

    public function getDangerousDronesWithReasons()
    {
        $drones = $this->droneRepository->getDangerous();

        return $drones->map(function ($drone) {
            $classifications = $drone->dangerClassifications()
                ->unresolved()
                ->get();

            return [
                'serial' => $drone->serial,
                'latitude' => $drone->latitude,
                'longitude' => $drone->longitude,
                'height' => $drone->height,
                'speed' => $drone->getCurrentSpeed(),
                'reasons' => $classifications->pluck('reason')->unique()->toArray(),
                'details' => $classifications->map(function ($classification) {
                    return [
                        'reason' => $classification->reason,
                        'details' => $classification->details,
                        'detected_at' => $classification->detected_at->toIso8601String(),
                    ];
                })->toArray(),
                'last_seen' => $drone->last_seen_at->toIso8601String(),
            ];
        });
    }

    public function markDroneAsSafe(string $serial): bool
    {
        $drone = $this->droneRepository->findBySerial($serial);

        if (!$drone) {
            return false;
        }

        // Mark drone as safe
        $this->droneRepository->markAsSafe($drone);

        // Resolve all danger classifications
        $this->dangerClassificationRepository->resolveAll($drone);

        Log::info("Drone {$serial} marked as safe by admin");

        return true;
    }

    public function markStaleConnectionsOffline(): int
    {
        return $this->droneRepository->markOfflineStale(
            config('drone.offline_timeout', 5)
        );
    }
}
