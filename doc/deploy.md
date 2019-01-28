Deployment
==========

Project environments
--------------------
The following environments are available for this project:

**Prod** : https://upont.enpc.fr/
  * ***IP*** : 195.221.194.9
  * ***Associated Git branch*** : master

**Preprod** : https://upont-preprod.enpc.org/
  * ***IP*** : 92.222.29.69
  * ***Associated Git branch*** : master-dev


Create a dev environment
-------------------------------

```bash
git checkout master-dev
git pull

cp .env.dist .env

mkdir -p docker/config/jwt
cp back/config/jwt/* docker/config/jwt/

docker-compose -f docker-compose.yml -f build.override.yml build --build-arg=BUILD_APP_ENV=dev
docker-compose up -d

docker-compose exec back bin/console doctrine:fixtures:load
```

Create a prod environment
-------------------------------

```bash
git checkout master-dev
git pull

cp .env.dist .env
# EDIT .env with SECRET values!!!

mkdir -p docker/config/jwt
cp <jwt_tokens> docker/config/jwt/

docker-compose -f docker-compose.yml -f build.override.yml build --build-arg=BUILD_APP_ENV=dev
docker-compose up -d

docker-compose exec back bin/console doctrine:fixtures:load
```


Deploy to production
--------------------
Lorsqu’une version est prête à être déployée, la branche de version est mergée dans master, un tag est créé, un changelog est posté sur uPont et les cartes Trello correspondantes sont archivées.

#### Comment déployer
```bash
ssh clubinfo
cd /srv/upont.enpc.fr
git checkout <version_tag>
git pull
docker-compose pull
docker-compose up -d
```

Bumper de version (exemple v2.0.1 −> v2.0.2)
--------------------------------------------
```
# Merger v2.0.2 dans master
git co master
git pull
git tag v2.0.2
git push −−tags
git br v2.0.3
git push −−set−upstream origin v2.0.3
```