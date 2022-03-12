DC=docker-compose -f docker-compose.yml
DCTEST=docker-compose -f docker-compose-test.yml
EXEC=$(DC) exec
EXEC_TEST=$(DCTEST) exec

.DEFAULT_GOAL := help
.PHONY: help

help:
.PHONY : help
help : Makefile
	@sed -n 's/^##//p' $<

##
## ---- Install ----
##

.PHONY: install

## install			: Install everything
install: env up-php composer-install down

## env				: Init a .env.local
env:
	touch .env.local .env.prod.local

## clean				: Clean var/*
clean:
	@sudo rm -rf var/*

##
## ---- Dev ----
##

## start				: Start local development
start: up wait-database

## pull				: Pull all images
pull:
	$(DC) pull

## pull				: Build all images
build:
	$(DC) build

## up				: Build and up all containers
up:
	$(DC) rm -sfv postgres
	$(DC) up --force-recreate --quiet-pull --build -d

## up-php				: Up docker php
up-php:
	$(DC) up --force-recreate -d php

## up-nginx			: Up docker nginx
up-nginx:
	$(DC) up --force-recreate -d nginx

## up-database			: Up docker database
up-database:
	$(DC) rm -sfv postgres
	$(DC) up --remove-orphans --quiet-pull --build -d postgres

## wait-database			: Wait for database connection
wait-database:
	./bin/dockerize -wait tcp://0.0.0.0:5440
	until $(DC) exec postgres sh /home/health.sh recipe postgres; \
	do \
  		echo 'Database not created yet, sleeping 5 seconds.'; \
  		sleep 5; \
	done;
	echo 'Database created!'

## wait-aws			: Wait for aws connection
wait-aws:
	./bin/dockerize -wait tcp://0.0.0.0:4566

## ps				: Docker ps local containers
ps:
	$(DC) ps

## down				: Down all containers
down:
	$(DC) rm -f -s -v

## stop				: Stop local development
stop:
	$(DC) rm -f -s -v

## bash				: Run into php container
bash:
	$(EXEC) php bash

## pgsql				: Run into postgres container
pgsql:
	$(EXEC) postgres bash

## composer-install		: Install all composer dependencies
composer-install:
	$(EXEC) php composer install
	$(EXEC) php composer dev

## composer-update		: Update all composer dependencies
composer-update:
	$(EXEC) php composer update

##
## ---- Tests ----
##

## stop-test			: Destroy all test containers
stop-test:
	$(DCTEST) rm -f -s -v

## start-test			: Build and up all test containers
start-test: stop-test
	$(DCTEST) up --quiet-pull --force-recreate --build -d

## composer-install-test		: Install all composer dependencies
composer-install-test:
	$(EXEC_TEST) -T php_test composer install

## up-database-test		: Up docker database
up-database-test:
	$(DCTEST) rm -sfv postgres_test
	$(DCTEST) up --remove-orphans --quiet-pull --build -d postgres_test

## wait-test-database		: Wait for database connection
wait-test-database:
	./bin/dockerize -wait tcp://0.0.0.0:5441
	until $(DCTEST) exec -T postgres_test sh /home/health.sh recipe postgres; \
	do \
  		echo 'Test database not created yet, sleeping 5 seconds.'; \
  		sleep 5; \
	done;
	echo 'Test database created!'

## wait-test-aws			: Wait for aws connection
wait-test-aws:
	./bin/dockerize -wait tcp://0.0.0.0:4567

## bash-test			: Enter into php container
bash-test:
	$(EXEC_TEST) php_test /bin/bash

## pgsql-test			: Enter into postgres test container
pgsql-test:
	$(EXEC_TEST) postgres_test bash

## ps-test			: Docker ps test containers
ps-test:
	$(DCTEST) ps

## clean-html-test-file		: Remove all .html error file in tests
clean-html-test-file:
	find ./tests -type f -name '*TestError.html' -print -delete

##
## ---- Test Suites ----
##

## test				: Run all test suites
test: start-test composer-install-test wait-test-database unit-test integration-test functional-test

## test-ci			: Test instructions for CI only
test-ci: env start-test composer-install-test wait-test-database test-suite-ci stop-test

## phpcs				: Run PHPCS Fixer
phpcs:
	$(EXEC) php vendor/bin/php-cs-fixer fix -v --allow-risky=yes --using-cache=no

## test-suite-ci			: Run all test suites for CI
test-suite-ci:
	$(EXEC_TEST) -T php_test vendor/bin/phpunit -vvv --no-logging -disallow-test-output --stderr --testdox --do-not-cache-result --no-coverage
	$(EXEC_TEST) -T php_test vendor/bin/php-cs-fixer fix -n --allow-risky=yes --using-cache=no

## unit-test			: Run unit testing
unit-test:
	$(EXEC_TEST) php_test vendor/bin/phpunit --stop-on-failure --group=unit

## integration-test 		: Run integration testing
integration-test:
	$(EXEC_TEST) php_test vendor/bin/phpunit --stop-on-failure --group=integration

## functional-test		: Run functional testing
functional-test:
	$(EXEC_TEST) php_test vendor/bin/phpunit --stop-on-failure --group=functional

##
## ---- Logs ----
##

## logs				: Logs containers
logs:
	$(DC) logs -f

## logs-database			: Docker logs -f database
logs-database:
	$(DC) logs -f postgres

## logs-database-test		: Docker logs -f test database
logs-database-test:
	$(DCTEST) logs -f postgres_test
