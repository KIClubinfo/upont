<?php

namespace KI\UserBundle\Entity;

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

    // Attention, ne pas changer les IDs des achievements à la légère !!!
    // En effet les fonctions de check dépendent des IDs, voir le fichier
    // EventListener > AchievementCheck
    const LOGIN = 0;
    const TOUR = 5;
    const PHOTO = 10;
    const COURSES = 20;
    const PROFILE = 30;
    //const FULL_PROFILE = 4;
    const ICS_CALENDAR = 40;
    //const MOBILE_APP = 6;
    const DOWNLOADER = 50;
    const SUPER_DOWNLOADER = 60;
    const ULTIMATE_DOWNLOADER = 70;
    //const LEECH = 8;
    const EVENT_ATTEND = 80;
    //const EVENT_SHOTGUN = 14;
    //const EVENT_PERSO = 15;
    const POOKIE = 90;
    const SPIRIT = 100;
    const NEWS_CREATE = 110;
    const EVENT_CREATE = 120;
    //const OBJECT_LOST = 21;
    //const OBJECT_FOUND = 22;
    //const COURSES_OFFER = 23;
    //const COURSES_SHOTGUN = 24;
    //const CONFIG_NOTIFICATIONS = 25;
    //const DVP = 27;
    const FOYER = 130;
    const FOYER_BIS = 140;
    //const MEDIATEK = 29;
    //const EVENT_OLD = 31;
    const PASSWORD = 150;
    //const TUTO_MAIL = 33;
    //const TUTO_LINUX = 34;
    //const PONTLYVALENT = 40;
    //const PONTBINOSCOPE = 41;
    //const FULL_PONTBINOSCOPE = 42;
    const GAME_PLAY = 153;
    const GAME_BEFORE = 154;
    const GAME_SELF = 155;
    const GAME_NEXT = 156;
    const GAME_OLD = 157;
    const BUG_REPORT = 160;
    const BUG_CONTACT = 170;
    const KIEN = 180;
    const ADMIN = 190;
    const UNLOCKER = 200;
    const CRAZY_UNLOCKER = 210;
    const TOTAL_UNLOCKER = 220;

    static public function getConstants() {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }

    // EN CAS DE RAJOUT D'ACHIEVEMENT, MODIFIER LE FICHIER DE DATAFIXTURES !!!
    protected static $achievements = array(
    self::LOGIN => array(
        'name'        => 'Ponts inside',
        'description' => 'Se connecter sur le site',
        'points'      => 10,
        'image'       => 'sign-in',
    ),
    self::TOUR => array(
        'name'        => 'The cake is not a lie',
        'description' => 'Faire le tour du site',
        'points'      => 30,
        'image'       => 'smile-o',
    ),
    self::PHOTO => array(
        'name'        => 'Photogénique',
        'description' => 'Changer sa photo de profil',
        'points'      => 10,
        'image'       => 'camera',
    ),
    self::COURSES => array(
        'name'        => 'Travailleur',
        'description' => 'Choisir ses cours',
        'points'      => 20,
        'image'       => 'suitcase',
    ),
    self::PROFILE => array(
        'name'        => 'Autobiographie',
        'description' => 'Remplir ses infos (chambre, téléphone, département, origine, nationalité...)',
        'points'      => 20,
        'image'       => 'book',
    ),
    /*self::FULL_PROFILE => array(
        'name'        => 'Data Provider',
        'description' => 'Remplir ses infos étendues (stages, projets...)',
        'points'      => 10,
        'image'       => 'street-view',
    ),*/
    self::ICS_CALENDAR => array(
        'name'        => 'Smart',
        'description' => 'Synchroniser le calendrier avec son téléphone',
        'points'      => 30,
        'image'       => 'calendar',
    ),
    /*self::MOBILE_APP => array(
        'name'        => 'Connecté',
        'description' => 'Installer l\'application mobile',
        'points'      => 50,
        'image'       => 'mobile',
    ),*/
    self::DOWNLOADER => array(
        'name'        => 'Downloader',
        'description' => 'Télécharger un fichier sur Ponthub',
        'points'      => 5,
        'image'       => 'arrow-down',
    ),
    self::SUPER_DOWNLOADER => array(
        'name'        => 'Super Downloader',
        'description' => 'Télécharger plus de 100Go sur Ponthub',
        'points'      => 20,
        'image'       => 'arrow-circle-down',
    ),
    self::ULTIMATE_DOWNLOADER => array(
        'name'        => 'Ultimate Downloader',
        'description' => 'Télécharger plus de 500Go sur Ponthub',
        'points'      => 100,
        'image'       => 'arrow-circle-o-down',
    ),
    /*self::LEECH => array(
        'name'        => 'Ça va pomper sévère !',
        'description' => 'Suggérer un fichier sur Ponthub',
        'points'      => 20,
        'image'       => 'hand-o-right',
    ),*/
    self::EVENT_ATTEND => array(
        'name'        => 'Will be there !',
        'description' => 'Participer à un événement',
        'points'      => 10,
        'image'       => 'user-plus',
    ),
    /*self::EVENT_SHOTGUN => array(
        'name'        => 'Shotgun !',
        'description' => 'Réussir un shotgun',
        'points'      => 30,
        'image'       => '',
    ),*/
    /*self::EVENT_PERSO => array(
        'name'        => 'Égoïste',
        'description' => 'Créer un event perso',
        'points'      => 20,
        'image'       => 'user-secret',
    ),*/
    self::POOKIE => array(
        'name'        => 'Pookie',
        'description' => 'Uploader une annale',
        'points'      => 10,
        'image'       => 'book',
    ),
    self::SPIRIT => array(
        'name'        => 'Spirit',
        'description' => 'Être membre d\'un club',
        'points'      => 10,
        'image'       => 'users',
    ),
    self::NEWS_CREATE => array(
        'name'        => 'Nouvelliste',
        'description' => 'Écrire une news pour un club',
        'points'      => 20,
        'image'       => 'pencil',
    ),
    self::EVENT_CREATE => array(
        'name'        => 'Organisateur',
        'description' => 'Créer un événement pour un club',
        'points'      => 30,
        'image'       => 'pencil-square-o',
    ),
    /*self::OBJECT_LOST => array(
        'name'        => 'Distrait',
        'description' => 'Perdre un objet',
        'points'      => 20,
        'image'       => '',
    ),*/
    /*self::OBJECT_FOUND => array(
        'name'        => 'Altruiste',
        'description' => 'Retrouver un objet',
        'points'      => 20,
        'image'       => '',
    ),*/
    /*self::COURSES_OFFER => array(
        'name'        => 'C\'est 15€ de l\'heure non négociables',
        'description' => 'Offrir un petit cours',
        'points'      => 20,
        'image'       => '',
    ),*/
    /*self::COURSES_SHOTGUN => array(
        'name'        => 'Shark',
        'description' => 'Shotgun un petit cours',
        'points'      => 20,
        'image'       => '',
    ),*/
    /*self::CONFIG_NOTIFICATIONS => array(
        'name'        => 'Configurateur',
        'description' => 'Configurer ses notifications',
        'points'      => 20,
        'image'       => '',
    ),*/
    /*self::DVP => array(
        'name'        => 'Végétarien',
        'description' => 'Commander un panier DVP',
        'points'      => 30,
        'image'       => 'recycle',
    ),*/
    self::FOYER => array(
        'name'        => 'Ruine',
        'description' => 'Avoir un solde foyer négatif',
        'points'      => -100,
        'image'       => 'warning',
    ),
    self::FOYER_BIS => array(
        'name'        => 'Honorable',
        'description' => 'Avoir un solde foyer positif',
        'points'      => 50,
        'image'       => 'beer',
    ),
    /*self::MEDIATEK => array(
        'name'        => 'Aime les BDs',
        'description' => 'Faire un emprunt sur la Mediatek',
        'points'      => 20,
        'image'       => '',
    ),*/
    self::PASSWORD => array(
        'name'        => 'Non, ce n\'était pas "password1234"',
        'description' => 'Oublier son mot de passe',
        'points'      => 10,
        'image'       => 'lock',
    ),
    /*self::EVENT_OLD => array(
        'name'        => 'C\'était mieux avant',
        'description' => 'Aller voir s\'il n\'y a pas des events avant l\'an 2000',
        'points'      => 50,
        'image'       => '',
    ),*/
    self::BUG_REPORT => array(
        'name'        => 'H3LLLP UPON SA BEUG!!!!',
        'description' => 'Reporter un bug',
        'points'      => 20,
        'image'       => 'bullhorn',
    ),
    /*self::TUTO_MAIL => array(
        'name'        => 'Enfin quelqu\'un de bien',
        'description' => 'Suivre le tuto mails',
        'points'      => 30,
        'image'       => 'envelope',
    ),
    self::TUTO_LINUX => array(
        'name'        => 'Viens, Linux c\'est bien, viens, voit ce qu\'on peut faire...',
        'description' => 'Suivre le tutoriel linux',
        'points'      => 50,
        'image'       => 'linux',
    ),*/
    self::BUG_CONTACT => array(
        'name'        => 'Technophobe',
        'description' => 'Contacter le KI pour un dépannage matériel/logiciel',
        'points'      => 20,
        'image'       => 'exclamation',
    ),
    self::KIEN => array(
        'name'        => 'KIen',
        'description' => 'Faire partie du KI',
        'points'      => 0,
        'image'       => 'signal',
    ),
    self::ADMIN => array(
        'name'        => 'Appelez-moi Dieu',
        'description' => 'Être admin',
        'points'      => 0,
        'image'       => 'diamond',
    ),
    /*self::PONTLYVALENT => array(
        'name'        => 'Commère',
        'description' => 'Écrire 5 commentaires sur le Pontlyvalent',
        'points'      => 30,
        'image'       => '',
    ),*/
    /*self::PONTBINOSCOPE => array(
        'name'        => 'Toi, j\'te connais !',
        'description' => 'Connaitre 10 personnes sur le Pontbinoscope',
        'points'      => 10,
        'image'       => '',
    ),*/
    /*self::FULL_PONTBINOSCOPE => array(
        'name'        => 'Sociable',
        'description' => 'Connaitre 100 personnes sur le Pontbinoscope',
        'points'      => 40,
        'image'       => '',
    ),*/
    self::GAME_PLAY => array(
        'name'        => 'The Game',
        'description' => 'Jouer à La Réponse D',
        'points'      => 20,
        'image'       => 'gamepad',
    ),
    self::GAME_BEFORE => array(
        'name'        => 'Bientôt vieux cons',
        'description' => 'Réussir un 100% en moins de 60 secondes sur la promo précédente dans La Réponse D',
        'points'      => 20,
        'image'       => 'check-square-o',
    ),
    self::GAME_SELF => array(
        'name'        => 'Connaisseur',
        'description' => 'Réussir un 100% en moins de 60 secondes sur sa promo dans La Réponse D',
        'points'      => 10,
        'image'       => 'check-square',
    ),
    self::GAME_NEXT => array(
        'name'        => 'Puceau, pas puceau',
        'description' => 'Réussir 100% en moins de 60 secondes sur la promo suivante dans La Réponse D',
        'points'      => 30,
        'image'       => 'check',
    ),
    self::GAME_OLD => array(
        'name'        => 'JRP\'1747',
        'description' => 'Réussir un 100% en moins de 60 secondes en mode hardcore sur une promo de vieux dans La Réponse D',
        'points'      => 100,
        'image'       => 'eye',
    ),
    self::UNLOCKER => array(
        'name'        => 'Unlocker',
        'description' => 'Compléter 10 achievements',
        'points'      => '+10%',
        'image'       => 'star-o',
    ),
    self::CRAZY_UNLOCKER => array(
        'name'        => 'Crazy Unlocker',
        'description' => 'Compléter 50% des achievements',
        'points'      => '+15%',
        'image'       => 'star-half-o',
    ),
    self::TOTAL_UNLOCKER => array(
        'name'        => 'Total Unlocker',
        'description' => 'Compléter 90% des achievements',
        'points'      => '+75%',
        'image'       => 'star',
    ),
);

    protected static $levels = array(
        array(
            'name'        => 'Newbie',
            'description' => 'Sait compter jusqu\'à trois et lacer ses chaussures',
            'points'      => 0,
            'image'       => 'bomb',
        ),
        array(
            'name'        => 'Disciple',
            'description' => 'Ne fait plus planter uPont',
            'points'      => 50,
            'image'       => 'fire-extinguisher',
        ),
        array(
            'name'        => 'Apprenti',
            'description' => 'S\'initie aux arts obscurs',
            'points'      => 100,
            'image'       => 'search',
        ),
        array(
            'name'        => 'Alchimiste',
            'description' => 'Tente d\'acquérir le pouvoir suprême',
            'points'      => 200,
            'image'       => 'flask',
        ),
        array(
            'name'        => 'Maître',
            'description' => 'Sur la voie de l\'Illumination',
            'points'      => 350,
            'image'       => 'key',
        ),
        array(
            'name'        => 'Gourou',
            'description' => 'Manipulateur de foules',
            'points'      => 500,
            'image'       => 'dollar',
        ),
        array(
            'name'        => 'Grand Manitou',
            'description' => 'Maîtrise les éléments',
            'points'      => 750,
            'image'       => 'moon-o',
        ),
        array(
            'name'        => 'Élu',
            'description' => 'Modifie le monde à son image',
            'points'      => 1000,
            'image'       => 'spoon',
        ),
        array(
            'name'        => 'Génie',
            'description' => 'UNLIMITED POWAAAH!',
            'points'      => 1500,
            'image'       => 'flash',
        ),
        array(
            'name'        => 'Dieu',
            'description' => 'Tout simplement.',
            'points'      => 2000,
            'image'       => 'globe',
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
