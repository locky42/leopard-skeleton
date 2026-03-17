#!/bin/bash

# --- Automatically fix permissions before starting installation ---
# Force change owner to www-data (UID 33) and open full write permissions (777).
# This keeps all your existing databases and logs safe, but grants Apache/Xdebug access.
docker run --rm -v "$(pwd)/storage:/data" alpine sh -c "chown -R 33:33 /data && chmod -R 777 /data" 2>/dev/null

# 1. Interactive configuration selection
echo "Select a database for the project:"
echo "1) SQLite"
echo "2) MySQL"
echo "3) MariaDB"
echo "4) PostgreSQL"
echo "5) PostgreSQL + pgvector"
echo "6) No database (Web server only)"
read -p "Enter number (1-6): " db_choice

case $db_choice in
    1) DB_TYPE="sqlite" ;;
    2) DB_TYPE="mysql" ;;
    3) DB_TYPE="mariadb" ;;
    4) DB_TYPE="postgres" ;;
    5) DB_TYPE="postgres-pgvector" ;;
    6) DB_TYPE="none" ;;
    *) echo "Invalid choice, aborting."; exit 1 ;;
esac

echo ""

# --- Dynamically detect default container name ---
DEFAULT_CONTAINER_NAME=""
if [ -f ".env" ]; then
    DEFAULT_CONTAINER_NAME=$(grep -E "^DOCKER_CONTAINER_NAME=" .env | cut -d'=' -f2)
fi

if [ -z "$DEFAULT_CONTAINER_NAME" ] && [ -f ".env.example" ]; then
    DEFAULT_CONTAINER_NAME=$(grep -E "^DOCKER_CONTAINER_NAME=" .env.example | cut -d'=' -f2)
fi

# Final fallback if both files don't have the variable
DEFAULT_CONTAINER_NAME=${DEFAULT_CONTAINER_NAME:-web-multitool}

read -p "Enter DOCKER_CONTAINER_NAME [default: $DEFAULT_CONTAINER_NAME]: " container_name
CONTAINER_NAME=${container_name:-$DEFAULT_CONTAINER_NAME}

echo "--------------------------------"
echo "Selected mode: $DB_TYPE"
echo "Container prefix: $CONTAINER_NAME"
echo "--------------------------------"

# 2. Process all .example files
for example_file in $(find . -type d \( -name ".git" \) -prune -o -type f -name "*.example" -print); do
    target_file="${example_file%.example}"
    
    if [ "$target_file" == "./docker-compose.yml" ] || [ ! -f "$target_file" ]; then
        echo "Generating $target_file from template..."
        cp "$example_file" "$target_file"
    else
        example_keys=$(grep -o '^[^#]*=' "$example_file" | sed 's/=.*//')
        target_keys=$(grep -o '^[^#]*=' "$target_file" | sed 's/=.*//')
        missing_keys=$(comm -23 <(echo "$example_keys" | sort) <(echo "$target_keys" | sort))
        extra_keys=$(comm -13 <(echo "$example_keys" | sort) <(echo "$target_keys" | sort))

        if [ -n "$missing_keys" ] || [ -n "$extra_keys" ]; then
            echo "Key differences found between $example_file and $target_file:"
            [ -n "$missing_keys" ] && echo "Keys in $example_file but not in $target_file: $missing_keys"
            [ -n "$extra_keys" ] && echo "Keys in $target_file but not in $example_file: $extra_keys"
        else
            echo "Skipping $target_file, keys are identical"
        fi
    fi
done

# --- Local SSL generation for development ---
mkdir -p ./Docker/apache2/ssl
if [ ! -f "./Docker/apache2/ssl/project.pem" ]; then
    echo "Local SSL certificates not found. Generating..."
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout ./Docker/apache2/ssl/project.key \
        -out ./Docker/apache2/ssl/project.pem \
        -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost" 2>/dev/null
    chmod 644 ./Docker/apache2/ssl/project.pem ./Docker/apache2/ssl/project.key
fi

