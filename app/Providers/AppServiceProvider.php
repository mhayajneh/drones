<?php

namespace App\Providers;

use App\Models\Drone;
use App\Repositories\DangerClassificationRepository;
use App\Repositories\DroneRepository;
use App\Repositories\LocationHistoryRepository;
use App\Repositories\NoFlyZoneRepository;
use App\Services\DroneService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DroneRepository::class, function ($app) {
            return new DroneRepository(new Drone());
        });

        $this->app->singleton(DroneService::class, function ($app) {
            return new DroneService(
                $app->make(DroneRepository::class),
                $app->make(LocationHistoryRepository::class),
                $app->make(DangerClassificationRepository::class),
                $app->make(NoFlyZoneRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
