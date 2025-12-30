<?php

namespace Tests\Feature;

use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use App\Models\Drone;
use App\Models\LocationHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DroneApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        Passport::actingAs($this->user);
    }

    public function test_can_list_all_drones()
    {
        Drone::factory()->count(3)->create();

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/drones');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'serial', 'latitude', 'longitude'],
                ],
            ]);
    }

    public function test_can_filter_drones_by_serial()
    {
        Drone::factory()->create(['serial' => 'ABC123XYZ']);
        Drone::factory()->create(['serial' => 'DEF456UVW']);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/drones?serial=ABC');

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(1, count($data));
        $this->assertStringContainsString('ABC', $data[0]['serial']);
    }

    public function test_can_list_online_drones()
    {
        Drone::factory()->create(['is_online' => true]);
        Drone::factory()->create(['is_online' => false]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/drones/online');

        $response->assertStatus(200)
            ->assertJson(['count' => 1]);
    }

    public function test_can_find_nearby_drones()
    {
        Drone::factory()->create([
            'is_online' => true,
            'latitude' => 31.9783,
            'longitude' => 35.8309,
        ]);

        Drone::factory()->create([
            'is_online' => true,
            'latitude' => 40.7128, // New York - far away
            'longitude' => -74.0060,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/drones/nearby?latitude=31.9783&longitude=35.8309&radius=5');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('count'));
    }

    public function test_nearby_drones_validates_coordinates()
    {
        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/drones/nearby?latitude=invalid&longitude=35.8309');

        $response->assertStatus(422);
    }

    public function test_can_get_flight_path()
    {
        $drone = Drone::factory()->create(['serial' => 'TEST123']);

        LocationHistory::factory()->count(5)->create([
            'drone_id' => $drone->id,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/drones/TEST123/flight-path");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'type',
                'features' => [
                    '*' => [
                        'type',
                        'geometry' => ['type', 'coordinates'],
                        'properties',
                    ],
                ],
            ]);
    }

    public function test_can_list_dangerous_drones()
    {
        Drone::factory()->create([
            'is_dangerous' => true,
            'is_marked_safe' => false,
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/drones/dangerous');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['serial', 'reasons', 'details'],
                ],
                'count',
            ]);
    }

    public function test_admin_can_mark_drone_as_safe()
    {
        $admin = User::factory()->create(['role' => 'admin']);
//        $adminToken = $admin->createToken('appToken')->accessToken;

        $drone = Drone::factory()->create([
            'serial' => 'DANGER123',
            'is_dangerous' => true,
        ]);

        $response = $this->actingAs($admin, 'api')
            ->postJson("/api/drones/DANGER123/mark-safe");

        $response->assertStatus(200);

        $this->assertDatabaseHas('drones', [
            'serial' => 'DANGER123',
            'is_marked_safe' => true,
            'is_dangerous' => false,
        ]);
    }
}