# 3. Smart modification of the generated .env file
if [ -f ".env" ]; then
    # Update DOCKER_CONTAINER_NAME
    if grep -q "^DOCKER_CONTAINER_NAME=" .env; then
        sed -i "s|^DOCKER_CONTAINER_NAME=.*|DOCKER_CONTAINER_NAME=$CONTAINER_NAME|" .env
    else
        echo "DOCKER_CONTAINER_NAME=$CONTAINER_NAME" >> .env
    fi

    # Update DB ports configuration
    if [[ "$DB_TYPE" == "mysql" || "$DB_TYPE" == "mariadb" ]]; then
        NEW_PORT="DOCKER_PORT_DB=3306:3306"
    elif [[ "$DB_TYPE" == "postgres" || "$DB_TYPE" == "postgres-pgvector" ]]; then
        NEW_PORT="DOCKER_PORT_DB=5432:5432"
    elif [[ "$DB_TYPE" == "sqlite" || "$DB_TYPE" == "none" ]]; then
        NEW_PORT="DOCKER_PORT_DB=# DOCKER_PORT_DB is not used"
    fi

    if grep -q "^DOCKER_PORT_DB=" .env; then
        sed -i "s|^DOCKER_PORT_DB=.*|$NEW_PORT|" .env
    else
        echo -e "\n$NEW_PORT" >> .env
    fi
    echo "Updated .env file"
fi

# 4. Smart modification of docker-compose.yml for volumes and services
if [ -f "docker-compose.yml" ]; then
    if [[ "$DB_TYPE" == "sqlite" || "$DB_TYPE" == "none" ]]; then
        sed -i '/^[[:space:]]\{2\}db:/,/^[[:alpha:]]/ { /^[[:alpha:]]/!d; }' docker-compose.yml
        sed -i '/^volumes:/,/^[[:alpha:]]/ { /db_data/d; }' docker-compose.yml
        sed -i '/^volumes:[[:space:]]*$/d' docker-compose.yml
        echo "Database container and its volumes removed from docker-compose.yml"
        
    elif [ "$DB_TYPE" == "mysql" ]; then
        sed -i 's|image: .*|image: mysql:8.0|' docker-compose.yml
        sed -i 's|- db_data:.*|- db_data:/var/lib/mysql|' docker-compose.yml
        echo "docker-compose.yml configured for MySQL"

    elif [ "$DB_TYPE" == "mariadb" ]; then
        sed -i 's|image: .*|image: mariadb:10.11|' docker-compose.yml
        sed -i 's|- db_data:.*|- db_data:/var/lib/mysql|' docker-compose.yml
        echo "docker-compose.yml configured for MariaDB"
        
    elif [ "$DB_TYPE" == "postgres" ]; then
        sed -i 's|image: .*|image: postgres:16|' docker-compose.yml
        sed -i 's|- db_data:.*|- db_data:/var/lib/postgresql/data|' docker-compose.yml
        sed -i 's|MYSQL_ROOT_PASSWORD:.*|POSTGRES_PASSWORD: \${POSTGRES_PASSWORD}|' docker-compose.yml
        sed -i 's|MYSQL_DATABASE:.*|POSTGRES_DB: \${POSTGRES_DB}|' docker-compose.yml
        sed -i 's|MYSQL_USER:.*|POSTGRES_USER: \${POSTGRES_USER}|' docker-compose.yml
        sed -i 's|MYSQL_PASSWORD:.*|#|' docker-compose.yml
        sed -i '/^[[:space:]]*#[[:space:]]*$/d' docker-compose.yml
        echo "docker-compose.yml configured for PostgreSQL"
        
    elif [ "$DB_TYPE" == "postgres-pgvector" ]; then
        sed -i 's|image: .*|image: pgvector/pgvector:pg16|' docker-compose.yml
        sed -i 's|- db_data:.*|- db_data:/var/lib/postgresql/data|' docker-compose.yml
        sed -i 's|MYSQL_ROOT_PASSWORD:.*|POSTGRES_PASSWORD: \${POSTGRES_PASSWORD}|' docker-compose.yml
        sed -i 's|MYSQL_DATABASE:.*|POSTGRES_DB: \${POSTGRES_DB}|' docker-compose.yml
        sed -i 's|MYSQL_USER:.*|POSTGRES_USER: \${POSTGRES_USER}|' docker-compose.yml
        sed -i 's|MYSQL_PASSWORD:.*|#|' docker-compose.yml
        sed -i '/^[[:space:]]*#[[:space:]]*$/d' docker-compose.yml
        echo "docker-compose.yml configured for PostgreSQL + pgvector"
    fi
fi

# 5. SQLite initialization (only for choice #1)
if [ "$DB_TYPE" == "sqlite" ]; then
    mkdir -p storage/database
    if [ ! -f "storage/database/db.sqlite" ]; then
        echo "Creating database storage/database/db.sqlite..."
        touch storage/database/db.sqlite
    fi
fi

echo "--------------------------------"
echo "All configuration files have been successfully configured!"
