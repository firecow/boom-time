#!/bin/bash
docker exec -w "//shell/" php-dev-cli sh 'mysqlexport_to_file.sh'
docker exec -w "//shell/" php-dev-cli sh 'mongoexport_to_file.sh'
