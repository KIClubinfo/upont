<?php

namespace KI\PublicationBundle\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use KI\CoreBundle\Service\CurlService;
use KI\PublicationBundle\Entity\Course;
use KI\PublicationBundle\Entity\CourseItem;

class CourseParserHelper
{
    protected $manager;
    protected $courseRepository;
    protected $curlService;
    protected $knownCourses;

    public function __construct(EntityManager $manager,
                                EntityRepository $courseRepository,
                                CurlService $curlService)
    {
        $this->manager          = $manager;
        $this->courseRepository = $courseRepository;
        $this->curlService      = $curlService;
    }

    /**
     * Parse emploidutemps.enpc.fr et récupère les nouveaux cours
     */
    public function updateCourses()
    {
        // On va reset les cours actuels au cas où ils seraient updatés
        $this->emptyCourseitems();

        // On construit un tableau des cours connus
        $this->knownCourses = $this->getKnownCourses();

        // On récupère les cours de la prochaine semaine
        $baseUrl = 'http://emploidutemps.enpc.fr/index_mobile.php?code_departement=&mydate=';

        for ($day = 0; $day < 8; $day++) {
            $date = mktime(0, 0, 0, date('n'), date('j') + $day);
            $url = $baseUrl.date('d', $date).'%2F'.date('m', $date).'%2F'.date('Y', $date);
            $response = $this->curlService->curl($url);

            // On parse le résultat
            $regex = '/<li class="store">.+<span class="image" align="center"><br><b>(.+)<br>(.+)<\/b><\/span><span class="comment">(.*) : (.*)<\/span><span class="name">(.*)<\/span><span class="starcomment">(.*)<\/span><span class="arrow"><\/span><\/a><\/li>/isU';
            $out = array();
            preg_match_all($regex, $response, $out);
            list($all, $start, $end, $department, $location, $courseName, $group) = $out;

            foreach (array_keys($all) as $id) {
                $name = $courseName[$id];
                $gr   = str_replace('(&nbsp;)', '', $group[$id]);
                $gr   = $gr != '' ? (int)str_replace(array('(Gr', ')'), array('', ''), $gr) : 0;

                $data      = explode(':', $start[$id]);
                $startDate = $data[0]*3600 + $data[1]*60;
                $data      = explode(':', $end[$id]);
                $endDate   = $data[0]*3600 + $data[1]*60;

                // Si le cours existe déjà, on le récupère, sinon on crée un nouveau cours
                $course = $this->getOrCreateCourse($name, $department[$id], $gr);

                // Si le groupe n'est pas connu on le rajoute
                if (!in_array($gr, $this->knownCourses[$name]['groups'])) {
                    $course->addGroup($gr);
                }

                // On ajoute l'objet à ce cours
                $courseItem = new CourseItem();
                $courseItem->setStartDate($date + $startDate);
                $courseItem->setEndDate($date + $endDate);
                $courseItem->setLocation($location[$id]);
                $courseItem->setGroup($gr);
                $courseItem->setCourse($course);
                $this->manager->persist($courseItem);
            }
        }

        $this->manager->flush();
    }

    private function emptyCourseitems()
    {
        $query = $this->manager->createQuery('DELETE FROM KIPublicationBundle:CourseItem c WHERE c.startDate > :time');
        $query->setParameter('time', mktime(0, 0, 0));
        $query->execute();
    }

    private function getKnownCourses()
    {
        $results = $this->courseRepository->findAll();
        $courses = array();
        foreach ($results as $course) {
            $courses[$course->getName()] = array(
                'course' => $course,
                'groups' => $course->getGroups()
            );
        }
        return $courses;
    }

    private function getOrCreateCourse($name, $department, $group)
    {
        if (array_key_exists($name, $this->knownCourses)) {
            $course = $this->knownCourses[$name]['course'];
        } else {
            $course = new Course();
            $course->setName($name);
            $course->setDepartment($department);
            $course->setSemester(0);
            $course->addGroup($group);

            $this->manager->persist($course);
            $this->knownCourses[$name] = array(
                'course' => $course,
                'groups' => array($group)
            );
        }

        return $course;
    }
}
