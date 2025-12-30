<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Drone;
use App\Strategies\HighAltitudeStrategy;

class HighAltitudeStrategyTest extends TestCase
{
    public function test_identifies_dangerous_high_altitude_drone()
    {
        $strategy = new HighAltitudeStrategy(500);
        $drone = new Drone(['height' => 550]);

        $this->assertTrue($strategy->isDangerous($drone));
        $this->assertEquals('high_altitude', $strategy->getReason());
        $this->assertStringContainsString('550', $strategy->getDetails($drone));
    }

    public function test_identifies_safe_altitude_drone()
    {
        $strategy = new HighAltitudeStrategy(500);
        $drone = new Drone(['height' => 400]);

        $this->assertFalse($strategy->isDangerous($drone));
    }

    public function test_handles_null_height()
    {
        $strategy = new HighAltitudeStrategy(500);
        $drone = new Drone(['height' => null]);

        $this->assertFalse($strategy->isDangerous($drone));
    }
}
