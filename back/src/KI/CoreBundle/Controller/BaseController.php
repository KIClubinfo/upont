<?php

namespace KI\CoreBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// Fonctions génériques
abstract class BaseController extends CoreController
{
    protected $class;
    protected $bundle;
    protected $className;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $manager;
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    protected $repository;
    protected $form;
    protected $save;

    /**
     * Initialise le controleur de base
     * @param string $class  Le nom de la classe sur laquelle se baser
     * @param string $bundle Le nom du bundle dans lequel se trouve cette classe
     */
    public function initialize($class, $bundle)
    {
        $this->class      = 'KI\\'.$bundle.'Bundle\Entity\\'.$class;
        $this->bundle     = $bundle;
        $this->className  = $class;

        $this->manager    = $this->getDoctrine()->getManager();
        $this->repository = $this->manager->getRepository('KI'.$bundle.'Bundle:'.$class);
        $this->form       = 'KI\\'.$bundle.'Bundle\Form\\'.$class.'Type';

        parent::setUser();
    }

    /**
     * Permet de changer le type d'objet sur lequel s'appuie le controleur
     * @param  string $class Le nom de la nouvelle classe. Si laissé à null,
     * revient à la classe précédent(celle-ci est sauvegardée à chaque changement)
     */
    protected function switchClass($class = null)
    {
        // On garde en mémoire la classe précédente
        if ($class === null) {
            $class = $this->save;
        } else {
            $this->save = $this->className;
        }

        // À priori, une sous ressource garde le même namespace
        $this->initialize($class, $this->bundle);
    }

    /**
     * Recherche une entité selon son slug
     * @param  string $slug
     * @return mixed
     * @throws NotFoundHttpException Si l'entité n'est pas trouvée
     */
    protected function findBySlug($slug)
    {
        if (!method_exists($this->class, 'setSlug')) {
            $item = $this->repository->findOneById($slug);
        } else {
            if ($this->className == 'User') {
                $item = $this->repository->findOneByUsername($slug);
            } else {
                $item = $this->repository->findOneBySlug($slug);
            }
        }
        if (!$item instanceof $this->class) {
            throw new NotFoundHttpException('Objet '.$this->className.' non trouvé');
        }

        return $item;
    }

    /**
     * Sert à checker si l'user actuel est membre du club au nom duquel il poste
     * @param  string $club
     * @return boolean
     */
    protected function isClubMember($club = null)
    {
        if ($this->is('ADMISSIBLE')) {
            return false;
        }

        // On vérifie que la requete est valide.
        // Si aucun club n'est précisé, c'est qu'on publie à son nom
        // (par exemple message perso) donc ok
        $request = $this->get('request_stack')->getCurrentRequest()->request;
        if (!$request->has('authorClub') && $club === null) {
            return $this->is('USER');
        }

        $repo = $this->manager->getRepository('KIUserBundle:Club');
        $club = $repo->findOneBySlug($request->has('authorClub') ? $request->get('authorClub') : $club);

        if (!$club) {
            return false;
        }

        // On vérifie que l'utilisateur fait bien partie du club
        return $this->get('ki_user.service.permission')->isClubMember($this->user, $club);
    }

    /**
     * Sert à checker si l'user courant est membre du foyer actuel
     * @return boolean
     */
    protected function isFoyerMember()
    {
        return $this->isClubMember('foyer')
        && in_array($this->user->getPromo(), $this->getConfig('foyer.trust'));
    }
}
