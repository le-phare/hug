PHP_VERSION ?= 7.2
COMPOSER_HOME ?= $(PWD)/.composer
DOCKER_OPTIONS = -v $(PWD)/:/var/www/symfony -v $(COMPOSER_HOME):/tmp/composer -e COMPOSER_HOME=/tmp/composer
DOCKER = docker run --rm -u $(shell id -u):$(shell id -g) $(DOCKER_OPTIONS) -it lephare/php:$(PHP_VERSION)

build: install 
	$(DOCKER) vendor/bin/box compile

install: 
	$(DOCKER) composer install
	$(DOCKER) composer bin box require --dev humbug/box

clean: 
	rm -fr vendor* hug.phar

