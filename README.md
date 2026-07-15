# Vehicle API

REST API for the Vehicle Inventory Management system.

Built with Symfony 7, PHP 8.4, MySQL 8.4, and Docker.

## Requirements

- Docker Desktop (or Docker Engine + Compose plugin)
- GNU Make (optional but recommended)

## First-time setup

```bash
# 1. Create the shared Docker network (only once, across both projects)
docker network create vehicle-network

# 2. Copy environment file
cp .env.example .env

# 3. Build and start containers
docker compose up -d --build

# 4. Install PHP dependencies
make install
# or: docker compose exec php composer install

# 5. Run database migrations
make migrate
# or: docker compose exec php bin/console doctrine:migrations:migrate --no-interaction
```

## Access

| Service     | URL                        |
|-------------|----------------------------|
| API         | http://localhost:8080       |
| phpMyAdmin  | http://localhost:8081       |
| MySQL       | localhost:3306              |

## Common commands

```bash
make up            # Start containers
make down          # Stop containers
make bash          # Shell into PHP container
make logs          # Tail logs
make migrate       # Run migrations
make migrate-diff  # Generate migration from entity changes
make cache-clear   # Clear Symfony cache
make test          # Run PHPUnit
```

## API base path

All endpoints are versioned under `/api/v1/`.

Example: `GET http://localhost:8080/api/v1/vehicles`

## Environment variables

| Variable              | Description                          | Default            |
|-----------------------|--------------------------------------|--------------------|
| `APP_ENV`             | Symfony environment                  | `dev`              |
| `APP_SECRET`          | Symfony secret (change in prod)      | —                  |
| `DATABASE_URL`        | Doctrine database DSN                | points to `mysql`  |
| `MYSQL_ROOT_PASSWORD` | MySQL root password                  | `rootpassword`     |
| `MYSQL_DATABASE`      | Database name                        | `vehicle_api`      |
| `NGINX_PORT`          | Host port for the API                | `8080`             |
| `PMA_PORT`            | Host port for phpMyAdmin             | `8081`             |
| `MYSQL_PORT`          | Host port for MySQL                  | `3306`             |

## Architecture

```
src/
├── Controller/Api/V1/   # Route handlers — thin, delegate to services
├── Entity/              # Doctrine ORM entities
├── Repository/          # Database query layer
├── Service/             # Business logic
├── DTO/                 # Request/Response shapes
└── EventListener/       # Cross-cutting concerns (e.g. error formatting)
```
