#!/bin/bash
# [AT'016] Met automatiquement tout à jour, à lancer à chaque pull/mise à jour des modèles, configurable dans un hook post-receive

if [[ $(hostname -s) = clubinfo ]]; then
    echo "Don't run this script on odin !!!"
    exit
fi

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
sudo chmod 777 -R var/cache && sudo chmod 777 -R var/logs && sudo chmod 777 -R web/uploads
bin/console cache:clear
sudo rm -rf var/cache/*
sudo rm -rf var/logs/*
bin/console doctrine:mi:mi -n
rm -rf web/uploads/exercices/*
rm -rf web/uploads/files/*
rm -rf web/uploads/images/*
rm -rf web/uploads/tmp/*
echo "Y" | bin/console doctrine:fixtures:load
sudo chmod 777 -R var/cache && sudo chmod 777 -R var/logs && sudo chmod 777 -R web/uploads
