#!/bin/bash

# Function to display usage
usage() {
  echo "Usage: $0 [testsuite|composer-script]"
  echo "Examples:"
  echo "  $0               # run all tests (composer test)"
  echo "  $0 leopard-user  # run phpunit --testsuite=leopard-user"
  echo "  $0 test:admin    # run composer test:admin"
  exit 1
}

# Check if Docker container is running
if ! docker-compose ps | grep -q web; then
  echo "Error: The 'web' container is not running. Start it first using 'docker-compose up'."
  exit 1
fi

# Decide which command to run
if [ "$#" -gt 1 ]; then
  usage
fi

if [ "$#" -eq 0 ]; then
  echo "Running full test suite inside the Docker container..."
  docker-compose exec web composer test
  exit $?
fi

ARG="$1"

if [[ "$ARG" == test:* ]]; then
  echo "Running composer $ARG inside the Docker container..."
  docker-compose exec web composer "$ARG"
  exit $?
else
  echo "Running phpunit --testsuite=$ARG inside the Docker container..."
  docker-compose exec web bash -lc "vendor/bin/phpunit --testsuite=$ARG"
  exit $?
fi

