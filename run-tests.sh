#!/bin/bash

# Function to display usage
usage() {
  echo "Usage: $0"
  echo "Runs PHPUnit tests inside the Docker container."
  exit 1
}

# Check if Docker container is running
if ! docker-compose ps | grep -q web; then
  echo "Error: The 'web' container is not running. Start it first using 'docker-compose up'."
  exit 1
fi

# Run PHPUnit tests
echo "Running PHPUnit tests inside the Docker container..."
docker-compose exec web vendor/bin/phpunit tests
