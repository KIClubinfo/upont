#!/bin/bash
# [AT'016] Script d'installation total de YouPont
# Configurer les variables ci dessous
# Attention ! Il faut que le proxy système soit configuré si nécessaire,
# dans tout le système ainsi que dans ce fichier.
# Pour régler le proxy pour apt-get, taper "sudo nano /etc/apt/apt.conf"
# dans un terminal, insérer la ligne
# Acquire::http::Proxy "http://ki:ZxO.p424512@etuproxy.enpc.fr:3128";
# et sauver/fermer le fichier avec Ctrl+X

dsi="trancara"                          # Identifiant DSI
mail="alberic.trancart@eleves.enpc.fr"  # Mail ENPC
name="Albéric Trancart"                 # Prénom Nom
user="alberic"                          # Nom d'utilisateur sur l'ordinateur
proxy="http://user:password@etuproxy.enpc.fr:3128" # Décommenter si proxy

### INSTALL ###
echo "Installation des dépendances..."
sudo apt-get install lamp-server^ php5-curl php5-intl php5-gd php5-imap phpmyadmin curl phpunit git nodejs npm ant gource libav-tools xvfb phantomjs

echo "Configuration de npm..."
if [ -z "$proxy" ]; then
    echo "Pas de proxy pour npm"
else
    npm config set proxy $proxy
    npm config set https-proxy $proxy
fi
sudo ln -s /usr/bin/nodejs /usr/bin/node
sudo npm install -g grunt-cli gulp karma-cli bower phonegap cordova

echo "Configuration de git..."
sudo chown -R $user:www-data /var/www
cd /var/www
git config --global user.name $name
git config --global user.email $mail
git config --global http.postBuffer 524288000
git config --global push.default simple
git config --global credential.helper 'cache --timeout=86400'

echo "Clonage du repo..."
git clone https://github.com/KIClubinfo/upont.git
cd upont

echo "Activation de phpmyadmin et réglages Apache..."
sudo ln -s /etc/phpmyadmin/apache.conf /etc/apache2/conf-enabled/phpmyadmin.conf
sudo cp utils/000-default.conf /etc/apache2/sites-enabled/000-default.conf
sudo cp utils/php.ini /etc/php5/apache2/php.ini
sudo a2enmod rewrite
sudo service apache2 reload

echo "Installation du front..."
cd front
sudo npm install
bower install
gulp build-js
gulp build-css

echo "Installation de l'appli mobile..."
cd ../mobile
sudo npm install
bower install
grunt build

echo "Installation du back..."
cd ../back

echo "Création d'une base de données..."
echo "CREATE DATABASE upont" | mysql -u root -p

echo "Installation de composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer update

echo "Mise à jour de la BDD..."
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load

echo "Vidage du cache..."
php app/console cache:clear
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs
git stash

echo "Test final à effectuer manuellement pour vérifier que tout va bien"
phpunit -c app/
