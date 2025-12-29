<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('drones', function (Blueprint $table) {
            $table->id();
            $table->string('serial')->unique();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            $table->decimal('horizontal_speed', 10, 2)->nullable();
            $table->decimal('vertical_speed', 10, 2)->nullable();
            $table->integer('elevation')->nullable();
            $table->integer('gear')->nullable();
            $table->decimal('height_limit', 10, 2)->nullable();
            $table->decimal('home_distance', 10, 2)->nullable();
            $table->boolean('is_near_area_limit')->default(false);
            $table->boolean('is_near_height_limit')->default(false);
            $table->integer('rc_lost_action')->nullable();
            $table->boolean('rid_state')->default(false);
            $table->integer('rth_altitude')->nullable();
            $table->json('storage')->nullable();
            $table->decimal('total_flight_distance', 10, 2)->nullable();
            $table->integer('total_flight_sorties')->nullable();
            $table->decimal('total_flight_time', 10, 2)->nullable();
            $table->string('track_id')->nullable();
            $table->integer('wind_direction')->nullable();
            $table->decimal('wind_speed', 10, 2)->nullable();
            $table->boolean('is_online')->default(false);
            $table->boolean('is_dangerous')->default(false);
            $table->boolean('is_marked_safe')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->index(['serial']);
            $table->index(['is_online']);
            $table->index(['is_dangerous']);
            $table->index(['latitude', 'longitude']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drones');
    }
};
