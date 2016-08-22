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
        $tags = shell_exec('git tag --sort=version:refname');
        $out = [];

        if (!preg_match_all('/v(\d+)\.(\d+)\.(\d+)/', $tags, $out)) {
            return [
                'version' => 2,
                'major'   => 0,
                'minor'   => 0,
                'build'   => 'Inconnu',
                'date'    => 0
            ];
        }

        return [
            'version' => $out[1][count($out[0])-1],
            'major'   => $out[2][count($out[0])-1],
            'minor'   => $out[3][count($out[0])-1],
            'build'   => shell_exec('git log --pretty=format:"%h" -n 1'),
            'date'    => (int)shell_exec('git log -1 --pretty=format:%ct')
        ];
    }
}
