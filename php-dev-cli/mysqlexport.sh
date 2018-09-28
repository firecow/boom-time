#!/bin/bash
mysqldump --defaults-file='/shell/sql.cnf' --default-character-set=utf8mb4 --skip-disable-keys --skip-add-locks --skip-comments --skip-dump-date --extended-insert=FALSE boom-time
