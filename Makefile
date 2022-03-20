DC=docker-compose -f docker-compose.yml
DCTEST=docker-compose -f docker-compose-test.yml
EXEC=$(DC) exec
EXEC_TEST=$(DCTEST) exec
EXEC_PHP_TEST=$(EXEC_TEST) -T php_test

.DEFAULT_GOAL := help
.PHONY: help

help:
.PHONY : help
help : Makefile
	sed -n 's/^##//p' $<

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
start: up wait-database wait-aws run-migration

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
	./bin/dockerize -wait tcp://postgres:5432
	until $(DC) exec postgres sh /home/ping.sh; \
	do \
  		echo 'Database not created yet, sleeping 5 seconds.'; \
  		sleep 5; \
	done;
	echo 'Database created!'

## wait-aws			: Wait for aws connection
wait-aws:
	./bin/dockerize -wait tcp://aws:4566

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

## composer-dumpautoload		: Dump autoload
composer-dumpautoload:
	$(EXEC) php composer dumpautoload

## composer-update		: Update all composer dependencies
composer-update:
	$(EXEC) php composer update

## run-migration			: Run migrations
run-migration:
	$(EXEC) php bin/console d:m:m -n

##
## ---- Tests ----
##

## stop-test			: Destroy all test containers
stop-test:
	$(DCTEST) rm -f -s -v

## start-test			: Build and up all test containers
start-test: stop-test up-test wait-test-database load-test-migrations load-fixtures wait-test-aws

## up-test			: Up all the test containers
up-test:
	$(DCTEST) up --quiet-pull --force-recreate --build -d

## load-test-migrations		: Load the doctrine migrations inside the php test container
load-test-migrations:
	$(EXEC_PHP_TEST) bin/console d:m:m -n

## load-fixtures			: Load the doctrine fixtures
load-fixtures:
	$(EXEC_PHP_TEST) bin/console doctrine:fixtures:load -n

## composer-install-test		: Install all composer dependencies
composer-install-test:
	$(EXEC_TEST) -T php_test composer install

## up-database-test		: Up docker database
up-database-test:
	$(DCTEST) rm -sfv postgres_test
	$(DCTEST) up --remove-orphans --quiet-pull --build -d postgres_test

## wait-test-database		: Wait for database connection
wait-test-database:
	./bin/dockerize -wait tcp://localhost:5431 -timeout 30s
	until $(DCTEST) exec -T postgres_test sh /home/ping.sh; \
	do \
  		echo 'Test database not created yet, sleeping 5 seconds.'; \
  		sleep 5; \
	done;
	echo 'Test database created!'

## wait-test-aws			: Wait for aws connection
wait-test-aws:
	./bin/dockerize -wait tcp://localhost:4567 -timeout 30s

## bash-test			: Enter into php container
bash-test:
	$(EXEC_TEST) php_test /bin/bash

## pgsql-test			: Enter into postgres test container
pgsql-test:
	$(EXEC_TEST) postgres_test bash

## ps-test			: Docker ps test containers
ps-test:
	$(DCTEST) ps

## run-migration-test		: Run migrations
run-migration-test:
	$(EXEC_TEST) php_test bin/console d:m:m -n

## clean-html-test-file		: Remove all .html error file in tests
clean-html-test-file:
	find ./tests -type f -name '*TestError.html' -print -delete

##
## ---- Test Suites ----
##

## test				: Run all test suites
test: start-test composer-install-test wait-test-database wait-test-aws test-suite

## test-suite			: Run all test suites
test-suite: unit-test acceptance-test integration-test in-memory-test functional-test

## phpcs				: Run PHPCS Fixer
phpcs:
	$(EXEC) php vendor/bin/php-cs-fixer fix -v --allow-risky=yes --using-cache=no

## phpcs-ci			: Run phpcs into CI mode
phpcs-ci:
	$(EXEC_TEST) -T php_test vendor/bin/php-cs-fixer fix -n --allow-risky=yes --using-cache=no

## test-suite-ci			: Run all test suites for CI
test-suite-ci:
	$(EXEC_TEST) -T php_test vendor/bin/phpunit -vvv --no-logging -disallow-test-output --stderr --testdox --do-not-cache-result --no-coverage

## unit-test			: Run unit testing
unit-test:
	$(EXEC_TEST) php_test vendor/bin/phpunit --stop-on-failure --group=unit

## in-memory-test			: Run in-memory testing
in-memory-test:
	$(EXEC_TEST) php_test vendor/bin/phpunit --stop-on-failure --group=in-memory

## integration-test 		: Run integration testing
integration-test:
	$(EXEC_TEST) php_test vendor/bin/phpunit --stop-on-failure --group=integration

## functional-test		: Run functional testing
functional-test:
	$(EXEC_TEST) php_test vendor/bin/phpunit --stop-on-failure --group=functional

## acceptance-test		: Run functional testing
acceptance-test:
	$(EXEC_TEST) php_test vendor/bin/phpunit --stop-on-failure --group=acceptance

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
