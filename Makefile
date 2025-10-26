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
	@echo '$(YELLOW)Tokyo Trip Planner - Docker Commands:$(NC)'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

build: ## Build Docker image
	@echo "$(YELLOW)Building Docker image...$(NC)"
	$(DOCKER_COMPOSE) build
	@echo "$(GREEN)✓ Build complete$(NC)"

up: ## Start containers
	@echo "$(YELLOW)Starting containers...$(NC)"
	$(DOCKER_COMPOSE) up -d
	@echo "$(GREEN)✓ Containers started$(NC)"
	@echo "$(BLUE)App running at: http://localhost:8000$(NC)"

down: ## Stop containers
	@echo "$(YELLOW)Stopping containers...$(NC)"
	$(DOCKER_COMPOSE) down
	@echo "$(GREEN)✓ Containers stopped$(NC)"

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
	@echo "$(RED)WARNING: This will remove all containers and volumes!$(NC)"
	@echo "Are you sure? [y/N] "; \
	@read REPLY; \
	if [ "$$REPLY" = "y" ] || [ "$$REPLY" = "Y" ]; then \
		$(DOCKER_COMPOSE) down -v; \
		echo "$(GREEN)✓ Cleaned up$(NC)"; \
	fi

rebuild: clean build up ## Clean, rebuild, and start

migrate: ## Run database migrations
	@echo "$(YELLOW)Running migrations...$(NC)"
	$(DOCKER_COMPOSE) exec app php artisan migrate
	@echo "$(GREEN)✓ Migrations complete$(NC)"

migrate-fresh: ## Fresh migration (WARNING: destroys data)
	@echo "$(RED)WARNING: This will destroy all data!$(NC)"
	@echo "Are you sure? [y/N] "; \
	@read REPLY; \
	if [ "$$REPLY" = "y" ] || [ "$$REPLY" = "Y" ]; then \
		$(DOCKER_COMPOSE) exec app php artisan migrate:fresh; \
		echo "$(GREEN)✓ Fresh migration complete$(NC)"; \
	fi

seed: ## Seed database
	$(DOCKER_COMPOSE) exec app php artisan db:seed

artisan: ## Run artisan command (usage: make artisan cmd="route:list")
	$(DOCKER_COMPOSE) exec app php artisan $(cmd)

composer: ## Run composer command (usage: make composer cmd="install")
	$(DOCKER_COMPOSE) exec app composer $(cmd)

install: build up migrate ## Complete installation
	@echo "$(GREEN)✓ Installation complete!$(NC)"
	@echo "$(BLUE)Visit: http://localhost:8000$(NC)"

stop: down ## Alias for down

start: up ## Alias for up

status: ps ## Alias for ps

# Production commands
prod-build: ## Build production image
	@echo "$(YELLOW)Building production image...$(NC)"
	$(DOCKER) build -t $(APP_NAME):latest .
	@echo "$(GREEN)✓ Production image built$(NC)"

prod-run: ## Run production container (without docker-compose)
	@echo "$(YELLOW)Starting production container...$(NC)"
	$(DOCKER) run -d \
--name $(APP_NAME) \
-p 8000:8000 \
-e APP_ENV=production \
-e APP_DEBUG=false \
--restart unless-stopped \
$(APP_NAME):latest
	@echo "$(GREEN)✓ Container started$(NC)"
	@echo "$(BLUE)App running at: http://localhost:8000$(NC)"

prod-stop: ## Stop production container
	$(DOCKER) stop $(APP_NAME)
	$(DOCKER) rm $(APP_NAME)

prod-logs: ## Show production container logs
	$(DOCKER) logs -f $(APP_NAME)

# Database backup
backup: ## Backup database
	@echo "$(YELLOW)Backing up database...$(NC)"
	@mkdir -p backups
	$(DOCKER_COMPOSE) exec -T db mysqldump -u tokyo_user -ptokyo_password tokyo_trips > backups/backup-$(shell date +%Y%m%d-%H%M%S).sql
	@echo "$(GREEN)✓ Backup created in backups/$(NC)"

restore: ## Restore database (usage: make restore file=backup.sql)
	@echo "$(YELLOW)Restoring database...$(NC)"
	$(DOCKER_COMPOSE) exec -T db mysql -u tokyo_user -ptokyo_password tokyo_trips < $(file)
	@echo "$(GREEN)✓ Database restored$(NC)"

# Maintenance
optimize: ## Optimize application
	$(DOCKER_COMPOSE) exec app php artisan config:cache
	$(DOCKER_COMPOSE) exec app php artisan route:cache
	$(DOCKER_COMPOSE) exec app php artisan view:cache
	@echo "$(GREEN)✓ Application optimized$(NC)"

clear-cache: ## Clear all caches
	$(DOCKER_COMPOSE) exec app php artisan config:clear
	$(DOCKER_COMPOSE) exec app php artisan route:clear
	$(DOCKER_COMPOSE) exec app php artisan view:clear
	$(DOCKER_COMPOSE) exec app php artisan cache:clear
	@echo "$(GREEN)✓ Caches cleared$(NC)"
