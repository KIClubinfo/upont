#!/bin/bash
# [AT'016] Met automatiquement tout à jour, à lancer à chaque pull/mise à jour des modèles, configurable dans un hook post-receive

sudo ls > /dev/null
cd front
sudo npm install
bower update
gulp build-js
gulp build-css

cd ../mobile
sudo npm install
bower update
grunt build

cd ../back
sudo composer self-update
composer update
php app/console cache:clear
sudo rm -rf app/cache/*
sudo rm -rf app/logs/*
php app/console doctrine:schema:drop --force
php app/console doctrine:schema:create
echo "Y" | php app/console doctrine:fixtures:load
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs
