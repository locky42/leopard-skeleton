# Project Structure

```
├── src/
│   ├── Commands/            # Console commands
│   ├── Controllers/         # Controllers
│       ├── Site/            # Site section controllers
│   ├── Models/              # Models
│   ├── Services/            # Services
│   ├── views/               # Templates and blocks
│       ├── site/            # Section files
│           ├── blocks/      # Blocks
│           ├── layouts/     # Templates
│               ├── main.php # Main template
│               ├── main/    # Blocks for the main template
├── storage/                 # Storage
│   ├── database/            # Directory for sqlite database, if used
│   ├── logs/                # Logs
│       ├── xdebug/          # Directory for xdebug logs
├── tests/                   # Tests
├── config/                  # Configurations
├── public/                  # Public directory (index.php, .htaccess)
│   ├── assets/              # Public static files
│       ├── css              # Styles
│       ├── js               # Scripts
├── docker-compose.yml       # Docker configuration
├── composer.json            # Composer configuration
```
