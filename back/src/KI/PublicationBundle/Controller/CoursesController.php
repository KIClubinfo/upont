<?php

namespace KI\PublicationBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Event\AchievementCheckEvent;
use KI\PublicationBundle\Entity\CourseUser;
use KI\PublicationBundle\Form\CourseUserType;

class CoursesController extends \KI\CoreBundle\Controller\ResourceController
{
    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->initialize('Course', 'Publication');
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Liste les cours disponibles",
     *  output="KI\PublicationBundle\Entity\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getCoursesAction()
    {
        if ($this->getRequest()->query->has('exercices'))
            return $this->getAll(null, 'exercices');
        return $this->getAll();
    }

    /**
     * @ApiDoc(
     *  description="Retourne un cours",
     *  output="KI\PublicationBundle\Entity\Course",
     *  statusCodes={
     *   200="Requête traitée avec succès",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function getCourseAction($slug) { return $this->getOne($slug); }

    /**
     * @ApiDoc(
     *  description="Crée un cours",
     *  input="KI\PublicationBundle\Form\CourseType",
     *  output="KI\PublicationBundle\Entity\Course",
     *  statusCodes={
     *   201="Requête traitée avec succès avec création d’un document",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function postCourseAction() { return $this->post(); }

    /**
     * @ApiDoc(
     *  description="Modifie un cours",
     *  input="KI\PublicationBundle\Form\CourseType",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   400="La syntaxe de la requête est erronée",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function patchCourseAction($slug) { return $this->patch($slug); }

    /**
     * @ApiDoc(
     *  description="Supprime un cours",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     */
    public function deleteCourseAction($slug)
    {
        // Les cours possèdent plein de sous propriétés, il faut faire gaffe à toutes les supprimer
        $course = $this->getOne($slug);
        $repo = $this->manager->getRepository('KIPublicationBundle:CourseUser');

        foreach ($repo->findByCourse($course) as $courseUser) {
                    $this->manager->remove($courseUser);
        }

        return $this->delete($slug);
    }

    /**
     * @ApiDoc(
     *  description="Ajoute un utilisateur au cours",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Post("/courses/{slug}/attend")
     */
    public function postCourseUserAction($slug) {
        $course = $this->findBySlug($slug);

        // Vérifie que la relation n'existe pas déjà
        $repoLink = $this->manager->getRepository('KIPublicationBundle:CourseUser');
        $link = $repoLink->findBy(array('course' => $course, 'user' => $this->user));

        // On crée la relation si elle n'existe pas déjà
        if (count($link) != 0)
            throw new BadRequestHttpException('La relation entre Course et User existe déjà');

        // Création de l'entité relation
        $link = new CourseUser();
        $link->setCourse($course);
        $link->setUser($this->user);

        if ($this->getRequest()->request->has('group')) {
            $link->setGroup($this->getRequest()->request->get('group'));

            if (!in_array($link->getGroup(), $link->getCourse()->getGroups()))
                throw new BadRequestHttpException('Ce groupe n\'existe pas.');
        }

        $dispatcher = $this->container->get('event_dispatcher');
        $achievementCheck = new AchievementCheckEvent(Achievement::COURSES);
        $dispatcher->dispatch('upont.achievement', $achievementCheck);

        $this->manager->persist($link);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
    }

    /**
     * @ApiDoc(
     *  description="Retire la demande d'inscription",
     *  statusCodes={
     *   204="Requête traitée avec succès mais pas d’information à renvoyer",
     *   401="Une authentification est nécessaire pour effectuer cette action",
     *   403="Pas les droits suffisants pour effectuer cette action",
     *   404="Ressource non trouvée",
     *   503="Service temporairement indisponible ou en maintenance",
     *  },
     *  section="Publications"
     * )
     * @Route\Delete("/courses/{slug}/attend")
     */
    public function deleteCourseUserAction($slug) {
        $user = $this->get('security.context')->getToken()->getUser();
        $course = $this->findBySlug($slug);

        $repoLink = $this->manager->getRepository('KIPublicationBundle:CourseUser');
        $link = $repoLink->findBy(array('course' => $course, 'user' => $user));

        if (count($link) != 1)
            throw new NotFoundHttpException('Relation entre Course et User non trouvée');

        $this->manager->remove($link[0]);
        $this->manager->flush();

        return $this->jsonResponse(null, 204);
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
     * @Route\Head("/courses")
     */
    public function parseCoursesAction()
    {
        $curl = $this->get('ki_core.service.curl');

        // On va reset les cours actuels au cas où ils seraient updatés
        $manager = $this->getDoctrine()->getManager();
        $query = $manager->createQuery('DELETE FROM KIPublicationBundle:CourseItem c WHERE c.startDate > :time');
        $query->setParameter('time', mktime(0, 0, 0));
        $query->execute();

        // On construit un tableau des cours connus
        $repo = $manager->getRepository('KIPublicationBundle:Course');
        $results = $repo->findAll();
        $courses = array();
        foreach ($results as $course) {
            $courses[$course->getName()] = array(
                'course' => $course,
                'groups' => $course->getGroups()
                );
        }

        // On récupère les cours de la prochaine semaine
        for ($day = 0; $day < 8; $day++) {
            $date = time() + $day*3600*24;
            $url = 'http://emploidutemps.enpc.fr/index_mobile.php?code_departement=&mydate='
            . date('d', $date).'%2F'.date('m', $date).'%2F'.date('Y', $date);
            $result = $curl->curl($url);

            // On parse le résultat
            $regex = '/<li class="store">.+<span class="image" align="center"><br><b>(.+)<br>(.+)<\/b><\/span><span class="comment">(.*) : (.*)<\/span><span class="name">(.*)<\/span><span class="starcomment">(.*)<\/span><span class="arrow"><\/span><\/a><\/li>/isU';
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
                if (array_key_exists($name, $courses)) {
                    $course = $courses[$name]['course'];
                } else {
                    $course = new Course();
                    $course->setName($name);
                    $course->setDepartment($department[$id]);
                    $course->setSemester(0);
                    $course->addGroup($gr);
                    $manager->persist($course);
                    $courses[$name] = array(
                        'course' => $course,
                        'groups' => array($gr)
                        );
                }

                // Si le groupe n'est pas connu on le rajoute
                if (!in_array($gr, $courses[$name]['groups'])) {
                    $course->addGroup($gr);
                }

                // On ajoute l'objet à ce cours
                $courseItem = new CourseItem();
                $courseItem->setStartDate(mktime(0, 0, 0) + $startDate);
                $courseItem->setEndDate(mktime(0, 0, 0) + $endDate);
                $courseItem->setLocation($location[$id]);
                $courseItem->setGroup($gr);
                $courseItem->setCourse($course);
                $manager->persist($courseItem);
            }
        }

        $manager->flush();

        return $this->jsonResponse(null, 202);
    }
}
