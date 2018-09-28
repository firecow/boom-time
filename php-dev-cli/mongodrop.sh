#!/bin/bash
mongo -h 'mongo' simple-php --eval "db.dropDatabase()" --quiet
