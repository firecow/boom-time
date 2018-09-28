#!/bin/bash
docker-compose run php-dev-cli php -c /usr/local/etc/php/conf.d/php.ini run_system_tests.php
