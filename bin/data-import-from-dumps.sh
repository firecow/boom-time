#!/bin/bash
docker-compose run -w "//shell/" php-dev-cli sh 'mysqlimport.sh'
docker-compose run -w "//shell/" php-dev-cli sh 'mongoimport.sh'
