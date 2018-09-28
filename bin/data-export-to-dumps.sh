#!/bin/bash
docker-compose run -w "//shell/" php-dev-cli sh 'mysqlexport_to_file.sh'
docker-compose run -w "//shell/" php-dev-cli sh 'mongoexport_to_file.sh'
