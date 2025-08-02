#!/bin/bash

# Function to display usage
usage() {
  echo "Usage: $0 {start|stop|reload}"
  exit 1
}

# Check if a parameter is provided
if [ $# -eq 0 ]; then
  usage
fi

# Perform actions based on the parameter
case $1 in
  start)
    echo "Starting Docker containers..."
    docker-compose up -d
    ;;
  stop)
    echo "Stopping Docker containers..."
    docker-compose down
    ;;
  reload)
    echo "Reloading Docker containers..."
    docker system prune -f
    docker volume prune -f
    docker-compose build --no-cache
    docker-compose up -d
    ;;
  *)
    usage
    ;;
esac
