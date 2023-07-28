#!/bin/bash

docker compose up -d
docker compose exec php composer install
docker compose exec php bin/console --no-interaction doctrine:migrations:migrate