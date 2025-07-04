#!/bin/bash

# Function to display usage
usage() {
  echo "Usage: $0 {install|update|dump-autoload}"
  exit 1
}

# Check if a parameter is provided
if [ $# -eq 0 ]; then
  usage
fi

# Perform actions based on the parameter
case $1 in
  install)
    echo "Running composer install in the Docker container..."
    docker-compose exec web composer install
    ;;
  update)
    echo "Running composer update in the Docker container..."
    docker-compose exec web composer update
    ;;
  dump-autoload)
    echo "Running composer dump-autoload in the Docker container..."
    docker-compose exec web composer dump-autoload
    ;;
  *)
    usage
    ;;
esac