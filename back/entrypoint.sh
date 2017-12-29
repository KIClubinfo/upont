#!/bin/sh

cd /app
composer install --no-dev --optimize-autoloader
bin/console cache:warmup --no-debug
bin/console doctrine:migration:migrate -n

# Hand off to the CMD
exec "$@"
