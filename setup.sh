#!/bin/bash

set -e

echo "================================================"
echo "Setup Script"
echo "================================================"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
else
    echo ".env file already exists, skipping..."
fi

# Install Composer dependencies
echo ""
echo "Installing Composer dependencies..."
composer install --optimize-autoloader

# Generate application key
echo ""
echo "Generating application key..."
php artisan key:generate

# Generate JWT secret
echo ""
echo "Generating passport keys..."
php artisan passport:install

# Run migrations
echo ""
read -p "Do you want to run database migrations? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Running migrations..."
    php artisan migrate --force
fi

# Seed database
echo ""
read -p "Do you want to seed the database with sample data? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Seeding database..."
    php artisan db:seed
fi

# Generate Swagger documentation
echo ""
echo "Generating API documentation..."
php artisan l5-swagger:generate

# Set permissions
echo ""
echo "Setting permissions..."
chmod -R 755 storage bootstrap/cache

echo ""
echo "================================================"
echo "Setup completed successfully!"
echo "================================================"
echo ""
echo "Default Admin Credentials:"
echo "Email: admin@admin.com"
echo "Password: admin123"
echo ""
echo "Default User Credentials:"
echo "Email: user@user.com"
echo "Password: user123"
echo ""
echo "Next steps:"
echo "1. Configure your .env file with database and MQTT settings"
echo "2. Start the application: php artisan serve"
echo "3. Start MQTT subscriber: php artisan mqtt:subscribe"
echo "4. Access API documentation: http://localhost:8000/api/documentation"
echo ""
echo "For Docker deployment:"
echo "1. Run: docker-compose up -d"
echo "2. Run migrations: docker-compose exec app php artisan migrate"
echo "3. Run seeder: docker-compose exec app php artisan db:seed"
echo ""
