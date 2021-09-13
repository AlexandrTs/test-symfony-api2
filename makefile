# make composer COMMAND="install --no-suggest --prefer-dist"
composer:
	docker pull composer:2.1.6
	docker run --rm \
	--network host \
	--volume $(CURDIR):${HOME} \
	--volume ${HOME}/.ssh:${HOME}/.ssh:ro \
	--volume /etc/passwd:/etc/passwd:ro \
	--volume /etc/group:/etc/group:ro \
	--user ${shell id -u}:$(shell id -g) \
	--env HOME=${HOME} \
	--env COMPOSER_HOME=${HOME} \
	--workdir ${HOME}  \
	--interactive \
	composer:2.1.6 \
	$(COMMAND)

# make php COMMAND="./vendor/bin/codecept run --step"
# make php COMMAND="./bin/console make:entity"
php:
	docker build -q ./docker/php -t symfonyapi
	docker run --rm \
	--network host \
	--volume $(CURDIR):${HOME} \
	--volume ${HOME}/.ssh:${HOME}/.ssh:ro \
	--volume /etc/passwd:/etc/passwd:ro \
	--volume /etc/group:/etc/group:ro \
	--user ${shell id -u}:$(shell id -g) \
	--env HOME=${HOME} \
	--env COMPOSER_HOME=${HOME} \
	--workdir ${HOME}  \
	--interactive \
	symfonyapi \
	$(COMMAND)

server:
	docker build -q ./docker/php -t symfonyapi
	docker run --rm \
	--network host \
	--volume $(CURDIR):${HOME} \
	--volume ${HOME}/.ssh:${HOME}/.ssh:ro \
	--volume /etc/passwd:/etc/passwd:ro \
	--volume /etc/group:/etc/group:ro \
	--user ${shell id -u}:$(shell id -g) \
	--env HOME=${HOME} \
	--env COMPOSER_HOME=${HOME} \
	--workdir ${HOME}  \
	--interactive \
	symfonyapi \
	php -S localhost:8000 -t public

server-phiremock:
	docker build -q ./docker/php -t symfonyapi
	docker run --rm \
	--network host \
	--volume $(CURDIR):${HOME} \
	--volume ${HOME}/.ssh:${HOME}/.ssh:ro \
	--volume /etc/passwd:/etc/passwd:ro \
	--volume /etc/group:/etc/group:ro \
	--user ${shell id -u}:$(shell id -g) \
	--env HOME=${HOME} \
	--env COMPOSER_HOME=${HOME} \
	--workdir ${HOME}  \
	--interactive \
	symfonyapi \
	./vendor/bin/phiremock -i 0.0.0.0 -p 8002 -e tests/_data/phiremock-expectations/
