Installation
============

Requirements
------------
Before anything, you need the following software installed on your machine:

  * Linux (based on Debian or Archlinux)
  * If needed, the proxy should be configured

Project installation
--------------------
To install the project, you must first clone the project's repository then run the installation script. By default this script will install the uPont project in the directory /var/www/upont/
**Warning** : if you already have a setup of fastCGI process manager, nginx or php on your computer, the installation script will erase and replace their configuration files, read the script for more details

```
cd /var/www
sudo chown $(whoami) .
git clone https://github.com/KIClubinfo/upont.git
cd upont
```

On a Debian-based distro
-------------------------
```
./install-debian.sh
```

On an Arch-based distro
------------------------
```
./install-arch.sh
```

The script is interactive, you will be prompted to provide parameters (default values will do, except if you are behind a proxy).

If you **do not** have an Debian-based or Arch-based distro, find the equivalent for your distro of the commands of the installation script.

Append the following line at the end of the http block in /etc/nginx/nginx.conf:
```
include servers-enabled/*;
```

If the installation of an [nginx server](http://nginx.org/en/docs/beginners_guide.html) with FastCGI via the installation script failed, you can understand the error messages thanks to [this tutorial](https://www.youtube.com/watch?v=SqE5uUbBU78) and install a php server with the help of [this page](http://symfony.com/doc/current/setup/web_server_configuration.html).

You can find the owner of the nginx process with :
```
ps aux | grep nginx
```
It should be www-data for Debian and http for Archlinux by default as mentioned by default in the configuration files of nginx and fpm

Some more documentation :
  - Debian :
    * https://www.howtoforge.com/tutorial/installing-nginx-with-php7-fpm-and-mysql-on-ubuntu-16.04-lts-lemp/
  - Archlinux :
    * https://wiki.archlinux.org/index.php/PHP
    * https://wiki.archlinux.org/index.php/nginx


Hosts
-----
Now you should be able to access the application in your Web browser :
  * Use http://localhost/front/ for the front-end interface
  * Use http://localhost/api/doc/ for the API documentation
  * Use http://localhost/mobile/ for the mobile interface


Setting up SSH keys with GitHub
-------------------------------
For your convenience, you can [setup SSH keys with GitHub](https://help.github.com/articles/generating-ssh-keys/).
Don't forget to update the Git remote:
```
git remote set-url origin git@github.com:KIClubinfo/upont
```
