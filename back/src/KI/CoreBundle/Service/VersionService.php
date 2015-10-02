<?php

namespace KI\CoreBundle\Service;

class VersionService
{
    /**
     * Calcule le numéro de version de uPont
     * @return array     $version, $major, $minor, $build, $date
     */
    public function getVersion()
    {
        // On récupère le tag de release le plus proche
        $tags = shell_exec('git tag');
        $out = array();

        if (!preg_match_all('/v(\d+)\.(\d+)\.(\d+)/', $tags, $out)) {
            return array(
                'version' => 2,
                'major'   => 0,
                'minor'   => 0,
                'build'   => 'Inconnu',
                'date'    => 0
            );
        }

        $countTags = count($out[0]);
        $version = 2;
        $major = 0;
        $minor = 0;

        for ($i = 0; $i < $countTags; $i++) {
            // On ne s'intéresse qu'à la dernière version
            if ($out[1][$i] < $version) {
                continue;
            }

            // Si on passe à une version supérieure on réinitialise les 3 composantes
            // aux valeurs du tag qui nous fait changer de version
            if ($out[1][$i] > $version) {
                $version = $out[1][$i];
                $major = $out[2][$i];
                $minor = $out[3][$i];
            } else {
                // Si on a la même version, on cherche la major maximale
                if ($out[2][$i] < $major) {
                    continue;
                }

                // Même raisonnement qu'avec la version
                if ($out[2][$i] > $major) {
                    $major = $out[2][$i];
                    $minor = $out[3][$i];
                }

                if ($out[3][$i] > $minor) {
                    $minor = $out[3][$i];
                }
            }
        }

        return array(
            'version' => $version,
            'major'   => $major,
            'minor'   => $minor,
            'build'   => shell_exec('git log --pretty=format:"%h" -n 1'),
            'date'    => (int)shell_exec('git log -1 --pretty=format:%ct')
        );
    }
}
