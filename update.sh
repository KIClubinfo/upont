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
gulp build-templates
gulp copy-fonts

cd ../back
sudo composer self-update
composer install
sudo chmod 777 -R var/cache && sudo chmod 777 -R var/logs && sudo chmod 777 -R public/uploads
bin/console cache:clear
sudo rm -rf var/cache/*
sudo rm -rf var/logs/*
bin/console doctrine:mi:mi -n
rm -rf public/uploads/exercices/*
rm -rf public/uploads/files/*
rm -rf public/uploads/images/*
rm -rf public/uploads/tmp/*
echo "Y" | bin/console doctrine:fixtures:load
sudo chmod 664 public/uploads/others/* && sudo chmod 664 public/uploads/tests/*

# Génère la documentation et les logs php à back/phpdoc
# phpdoc
