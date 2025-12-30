<?php

/**
* @OA\Schema(
*     schema="Drone",
*     type="object",
*     title="Drone",
*     required={"serial"},
*     @OA\Property(property="id", type="integer", example=1),
*     @OA\Property(property="serial", type="string", example="1581F6Q8D81F6Q8DEYN10"),
*     @OA\Property(property="latitude", type="number", format="float", example=31.978369),
*     @OA\Property(property="longitude", type="number", format="float", example=35.830921),
*     @OA\Property(property="height", type="number", format="float", example=17.20),
*     @OA\Property(property="horizontal_speed", type="number", format="float", example=0),
*     @OA\Property(property="vertical_speed", type="number", format="float", example=0),
*     @OA\Property(property="is_online", type="boolean", example=true),
*     @OA\Property(property="is_dangerous", type="boolean", example=false),
*     @OA\Property(property="last_seen_at", type="string", format="date-time", example="2024-01-01T10:00:00Z")
* )
*
* @OA\Schema(
*     schema="DangerousDrone",
*     type="object",
*     @OA\Property(property="serial", type="string", example="1581F6Q8D81F6Q8DEYN10"),
*     @OA\Property(property="latitude", type="number", format="float", example=31.978369),
*     @OA\Property(property="longitude", type="number", format="float", example=35.830921),
*     @OA\Property(property="height", type="number", format="float", example=520.5),
*     @OA\Property(property="speed", type="number", format="float", example=12.3),
*     @OA\Property(
*         property="reasons",
*         type="array",
*         @OA\Items(type="string", enum={"high_altitude", "high_speed", "geofence_violation"})
*     ),
*     @OA\Property(
*         property="details",
*         type="array",
*         @OA\Items(
*             type="object",
*             @OA\Property(property="reason", type="string"),
*             @OA\Property(property="details", type="string"),
*             @OA\Property(property="detected_at", type="string", format="date-time")
*         )
*     ),
*     @OA\Property(property="last_seen", type="string", format="date-time")
* )
*
* @OA\Schema(
*     schema="GeoJSON",
*     type="object",
*     @OA\Property(property="type", type="string", example="FeatureCollection"),
*     @OA\Property(
*         property="features",
*         type="array",
*         @OA\Items(
*             type="object",
*             @OA\Property(property="type", type="string", example="Feature"),
*             @OA\Property(
*                 property="geometry",
*                 type="object",
*                 @OA\Property(property="type", type="string", example="LineString"),
*                 @OA\Property(
*                     property="coordinates",
*                     type="array",
*                     @OA\Items(type="array", @OA\Items(type="number"))
*                 )
*             ),
*             @OA\Property(
*                 property="properties",
*                 type="object",
*                 @OA\Property(property="serial", type="string"),
*                 @OA\Property(property="start_time", type="string", format="date-time"),
*                 @OA\Property(property="end_time", type="string", format="date-time")
*             )
*         )
*     )
* )
*
* @OA\Schema(
*     schema="Error",
*     type="object",
*     @OA\Property(property="message", type="string", example="Error message"),
*     @OA\Property(
*         property="errors",
*         type="object",
*         @OA\AdditionalProperties(
*             type="array",
*             @OA\Items(type="string")
*         )
*     )
* )
*/
