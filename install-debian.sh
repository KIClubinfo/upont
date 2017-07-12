#!/bin/bash
# [LT'018] Prérequis uPont sans vagrant

echo -e "\e[1m\e[34mBienvenue sur le script d'installation de uPont"
echo -e "Ce script est destiné aux distributions Ubuntu-like. \e[31mUn compte GitHub est nécessaire.\e[0m"
read -p "Adresse mail du compte GitHub : " mail
read -p "Prénom Nom : " name

### INSTALL ###
echo -e "\e[1m\e[34mInstallation des dépendances...\e[0m"
sudo -E apt-get update
sudo -E apt-get install -y curl expect git make nano netcat traceroute sl tree vim unzip zip
sudo -E apt-get install -y mysql-server python-mysqldb php-cli php-fpm php-curl php-gd php-imap php-intl php-mcrypt php-mysql nginx apt-transport-https

echo -e "\e[1m\e[34mConfiguration de git...\e[0m"
sudo chown -R www-data:www-data /var/www/upont

git config --global user.name $name
git config --global user.email $mail
git config --global http.postBuffer 524288000
git config --global push.default simple
git config --global push.rebase true
git config --global credential.helper 'cache --timeout=86400'

echo -e "\e[1m\e[34mCréation d'une base de données...\e[0m"
echo "CREATE DATABASE upont" | mysql -u root -p

sudo cp utils/install/www.conf-debian /etc/php/7.0/fpm/pool.d/www.conf-debian
sudo mkdir /etc/php/7.0/conf.d
sudo cp utils/install/global.ini /etc/php/7.0/conf.d/global.ini

sudo cp utils/install/php-fpm.conf /etc/nginx/conf.d/php-fpm.conf
sudo service php5-fpm restart

curl -sL https://getcomposer.org/installer | sudo -E php -- --install-dir=/usr/local/bin
sudo mv /usr/local/bin/composer.phar /usr/local/bin/composer
mkdir ~/.composer/cache
chmod -R 0777 ~/.composer/cache

curl -sL http://www.phpdoc.org/phpDocumentor.phar | sudo -E php -- --install-dir=/usr/local/bin
sudo mv /usr/local/bin/phpDocumentor.phar /usr/local/bin/phpdoc

sudo cp utils/install/dev-upont.enpc.fr.conf /etc/nginx/sites-available/dev-upont.enpc.fr.conf
sudo ln -s /etc/nginx/sites-available/dev-upont.enpc.fr.conf /etc/nginx/sites-enabled/dev-upont.enpc.fr.conf
sudo service nginx restart

curl -sL https://deb.nodesource.com/setup_4.x | sudo -E bash -
sudo -E apt-get install nodejs

echo -e "\e[1m\e[34mConfiguration du proxy...\e[0m"
if [ -z "$http_proxy" ]; then
    echo "\e[31mPas de proxy configuré.\e[0m"
else
    npm config set proxy $http_proxy
    npm config set https-proxy $http_proxy
fi

sudo npm install -g npm
sudo npm install -g bower
sudo npm install -g gulp

echo "127.0.0.1 dev-upont.enpc.fr" | sudo tee -a /etc/hosts

cd back
composer install

cd ../front
npm install
bower install

sudo chown -R http:http /var/www/upont
usermod -a -G http $(whoami)
sudo chmod 2775 /var/www/upont
sudo setfacl -dR -m u::rwX,g::rwX /var/www/upont
sudo setfacl -R -m u::rwX,g::rwX /var/www/upont

# Génère la documentation et les logs php à back/phpdoc
# phpdoc
