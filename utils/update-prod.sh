#!/bin/bash
# [AT'016] Script d'update pour Clubinfo

git pull
export SYMFONY_ENV=prod

cd /server/upont/front
npm install
bower update --allow-root
grunt build

cd ../mobile
npm install
grunt build

cd ../back
composer self-update
composer update --no-dev --optimize-autoloader
php app/console cache:clear --env=prod --no-debug
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs
php app/check.php
cp web/.htaccess.prod web/.htaccess
