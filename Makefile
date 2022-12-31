EXEC_SERVER = docker container exec -it web_server

build:
	docker-compose build

up:
	docker-compose up -d --force-recreate

install: build up composer install-assets schema-update

start:
	docker-compose up -d --no-recreate --remove-orphans

stop:
	docker container stop $$(docker container ps -qa)

delete:
	docker container rm server database phpmyadmin mailer

enter:
	$(EXEC_SERVER) bash

list:
	docker-compose ps

cc:
	$(EXEC_SERVER) php bin/console c:c

cd:
	$(EXEC_SERVER) php bin/console doctrine:cache:clear-metadata
	$(EXEC_SERVER) php bin/console doctrine:cache:clear-query
	$(EXEC_SERVER) php bin/console doctrine:cache:clear-result

c: cd cc

reload-apache:
	$(EXEC_SERVER) /etc/init.d/apache2 reload

schema-update:
	$(EXEC_SERVER) php bin/console doctrine:schema:update --force

install-assets:
	$(EXEC_SERVER) php bin/console assets:install --symlink public

restart: stop start

composer:
	$(EXEC_SERVER) composer install