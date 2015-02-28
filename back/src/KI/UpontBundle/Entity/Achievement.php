<?php

namespace KI\UpontBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @JMS\ExclusionPolicy("all")
 */
class Achievement
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Identifiant de l'achievement
     * @ORM\Column(name="achievement", type="integer")
     * @Assert\Type("integer")
     */
    protected $achievement;

    /**
     * @JMS\VirtualProperty()
     */
    public function name()
    {
        return self::$achievements[$this->achievement]['name'];
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function description()
    {
        return self::$achievements[$this->achievement]['description'];
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function points()
    {
        return self::$achievements[$this->achievement]['points'];
    }

    /**
     * @JMS\VirtualProperty()
     */
    public function image()
    {
        return self::$achievements[$this->achievement]['image'];
    }

    // Retourne des données intéressantes sur le niveau actuel et le prochain
    public static function getLevel($points)
    {
        $number = 0;
        $current = self::$levels[0];

        foreach (self::$levels as $key => $level) {
            if ($points >= $level['points']) {
                $current = $level;
                $number = $key;
            }
        }

        return array(
            'number' => $number,
            'current' => $current,
            'next' => isset(self::$levels[$number + 1]) ? self::$levels[$number + 1] : null,
        );
    }

    public function __construct($achievement)
    {
        $this->achievement = $achievement;
    }

    // EN CAS DE RAJOUT D'ACHIEVEMENT, MODIFIER LE FICHIER DE DATAFIXTURES !!!
    // Attention, ne pas changer les IDs des achievements à la légère !!!
    // En effet les fonctions de check dépendent des IDs, voir le fichier
    // EventListener > AchievementCheck
    const LOGIN = 0;
    const PHOTO = 1;
    const COURSES = 2;
    const PROFILE = 3;
    const FULL_PROFILE = 4;
    const ICS_CALENDAR = 5;
    const MOBILE_APP = 6;
    const NEWS_READ = 7;
    const DOWNLOADER = 8;
    const SUPER_DOWNLOADER = 9;
    const ULTIMATE_DOWNLOADER = 10;
    const LEECH = 11;
    const POLL_ANSWER = 12;
    const EVENT_ATTEND = 13;
    const EVENT_SHOTGUN = 14;
    const EVENT_PERSO = 15;
    const POOKIE = 16;
    const SPIRIT = 17;
    const POLL_CREATE = 18;
    const NEWS_CREATE = 19;
    const EVENT_CREATE = 20;
    const OBJECT_LOST = 21;
    const OBJECT_FOUND = 22;
    const COURSES_OFFER = 23;
    const COURSES_SHOTGUN = 24;
    const CONFIG_NOTIFICATIONS = 25;
    const GITLAB = 26;
    const DVP = 27;
    const FOYER = 28;
    const MEDIATEK = 29;
    const PASSWORD = 30;
    const EVENT_OLD = 31;
    const BUG_REPORT = 32;
    const TUTO_MAIL = 33;
    const TUTO_LINUX = 34;
    const BUG_CONTACT = 35;
    const KIEN = 36;
    const MODO = 37;
    const ADMIN = 38;
    const BUG_SOLVE = 39;
    const PONTLYVALENT = 40;
    const PONTBINOSCOPE = 41;
    const FULL_PONTBINOSCOPE = 42;
    const GAME_PLAY = 43;
    const GAME_BEFORE = 44;
    const GAME_SELF = 45;
    const GAME_NEXT = 46;
    const GAME_OLD = 47;
    const UNLOCKER = 48;
    const CRAZY_UNLOCKER = 49;
    const TOTAL_UNLOCKER = 50;

    // EN CAS DE RAJOUT D'ACHIEVEMENT, MODIFIER LE FICHIER DE DATAFIXTURES !!!
    protected static $achievements = array(
    self::LOGIN => array(
        'name'        => 'Ponts inside',
        'description' => 'Se logger sur le site',
        'points'      => 10,
        'image'       => '',
    ),
    self::PHOTO => array(
        'name'        => 'Photogénique',
        'description' => 'Changer la photo de son profil',
        'points'      => 10,
        'image'       => '',
    ),
    self::COURSES => array(
        'name'        => 'Travailleur',
        'description' => 'Choisir ses cours',
        'points'      => 20,
        'image'       => '',
    ),
    self::PROFILE => array(
        'name'        => 'Autobiographie',
        'description' => 'Remplir ses infos (chambre, téléphone, département, origine, nationalité...)',
        'points'      => 20,
        'image'       => '',
    ),
    self::FULL_PROFILE => array(
        'name'        => 'Data Provider',
        'description' => 'Remplir ses infos étendues (stages, projets...)',
        'points'      => 10,
        'image'       => '',
    ),
    self::ICS_CALENDAR => array(
        'name'        => 'Smart',
        'description' => 'Synchroniser le calendrier avec son téléphone',
        'points'      => 30,
        'image'       => '',
    ),
    self::MOBILE_APP => array(
        'name'        => 'Connecté',
        'description' => 'Installer l\'application mobile',
        'points'      => 50,
        'image'       => '',
    ),
    self::NEWS_READ => array(
        'name'        => 'Au courant',
        'description' => 'Lire une news complète',
        'points'      => 5,
        'image'       => '',
    ),
    self::DOWNLOADER => array(
        'name'        => 'Downloader',
        'description' => 'Télécharger un fichier sur Ponthub',
        'points'      => 5,
        'image'       => '',
    ),
    self::SUPER_DOWNLOADER => array(
        'name'        => 'Super Downloader',
        'description' => 'Télécharger plus de 100Go sur Ponthub',
        'points'      => 20,
        'image'       => '',
    ),
    self::ULTIMATE_DOWNLOADER => array(
        'name'        => 'Ultimate Downloader',
        'description' => 'Télécharger plus de 500Go sur Ponthub',
        'points'      => 100,
        'image'       => '',
    ),
    self::LEECH => array(
        'name'        => 'Ça va pomper sévère !',
        'description' => 'Suggérer un fichier sur Ponthub',
        'points'      => 20,
        'image'       => '',
    ),
    self::POLL_ANSWER => array(
        'name'        => 'Sondé',
        'description' => 'Répondre à un sondage',
        'points'      => 30,
        'image'       => '',
    ),
    self::EVENT_ATTEND => array(
        'name'        => 'Will be there !',
        'description' => 'Participer à un event',
        'points'      => 10,
        'image'       => '',
    ),
    self::EVENT_SHOTGUN => array(
        'name'        => 'Shotgun !',
        'description' => 'Réussir un shotgun',
        'points'      => 30,
        'image'       => '',
    ),
    self::EVENT_PERSO => array(
        'name'        => 'Égoïste',
        'description' => 'Créer un event perso',
        'points'      => 20,
        'image'       => '',
    ),
    self::POOKIE => array(
        'name'        => 'Pookie',
        'description' => 'Télécharger un fichier d\'annale',
        'points'      => 10,
        'image'       => '',
    ),
    self::SPIRIT => array(
        'name'        => 'Spirit',
        'description' => 'Devenir membre d\'un club',
        'points'      => 10,
        'image'       => '',
    ),
    self::POLL_CREATE => array(
        'name'        => 'Référendum',
        'description' => 'Créer un sondage pour un club',
        'points'      => 50,
        'image'       => '',
    ),
    self::NEWS_CREATE => array(
        'name'        => 'Nouvelliste',
        'description' => 'Écrire une news pour un club',
        'points'      => 30,
        'image'       => '',
    ),
    self::EVENT_CREATE => array(
        'name'        => 'Organisateur',
        'description' => 'Créer un event pour un club',
        'points'      => 20,
        'image'       => '',
    ),
    self::OBJECT_LOST => array(
        'name'        => 'Distrait',
        'description' => 'Perdre un objet',
        'points'      => 20,
        'image'       => '',
    ),
    self::OBJECT_FOUND => array(
        'name'        => 'Altruiste',
        'description' => 'Retrouver un objet',
        'points'      => 20,
        'image'       => '',
    ),
    self::COURSES_OFFER => array(
        'name'        => 'C\'est 15€ de l\'heure non négociables',
        'description' => 'Offrir un petit cours',
        'points'      => 20,
        'image'       => '',
    ),
    self::COURSES_SHOTGUN => array(
        'name'        => 'Shark',
        'description' => 'Shotgun un petit cours',
        'points'      => 20,
        'image'       => '',
    ),
    self::CONFIG_NOTIFICATIONS => array(
        'name'        => 'Configurateur',
        'description' => 'Configurer ses notifications',
        'points'      => 20,
        'image'       => '',
    ),
    self::GITLAB => array(
        'name'        => 'Développeur',
        'description' => 'Créer un compte Gitlab',
        'points'      => 30,
        'image'       => '',
    ),
    self::DVP => array(
        'name'        => 'Végétarien',
        'description' => 'Commander un panier DVP',
        'points'      => 30,
        'image'       => '',
    ),
    self::FOYER => array(
        'name'        => 'Ruiné',
        'description' => 'Avoir un solde foyer négatif',
        'points'      => -100,
        'image'       => '',
    ),
    self::MEDIATEK => array(
        'name'        => 'Aime les BDs',
        'description' => 'Faire un emprunt sur la Mediatek',
        'points'      => 20,
        'image'       => '',
    ),
    self::PASSWORD => array(
        'name'        => 'Non, ce n\'était pas "password1234"',
        'description' => 'Oublier son mot de passe',
        'points'      => 10,
        'image'       => '',
    ),
    self::EVENT_OLD => array(
        'name'        => 'C\'était mieux avant',
        'description' => 'Aller voir s\'il n\'y a pas des events avant l\'an 2000',
        'points'      => 50,
        'image'       => '',
    ),
    self::BUG_REPORT => array(
        'name'        => 'H3LLLP UPON SA BEUG!!!!',
        'description' => 'Reporter un bug',
        'points'      => 20,
        'image'       => '',
    ),
    self::TUTO_MAIL => array(
        'name'        => 'Enfin quelqu\'un de bien',
        'description' => 'Suivre le tuto mails',
        'points'      => 30,
        'image'       => '',
    ),
    self::TUTO_LINUX => array(
        'name'        => 'Viens, Linux c\'est bien, viens, voit ce qu\'on peut faire...',
        'description' => 'Suivre le tutoriel linux',
        'points'      => 50,
        'image'       => '',
    ),
    self::BUG_CONTACT => array(
        'name'        => 'Technophobe',
        'description' => 'Contacter le KI pour un dépannage matériel/logiciel',
        'points'      => 10,
        'image'       => '',
    ),
    self::KIEN => array(
        'name'        => 'KIen',
        'description' => 'Faire partie du KI',
        'points'      => 20,
        'image'       => '',
    ),
    self::MODO => array(
        'name'        => 'Complexe de supériorité',
        'description' => 'Être modo',
        'points'      => 10,
        'image'       => '',
    ),
    self::ADMIN => array(
        'name'        => 'Appelez-moi Dieu',
        'description' => 'Être admin',
        'points'      => 50,
        'image'       => '',
    ),
    self::BUG_SOLVE => array(
        'name'        => 'Technophile',
        'description' => 'Résoudre un problème de dépannage',
        'points'      => 20,
        'image'       => '',
    ),
    self::PONTLYVALENT => array(
        'name'        => 'Commère',
        'description' => 'Écrire 5 commentaires sur le Pontlyvalent',
        'points'      => 30,
        'image'       => '',
    ),
    self::PONTBINOSCOPE => array(
        'name'        => 'Toi, j\'te connais !',
        'description' => 'Connaitre 10 personnes sur le Pontbinoscope',
        'points'      => 10,
        'image'       => '',
    ),
    self::FULL_PONTBINOSCOPE => array(
        'name'        => 'Sociable',
        'description' => 'Connaitre 100 personnes sur le Pontbinoscope',
        'points'      => 40,
        'image'       => '',
    ),
    self::GAME_PLAY => array(
        'name'        => 'The Game',
        'description' => 'Jouer au jeu du Pontbinoscope',
        'points'      => 20,
        'image'       => '',
    ),
    self::GAME_BEFORE => array(
        'name'        => 'Puceau, pas puceau',
        'description' => 'Réussir 100% sur la promo d\'en dessous dans le jeu du Pontbinoscope',
        'points'      => 30,
        'image'       => '',
    ),
    self::GAME_SELF => array(
        'name'        => 'Connaisseur',
        'description' => 'Réussir un 100% sur sa promo dans le jeu du Pontbinoscope',
        'points'      => 10,
        'image'       => '',
    ),
    self::GAME_NEXT => array(
        'name'        => 'Bientôt vieux cons',
        'description' => 'Réussir un 100% sur la promo d\'au dessus dans le jeu du Pontbinoscope',
        'points'      => 20,
        'image'       => '',
    ),
    self::GAME_OLD => array(
        'name'        => 'JRP\'1747',
        'description' => 'Réussir un 100% en mode hardcore sur une promo de vieux dans le jeu du Pontbinoscope',
        'points'      => 100,
        'image'       => '',
    ),
    self::UNLOCKER => array(
        'name'        => 'Unlocker',
        'description' => 'Compléter 10 achievements',
        'points'      => '+10%',
        'image'       => '',
    ),
    self::CRAZY_UNLOCKER => array(
        'name'        => 'Crazy Unlocker',
        'description' => 'Compléter 50% des achievements',
        'points'      => '+15%',
        'image'       => '',
    ),
    self::TOTAL_UNLOCKER => array(
        'name'        => 'Total Unlocker',
        'description' => 'Compléter 90% des achievements',
        'points'      => '+75%',
        'image'       => '',
    ),
);

    protected static $levels = array(
        array(
            'name'        => 'Newbie',
            'description' => '',
            'points'      => 0,
            'image'       => '',
        ),
        array(
            'name'        => 'Apprenti',
            'description' => '',
            'points'      => 50,
            'image'       => '',
        ),
        array(
            'name'        => 'Disciple',
            'description' => '',
            'points'      => 100,
            'image'       => '',
        ),
        array(
            'name'        => 'Favori',
            'description' => '',
            'points'      => 200,
            'image'       => '',
        ),
        array(
            'name'        => 'Maître',
            'description' => '',
            'points'      => 350,
            'image'       => '',
        ),
        array(
            'name'        => 'Grand Maître',
            'description' => '',
            'points'      => 500,
            'image'       => '',
        ),
        array(
            'name'        => 'Gourou',
            'description' => '',
            'points'      => 750,
            'image'       => '',
        ),
        array(
            'name'        => 'Grand Manitou',
            'description' => '',
            'points'      => 1000,
            'image'       => '',
        ),
        array(
            'name'        => 'Génie',
            'description' => '',
            'points'      => 1500,
            'image'       => '',
        ),
        array(
            'name'        => 'Dieu',
            'description' => '',
            'points'      => 2000,
            'image'       => '',
        ),
    );

    //===== GENERATED AUTOMATICALLY =====//

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set achievement
     *
     * @param integer $achievement
     * @return Achievement
     */
    public function setIdAchievement($achievement)
    {
        $this->achievement = $achievement;

        return $this;
    }

    /**
     * Get achievement
     *
     * @return integer
     */
    public function getIdA()
    {
        return $this->achievement;
    }
}
