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
        Schema::create('danger_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drone_id')->constrained()->onDelete('cascade');
            $table->string('reason'); // high_altitude, high_speed, geofence_violation
            $table->text('details')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('detected_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['drone_id', 'is_resolved']);
            $table->index(['detected_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('danger_classifications');
    }
};
