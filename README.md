# Sager Drone Tracking System

A Laravel-based backend system for real-time drone tracking via MQTT with RESTful APIs.

## Features

- ✅ Real-time MQTT drone data processing
- ✅ Drone tracking with location history
- ✅ Dangerous drone classification (height > 500m, speed > 10m/s)
- ✅ Geofencing for restricted no-fly zones
- ✅ GeoJSON flight path generation
- ✅ JWT Authentication with role-based access control
- ✅ Repository and Strategy design patterns
- ✅ Comprehensive unit and feature tests
- ✅ API documentation with Swagger/OpenAPI
- ✅ Docker deployment ready

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+

## Installation

### Local Setup

1. **Clone and install dependencies**
```bash
unzip sager-drone.zip OR Clone the repo
cd sager-drone-system
composer install
```

2. **Environment configuration**
```bash
cp .env.example .env
php artisan key:generate
```

3. **Configure .env file**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sager_drone
DB_USERNAME=root
DB_PASSWORD=

MQTT_HOST=127.0.0.1
MQTT_PORT=1883
MQTT_USERNAME=
MQTT_PASSWORD=
MQTT_CLIENT_ID=sager_drone_backend

JWT_SECRET=
JWT_TTL=60
```

4. **Database setup**
```bash
php artisan migrate
php artisan db:seed
```

5. **Generate JWT Passport Tokens**
```bash
php artisan passport:install
```

6. **Start the application**
```bash
php artisan serve
php artisan mqtt:subscribe
```

### Docker Setup

1. **Build and start containers**
```bash
docker-compose up -d
```

2. **Run migrations**
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

3. **Generate JWT Passport secret**
```bash
docker-compose exec app php artisan passport:install
```

The application will be available at `http://localhost:8000`

## API Documentation

Access Swagger UI at: `http://localhost:8000/api/documentation`

### Authentication

**Register User**
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "Admin User",
  "email": "admin@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "admin"
}
```

**Login**
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password123"
}

Response:
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600
}
```

**Use token in subsequent requests**
```http
Authorization: Bearer {access_token}
```

### Drone APIs

**List All Drones**
```http
GET /api/drones
GET /api/drones?serial=1581F6Q8
GET /api/drones?per_page=20&page=1
Authorization: Bearer {token}
```

**List Online Drones**
```http
GET /api/drones/online
Authorization: Bearer {token}
```

**Find Drones Near Location**
```http
GET /api/drones/nearby?latitude=31.9783&longitude=35.8309&radius=5
Authorization: Bearer {token}
```

**Get Drone Flight Path (GeoJSON)**
```http
GET /api/drones/{serial}/flight-path
Authorization: Bearer {token}

Response:
{
  "type": "FeatureCollection",
  "features": [{
    "type": "Feature",
    "geometry": {
      "type": "LineString",
      "coordinates": [[35.8309, 31.9783], ...]
    },
    "properties": {
      "serial": "1581F6Q8D81F6Q8DEYN10",
      "start_time": "2024-01-01T10:00:00Z",
      "end_time": "2024-01-01T10:30:00Z"
    }
  }]
}
```

**List Dangerous Drones**
```http
GET /api/drones/dangerous
Authorization: Bearer {token}

Response:
{
  "data": [{
    "serial": "1581F6Q8D81F6Q8DEYN10",
    "latitude": 31.9783,
    "longitude": 35.8309,
    "height": 520.5,
    "speed": 12.3,
    "reasons": ["high_altitude", "high_speed"],
    "last_seen": "2024-01-01T10:00:00Z"
  }]
}
```

**Mark Drone as Safe (Admin only)**
```http
POST /api/drones/{serial}/mark-safe
Authorization: Bearer {admin_token}

Response:
{
  "message": "Drone marked as safe",
  "serial": "1581F6Q8D81F6Q8DEYN10"
}
```

## Testing

**Run all tests**
```bash
php artisan test
```

**Run specific test suites**
```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

**With coverage**
```bash
php artisan test --coverage
```

## Design Patterns

### Repository Pattern
- `DroneRepository` - Abstracts data access layer
- `LocationHistoryRepository` - Manages location data
- Enables easy testing with mock repositories

### Strategy Pattern
- `DangerClassificationStrategy` - Interface for classification rules
- `HighAltitudeStrategy` - Checks altitude > 500m
- `HighSpeedStrategy` - Checks speed > 10m/s
- `GeofenceStrategy` - Checks no-fly zones
- Easily extensible for new classification rules

### Service Pattern
- `DroneService` - Business logic layer
- `MqttService` - MQTT communication handling
- Separates concerns and improves testability

## Architecture

```
app/
├── Console/Commands/
│   └── MqttSubscribeCommand.php
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   └── DroneController.php
│   ├── Middleware/
│   │   └── CheckRole.php
│   └── Requests/
│       ├── NearbyDronesRequest.php
│       └── AuthRequest.php
├── Models/
│   ├── Drone.php
│   ├── LocationHistory.php
│   ├── NoFlyZone.php
│   └── User.php
├── Repositories/
│   ├── DroneRepository.php
│   └── LocationHistoryRepository.php
├── Services/
│   ├── DroneService.php
│   └── MqttService.php
└── Strategies/
    ├── DangerClassificationStrategy.php
    ├── HighAltitudeStrategy.php
    ├── HighSpeedStrategy.php
    └── GeofenceStrategy.php
```

## MQTT Topic Structure

The system subscribes to: `device/+/osd`

Example: `device/1581F6Q8D81F6Q8DEYN10/osd`

### Payload Example
```json
{
  "elevation": 0,
  "gear": 1,
  "height": 17.2,
  "latitude": 31.978369,
  "longitude": 35.830921,
  "horizontal_speed": 0,
  "vertical_speed": 0,
  "total_flight_time": 360.5,
  "total_flight_distance": 459.4
}
```

## Default Users

**Admin User**
- Email: admin@admin.com
- Password: admin123
- Role: admin

**Regular User**
- Email: user@user.com
- Password: user123
- Role: user

## Configuration

### No-Fly Zones

Configure in database seeder or via API:

```sql
INSERT INTO no_fly_zones (name, latitude, longitude, radius, created_at, updated_at)
VALUES ('Airport Zone', 31.9780, 35.8300, 5000, NOW(), NOW());
```

### Danger Classification Thresholds

Modify in `.env`:
```env
DANGER_HEIGHT_THRESHOLD=500
DANGER_SPEED_THRESHOLD=10
```

## Monitoring

**Check MQTT Connection**
```bash
php artisan mqtt:status
```

**View Logs**
```bash
tail -f storage/logs/laravel.log
```

## Production Deployment

1. Set `APP_ENV=production` in `.env`
2. Run `php artisan config:cache`
3. Run `php artisan route:cache`
4. Set up supervisor for MQTT subscriber:

```ini
[program:mqtt-subscriber]
process_name=%(program_name)s
command=php /path/to/artisan mqtt:subscribe
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/mqtt.log
```
