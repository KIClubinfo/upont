#!/bin/bash
# [AT'016] Génère les statistiques de uPont ensuite interprétées par PHP
# Script appelé par la crontab une fois par jour
 
# Reset du stockage des infos
source "$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )/config.cfg"
cd "$WORKINGDIR"
rm stats
touch stats

# Infos generales
echo "[info]" >> stats
git --version >> stats

# Liste des utilisateurs avec leur nombre de commits
echo "[dev]" >> stats
git shortlog -s HEAD >> stats

# Liste des commits (timestamp, id, data, heure, dev, email
buffer=$(git rev-list --pretty=format:"#%T|%at|%ai|%aN|%aE" HEAD | grep -v ^commit)
commits=( $(echo "$buffer" | grep -o '[a-f0-9]\{40\}') )
echo "[commit]" >> stats
echo "$buffer" >> stats

# Liste des fichiers (file tree) avec leurs identifiants et leur taille
buffer=$(git ls-tree -r -l HEAD)
blobs=( $(echo "$buffer" | grep -o '[a-f0-9]\{40\}') )
echo "[fichiers]" >> stats
echo "$buffer" >> stats

# Récupère le nombre de ligne des fichiers
echo "[lignes]" >> stats
for ((i = 0 ; i < ${#blobs[@]} ; i++ ))
do
        lignes=$(git cat-file blob "${blobs[i]}" | wc -l)
        echo "${blobs[$i]} $lignes" >> stats
done

# Nombre de deletions/insertions par commit
echo "[delins]" >> stats
git log --shortstat --date-order --pretty=format:"%at %aN" HEAD >> stats
