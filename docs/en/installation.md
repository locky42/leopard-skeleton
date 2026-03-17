# Installation

## 1. Using Docker (with Makefile) — Recommended

1. **Run full automated installation:**
    ```bash
    make install
    ```
    *This command interactively runs `./install.sh` to let you choose your database configuration, installs Composer dependencies on your host machine, generates local SSL certificates, and spins up the Docker containers.*

2. **Edit configuration files (Optional):**
    If you need to tweak the default settings, edit the following files:
    * `.env`
    * `./config/app.php`
    * `./config/routes.yaml`
    * `./public/.htaccess`

3. **Access the project:**
    Open the project in your browser at: [http://localhost:8080](http://localhost:8080)

---

## 2. Using Docker (Manually)

1. **Generate configuration files & set permissions:**
    ```bash
    ./install.sh
    ```
    *Choose your database driver and setup your environment variables via the interactive CLI menu.*

2. **Edit configuration files (Optional):**
    Review and update the generated configuration files if needed:
    * `.env`
    * `./config/app.php`
    * `./config/routes.yaml`
    * `./public/.htaccess`

3. **Start the Docker containers:**
    ```bash
    docker compose up -d --build
    ```

4. **Install dependencies inside the container:**
    ```bash
    docker compose exec web composer install
    ```

5. **Access the project:**
    Open the project in your browser at the port specified in your `.env` file (default is [http://localhost:8080](http://localhost:8080)).

---

## 3. Deploying on LAMP

1. **Generate configuration files:**
    ```bash
    ./install.sh
    ```

2. **Install composer packages locally:**
    ```bash
    composer install
    ```
    *If you face platform version issues with your host PHP, use `composer install --ignore-platform-reqs`.*

3. **Configure environment:**
    Edit the generated configuration files to match your local Apache environment settings:
    * `.env`
    * `./config/app.php`
    * `./config/routes.yaml`
    * `./public/.htaccess`

4. **Access the project:**
    Open the project in your browser using the local virtual host URL configured in your Apache server.
