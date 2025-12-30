# Sager Drone API - Usage Examples

## Authentication

### Register New User
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

### Login
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@sager.com",
    "password": "admin123"
  }'
```

Save the `access_token` from the response.

## Drone Endpoints

### List All Drones
```bash
curl -X GET "http://localhost:8000/api/drones" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Filter Drones by Serial
```bash
curl -X GET "http://localhost:8000/api/drones?serial=1581F6" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### List Online Drones
```bash
curl -X GET "http://localhost:8000/api/drones/online" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Find Nearby Drones
```bash
curl -X GET "http://localhost:8000/api/drones/nearby?latitude=31.9783&longitude=35.8309&radius=5" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Get Flight Path (GeoJSON)
```bash
curl -X GET "http://localhost:8000/api/drones/1581F6Q8D81F6Q8DEYN10/flight-path" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### List Dangerous Drones
```bash
curl -X GET "http://localhost:8000/api/drones/dangerous" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Mark Drone as Safe (Admin Only)
```bash
curl -X POST "http://localhost:8000/api/drones/1581F6Q8D81F6Q8DEYN10/mark-safe" \
  -H "Authorization: Bearer YOUR_ADMIN_TOKEN_HERE"
```

## Testing with Postman

1. Import the collection (create from Swagger)
2. Set environment variables:
    - `base_url`: http://localhost:8000
    - `token`: Your JWT token

## Testing MQTT

Publish test data:
```bash
php mqtt-test-publisher.php
```

Or use mosquitto_pub:
```bash
mosquitto_pub -h localhost -t "device/TEST123/osd" -m '{
  "latitude": 31.978369,
  "longitude": 35.830921,
  "height": 520.0,
  "horizontal_speed": 12.5,
  "vertical_speed": 0
}'
```
