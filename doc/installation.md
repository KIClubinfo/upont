Installation
============

Requirements
------------
Before anything, you need the following software installed on your machine:

  * Linux (any Ubuntu-like distro will do)
  * If needed, the proxy should be configured

Project installation
--------------------
To install the project, you must at first copy the installation script then run it:
```
wget https://raw.githubusercontent.com/KIClubinfo/upont/master/install.sh
chmod +x install.sh
./install.sh
```

The script is interactive, you will be prompted to provide parameters (default values will do, except if you are behind a proxy).

If you **do not** have an Ubuntu-like distro, find the equivalent for your distro of the commands of the installation script.

Hosts
-----
Now you should be able to access the application in your Web browser:
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

Using Vagrant
=============

```bash
vagrant up
vagrant ssh

mkdir -p /dev/shm/upont
HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX /dev/shm/upont
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX /dev/shm/upont

cd /var/www/upont/back
composer install
app/console doctrine:mig:mig -n
app/console doctrine:fix:load -n

cd /var/www/upont/front
npm install
bower install
gulp build
```
