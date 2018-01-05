<?php

namespace App\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use App\Entity\Course;
use App\Entity\CourseUser;
use App\Entity\Achievement;
use App\Entity\User;
use App\Event\AchievementCheckEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CourseHelper
{
    protected $courseUserRepository;
    protected $dispatcher;
    protected $manager;

    public function __construct(EntityRepository $courseUserRepository,
                                EventDispatcherInterface $dispatcher,
                                EntityManager $manager)
    {
        $this->courseUserRepository = $courseUserRepository;
        $this->dispatcher           = $dispatcher;
        $this->manager              = $manager;
    }

    /**
     * Inscrit un élève à un cours
     * @param  Course $course
     * @param  User   $user
     * @param  int    $group
     * @throws BadRequestHttpException si les deux sont déjà reliés
     * @throws BadRequestHttpException si le groupe n'existe pas pour ce cours
     */
    public function linkCourseUser(Course $course, User $user, $group = 0)
    {
        $link = $this->courseUserRepository->findBy(['course' => $course, 'user' => $user]);

        // On crée la relation si elle n'existe pas déjà
        if (count($link) != 0) {
            throw new BadRequestHttpException('La relation entre Course et User existe déjà');
        }

        // Création de l'entité relation
        $link = new CourseUser();
        $link->setCourse($course);
        $link->setUser($user);

        if (!in_array($group, $course->getGroups())) {
            throw new BadRequestHttpException('Ce groupe n\'existe pas.');
        }
        $link->setGroup($group);

        $achievementCheck = new AchievementCheckEvent(Achievement::COURSES);
        $this->dispatcher->dispatch('upont.achievement', $achievementCheck);

        $this->manager->persist($link);
        $this->manager->flush();
    }

    /**
     * Désinscrit un élève d'un cours
     * @param  Course $course
     * @param  User   $user
     * @throws BadRequestHttpException si les deux ne sont pas reliés
     */
    public function unlinkCourseUser(Course $course, User $user)
    {
        $link = $this->courseUserRepository->findBy(['course' => $course, 'user' => $user]);

        if (count($link) != 1) {
            throw new BadRequestHttpException('Relation entre Course et User non trouvée');
        }

        $this->manager->remove($link[0]);
        $this->manager->flush();
    }
}
