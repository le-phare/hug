build:
	docker-compose run --rm php composer install && \
    docker-compose run --rm php composer bin box require --dev humbug/box && \
    docker-compose run --rm php vendor/bin/box compile
