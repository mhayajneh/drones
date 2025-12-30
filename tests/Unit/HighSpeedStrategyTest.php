<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Drone;
use App\Strategies\HighSpeedStrategy;

class HighSpeedStrategyTest extends TestCase
{
    public function test_identifies_dangerous_high_speed_drone()
    {
        $strategy = new HighSpeedStrategy(10);
        $drone = new Drone([
            'horizontal_speed' => 12,
            'vertical_speed' => 0,
        ]);

        $this->assertTrue($strategy->isDangerous($drone));
        $this->assertEquals('high_speed', $strategy->getReason());
    }

    public function test_identifies_safe_speed_drone()
    {
        $strategy = new HighSpeedStrategy(10);
        $drone = new Drone([
            'horizontal_speed' => 5,
            'vertical_speed' => 3,
        ]);

        $this->assertFalse($strategy->isDangerous($drone));
    }

    public function test_calculates_combined_speed()
    {
        $strategy = new HighSpeedStrategy(10);
        $drone = new Drone([
            'horizontal_speed' => 8,
            'vertical_speed' => 6,
        ]);

        // sqrt(8^2 + 6^2) = 10
        $this->assertFalse($strategy->isDangerous($drone));

        $drone->horizontal_speed = 9;
        $this->assertTrue($strategy->isDangerous($drone));
    }
}
