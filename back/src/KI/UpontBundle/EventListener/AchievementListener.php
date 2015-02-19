<?php

namespace KI\UpontBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use KI\UpontBundle\Entity\Achievement;
use KI\UpontBundle\Entity\AchievementUser;
use KI\UpontBundle\Entity\Notification;
use KI\UpontBundle\Event\AchievementCheckEvent;

class AchievementListener
{
    protected $container;
    protected $manager;
    protected $user;
    // Liste des achievements unlockés actuellement
    protected $achievements = array();

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->manager = $this->container->get('doctrine')->getManager();

        $repoAU = $this->manager->getRepository('KIUpontBundle:AchievementUser');
        $token = $this->container->get('security.context')->getToken();
        $this->user = $token === null ? null : $token->getUser();
        if ($this->user !== null) {
            $response = $repoAU->findByUser($this->user);
            foreach($response as $achievementUser)
                $this->achievements[] = $achievementUser->getAchievement();
        }
    }

    // Check si un achievement donné est accompli, si oui envoie une notification
    public function check(AchievementCheckEvent $event)
    {
        // On vérifie tout d'abord si l'achievement concerné n'est pas déjà reçu
        $achievement = $event->getAchievement();

        if(!$this->user instanceof \KI\UpontBundle\Entity\Users\User || in_array($achievement, $this->achievements))
            return false;

        // Sinon, on lance le check associé
        $check = false;
        $method = 'check' . $achievement->getAchievement();
        if(method_exists($this, $method))
            $check = $this->$method();

        // Si le check est bon, on ajoute l'achievement et on crée une notification
        if(!$check)
            return false;

        $achievementUser = new AchievementUser();
        $repoA = $this->manager->getRepository('KIUpontBundle:Achievement');
        $achievementUser->setAchievement($repoA->findOneByAchievement($achievement->getAchievement()));
        $achievementUser->setUser($this->user);
        $achievementUser->setDate(time());
        $this->manager->persist($achievementUser);

        $notification = new Notification($achievement->name(), $achievement->description(), 'to');
        $notification->addRecipient($this->user);
        $this->manager->persist($notification);

        $this->manager->flush();
        return true;
    }

    // Fonctions de check correspondant aux divers achievements
    // Attention, ne pas changer les IDs des achievements à la légère !!!
    // Les checks qui retournent true sont en fait assez simples :
    // L'achievement associé est donc du type "faire ça action au moins une fois"
    // Les check marqués d'un TODO ne sont pas encore faits
    // Ceux marqués par un FIXME ne sont pas encore déclenché par un event dispatch

    // FIXME
    // ID : 0
    // Ponts inside
    // Se logger sur le site
    public function check0()
    {
        return true;
    }

    // FIXME
    // ID : 1
    // Photogénique
    // Changer la photo de son profil
    public function check1()
    {
        return true;
    }

    // FIXME
    // ID : 2
    // Travailleur
    // Choisir ses cours
    public function check2()
    {
        return true;
    }

    // FIXME
    // ID : 3
    // Autobiographie
    // Remplir ses infos (chambre, téléphone, département, origine, nationalité...)
    public function check3()
    {
        return true;
    }

    // FIXME
    // ID : 4
    // Data Provider
    // Remplir ses infos étendues (stages, projets...)
    public function check4()
    {
        return true;
    }

    // FIXME
    // ID : 5
    // Smart
    // Synchroniser le calendrier avec son téléphone
    public function check5()
    {
        return true;
    }

    // FIXME
    // ID : 6
    // Connecté
    // Installer l'application mobile
    public function check6()
    {
        return true;
    }

    // FIXME
    // ID : 7
    // Au courant
    // Lire une news complète
    public function check7()
    {
        return true;
    }

    // FIXME
    // ID : 8
    // Downloader
    // Télécharger un fichier sur Ponthub
    public function check8()
    {
        return true;
    }

    // TODO
    // FIXME
    // ID : 9
    // Super Downloader
    // Télécharger plus de 100Go sur Ponthub
    public function check9()
    {
        return false;
    }

    // TODO
    // FIXME
    // ID : 10
    // Ultimate Downloader
    // Télécharger plus de 500Go sur Ponthub
    public function check10()
    {
        return false;
    }

    // FIXME
    // ID : 11
    // Ça va pomper sévère !
    // Suggérer un fichier sur Ponthub
    public function check11()
    {
        return true;
    }

    // FIXME
    // ID : 12
    // Sondé
    // Répondre à un sondage
    public function check12()
    {
        return true;
    }

    // FIXME
    // ID : 13
    // Will be there !
    // Participer à un event
    public function check13()
    {
        return true;
    }

    // FIXME
    // ID : 14
    // Shotgun !
    // Réussir un shotgun
    public function check14()
    {
        return true;
    }

    // FIXME
    // ID : 15
    // Égoïste
    // Créer un event perso
    public function check15()
    {
        return true;
    }

    // FIXME
    // ID : 16
    // Pookie
    // Télécharger un fichier d'annale
    public function check16()
    {
        return true;
    }

    // TODO
    // FIXME
    // ID : 17
    // Spirit
    // Devenir membre d'un club
    public function check17()
    {
        return false;
    }

    // FIXME
    // ID : 18
    // Référendum
    // Créer un sondage pour un club
    public function check18()
    {
        return true;
    }

    // FIXME
    // ID : 19
    // Nouvelliste
    // Écrire une news pour un club
    public function check19()
    {
        return true;
    }

    // FIXME
    // ID : 20
    // Organisateur
    // Créer un event pour un club
    public function check20()
    {
        return true;
    }

    // FIXME
    // ID : 21
    // Distrait
    // Perdre un objet
    public function check21()
    {
        return true;
    }

    // FIXME
    // ID : 22
    // Altruiste
    // Retrouver un objet
    public function check22()
    {
        return true;
    }

    // FIXME
    // ID : 23
    // C'est 15€ de l'heure non négociables
    // Offrir un petit cours
    public function check23()
    {
        return true;
    }

    // FIXME
    // ID : 24
    // Shark
    // Shotgun un petit cours
    public function check24()
    {
        return true;
    }

    // FIXME
    // ID : 25
    //
    //
    public function check25()
    {
        return false;
    }

    // FIXME
    // ID : 26
    //
    //
    public function check26()
    {
        return false;
    }

    // FIXME
    // ID : 27
    //
    //
    public function check27()
    {
        return false;
    }

    // FIXME
    // ID : 28
    //
    //
    public function check28()
    {
        return false;
    }

    // FIXME
    // ID : 29
    //
    //
    public function check29()
    {
        return false;
    }

    // FIXME
    // ID : 30
    //
    //
    public function check30()
    {
        return false;
    }

    // FIXME
    // ID : 31
    //
    //
    public function check31()
    {
        return false;
    }

    // FIXME
    // ID : 32
    //
    //
    public function check32()
    {
        return false;
    }

    // FIXME
    // ID : 33
    //
    //
    public function check33()
    {
        return false;
    }

    // FIXME
    // ID : 34
    //
    //
    public function check34()
    {
        return false;
    }

    // FIXME
    // ID : 35
    //
    //
    public function check35()
    {
        return false;
    }

    // FIXME
    // ID : 36
    //
    //
    public function check36()
    {
        return false;
    }

    // FIXME
    // ID : 37
    //
    //
    public function check37()
    {
        return false;
    }

    // FIXME
    // ID : 38
    //
    //
    public function check38()
    {
        return false;
    }

    // FIXME
    // ID : 39
    //
    //
    public function check39()
    {
        return false;
    }

    // FIXME
    // ID : 40
    //
    //
    public function check40()
    {
        return false;
    }

    // TODO
    // FIXME
    // ID : 41
    // Toi, j'te connais !
    // Connaitre 10 personnes sur le Pontbinoscope
    public function check41()
    {
        return false;
    }

    // TODO
    // FIXME
    // ID : 42
    // Sociable
    // Connaitre 100 personnes sur le Pontbinoscope
    public function check42()
    {
        return false;
    }

    // FIXME
    // ID : 43
    //
    //
    public function check43()
    {
        return false;
    }

    // FIXME
    // ID : 44
    //
    //
    public function check44()
    {
        return false;
    }

    // FIXME
    // ID : 45
    //
    //
    public function check45()
    {
        return false;
    }

    // FIXME
    // ID : 46
    //
    //
    public function check46()
    {
        return false;
    }

    // FIXME
    // ID : 47
    //
    //
    public function check47()
    {
        return false;
    }

    // FIXME
    // ID : 48
    //
    //
    public function check48()
    {
        return false;
    }

    // FIXME
    // ID : 49
    //
    //
    public function check49()
    {
        return false;
    }

    // FIXME
    // ID : 50
    //
    //
    public function check50()
    {
        return false;
    }

    // FIXME
    // ID : 51
    //
    //
    public function check51()
    {
        return false;
    }
}
