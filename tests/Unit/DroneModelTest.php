<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Drone;

class DroneModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_current_speed()
    {
        $drone = new Drone([
            'horizontal_speed' => 3,
            'vertical_speed' => 4,
        ]);

        $this->assertEquals(5, $drone->getCurrentSpeed());
    }

    public function test_handles_null_speeds()
    {
        $drone = new Drone([
            'horizontal_speed' => null,
            'vertical_speed' => null,
        ]);

        $this->assertEquals(0, $drone->getCurrentSpeed());
    }

    public function test_online_scope()
    {
        Drone::factory()->create(['is_online' => true, 'serial' => 'ONLINE1']);
        Drone::factory()->create(['is_online' => false, 'serial' => 'OFFLINE1']);

        $onlineDrones = Drone::online()->get();

        $this->assertEquals(1, $onlineDrones->count());
        $this->assertEquals('ONLINE1', $onlineDrones->first()->serial);
    }

    public function test_dangerous_scope()
    {
        Drone::factory()->create([
            'is_dangerous' => true,
            'is_marked_safe' => false,
            'serial' => 'DANGER1',
        ]);

        Drone::factory()->create([
            'is_dangerous' => true,
            'is_marked_safe' => true,
            'serial' => 'SAFE1',
        ]);

        $dangerousDrones = Drone::dangerous()->get();

        $this->assertEquals(1, $dangerousDrones->count());
        $this->assertEquals('DANGER1', $dangerousDrones->first()->serial);
    }
}
