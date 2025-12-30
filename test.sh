#!/bin/bash

echo "Running Sager Drone System Tests..."
echo "===================================="
echo ""

# Run PHPUnit tests
echo "Running PHPUnit tests..."
php artisan test

# Check test results
if [ $? -eq 0 ]; then
    echo ""
    echo "✅ All tests passed!"
else
    echo ""
    echo "❌ Some tests failed!"
    exit 1
fi
