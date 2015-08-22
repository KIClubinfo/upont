<?php

namespace KI\UserBundle\Listener;

use Doctrine\ORM\EntityManager;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Entity\AchievementUser;
use KI\UserBundle\Entity\User;
use KI\UserBundle\Event\AchievementCheckEvent;
use Symfony\Component\Security\Core\SecurityContext;

class AchievementCheckListener
{
    protected $manager;
    protected $securityContext;

    // L'utilisateur qui tente d'obtenir l'achievement
    protected $user;

    // Liste des achievements unlockés actuellement (identifiants seulement)
    protected $achievements = array();

    public function __construct(EntityManager $manager, SecurityContext $securityContext)
    {
        $this->manager         = $manager;
        $this->securityContext = $securityContext;

        $token = $this->securityContext->getToken();
        $this->user = $token === null ? null : $token->getUser();

        if ($this->user !== null) {
            $this->loadUser($this->user);
        }
    }

    private function loadUser(User $user) {
        $this->user = $user;
        $repoAU = $this->manager->getRepository('KIUserBundle:AchievementUser');
        $response = $repoAU->findByUser($this->user);

        foreach ($response as $achievementUser) {
            $this->achievements[] = $achievementUser->getAchievement()->getIdA();
        }
    }

    // Check si un achievement donné est accompli, si oui envoie une notification
    public function check(AchievementCheckEvent $event)
    {
        // On vérifie tout d'abord si l'achievement concerné n'est pas déjà reçu
        $achievement = $event->getAchievement();

        // On peut préciser l'user pour les routes sans authentification
        if ($event->getuser() !== null) {
            $this->loadUser($event->getUser());
        }
        if (!$this->user instanceof User
            || in_array($achievement->getIdA(), $this->achievements))
            return false;

        // Sinon, on lance le check associé
        $check = false;
        $method = 'check'.$achievement->getIdA();
        if (method_exists($this, $method))
            $check = $this->$method();

        // Si le check est bon
        if (!$check)
            return false;

        // On ajoute l'achievement
        $achievementUser = new AchievementUser();
        $repoA = $this->manager->getRepository('KIUserBundle:Achievement');
        $achievementUser->setAchievement($repoA->findOneByAchievement($achievement->getIdA()));
        $achievementUser->setUser($this->user);
        $achievementUser->setDate(time());
        $achievementUser->setSeen(false);
        $this->manager->persist($achievementUser);
        $this->achievements[] = $achievement->getIdA();

        // Achievements basés sur le nombre d'achievements
        if (count($this->achievements) >= 10) {
            $achievementCheck = new AchievementCheckEvent(Achievement::UNLOCKER);
            $this->check($achievementCheck);
        }

        $total = count(Achievement::getConstants());
        if (count($this->achievements) >= $total*0.5) {
            $achievementCheck = new AchievementCheckEvent(Achievement::CRAZY_UNLOCKER);
            $this->check($achievementCheck);
        }
        if (count($this->achievements) >= $total*0.9) {
            $achievementCheck = new AchievementCheckEvent(Achievement::TOTAL_UNLOCKER);
            $this->check($achievementCheck);
        }

        $this->manager->flush();
        return true;
    }

    // Calcule le nombre de points de l'utilisateur
    public function points()
    {
        $points = 0;
        $factor = 1;

        // On regarde quels achievements sont locked et on en profite pour
        // calculer le nombre de points de l'utilisateur obtenus par les
        // achievements
        foreach ($this->achievements as $achievement) {
            $achievement = new Achievement($achievement);
            if (gettype($achievement->points()) == 'integer') {
                $points += $achievement->points();
            } else if ($achievement->points() == '+10%') {
                $factor += 0.1;
            } else if ($achievement->points() == '+15%') {
                $factor += 0.15;
            } else if ($achievement->points() == '+75%') {
                $factor += 0.75;
            }
        }
        return ceil($factor*$points);
    }

    // Fonctions de check correspondant aux divers achievements
    // Attention, ne pas changer les IDs des achievements à la légère !!!
    // Les checks qui retournent true sont en fait assez simples :
    // L'achievement associé est donc du type "faire ça action au moins une fois"

    // Ponts inside
    // Se logger sur le site
    public function check0() { return true; }

    // Fouilleur
    // Faire le tour du site
    public function check5() { return true; }

    // Photogénique
    // Changer la photo de son profil
    public function check10() { return true; }

    // Travailleur
    // Choisir ses cours
    public function check20() { return true; }

    // Autobiographie
    // Remplir ses infos (chambre, téléphone, département, origine, nationalité...)
    public function check30()
    {
        return !empty($this->user->getDepartment()) && !empty($this->user->getPromo()) && !empty($this->user->getLocation()) && !empty($this->user->getNationality()) && !empty($this->user->getPhone()) && !empty($this->user->getOrigin());
    }

