#!/bin/sh

cd /app
bin/console doctrine:migration:migrate -n

# Hand off to the CMD
exec "$@"
