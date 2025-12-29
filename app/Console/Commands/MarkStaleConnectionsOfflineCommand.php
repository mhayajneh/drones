<?php

namespace App\Console\Commands;

use App\Services\DroneService;
use Illuminate\Console\Command;

class MarkStaleConnectionsOfflineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drones:mark-stale-offline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark drones with stale connections offine';

    /**
     * Execute the console command.
     */
    public function handle(DroneService $droneService)
    {
        $count = $droneService->markStaleConnectionsOffline();

        $this->info("Marked {$count} drone(s) as offline");

        return Command::SUCCESS;
    }
}
