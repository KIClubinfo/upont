#!/bin/bash
# [AT'016] Met automatiquement tout à jour, à lancer à chaque pull/mise à jour des modèles, configurable dans un hook post-receive

sudo ls > /dev/null
cd front
sudo npm install
bower update
gulp build-js
gulp build-css
gulp build-html

cd ../mobile
sudo npm install
bower update
gulp build-js
gulp build-css-light
gulp build-css-dark

cd ../back
sudo composer self-update
composer update
php app/console cache:clear
sudo rm -rf app/cache/*
sudo rm -rf app/logs/*
php app/console doctrine:schema:drop --force
php app/console doctrine:schema:create
rm web/uploads/exercices/*
rm web/uploads/files/*
rm web/uploads/images/*
rm web/uploads/tmp/*
echo "Y" | php app/console doctrine:fixtures:load
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs && sudo chmod 777 -R web/uploads
