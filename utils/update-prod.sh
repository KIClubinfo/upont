#!/bin/bash
# [AT'016] Script d'update pour Clubinfo

sudo ls > /dev/null
cd /srv/upont
touch back/app/cache/maintenance.lock
git pull

export SYMFONY_ENV=prod

cd front
sudo npm install
bower update --allow-root
gulp build-html --type=production
gulp build-templates --type=production
gulp build-js --type=production
gulp build-css --type=production
gulp copy-fonts

cd ../back
sudo composer self-update
composer install --no-dev --optimize-autoloader
php app/console cache:clear --env=prod --no-debug
php app/console do:mi:mi -n
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs
rm app/cache/maintenance.lock

cd ..
./utils/newrelic-deploy.sh
