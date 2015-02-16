<?php

namespace KI\UpontBundle\Controller;

use KI\UpontBundle\Entity\Log;
use KI\UpontBundle\Controller\BaseController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class LogsController extends BaseController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Log');
    }
    
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Retourne les logs. Seule action recevable : les logs sont en lecture seule",
     *  output="KI\UpontBundle\Entity\Log",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function getLogsAction() { return $this->getAll(); }
}
