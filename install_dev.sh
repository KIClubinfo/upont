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

HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX var

composer install
php bin/console assets:install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console cache:warmup

rm -rf web/uploads/exercices/*
rm -rf web/uploads/files/*
rm -rf web/uploads/images/*
rm -rf web/uploads/tmp/*
echo "Y" | bin/console doctrine:fixtures:load
