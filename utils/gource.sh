#!/bin/bash
# [AT'016] Génère une vidéo de l'histoire du dépôt
# Génération de la video gource

resolution="1900x1000"
scale="0.5"

gource --viewport $resolution --colour-images --stop-at-end -s $scale -a 0.01 --max-files 0 --highlight-users --highlight-dirs --file-filter ".+\.(DS_Store|gitignore)$" --key --title "L'histoire de uPont" -i 0

# Enregistre la vidéo dans un fichier mp4
# rm youpont.mp4
# xvfb-run -a -s "-screen 0 1280x720x24" gource --viewport $resolution --colour-images --stop-at-end -s $scale -a 0.01 --max-files 0 --highlight-users --highlight-dirs --file-filter ".+\.(DS_Store|gitignore)$" --key --title "L'histoire de uPont" -i 0 -o - | avconv -y -f image2pipe -vcodec ppm -i - -c:v libx264 -b 5000K -r 30  youpont.mp4 > /dev/null
