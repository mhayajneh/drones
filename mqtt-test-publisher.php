<?php

require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

// Configuration
$server = getenv('MQTT_HOST') ?: '127.0.0.1';
$port = getenv('MQTT_PORT') ?: 1883;
$clientId = 'test_drone_publisher';
$droneSerial = '1581F6Q8D81F6Q8DEYN10';

echo "Connecting to MQTT broker at {$server}:{$port}...\n";

$client = new MqttClient($server, $port, $clientId);
$connectionSettings = (new ConnectionSettings())
    ->setKeepAliveInterval(60)
    ->setUseTls(false)
    ->setTlsSelfSignedAllowed(true);

try {
    $client->connect($connectionSettings, true);
    echo "Connected successfully!\n\n";

    // Sample drone data
    $scenarios = [
        [
            'name' => 'Normal Flight',
            'data' => [
                'elevation' => 0,
                'gear' => 1,
                'height' => 50.5,
                'height_limit' => 70,
                'home_distance' => 0.15,
                'horizontal_speed' => 3.2,
                'is_near_area_limit' => false,
                'is_near_height_limit' => false,
                'latitude' => 31.978369,
                'longitude' => 35.830921,
                'rc_lost_action' => 2,
                'rid_state' => false,
                'rth_altitude' => 20,
                'storage' => ['total' => 60368000, 'used' => 2000],
                'total_flight_distance' => 459.40,
                'total_flight_sorties' => 5,
                'total_flight_time' => 360.50,
                'track_id' => '',
                'vertical_speed' => 0.5,
                'wind_direction' => 0,
                'wind_speed' => 0,
            ],
        ],
        [
            'name' => 'High Altitude (Dangerous)',
            'data' => [
                'elevation' => 0,
                'gear' => 1,
                'height' => 520.0,
                'height_limit' => 600,
                'home_distance' => 0.20,
                'horizontal_speed' => 4.0,
                'is_near_area_limit' => false,
                'is_near_height_limit' => false,
                'latitude' => 31.979000,
                'longitude' => 35.831000,
                'rc_lost_action' => 2,
                'rid_state' => false,
                'rth_altitude' => 20,
                'storage' => ['total' => 60368000, 'used' => 3000],
                'total_flight_distance' => 500.00,
                'total_flight_sorties' => 6,
                'total_flight_time' => 400.00,
                'track_id' => '',
                'vertical_speed' => 2.0,
                'wind_direction' => 45,
                'wind_speed' => 5.0,
            ],
        ],
        [
            'name' => 'High Speed (Dangerous)',
            'data' => [
                'elevation' => 0,
                'gear' => 4,
                'height' => 100.0,
                'height_limit' => 200,
                'home_distance' => 1.50,
                'horizontal_speed' => 15.5,
                'is_near_area_limit' => false,
                'is_near_height_limit' => false,
                'latitude' => 31.980000,
                'longitude' => 35.832000,
                'rc_lost_action' => 2,
                'rid_state' => true,
                'rth_altitude' => 20,
                'storage' => ['total' => 60368000, 'used' => 4000],
                'total_flight_distance' => 1200.00,
                'total_flight_sorties' => 8,
                'total_flight_time' => 600.00,
                'track_id' => 'TRACK001',
                'vertical_speed' => 1.0,
                'wind_direction' => 90,
                'wind_speed' => 3.0,
            ],
        ],
    ];

    foreach ($scenarios as $scenario) {
        echo "Publishing: {$scenario['name']}\n";

        $topic = "device/{$droneSerial}/osd";
        $message = json_encode($scenario['data']);

        $client->publish($topic, $message, 0);
        echo "  Topic: {$topic}\n";
        echo "  Data: {$message}\n\n";

        sleep(2);
    }

    echo "All test messages published successfully!\n";
    $client->disconnect();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
