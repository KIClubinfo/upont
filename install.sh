#!/bin/bash
# [AT'016] Script d'installation total de YouPont

echo -e "\e[1m\e[34mBienvenue sur le script d'installation de uPont"
echo -e "Ce script est destiné aux distributions Ubuntu-like. \e[31mUn compte GitHub est nécessaire.\e[0m"
read -p "Identifiant DSI : " dsi
read -p "Adresse mail du compte GitHub : " mail
read -p "Prénom Nom : " name
read -p "Nom du compte utilisateur Linux : " user
read -p "Adresse proxy (au format http://user:password@etuproxy.enpc.fr:3128) : " proxy

### INSTALL ###
echo -e "\e[1m\e[34mInstallation des dépendances..."
sudo apt-get install -y lamp-server^ php5-curl php5-intl php5-gd php5-imap phpmyadmin curl phpunit git nodejs npm ant gource libav-tools xvfb phantomjs

echo -e "\e[1m\e[34mConfiguration du proxy..."
if [ -z "$proxy" ]; then
    echo "Pas de proxy configuré."
else
    npm config set proxy $proxy
    npm config set https-proxy $proxy
fi
sudo ln -s /usr/bin/nodejs /usr/bin/node
sudo npm install -g gulp bower phonegap cordova

echo -e "\e[1m\e[34mConfiguration de git..."
sudo chown -R $user:www-data /var/www
cd /var/www
git config --global user.name $name
git config --global user.email $mail
git config --global http.postBuffer 524288000
git config --global push.default simple
git config --global credential.helper 'cache --timeout=86400'

echo -e "\e[1m\e[34mClonage du repo..."
git clone https://github.com/KIClubinfo/upont.git
cd upont

echo -e "\e[1m\e[34mActivation de phpmyadmin et réglages Apache..."
sudo ln -s /etc/phpmyadmin/apache.conf /etc/apache2/conf-enabled/phpmyadmin.conf
sudo cp utils/upont.enpc.fr.conf /etc/apache2/sites-enabled/upont.enpc.fr.conf
sudo cp utils/php.ini /etc/php5/apache2/php.ini
sudo a2enmod rewrite
sudo service apache2 reload

echo -e "\e[1m\e[34mInstallation du front..."
cd front
sudo npm install
bower install
gulp build

echo -e "\e[1m\e[34mInstallation de l'appli mobile..."
cd ../mobile
sudo npm install
bower install
gulp build
cordova plugin add org.apache.cordova.device org.apache.cordova.splashscreen https://github.com/phonegap-build/PushPlugin.git

echo -e "\e[1m\e[34mInstallation du back..."
cd ../back

echo -e "\e[1m\e[34mCréation d'une base de données..."
echo "CREATE DATABASE upont" | mysql -u root -p

echo -e "\e[1m\e[34mInstallation de composer..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer install

echo -e "\e[1m\e[34mMise à jour de la BDD..."
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load

echo -e "\e[1m\e[34mVidage du cache..."
php app/console cache:clear
sudo chmod 777 -R app/cache && sudo chmod 777 -R app/logs && sudo chmod 777 -R web/uploads

echo -e "\e[1m\e[34mTest final à effectuer manuellement pour vérifier que tout va bien"
phpunit -c app/
