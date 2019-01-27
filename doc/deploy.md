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

Deploy to pre-production
------------------------
TODO

Deploy to production
--------------------
Lorsqu’une version est prête à être déployée, la branche de version est mergée dans master, un tag est créé, un changelog est posté sur uPont et les cartes Trello correspondantes sont archivées. Côté serveur, le déploiement est effectué en utilisant le script utils/update-prod.sh après s’être mis sur la branche voulue. Il ne faut en aucun cas utiliser le script update.sh !
En effet, celui-ci télécharge des bundles de version dev, efface totalement la base de données et purge les images.
Comment déployer
```
ssh odin
[MDP]
cd /srv/upont
git pull
./utils/update−prod.sh
# NE SURTOUT PAS lancer le script update.sh
# il doit etre utilise uniquement pour la version locale car il reset la BDD
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