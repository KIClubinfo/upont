#!/bin/bash
# [AT'016] Reset le cache et les données du back

sudo ls > /dev/null
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
