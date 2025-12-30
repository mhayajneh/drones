<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\NoFlyZone;

class NoFlyZoneTest extends TestCase
{
    public function test_detects_point_inside_zone()
    {
        $zone = new NoFlyZone([
            'latitude' => 31.9780,
            'longitude' => 35.8300,
            'radius' => 1000,
        ]);

        // Point about 500m away (should be inside)
        $this->assertTrue($zone->isPointInside(31.9825, 35.8300));
    }

    public function test_detects_point_outside_zone()
    {
        $zone = new NoFlyZone([
            'latitude' => 31.9780,
            'longitude' => 35.8300,
            'radius' => 1000,
        ]);

        // Point about 5km away (should be outside)
        $this->assertFalse($zone->isPointInside(32.0200, 35.8300));
    }
}
