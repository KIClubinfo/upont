#!/bin/bash
# [AT'016] Script d'update pour Clubinfo

cd /server/upont
git pull

export SYMFONY_ENV=prod

cd front
npm install
bower update --allow-root
gulp build-js
gulp build-css

cd ../mobile
npm install
bower update --allow-root
gulp build-js
gulp build-css-light
gulp build-css-dark

cd ../back
composer self-update
composer update --no-dev --optimize-autoloader
php app/console cache:clear --env=prod --no-debug
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs
