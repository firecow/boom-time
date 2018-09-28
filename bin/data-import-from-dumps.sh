#!/bin/bash
docker exec -w "//shell/" php-dev-cli sh 'mysqlimport.sh'
docker exec -w "//shell/" php-dev-cli sh 'mongoimport.sh'
