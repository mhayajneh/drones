<?php

namespace App\Strategies;

use App\Models\Drone;
use Illuminate\Support\Collection;

class DangerClassificationContext
{
    private Collection $strategies;

    public function __construct()
    {
        $this->strategies = collect();
    }

    public function addStrategy(DangerClassificationStrategy $strategy): self
    {
        $this->strategies->push($strategy);
        return $this;
    }

    public function setStrategies(array $strategies): self
    {
        $this->strategies = collect($strategies);
        return $this;
    }

    public function classify(Drone $drone): array
    {
        $reasons = [];

        foreach ($this->strategies as $strategy) {
            if ($strategy->isDangerous($drone)) {
                $reasons[] = [
                    'reason' => $strategy->getReason(),
                    'details' => $strategy->getDetails($drone),
                ];
            }
        }

        return [
            'is_dangerous' => !empty($reasons),
            'classifications' => $reasons,
        ];
    }

    public function getReasons(Drone $drone): array
    {
        return collect($this->classify($drone)['classifications'])
            ->pluck('reason')
            ->toArray();
    }

    public function isDangerous(Drone $drone): bool
    {
        return $this->classify($drone)['is_dangerous'];
    }
}
