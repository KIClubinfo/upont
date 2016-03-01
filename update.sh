#!/bin/bash
# [AT'016] Met automatiquement tout à jour, à lancer à chaque pull/mise à jour des modèles, configurable dans un hook post-receive

cd front
npm install
bower update
gulp build-js
gulp build-css
gulp build-html
gulp build-templates
gulp copy-fonts

cd ../back
composer self-update
composer install
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs && sudo chmod 777 -R web/uploads
php app/console cache:clear
sudo rm -rf app/cache/*
sudo rm -rf app/logs/*
php app/console doctrine:mi:mi -n
rm -rf web/uploads/exercices/*
rm -rf web/uploads/files/*
rm -rf web/uploads/images/*
rm -rf web/uploads/tmp/*
echo "Y" | php app/console doctrine:fixtures:load
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs && sudo chmod 777 -R web/uploads