    // Remplir ses infos étendues (stages, projets...)
    //public function check4() { return true; }

    // Smart
    // Synchroniser le calendrier avec son téléphone
    public function check40() { return true; }

    // Connecté
    // Installer l'application mobile
    //public function check6() { return true; }

    // Downloader
    // Télécharger un fichier sur Ponthub
    public function check50() { return true; }

    // Super Downloader
    // Télécharger plus de 100Go sur Ponthub
    public function check60()
    {
        return $this->totalPontHubSize() > (100*1024*1024*1024);
    }

    // Ultimate Downloader
    // Télécharger plus de 500Go sur Ponthub
    public function check70()
    {
        return $this->totalPontHubSize() > (500*1024*1024*1024);
    }

    private function totalPontHubSize()
    {
        $repo = $this->manager->getRepository('KIPonthubBundle:PonthubFileUser');
        $downloads = $repo->findBy(array('user' => $this->user));
        $total = 0;

        foreach ($downloads as $download) {
            $total += $download->getFile()->getSize();
        }
        return $total;
    }

    // Will be there !
    // Participer à un event
    public function check80() { return true; }

    // Pookie
    // Uploader un fichier d'annale
    public function check90() { return true; }

    // Spirit
    // Être membre d'un club
    public function check100()
    {
        $repo = $this->manager->getRepository('KIUserBundle:ClubUser');
        $return = $repo->findBy(array('user' => $this->user));
        return count($return) > 0;
    }

    // Nouvelliste
    // Écrire une news pour un club
    public function check110() { return true; }

    // Organisateur
    // Créer un event pour un club
    public function check120() { return true; }

    // Ruiné
    // Avoir un solde foyer négatif
    public function check130()
    {
        // On enlève l'achievement opposé (solde positif)
        $repoA = $this->manager->getRepository('KIUserBundle:Achievement');
        $oAchievement = $repoA->findOneByAchievement(Achievement::FOYER_BIS);

        $repoAU = $this->manager->getRepository('KIUserBundle:AchievementUser');
        $achievementUsers = $repoAU->findBy(array('achievement' => $oAchievement, 'user' => $this->user));

        if (count($achievementUsers) == 1) {
            $this->manager->remove($achievementUsers[0]);
            $this->manager->flush();
        }
        return true;
    }

    // Ruiné
    // Avoir un solde foyer positif
    public function check140()
    {
        // On enlève l'achievement opposé (solde positif)
        $repoA = $this->manager->getRepository('KIUserBundle:Achievement');
        $oAchievement = $repoA->findOneByAchievement(Achievement::FOYER);

        $repoAU = $this->manager->getRepository('KIUserBundle:AchievementUser');
        $achievementUsers = $repoAU->findBy(array('achievement' => $oAchievement, 'user' => $this->user));

        if (count($achievementUsers) == 1) {
            $this->manager->remove($achievementUsers[0]);
            $this->manager->flush();
        }
        return true;
    }

    // Non, ce n'était pas "password1234"
    // Oublier son mot de passe
    public function check150() { return true; }

    // The Game
    // Jouer à La Réponse D
    public function check153() { return true; }

    // Puceau, pas puceau
    // Réussir 100% sur la promo d'en dessous dans La Réponse D
    public function check154() { return true; }

    // Connaisseur
    // Réussir un 100% sur sa promo dans La Réponse D
    public function check155() { return true; }

    // Bientôt vieux cons
    // Réussir un 100% sur la promo d'au dessus dans La Réponse D
    public function check156() { return true; }

    // JRP'1747
    // Réussir un 100% en mode hardcore sur une promo de vieux dans La Réponse D
    public function check157() { return true; }

    // H3LLLP UPON SA BEUG!!!!
    // Reporter un bug
    public function check160() { return true; }

    // Technophobe
    // Contacter le KI pour un dépannage matériel/logiciel
    public function check170() { return true; }

    // KIen
    // Faire partie du KI
    public function check180()
    {
        $repo = $this->manager->getRepository('KIUserBundle:Club');
        $club = $repo->findOneBySlug('ki');
        $repo = $this->manager->getRepository('KIUserBundle:ClubUser');
        $return = $repo->findBy(array('user' => $this->user, 'club' => $club));
        return count($return) == 1;
    }

    // Appelez-moi Dieu
    // Être admin
    public function check190() { return $this->securityContext->isGranted('ROLE_ADMIN'); }

    // Unlocker
    // Compléter 10 achievements
    public function check200() { return true; }

    // Crazy Unlocker
    // Compléter 50% des achievements
    public function check210() { return true; }

    // Total Unlocker
    // Compléter 90% des achievements
    public function check220() { return true; }
}
