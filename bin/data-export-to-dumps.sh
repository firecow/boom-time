#!/bin/bash
docker-compose run --rm -w "//shell/" php-dev-cli sh 'mysqlexport_to_file.sh'
