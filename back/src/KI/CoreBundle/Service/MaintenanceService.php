<?php

namespace KI\CoreBundle\Service;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class MaintenanceService
{
    protected $lockfilePath;

    public function __construct($lockfilePath)
    {
        $this->lockfilePath = $lockfilePath;
    }

    /**
     * Met le site en mode maintenance
     * @param  string $until           Date de la fin de maintenance
     * @throws BadRequestHttpException Si le serveur n'est pas en maintenance
     */
    public function lock($until)
    {
        file_put_contents($this->lockfilePath, $until);
    }

    /**
     * Sort le site de la maintenance
     * @throws BadRequestHttpException Si le serveur n'est pas en maintenance
     */
    public function unlock()
    {
        if (file_exists($this->lockfilePath)) {
            unlink($this->lockfilePath);
        } else {
            throw new BadRequestHttpException('Le serveur n\'est pas en mode maintenance');
        }
    }
}
