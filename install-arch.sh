#!/bin/bash
# [Archlinux'019] Prérequis uPont sans vagrant

echo -e "\e[1m\e[34mBienvenue sur le script d'installation de uPont"
echo -e "Ce script est destiné aux distributions basées sur Arch. \e[31mUn compte GitHub est nécessaire.\e[0m"
read -p "Adresse mail du compte GitHub : " mail
read -p "Prénom Nom : " name

### INSTALL ###
echo -e "\e[1m\e[34mInstallation des dépendances...\e[0m"
sudo -E pacman -Syu
sudo -E pacman -S curl expect git make nano gnu-netcat traceroute sl tree vim unzip zip
sudo -E pacman -S mysql php php-fpm php-gd php-imap php-intl php-mcrypt nginx-mainline

echo -e "\e[1m\e[34mConfiguration de git...\e[0m"

git config --global user.name $name
git config --global user.email $mail
git config --global http.postBuffer 524288000
git config --global push.default simple
git config --global credential.helper 'cache --timeout=86400'

echo -e "\e[1m\e[34mCréation d'une base de données...\e[0m"

sudo mysql_install_db --user=mysql --basedir=/usr --datadir=/var/lib/mysql
sudo systemctl start mariadb
echo "CREATE DATABASE upont" | mysql -u root -p

sudo cp utils/install/www.conf-arch /etc/php/php-fpm.d/www.conf
sudo mkdir /etc/php/conf.d # vérifier si ce répertoire existe déjà
sudo cp utils/install/global.ini /etc/php/conf.d/global.ini

sudo cp utils/install/php-fpm.conf /etc/php/php-fpm.conf
sudo systemctl php-fpm restart

curl -sL https://getcomposer.org/installer | sudo -E php -- --install-dir=/usr/local/bin
sudo mv /usr/local/bin/composer.phar /usr/local/bin/composer
mkdir ~/.composer	# vérifier si ce répertoire existe déjà
mkdir ~/.composer/cache
chmod -R 0777 ~/.composer/cache

curl -sL http://www.phpdoc.org/phpDocumentor.phar
sudo mv /usr/local/bin/phpDocumentor.phar /usr/local/bin/phpdoc

sudo mkdir /etc/nginx/servers-available # vérifier si ce répertoire existe déjà
sudo mkdir /etc/nginx/servers-enabled # vérifier si ce répertoire existe déjà

sudo cp utils/install/dev-upont.enpc.fr.conf /etc/nginx/servers-available/dev-upont.enpc.fr.conf
sudo ln -s /etc/nginx/servers-available/dev-upont.enpc.fr.conf /etc/nginx/servers-enabled/dev-upont.enpc.fr.conf
sudo systemctl restart nginx

sudo -E pacman -S nodejs

echo -e "\e[1m\e[34mConfiguration du proxy...\e[0m"
if [ -z "$http_proxy" ]; then
    echo "\e[31mPas de proxy configuré.\e[0m"
else
    npm config set proxy $http_proxy
    npm config set https-proxy $http_proxy
    mkdir ~/.bowerrc
    echo { >> ~/.bowerrc
    echo "directory": "bower_components", >> ~/.bowerrc
    echo "proxy": "$http_proxy", >> ~/.bowerrc
    echo "https-proxy":"$http_proxy" >> ~/.bowerrc
    echo } >> ~/.bowerrc
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
