<?php

namespace App\Repositories;

use App\Models\Drone;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class DroneRepository
{
    public function __construct(
        private Drone $model
    ) {}

    public function all(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (!empty($filters['serial'])) {
            $query->serialLike($filters['serial']);
        }

        return $query->orderBy('last_seen_at', 'desc')
            ->paginate($perPage);
    }

    public function findBySerial(string $serial): ?Drone
    {
        return $this->model->where('serial', $serial)->first();
    }

    public function getOnline(): Collection
    {
        return $this->model->online()
            ->orderBy('last_seen_at', 'desc')
            ->get();
    }

    public function getDangerous(): Collection
    {
        return $this->model->dangerous()
            ->with('dangerClassifications')
            ->orderBy('last_seen_at', 'desc')
            ->get();
    }

    public function getNearby(float $latitude, float $longitude, float $radiusKm = 5): Collection
    {
        if (config('database.default') === 'sqlite') {
            return $this->getNearbyUsingPhp($latitude, $longitude, $radiusKm);
        }

        return $this->getNearbyUsingSql($latitude, $longitude, $radiusKm);
    }

    public function updateOrCreate(string $serial, array $data): Drone
    {
        return $this->model->updateOrCreate(
            ['serial' => $serial],
            $data
        );
    }

    public function markAsOnline(Drone $drone): bool
    {
        return $drone->update([
            'is_online' => true,
            'last_seen_at' => now(),
        ]);
    }

    public function markAsOffline(string $serial): bool
    {
        $drone = $this->findBySerial($serial);

        if (!$drone) {
            return false;
        }

        return $drone->update(['is_online' => false]);
    }

    public function markAsDangerous(Drone $drone, bool $isDangerous = true): bool
    {
        return $drone->update(['is_dangerous' => $isDangerous]);
    }

    public function markAsSafe(Drone $drone): bool
    {
        return $drone->update([
            'is_marked_safe' => true,
            'is_dangerous' => false,
        ]);
    }

    public function markOfflineStale(int $minutesStale = 5): int
    {
        return $this->model->online()
            ->where('last_seen_at', '<', now()->subMinutes($minutesStale))
            ->update(['is_online' => false]);
    }

    private function getNearbyUsingSql(float $latitude, float $longitude, float $radiusKm): Collection
    {
        $radiusMeters = $radiusKm * 1000;
        $earthRadius = 6371000;

        return $this->model->online()
            ->selectRaw("
            *,
            (
                {$earthRadius} * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )
            ) AS distance
        ", [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radiusMeters)
            ->orderBy('distance')
            ->get();
    }

    private function getNearbyUsingPhp(float $lat, float $lng, float $radiusKm): Collection
    {
        return $this->model
            ->online()
            ->get()
            ->filter(function ($drone) use ($lat, $lng, $radiusKm) {
                return $this->haversine(
                        $lat,
                        $lng,
                        $drone->latitude,
                        $drone->longitude
                    ) <= $radiusKm;
            })
            ->values();
    }

    private function haversine($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        return 2 * $earthRadius * asin(min(1, sqrt($a)));
    }
}
