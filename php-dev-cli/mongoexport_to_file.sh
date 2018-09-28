#!/bin/bash
mongoexport -h 'mongo' --db 'simple-php' --collection 'photos' --pretty --out '/dumps/photos.jsonl' --quiet
