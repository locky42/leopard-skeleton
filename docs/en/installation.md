# Installation

## Using Docker
1. Start the Docker containers:
    ```bash
    ./manage-docker.sh start
    ```
2. Run `./install.sh` to generate configuration files.
3. Edit the following configuration files:
     * `.env`
     * `./config/app.php`
     * `./config/routes.yaml`
     * `./public/.htaccess`
4. If necessary, modify the `Dockerfile` and `docker-compose.yaml` files.
5. Open the project in your browser at: [http://localhost:8080](http://localhost:8080).  
    The address may vary depending on the settings in `.env`.

## Deploying on LAMP

1. Install composer packages:
     ```bash
     composer install
     ```
2. Run `./install.sh` to generate configuration files.
3. Edit the following configuration files:
     * `.env`
     * `./config/app.php`
     * `./config/routes.yaml`
     * `./public/.htaccess`
4. If necessary, modify the `Dockerfile` and `docker-compose.yaml` files.
5. Open the project in your browser at the URL specified in the LAMP settings.
