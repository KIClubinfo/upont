<?php

namespace KI\UpontBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use KI\UpontBundle\Entity\Publications\Course;
use KI\UpontBundle\Entity\Publications\CourseItem;

class DefaultController extends \KI\UpontBundle\Controller\Core\BaseController
{
    /**
     * @ApiDoc(
     *  description="Procédure de nettoyage de la BDD à lancer régulièrement",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function cleanAction()
    {
        $manager = $this->getDoctrine()->getManager();

        // Nettoyages des notifications plus vieilles que 15 jours
        $repo = $manager->getRepository('KIUpontBundle:Notification');
        $notifications = $repo->findAll();

        foreach ($notifications as $notification) {
            if ($notification->getDate() < time() - 15*24*3600)
                $manager->remove($notification);
        }

        $manager->flush();
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Avoir du café",
     *  statusCodes={
     *   418="Je suis une théière",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function coffeeAction()
    {
        $engine = $this->container->get('templating');
        $content = $engine->render('KIUpontBundle::coffee.html.twig');
        return $this->htmlResponse($content, 418);
    }

    /**
     * @ApiDoc(
     *  description="Parse l'emploi du temps emploidutemps.enpc.fr",
     *  statusCodes={
     *   202="Requête traitée mais sans garantie de résultat",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function parseCoursesAction()
    {
        $curl = $this->get('ki_upont.curl');

        // On va reset le cours actuels au cas où ils seraient updatés
        $manager = $this->getDoctrine()->getManager();
        $query = $manager->createQuery('DELETE FROM KIUpontBundle:Publications\CourseItem c WHERE c.startDate > :time');
        $query->setParameter('time', mktime(0, 0, 0));
        $query->execute();

        // On construit un tableau des cours connus
        $repo = $manager->getRepository('KIUpontBundle:Publications\Course');
        $results = $repo->findAll();
        $courses = array();
        $coursesNames = array();
        foreach ($results as $course) {
            $courses[$course->getId()] = $course;
            $coursesNames[$course->getId()] = $course->getName().$course->getGroup();
        }

        // On récupère les cours de la prochaine semaine
        for ($day = 0; $day < 8; $day++) {
            $date = time() + $day*3600*24;
            $url = 'http://emploidutemps.enpc.fr/index_mobile.php?code_departement=&mydate='
            . date('d', $date).'%2F'.date('m', $date).'%2F'.date('Y', $date);
            $result = $curl->curl($url);

            // On parse le résultat
            $regex = '#<li class="store">.+<span class="image" align="center"><br><b>(.+)<br>(.+)</b></span><span class="comment">(.*) : (.*)</span><span class="name">(.*)</span><span class="starcomment">(.*)</span><span class="arrow"></span></a></li>#isU';
            $out = array();
            preg_match_all($regex, $result, $out);

            // Le résultat est sous la forme
            // array(
            //     [0] => array(merde),
            //     [1] => array(heure de début),
            //     [2] => array(heure de fin),
            //     [3] => array(département),
            //     [4] => array(salle),
            //     [5] => array(cours),
            //     [6] => array(groupe),
            // )
            list($all, $start, $end, $department, $location, $courseName, $group) = $out;

            foreach ($all as $id => $blank) {
                $gr = str_replace('(&nbsp;)', '', $group[$id]);
                $gr = $gr != '' ? (int)str_replace(array('(Gr', ')'), array('', ''), $gr) : 0;
                $name = $courseName[$id];
                $data = explode(':', $start[$id]);
                $startDate = $data[0]*3600 + $data[1]*60;
                $data = explode(':', $end[$id]);
                $endDate = $data[0]*3600 + $data[1]*60;

                // Si le cours existe déjà, on le récupère
                // Sinon on crée un nouveau cours
                if ($key = array_search($name.$gr, $coursesNames)) {
                    $course = $courses[$key];

                    // On règle quand même l'heure de début et de fin si ce n'était pas fait
                    if ($course->getStartDate() == null)
                        $course->setStartDate($startDate);
                    if ($course->getEndDate() == null)
                        $course->setEndDate($endDate);
                } else {
                    $course = new Course();
                    $course->setName($name);
                    $course->setGroup($gr);
                    $course->setStartDate($startDate);
                    $course->setEndDate($endDate);
                    $course->setDepartment($department[$id]);
                    $course->setSemester(0);
                    $manager->persist($course);
                    $courses[] = $course;
                    $coursesNames[] = $name.$gr;
                }

                // On ajoute l'objet à ce cours
                $courseItem = new CourseItem();
                $courseItem->setCourse($course);
                $courseItem->setStartDate(mktime(0, 0, 0) + $startDate);
                $courseItem->setEndDate(mktime(0, 0, 0) + $endDate);
                $courseItem->setLocation($location[$id]);
                $manager->persist($courseItem);
            }
        }
        $manager->flush();

        return $this->jsonResponse(null, 202);
    }

    /**
     * @ApiDoc(
     *  description="Déclenche le déploiement de master. DANGEREUX ! Ne doit pas être testé pour des raisons évidentes.",
     *  statusCodes={
     *   202="Requête traitée mais sans garantie de résultat",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  tags={
     *    "WARNING"
     *  },
     *  section="Général"
     * )
     */
    public function deployAction()
    {
        shell_exec("ssh root@localhost '/bin/bash /server/upont/utils/update-prod.sh'");
        return $this->jsonResponse(null, 202);
    }

    /**
     * @ApiDoc(
     *  description="Let's get dirty !",
     *  statusCodes={
     *   202="Requête traitée mais sans garantie de résultat",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function dirtyAction()
    {
        return $this->redirect('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
    }

    /**
     * @ApiDoc(
     *  description="Se loger et recevoir un JSON Web Token",
     *  requirements={
     *   {
     *    "name"="username",
     *    "dataType"="string",
     *    "description"="Le nom d'utilisateur (format : sept premières lettres du nom et première lettre du prénom)"
     *   },
     *   {
     *    "name"="password",
     *    "dataType"="string",
     *    "description"="Le mot de passe en clair"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Mauvaise combinaison username/password ou champ nom rempli",
     *   502="Erreur Proxy : l'utilisateur se connecte pour la première fois, mais le proxy DSI n'est pas configuré",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    // Cette action sert juste à documenter cette route,
    // tout le reste est géré par le LexikJWTAuthenticationBundle
    public function loginAction() { return $this->restResponse(null); }

    /**
     * @ApiDoc(
     *  description="Mettre le serveur en mode maintenance",
     *  parameters={
     *   {
     *    "name"="until",
     *    "dataType"="integer",
     *    "required"=false,
     *    "description"="La date prévue de remise en route du service (timestamp)"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Général"
     * )
     */
    public function maintenanceLockAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException();

