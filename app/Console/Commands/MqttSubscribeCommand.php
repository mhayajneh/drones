<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MqttService;

class MqttSubscribeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to MQTT';

    /**
     * Execute the console command.
     */
    public function handle(MqttService $mqttService)
    {
        $this->info('Starting MQTT subscriber...');

        try {
            $mqttService->connect();
            $this->info('Connected to MQTT broker');

            $this->info('Subscribing to device/+/osd...');
            $mqttService->subscribe();

        } catch (\Exception $e) {
            $this->error('MQTT Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
