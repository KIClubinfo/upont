#!/bin/bash
# [AT'016] Script d'update pour Clubinfo

cd /srv/upont
touch back/app/cache/maintenance.lock
git pull

export SYMFONY_ENV=prod

cd front
npm install
bower update --allow-root
gulp build-html --type=production
gulp build-js --type=production
gulp build-css --type=production

cd ../mobile
npm install
bower update --allow-root
gulp build-js --type=production
gulp build-css-light --type=production
gulp build-css-dark --type=production

cd ../back
sudo composer self-update
composer update --no-dev --optimize-autoloader
php app/console cache:clear --env=prod --no-debug
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs
rm app/cache/maintenance.lock
