#!/bin/bash
while :
do
docker-compose run --rm php-dev-cli php update_armory_data.php
sleep 10s
done
