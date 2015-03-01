<?php

namespace KI\UpontBundle\Controller\Users;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PromoController extends \KI\UpontBundle\Controller\Core\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('User', 'Users');
    }

    /**
     * @ApiDoc(
     *  description="Retourne un tableau de données pour le jeu du trombinoscope",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function getPromoGameAction()
    {
        $maxId = $this->em->createQuery('SELECT MAX(u.id) FROM KIUpontBundle:Users\User u')->getSingleScalarResult();
        $query = $this->em->createQuery('SELECT u FROM KIUpontBundle:Users\User u WHERE u.id >= :rand ORDER BY u.id ASC');
        $rand1 = rand(0, $maxId);

        do {
            $rand2 = rand(0, $maxId);
        } while ($rand1 == $rand2);

        do {
            $rand3 = rand(0, $maxId);
        } while ($rand3 == $rand2 || $rand3 == $rand1);

        $users = array(
            $query->setParameter('rand', $rand1)->setMaxResults(1)->getSingleResult(),
            $query->setParameter('rand', $rand2)->setMaxResults(1)->getSingleResult(),
            $query->setParameter('rand', $rand3)->setMaxResults(1)->getSingleResult()
        );

        return $this->restResponse($users);
    }

    /**
     * @ApiDoc(
     *  description="Met à jour les photos de profil d'une promo via Facebook",
     *  requirements={
     *   {
     *    "name"="token",
     *    "dataType"="string",
     *    "description"="Token facebook (doit avoir les permissions user_group !!!)"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     */
    public function patchPromoPicturesAction($promo)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException();

        $users = $this->repo->findByPromo($promo);
        $curl = $this->get('ki_upont.curl');
        $images = $this->get('ki_upont.images');
        $i = 0;

        $query = $this->getRequest()->query;
        if (!$query->has('token'))
            throw new BadRequestHttpException('Il faut préciser un token Facebook');
        $token = $query->get('token');

        // Ids des différents groupes facebook
        switch ($promo) {
            case '014': $id = 0; break; // Kohlant'wei
            case '015': $id = 0; break; // Kohlant'wei
            case '016': $id = 0; break; // Wei't spirit
            case '017': $id = 0; break; // Wei't the phoque
            default: throw new \Exception('Promo ' . $promo . ' non prise en charge');
        }

        // On récupère la liste des membres
        // TODO
        $data = json_decode($curl->curl('/v2.2/' . $id . '/members'), true);

        // Pour chaque utilisateur on essaye de trouver son profil fb, et si oui
        // on récupère la photo de profil
        foreach ($users as $user) {
            foreach ($data as $member) {
                if ($this->isSimilar($user, $member)) {
                    // TODO
                    $url = '/v2.2/' . $member['id'] . '/picture?width=9999&redirect=true';
                    $user->setImage($images->upload($url));
                    $i++;
                }
            }
        }

        $this->em->flush();
        return $this->jsonResponse(array(
            'hits'  => $i,
            'fails' => count($users) - $i,
            'ratio' => $i / count($users)
        ));
    }

    // Compare un User uPont et un utilisateur Facebook et essaye de deviner si
    // ce sont les mêmes personnes
    private function isSimilar(\KI\UpontBundle\Entity\Users\User $user, array $member)
    {
        // TODO
    }
}
