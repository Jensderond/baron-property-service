ifndef APP_ENV
	include .env
	EXEC_PHP       = /opt/homebrew/bin/ddev exec php
else
	EXEC_PHP       = /usr/bin/php
endif

SYMFONY        = $(EXEC_PHP) ./bin/console
COMPOSER       = $(EXEC_PHP) composer

migration:
	$(SYMFONY) cache:clear
	$(SYMFONY) doctrine:cache:clear-metadata
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration

recreate-db:
	$(SYMFONY) doctrine:database:drop --force
	$(SYMFONY) doctrine:database:create

reset-db:
	$(SYMFONY) doctrine:database:drop --force
	$(SYMFONY) doctrine:database:create
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration
	$(SYMFONY) app:import-properties
	$(SYMFONY) app:import-projects

php: up
	ddev exec php \
		$(filter-out $@,$(MAKECMDGOALS))

composer: up
	ddev composer \
		$(filter-out $@,$(MAKECMDGOALS))

lint: up
	ddev exec php vendor/bin/php-cs-fixer fix src

rector: up
	ddev exec php vendor/bin/rector

import:
	$(SYMFONY) app:import-properties
	$(SYMFONY) app:import-projects
	$(SYMFONY) app:import-bog-objects

schema:
	$(SYMFONY) api:openapi:export --yaml > schema.yaml && bash ./fix-schema.sh

.PHONY: migrations reset-db import lint schema up php composer

up:
	if [ ! "$$(ddev describe | grep OK)" ]; then \
		ddev auth ssh; \
		ddev start; \
		ddev composer install; \
		ddev exec npm install; \
	fi

%:
	@:
