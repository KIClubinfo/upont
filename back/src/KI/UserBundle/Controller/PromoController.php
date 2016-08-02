<?php

namespace KI\UserBundle\Controller;

use KI\CoreBundle\Controller\ResourceController;
use KI\UserBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PromoController extends ResourceController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('User', 'User');
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
     * @Route("/promo/{promo}/pictures")
     * @Method("PATCH")
     */
    public function patchPromoPicturesAction(Request $request, $promo)
    {
        set_time_limit(3600);
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException();

        $users = $this->repository->findByPromo($promo);
        $curl = $this->get('ki_core.service.curl');
        $images = $this->get('ki_core.service.image');
        $i = 0;

        if (!$request->request->has('token'))
            throw new BadRequestHttpException('Il faut préciser un token Facebook');
        $token = '?access_token=' . $request->request->get('token');

        // Ids des différents groupes facebook
        switch ($promo) {
            // Attention, toujours préciser l'id facebook de la promo d'après
            // pour avoir les étrangers
            case '014':
                $id = '0';
                break;                // Kohlant'wei
            case '015':
                $id = '359646667495742';
                break;  // Wei't spirit
            case '016':
                $id = '1451446761806184';
                break; // Wei't the phoque
            case '017':
                $id = '737969042997359';
                break;  // F'wei'ght Club
            case '018':
                $id = '737969042997359';
                break;  // F'wei'ght Club
            default:
                throw new \Exception('Promo ' . $promo . ' non prise en charge');
        }

        // On récupère la liste des membres
        $baseUrl = 'https://graph.facebook.com/v2.4';
        $data = json_decode($curl->curl($baseUrl . '/' . $id . '/members' . $token . '&limit=10000'), true);

        // Pour chaque utilisateur on essaye de trouver son profil fb, et si oui
        // on récupère la photo de profil
        $alreadyMatched = [];
        foreach ($users as $user) {
            $bestMatch = null;
            $bestPercent = -1;
            foreach ($data['data'] as $member) {
                $percent = $this->isSimilar($user, $member);
                if ($percent > $bestPercent) {
                    $bestPercent = $percent;
                    $bestMatch = $member;
                }
            }

            if ($bestPercent > 70 && !in_array($user, $alreadyMatched)) {
                $url = '/' . $bestMatch['id'] . '/picture' . $token . '&width=9999&redirect=false';
                $dataImage = json_decode($curl->curl($baseUrl . $url), true);
                $image = $images->upload($dataImage['data']['url'], true);
                $user->setImage($image);
                $alreadyMatched[] = $user;
                $i++;
            }
        }

        $this->manager->flush();
        return $this->json([
            'hits' => $i,
            'fails' => count($users) - $i,
            'ratio' => $i / count($users)
        ]);
    }

    // Compare un User uPont et un utilisateur Facebook et essaye de deviner si
    // ce sont les mêmes personnes
    private function isSimilar(User $user, array $member)
    {
        $percent = 0;
        similar_text($user->getFirstName() . ' ' . $user->getLastName(), $member['name'], $percent);
        return $percent;
    }
}
