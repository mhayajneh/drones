<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;

class MqttService
{
    private MqttClient $client;
    private DroneService $droneService;

    public function __construct(DroneService $droneService)
    {
        $this->droneService = $droneService;

        $this->client = new MqttClient(
            config('mqtt.host'),
            config('mqtt.port'),
            config('mqtt.client_id', 'sager_drone_backend')
        );
    }

    public function connect(): void
    {
        $connectionSettings = (new ConnectionSettings())
            ->setKeepAliveInterval(60)
            ->setLastWillTopic('system/status')
            ->setLastWillMessage('offline')
            ->setLastWillQualityOfService(1);

        if (config('mqtt.username')) {
            $connectionSettings
                ->setUsername(config('mqtt.username'))
                ->setPassword(config('mqtt.password'));
        }

        $this->client->connect($connectionSettings, true);

        Log::info('Connected to MQTT broker', [
            'host' => config('mqtt.host'),
            'port' => config('mqtt.port'),
        ]);
    }

    public function subscribe(): void
    {
        $this->client->subscribe(
            'device/+/osd',
            function (string $topic, string $message) {
                $this->handleMessage($topic, $message);
            },
            0
        );

        Log::info('Subscribed to MQTT topic: device/+/osd');

        $this->client->loop(true);
    }

    private function handleMessage(string $topic, string $message): void
    {
        try {
            // Extract serial from topic: device/{serial}/osd
            preg_match('/device\/(.+)\/osd/', $topic, $matches);

            if (!isset($matches[1])) {
                Log::warning('Invalid topic format', ['topic' => $topic]);
                return;
            }

            $serial = $matches[1];
            $data = json_decode($message, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON in MQTT message', [
                    'topic' => $topic,
                    'error' => json_last_error_msg(),
                ]);
                return;
            }

            $this->droneService->processOsdData($serial, $data);

        } catch (\Exception $e) {
            Log::error('Error processing MQTT message', [
                'topic' => $topic,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    public function disconnect(): void
    {
        $this->client->disconnect();
        Log::info('Disconnected from MQTT broker');
    }
}
