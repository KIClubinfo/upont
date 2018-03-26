#!/bin/bash
# [AT'016] Reset le cache et les données du back

sudo ls > /dev/null
bin/console cache:clear
sudo rm -rf app/cache/*
sudo rm -rf app/logs/*
rm -rf public/uploads/exercices/*
rm -rf public/uploads/files/*
rm -rf public/uploads/images/*
rm -rf public/uploads/thumbnails/*
rm -rf public/uploads/tmp/*
echo "Y" | bin/console doctrine:fixtures:load
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs && sudo chmod 777 -R public/uploads
