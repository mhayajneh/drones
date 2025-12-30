<?php

namespace App\Providers;

use App\Models\DangerClassification;
use App\Models\Drone;
use App\Models\LocationHistory;
use App\Models\NoFlyZone;
use App\Repositories\DangerClassificationRepository;
use App\Repositories\DroneRepository;
use App\Repositories\LocationHistoryRepository;
use App\Repositories\NoFlyZoneRepository;
use App\Services\DroneService;
use App\Services\MqttService;
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

        $this->app->singleton(LocationHistoryRepository::class, function ($app) {
            return new LocationHistoryRepository(new LocationHistory());
        });

        $this->app->singleton(NoFlyZoneRepository::class, function ($app) {
            return new NoFlyZoneRepository(new NoFlyZone());
        });

        $this->app->singleton(DangerClassificationRepository::class, function ($app) {
            return new DangerClassificationRepository(new DangerClassification());
        });
        $this->app->singleton(DroneService::class, function ($app) {
            return new DroneService(
                $app->make(DroneRepository::class),
                $app->make(LocationHistoryRepository::class),
                $app->make(DangerClassificationRepository::class),
                $app->make(NoFlyZoneRepository::class)
            );
        });


        $this->app->singleton(MqttService::class, function ($app) {
            return new MqttService(
                $app->make(DroneService::class)
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