        $path = $this->get('kernel')->getRootDir().$this->container->getParameter('upont_maintenance_lock');
        $until = $request->request->has('until') ? (string)$request->request->get('until') : '';
        file_put_contents($path, $until);
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Sortir le serveur du mode maintenance",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *  },
     *  section="Général"
     * )
     */
    public function maintenanceUnlockAction(Request $request)
    {
        if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            throw new AccessDeniedException();

        $path = $this->get('kernel')->getRootDir().$this->container->getParameter('upont_maintenance_lock');

        if (file_exists($path))
            unlink($path);
        else
            throw new BadRequestHttpException('Le serveur n\'est pas en mode maintenance');
        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Pinger l'API",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function pingAction() { return $this->jsonResponse(null, 204); }

    /**
     * @ApiDoc(
     *  description="Retourne les utilisateurs étant connectés (intervalle de x minutes)",
     *  parameters={
     *   {
     *    "name"="delay",
     *    "dataType"="integer",
     *    "required"=false,
     *    "description"="Temps de l'intervalle considéré en minutes (30 minutes par défaut)"
     *   }
     *  },
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Utilisateurs"
     * )
     * @Route\Get("/online")
     */
    public function onlineAction(Request $request)
    {
        $delay = $request->query->has('delay') ? (int)$request->query->get('delay') : 30;

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('u')
            ->from('KIUpontBundle:Users\User', 'u')
            ->where('u.lastConnect > :date')
            ->setParameter('date', time() - $delay*60);
        return $qb->getQuery()->getResult(); ;
    }

    /**
     * @ApiDoc(
     *  description="Envoie un mail permettant de reset le mot de passe",
     *  requirements={
     *   {
     *    "name"="username",
     *    "dataType"="string",
     *    "description"="Le nom d'utilisateur"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Mauvaise combinaison username/password ou champ nom rempli",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function resettingAction(Request $request)
    {
        if (!$request->request->has('username'))
            throw new BadRequestHttpException('Aucun nom d\'utilisateur fourni');

        $manager = $this->getDoctrine()->getManager();
        $repo = $manager->getRepository('KIUpontBundle:Users\User');
        $user = $repo->findOneByUsername($request->request->get('username'));

        if ($user) {
            $token = $this->get('ki_upont.token')->getToken($user);
            $message = \Swift_Message::newInstance()
                ->setSubject('Réinitialisation du mot de passe')
                ->setFrom('noreply@upont.enpc.fr')
                ->setTo($user->getEmail())
                ->setBody($this->renderView('KIUpontBundle::resetting.txt.twig', array('token' => $token, 'name' => $user->getFirstName())));
            $this->get('mailer')->send($message);

            return $this->restResponse(null, 204);
        } else
            throw new NotFoundHttpException('Utilisateur non trouvé');
    }

    /**
     * @ApiDoc(
     *  description="Reset son mot de passe à partir du mail",
     *  requirements={
     *   {
     *    "name"="password",
     *    "dataType"="string",
     *    "description"="Le mot de passe"
     *   },
     *   {
     *    "name"="check",
     *    "dataType"="string",
     *    "description"="Le mot de passe une seconde fois (confirmation)"
     *   }
     *  },
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Mauvaise combinaison username/password ou champ nom rempli",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function resettingTokenAction($token)
    {
        $request = $this->getRequest()->request;
        if (!$request->has('password') || !$request->has('check'))
            throw new BadRequestHttpException('Champs password/check non rempli(s)');

        $manager = $this->getDoctrine()->getManager();
        $repo = $manager->getRepository('KIUpontBundle:Users\User');
        $user = $repo->findOneByToken($token);

        if ($user) {
            if ($request->get('password') != $request->get('check'))
                throw new BadRequestHttpException('Mots de passe non identiques');

            $user->setPlainPassword($request->get('password'));
            $manager->flush();

            return $this->restResponse(null, 204);
        } else
            throw new NotFoundHttpException('Utilisateur non trouvé');
    }

    /**
     * @ApiDoc(
     *  description="Retourne la version de uPont",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Général"
     * )
     */
    public function versionAction()
    {
        $env = $this->get('kernel')->getEnvironment();

        // On récupère le tag de release le plus proche
        $tags = shell_exec('git tag');
        $out = array();

        if (preg_match_all('#v([0-9]+)\.([0-9]+)\.([0-9]+)#', $tags, $out))
        {
            $count = count($out[0]);
            $version = 2;
            $major = 0;
            $minor = 0;

            for ($j = 0 ; $j < $count ; $j++) {
                if ($out[1][$j] > $version) $version = $out[1][$j];
            }

            if (preg_match_all('#v'.$version.'\.([0-9]+)\.([0-9]+)#', $tags, $out)) {
                $count = count($out[0]);

                for ($i = 0 ; i < $count ; i++) {
                    // On ne s'intéresse qu'à la dernière version
                    if ($out[1][i] < $vers) continue;

                    // Si on passe à une version supérieure
                    // on réinitialise les 3 composantes
                    // aux valeurs du tag qui nous fait changer de version
                    if ($out[1][i] > $vers) {
                        $vers = $out[1][i];
                        $major = $out[2][i];
                        $minor = $out[3][i];
                    }

                    // Si on a la même version, on cherche la major maximale
                    else {
                        if ($out[2][i] < $maj) continue;

                        // Même raisonnement qu'avec la version
                        if ($out[2][i] > $maj) {
                            $major = $out[2][i];
                            $minor = $out[3][i];
                        } else if ($out[3][i] > $min) $minor = $out[3][i];
                    }
                }

                return $this->jsonResponse(array(
                    'version'     => $version,
                    'major'       => $major,
                    'minor'       => $minor,
                    'build'       => shell_exec('git log --pretty=format:"%h" -n 1'),
                    'date'        => (int)shell_exec('git log -1 --pretty=format:%ct'),
                    'environment' => $env
                ));
            }
        }

        return $this->jsonResponse(array(
            'version'     => 2,
            'major'       => 0,
            'minor'       => 0,
            'build'       => 'Erreur - no tag found',
            'date'        => time(),
            'environment' => $env
        ));
    }
}
