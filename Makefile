.PHONY: help build up down restart logs shell clean rebuild migrate

# Variables
APP_NAME = tokyo-trip-planner
DOCKER_COMPOSE = docker compose
DOCKER = docker
PHP_CONTAINER = $(APP_NAME)
DB_CONTAINER = $(APP_NAME)-db

# Colors
RED = \033[0;31m
GREEN = \033[0;32m
YELLOW = \033[1;33m
BLUE = \033[0;34m
NC = \033[0m # No Color

help: ## Show this help message
	@printf "$(YELLOW)Tokyo Trip Planner - Docker Commands:$(NC)\n"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

build: ## Build Docker image
	@printf "$(YELLOW)Building Docker image...$(NC)\n"
	$(DOCKER_COMPOSE) build
	@printf "$(GREEN)✓ Build complete$(NC)\n"

up: ## Start containers
	@printf "$(YELLOW)Starting containers...$(NC)\n"
	$(DOCKER_COMPOSE) up -d
	@printf "$(GREEN)✓ Containers started$(NC)\n"
	@printf "$(BLUE)App running at: http://localhost:8000$(NC)\n"

down: ## Stop containers
	@printf "$(YELLOW)Stopping containers...$(NC)\n"
	$(DOCKER_COMPOSE) down
	@printf "$(GREEN)✓ Containers stopped$(NC)\n"

restart: down up ## Restart containers

logs: ## Show container logs
	$(DOCKER_COMPOSE) logs -f

logs-app: ## Show app logs
	$(DOCKER_COMPOSE) logs -f app

logs-db: ## Show database logs
	$(DOCKER_COMPOSE) logs -f db

shell: ## Open shell in app container
	$(DOCKER_COMPOSE) exec app bash

shell-db: ## Open MySQL shell
	$(DOCKER_COMPOSE) exec db mysql -u tokyo_user -ptokyo_password tokyo_trips

ps: ## Show running containers
	$(DOCKER_COMPOSE) ps

clean: ## Remove containers and volumes
	@printf "$(RED)WARNING: This will remove all containers and volumes!$(NC)\n"
	@printf "Are you sure? [y/N] \n"; \
	read REPLY; \
	if [ "$$REPLY" = "y" ] || [ "$$REPLY" = "Y" ]; then \
		$(DOCKER_COMPOSE) down -v; \
		printf "$(GREEN)✓ Cleaned up$(NC)\n"; \
	fi

rebuild: clean build up ## Clean, rebuild, and start

migrate: ## Run database migrations
	@printf "$(YELLOW)Running migrations...$(NC)\n"
	$(DOCKER_COMPOSE) exec app php artisan migrate
	@printf "$(GREEN)✓ Migrations complete$(NC)\n"

migrate-fresh: ## Fresh migration (WARNING: destroys data)
	@printf "$(RED)WARNING: This will destroy all data!$(NC)\n"
	@printf "Are you sure? [y/N] \n"; \
	read REPLY; \
	if [ "$$REPLY" = "y" ] || [ "$$REPLY" = "Y" ]; then \
		$(DOCKER_COMPOSE) exec app php artisan migrate:fresh; \
		printf "$(GREEN)✓ Fresh migration complete$(NC)\n"; \
	fi

seed: ## Seed database
	$(DOCKER_COMPOSE) exec app php artisan db:seed

artisan: ## Run artisan command (usage: make artisan cmd="route:list")
	$(DOCKER_COMPOSE) exec app php artisan $(cmd)

composer: ## Run composer command (usage: make composer cmd="install")
	$(DOCKER_COMPOSE) exec app composer $(cmd)

install: build up migrate ## Complete installation
	@printf "$(GREEN)✓ Installation complete!$(NC)\n"
	@printf "$(BLUE)Visit: http://localhost:8000$(NC)\n"

stop: down ## Alias for down

start: up ## Alias for up

status: ps ## Alias for ps

# Production commands
prod-build: ## Build production image
	@printf "$(YELLOW)Building production image...$(NC)\n"
	$(DOCKER) build -t $(APP_NAME):latest .
	@printf "$(GREEN)✓ Production image built$(NC)\n"

prod-run: ## Run production container (without docker-compose)
	@printf "$(YELLOW)Starting production container...$(NC)\n"
	$(DOCKER) run -d \
--name $(APP_NAME) \
-p 8000:8000 \
-e APP_ENV=production \
-e APP_DEBUG=false \
--restart unless-stopped \
$(APP_NAME):latest
	@printf "$(GREEN)✓ Container started$(NC)\n"
	@printf "$(BLUE)App running at: http://localhost:8000$(NC)\n"

prod-stop: ## Stop production container
	$(DOCKER) stop $(APP_NAME)
	$(DOCKER) rm $(APP_NAME)

prod-logs: ## Show production container logs
	$(DOCKER) logs -f $(APP_NAME)

# Database backup
backup: ## Backup database
	@printf "$(YELLOW)Backing up database...$(NC)\n"
	@mkdir -p backups
	$(DOCKER_COMPOSE) exec -T db mysqldump -u tokyo_user -ptokyo_password tokyo_trips > backups/backup-$(shell date +%Y%m%d-%H%M%S).sql
	@printf "$(GREEN)✓ Backup created in backups/$(NC)\n"

restore: ## Restore database (usage: make restore file=backup.sql)
	@printf "$(YELLOW)Restoring database...$(NC)\n"
	$(DOCKER_COMPOSE) exec -T db mysql -u tokyo_user -ptokyo_password tokyo_trips < $(file)
	@printf "$(GREEN)✓ Database restored$(NC)\n"

# Maintenance
optimize: ## Optimize application
	$(DOCKER_COMPOSE) exec app php artisan config:cache
	$(DOCKER_COMPOSE) exec app php artisan route:cache
	$(DOCKER_COMPOSE) exec app php artisan view:cache
	@printf "$(GREEN)✓ Application optimized$(NC)\n"

clear-cache: ## Clear all caches
	$(DOCKER_COMPOSE) exec app php artisan config:clear
	$(DOCKER_COMPOSE) exec app php artisan route:clear
	$(DOCKER_COMPOSE) exec app php artisan view:clear
	$(DOCKER_COMPOSE) exec app php artisan cache:clear
	@printf "$(GREEN)✓ Caches cleared$(NC)\n"
