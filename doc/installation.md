Installation
============

Requirements
------------
Before anything, you need the following software installed on your machine:

  * Linux (based on Debian or Archlinux)
  * If needed, the proxy should be configured
  * `npm`
  * `php` 7+ et `php-gd`
  * `mysql` 5.6 or above

Project installation
--------------------

### System
- Uncomment the following lines in /etc/php/php.ini :
```
extension=pdo_mysql.so
extension=gd
```
- Create the database `mysql -u root`
```
CREATE DATABASE upont;
CREATE USER upont;
GRANT ALL ON upont.* TO 'upont'@'localhost' IDENTIFIED BY 'upont';
```
- `sudo npm install -g yarn`

- Install Composer
```
curl -sL https://getcomposer.org/installer | sudo -E php -- --install-dir=/usr/local/bin
sudo mv /usr/local/bin/composer.phar /usr/local/bin/composer
```

### Front
- Go to `front/`
- `yarn`
- `yarn start` to launch webpack dev server

### Back
- Go to `back/`
- `cp .env.dist .env`
- `composer install`
- `bin/console doctrine:migration:migrate` to create the tables
- `bin/console doctrine:fixture:load` to load example data
- `bin/console server:run` to run symfony dev server

Setting up SSH keys with GitHub
-------------------------------
For your convenience, you can [setup SSH keys with GitHub](https://help.github.com/articles/generating-ssh-keys/).
Don't forget to update the Git remote:
```
git remote set-url origin git@github.com:KIClubinfo/upont
```
