#!/bin/bash
# [LT'018] Prérequis uPont sans vagrant

echo -e "\e[1m\e[34mBienvenue sur le script d'installation de uPont"
echo -e "Ce script est destiné aux distributions Ubuntu-like. \e[31mUn compte GitHub est nécessaire.\e[0m"
read -p "Adresse mail du compte GitHub : " mail
read -p "Prénom Nom : " name
read -p "Adresse proxy (au format http://user:password@etuproxy.enpc.fr:3128) : " proxy

### INSTALL ###
echo -e "\e[1m\e[34mInstallation des dépendances...\e[0m"
sudo apt-get update
sudo apt-get install -y curl expect git htop iotop lynx make nano netcat traceroute sl tree vim unzip zip mysql-server python-mysqldb php5-cli php5-fpm php5-curl php5-gd php5-imap php5-intl php5-mcrypt php5-mysql nginx apt-transport-https

echo -e "\e[1m\e[34mConfiguration du proxy...\e[0m"
if [ -z "$proxy" ]; then
    echo "\e[31mPas de proxy configuré.\e[0m"
else
    npm config set proxy $proxy
    npm config set https-proxy $proxy
fi

echo -e "\e[1m\e[34mConfiguration de git...\e[0m"
sudo chown -R $user:www-data /var/www
cd /var/www
git config --global user.name $name
git config --global user.email $mail
git config --global http.postBuffer 524288000
git config --global push.default simple
git config --global credential.helper 'cache --timeout=86400'

echo -e "\e[1m\e[34mCréation d'une base de données...\e[0m"
echo "CREATE DATABASE upont" | mysql -u root -p

sudo cp utils/install/www.conf /etc/php5/fpm/pool.d/www.conf
sudo mkdir /etc/php5/conf.d
sudo cp utils/install/global.ini /etc/php5/conf.d/global.ini

sudo curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin
sudo mv /usr/local/bin/composer.phar /usr/local/bin/composer
chmod -R 0777 ~/.composer/cache

sudo cp utils/install/php5-fpm.conf /etc/nginx/conf.d/php5-fpm.conf
sudo service restart php5-fpm

sudo cp utils/install/dev-upont.enpc.fr.conf /etc/nginx/sites-available/dev-upont.enpc.fr.conf
sudo ln -s /etc/nginx/sites-available/dev-upont.enpc.fr.conf /etc/nginx/sites-enabled/dev-upont.enpc.fr.conf
sudo service nginx restart

wget -qO - https://deb.nodesource.com/gpgkey/nodesource.gpg.key | sudo apt-key add -
sudo cp utils/install/nodesource.list /etc/apt/sources.list.d/nodesource.list
sudo cp utils/install/deb_nodesource_com_node.pref /etc/apt/preferences.d/deb_nodesource_com_node.pref

sudo apt-get update
sudo apt-get install nodejs
sudo npm install -g npm bower gulp

echo "127.0.0.1 dev-upont.enpc.fr" | sudo tee -a /etc/hosts
