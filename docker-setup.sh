#!/bin/bash

set -e

echo "================================================"
echo "Sager Drone System - Docker Setup"
echo "================================================"
echo ""

# Build and start containers
echo "Building and starting Docker containers..."
docker-compose up -d

# Wait for MySQL to be ready
echo ""
echo "Waiting for MySQL to be ready..."
sleep 10

# Install dependencies
echo ""
echo "Installing Composer dependencies..."
docker-compose exec -T app composer install --optimize-autoloader

# Generate keys
echo ""
echo "Generating application key..."
docker-compose exec -T app php artisan key:generate

echo ""
echo "Generating passport keys..."
docker-compose exec -T app php artisan passport:install

# Run migrations
echo ""
echo "Running migrations..."
docker-compose exec -T app php artisan migrate --force

# Seed database
echo ""
echo "Seeding database..."
docker-compose exec -T app php artisan db:seed

# Generate Swagger docs
echo ""
echo "Generating API documentation..."
docker-compose exec -T app php artisan l5-swagger:generate

# Set permissions
echo ""
echo "Setting permissions..."
docker-compose exec -T app chmod -R 755 storage bootstrap/cache

# Start MQTT subscriber
echo ""
echo "Starting MQTT subscriber in background..."
docker-compose exec -d app php artisan mqtt:subscribe

echo ""
echo "================================================"
echo "Docker setup completed successfully!"
echo "================================================"
echo ""
echo "Application is running at: http://localhost:8000"
echo "API Documentation: http://localhost:8000/api/documentation"
echo "MQTT Broker: localhost:1883"
echo "MySQL Database: localhost:3307"
echo ""
echo "Default credentials are in README.md"
echo ""
echo "Useful commands:"
echo "- View logs: docker-compose logs -f"
echo "- Stop containers: docker-compose down"
echo "- Restart: docker-compose restart"
echo "- Access shell: docker-compose exec app bash"
echo ""
