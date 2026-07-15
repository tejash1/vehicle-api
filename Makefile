.DEFAULT_GOAL := help

help: ## Show available commands
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-22s\033[0m %s\n", $$1, $$2}'

## ── Docker ──────────────────────────────────────────────────────────────────

up: ## Start containers in background
	docker compose up -d

down: ## Stop and remove containers
	docker compose down

build: ## Rebuild the PHP image
	docker compose build php

logs: ## Tail container logs
	docker compose logs -f

ps: ## Show running containers
	docker compose ps

## ── Application ─────────────────────────────────────────────────────────────

install: ## Run composer install inside the container
	docker compose exec php composer install

bash: ## Open a shell in the PHP container
	docker compose exec php sh

console: ## Run a Symfony console command  (usage: make console CMD="cache:clear")
	docker compose exec php bin/console $(CMD)

cache-clear: ## Clear the Symfony cache
	docker compose exec php bin/console cache:clear

## ── Database ─────────────────────────────────────────────────────────────────

migrate: ## Run pending migrations
	docker compose exec php bin/console doctrine:migrations:migrate --no-interaction

migrate-diff: ## Generate a migration from entity changes
	docker compose exec php bin/console doctrine:migrations:diff

migrate-status: ## Show migration status
	docker compose exec php bin/console doctrine:migrations:status

db-drop: ## Drop the database (DESTRUCTIVE)
	docker compose exec php bin/console doctrine:database:drop --force

db-create: ## Create the database
	docker compose exec php bin/console doctrine:database:create

db-reset: db-drop db-create migrate ## Drop, recreate, and migrate (DESTRUCTIVE)

## ── Testing ──────────────────────────────────────────────────────────────────

test: ## Run all PHPUnit tests
	docker compose exec php bin/phpunit

test-filter: ## Run a specific test  (usage: make test-filter FILTER=MyTest)
	docker compose exec php bin/phpunit --filter $(FILTER)
