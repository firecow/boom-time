#!/bin/bash
until mysql --defaults-file=/shell/sql.cnf --wait --connect-timeout=10 boom-time < /dumps/dump.sql
do
    echo "SQL import retrying in 5 seconds..."
    sleep 5
done
echo "SQL data imported successfully"
