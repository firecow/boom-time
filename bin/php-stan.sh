#!/bin/bash
docker-compose run php-dev-cli bash -c "./vendor/bin/phpstan analyse --no-progress -l 7 src/"