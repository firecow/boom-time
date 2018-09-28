#!/bin/bash
until mongoimport -h 'mongo' --db 'simple-php' --collection 'photos' --drop --file '/dumps/photos.jsonl' --quiet
do
    echo "Mongo import retrying in 5 seconds..."
    sleep 5
done
echo "Mongo data imported successfully"