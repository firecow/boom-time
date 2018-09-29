#!/bin/bash
docker-compose run --rm php-dev-cli bash -c "./vendor/bin/phpstan analyse --no-progress -l 7 src/"