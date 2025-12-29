<?php

return [
    'offline_timeout' => env('DRONE_OFFLINE_TIMEOUT', 5), // minutes
    'danger' => [
        'height_threshold' => env('DANGER_HEIGHT_THRESHOLD', 500), // meters
        'speed_threshold' => env('DANGER_SPEED_THRESHOLD', 10), // m/s
    ],
];
