#!/bin/bash
docker-compose run --rm -w "//shell/" php-dev-cli sh 'mysqlimport.sh'
