<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\NoFlyZone;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // Create regular user
        User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@user.com',
            'password' => bcrypt('user123'),
            'role' => 'user',
        ]);

        // Create sample no-fly zones
        NoFlyZone::create([
            'name' => 'Amman Airport',
            'latitude' => 31.9783,
            'longitude' => 35.8309,
            'radius' => 5000, // 5km
            'description' => 'Queen Alia International Airport restricted zone',
            'is_active' => true,
        ]);

        NoFlyZone::create([
            'name' => 'Military Base',
            'latitude' => 31.9500,
            'longitude' => 35.9000,
            'radius' => 3000,
            'description' => 'Restricted military zone',
            'is_active' => true,
        ]);

        $this->command->info('Database seeded successfully!');
    }
}
