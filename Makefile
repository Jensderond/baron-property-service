ifndef APP_ENV
	include .env
	EXEC_PHP       = /usr/local/bin/ddev exec php
else
	EXEC_PHP       = /usr/bin/php
endif

SYMFONY        = $(EXEC_PHP) ./bin/console
COMPOSER       = $(EXEC_PHP) composer

migration:
	$(SYMFONY) cache:clear
	$(SYMFONY) doctrine:cache:clear-metadata
	$(SYMFONY) doctrine:migrations:diff --allow-empty-diff
	$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration

reset-db:
	$(SYMFONY) doctrine:database:drop --force
	$(SYMFONY) doctrine:database:create
	$(SYMFONY) doctrine:migrations:migrate --no-interaction
	$(SYMFONY) app:import-properties

import:
	$(SYMFONY) app:import-properties

schema:
	$(SYMFONY) api:openapi:export --yaml > schema.yaml

.PHONY: migrations reset-db import schema
