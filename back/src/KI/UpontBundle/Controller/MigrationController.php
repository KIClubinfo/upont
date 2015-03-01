<?php

namespace KI\UpontBundle\Controller;

use PDO;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use KI\UpontBundle\Entity\Ponthub as PH;
use KI\UpontBundle\Entity\Publications as PU;
use KI\UpontBundle\Entity\Users as US;
use KI\UpontBundle\Entity\Image;

class MigrationController extends \KI\UpontBundle\Controller\Core\BaseController
{
    public function migrationAction()
    {
        $em = $this->getDoctrine()->getManager();
        $generator = new Generator($this->container);
        $log = $generator->load($em);

        return $this->htmlResponse(nl2br($log));
    }
}


class Generator extends AbstractFixture
{
    private $container;
    private $em;
    protected $bdd;
    protected $log = "===== LOG =====\n\n";

    public function __construct($container) { $this->container = $container; }

    public function load(Objectmanager $manager)
    {
        // [AT'016] Comme au bon vieux temps, faisons du PHP très sale pour migrer cette foutue BDD
        $_bddpass = $this->container->getParameter('database_v1_password');
        $this->bdd = new PDO('mysql:host=localhost; dbname=enpcintra', 'root', $_bddpass, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
        $this->bdd->exec("SET CHARACTER SET utf8");
        $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->em = $manager;

        // Beware, ca va etre très sale
        ini_set('memory_limit','4096M');
        ini_set('max_execution_time', 1000000);

        // On vide la BDD actuelle
        // Normalement la BDD devra être reset proprement à la main, là on le
        // fait manuellement pour pouvoir tester la migration rapidement
        $this->truncate(array(
            'fos_user',
            'Club',
            'ClubUser',
            'Image',
            'Post',
            'Newsitem',
            'Event',
            'event_attendee',
            'event_pookie',
            'Movie',
            'Serie',
            'Episode',
            'Album',
            'Music',
            'Software',
            'Other',
            'Actor',
            'Genre',
            'PonthubFile',
            'ponthubfile_user',
            'ponthubfile_genre',
            'movie_actor',
            'serie_actor',
        ));

        // Effectue les diverses opérations de migration
        $this->loadImages();
        $this->loadUsers();
        $this->loadClubs();
        $this->loadNews();
        $this->loadEvents();
        $this->loadPonthub();
        $this->loadCourses();

        //Depannage
        //Mdtk
        //Content
        //Pontlyvalent
        //Sondage

        return $this->log;
    }

    protected function log($message)
    {
        $this->log .= $message . "\n";
    }

    protected function truncate(array $tables)
    {
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');

        foreach ($tables as $table) {
            $truncateSql = $platform->getTruncateTableSQL($table);
            $connection->executeUpdate($truncateSql);
        }
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }

    protected function loadTable($table)
    {
        $items = array();
        $requete = $this->bdd->query('SELECT * FROM ' . $table);
        while ($donnees = $requete->fetch())
            $items[$donnees['id']] = $donnees;
        $requete->closeCursor();
        return $items;
    }

    protected function loadImages()
    {
        $this->log('===== IMAGES =====');
        $items = $this->loadTable('image');
        $i = 0;
        $this->images = array();

        $path = __DIR__ . '/../../../../web/uploads/tmp/';
        $fs = new Filesystem();

        foreach ($items as $id => $item) {
            $ext = $item['nom'] . '.' . $item['extension'];

            if (!file_exists($path . $ext) || empty($item['nom']) || empty($item['extension']))
                continue;

            $fs->copy($path . $ext, $path . 'tmp_' . $id);
            $file = new File($path . 'tmp_' . $id);

            $entity = new Image();
            $entity->setFile($file);
            $entity->setExt($file->getExtension());
            $this->em->persist($entity);
            $this->images[$id] = $entity;
            $i++;
        }

        $this->em->flush();

        $this->log($i . ' images importées');
    }
















    protected function loadUsers()
    {
        $this->log('===== UTILISATEURS =====');
        $items = $this->loadTable('eleve');
        $i = 0;
        $this->users = array();

        foreach ($items as $id => $item) {
            $entity = new US\User();
            $entity->setFirstName($item['prenom']);
            $entity->setLastName($item['nom']);
            $entity->setUsername($item['pseudo']);
            $entity->setEmail($item['email']);
            $entity->setPromo($item['promo']);
            $entity->setDepartment($item['departement']);
            $entity->setOrigin($item['origine']);
            $entity->setPlainPassword('<d\'cv 511s2q)d4s5qqs');
            $entity->setNationality(ucfirst(strtolower($item['nationalite'])));
            if (isset($this->images[$item['id_image']]))
                $entity->setImage($this->images[$item['id_image']]);
            $this->em->persist($entity);
            $this->users[$id] = $entity;
            $i++;
        }

        $this->em->flush();
        $this->log($i . ' utilisateurs importés');
    }

















    protected function loadClubs()
    {
        $this->log('===== CLUBS =====');
        $items = $this->loadTable('club');
        $i = 0;
        $this->clubs = array();

        foreach ($items as $id => $item) {
            $entity = new US\Club();
            $entity->setName($item['nom']);
            $entity->setFullname($item['nom_long']);
            $entity->setActive(true);
            if (isset($this->images[$item['id_image']]))
                $entity->setImage($this->images[$item['id_image']]);
            $this->em->persist($entity);
            $this->clubs[$id] = $entity;
            $i++;
        }
        $this->em->flush();
        $this->log($i . ' clubs importés');



        // Membres de clubs
        $items = $this->loadTable('club_eleve');
        $i = 0;

        foreach ($items as $id => $item) {
            if ($this->clubs[$item['id_club']] === null || $this->users[$item['id_eleve']] === null)
                continue;

            $entity = new US\ClubUser();
            $entity->setClub($this->clubs[$item['id_club']]);
            $entity->setUser($this->users[$item['id_eleve']]);
            $entity->setRole($item['role']);
            $this->em->persist($entity);
            $i++;
        }
        $this->em->flush();
        $this->log($i . ' membres de clubs importés');
    }

















    protected function loadNews()
    {
        $this->log('===== NEWS =====');
        $items = array();
        $requete = $this->bdd->query('SELECT canal_publication.*, news.titre, news.valeur FROM canal_publication JOIN news ON news.id = canal_publication.id WHERE type="news"');
        while ($donnees = $requete->fetch())
            $items[$donnees['id']] = $donnees;
        $requete->closeCursor();
        $i = 0;

        foreach ($items as $id => $item) {
            $entity = new PU\Newsitem();
            $entity->setName($item['titre']);
            $entity->setTextLong($item['valeur']);
            $entity->setAuthorClub($this->clubs[$item['id_auteur']]);
            $entity->setDate(strtotime($item['date']));
            $this->em->persist($entity);
            $i++;
        }

        $this->em->flush();
        $this->log($i . ' news importées');
    }








    protected function loadEvents()
    {
        $this->log('===== EVENEMENTS =====');
        $items = array();
        $requete = $this->bdd->query('SELECT canal_publication.*, evenement.label, evenement.details, evenement.lieu, evenement.date_deb, evenement.date_fin, evenement.date_shotgun, evenement.mode_inscription FROM canal_publication JOIN evenement ON evenement.id = canal_publication.id WHERE type="event"');
        while ($donnees = $requete->fetch())
            $items[$donnees['id']] = $donnees;
        $requete->closeCursor();
        $i = 0;
        $this->events = array();

        foreach ($items as $id => $item) {
            $entity = new PU\Event();
            $entity->setName($item['label']);
            $entity->setTextLong($item['details']);
            $entity->setDate(strtotime($item['date']));
            $entity->setStartDate(strtotime($item['date_deb']));
            $entity->setEndDate(strtotime($item['date_fin']));
            if ($item['date_shotgun'] != '0000-00-00 00:00:00')
                $entity->setShotgunDate(strtotime($item['date_shotgun']));
            $entity->setAuthorClub($this->clubs[$item['id_auteur']]);
            $entity->setEntryMethod(ucfirst($item['mode_inscription']));
            $entity->setPlace($item['lieu']);
            $this->em->persist($entity);
            $this->events[$id] = $entity;
            $i++;
        }
        $this->log($i . ' utilisateurs importés');

        // Attendees/pookies
        $items = $this->loadTable('evenement_eleve');
        $i = 0;
        foreach ($items as $id => $item) {
            if ($item['type'] == 'rejet' && isset($this->users[$item['id_eleve']]) && isset($this->events[$item['id_evenement']]))
                $this->events[$item['id_evenement']]->addPookie($this->users[$item['id_eleve']]);
            if ($item['type'] == 'chaud' && isset($this->users[$item['id_eleve']]) && isset($this->events[$item['id_evenement']]))
                $this->events[$item['id_evenement']]->addAttendee($this->users[$item['id_eleve']]);
            $i++;
        }

        $this->em->flush();
        $this->log($i . ' utilisateurs liés à des événements');
    }














    protected function loadPonthub()
    {
        $this->log('===== PONTHUB =====');
        $items = $this->loadTable('dc_fichier');
        $f = $s = $e = $a = $m = $j = $l = $o = $ac = $ge = 0;
        $this->film = array();
        $this->serie = array();
        $this->musique = array();
        $this->jeu = array();
        $this->episode = array();
        $this->album = array();
        $this->logiciel = array();
        $this->autre = array();
        $this->acteur = array();
        $this->genre = array();

        foreach ($items as $id => $item) {
            if ($item['statut'] == 'not_found' || $item['type'] == 'musique' || $item['type'] == 'episode')
                continue;
            switch ($item['type']) {
                case 'film':
                    $entity = new PH\Movie();
                    $entity->setDuration($item['duree']);
                    $entity->setYear($item['annee']);
                    $entity->setVo($item['vo']);
                    $entity->setVf($item['vf']);
                    $entity->setVost($item['st_vo']);
                    $entity->setVostfr($item['st_vf']);
                    $entity->setHd($item['qualite'] == 'HD');
                    $entity->setDirector($item['artiste']);

                    $actors = explode(', ', $item['avec']);
                    foreach ($actors as $actor) {
                        if(!isset($this->acteur[$actor])) {
                            $lowl = new PH\Actor();
                            $lowl->setName($actor);
                            $this->em->persist($lowl);
                            $this->acteur[$actor] = $lowl;
                        }
                        $entity->addActor($this->acteur[$actor]);
                    }

                    $genres = explode(', ', $item['genre']);
                    foreach ($genres as $genre) {
                        if(!isset($this->genre[$genre])) {
                            $lowl = new PH\Genre();
                            $lowl->setName($genre);
                            $this->em->persist($lowl);
                            $this->genre[$genre] = $lowl;
                        }
                        $entity->addGenre($this->genre[$genre]);
                    }
                    $f++;
                    break;
                case 'serie':
                    $entity = new PH\Serie();
                    $entity->setDuration($item['duree']);
                    $entity->setYear($item['annee']);
                    $entity->setVo($item['vo']);
                    $entity->setVf($item['vf']);
                    $entity->setVost($item['st_vo']);
                    $entity->setVostfr($item['st_vf']);
                    $entity->setHd($item['qualite'] == 'HD');
                    $entity->setDirector($item['artiste']);

                    $genres = explode(', ', $item['genre']);
                    foreach ($genres as $genre) {
                        if(!isset($this->genre[$genre])) {
                            $lowl = new PH\Genre();
                            $lowl->setName($genre);
                            $this->em->persist($lowl);
                            $this->genre[$genre] = $lowl;
                        }
                        $entity->addGenre($this->genre[$genre]);
                    }
                    $actors = explode(', ', $item['avec']);
                    foreach ($actors as $actor) {
                        if(!isset($this->acteur[$actor])) {
                            $lowl = new PH\Actor();
                            $lowl->setName($actor);
                            $this->em->persist($lowl);
                            $this->acteur[$actor] = $lowl;
                        }
                        $entity->addActor($this->acteur[$actor]);
                    }
                    $s++;
                    break;
                case 'jeu':
                    $entity = new PH\Game();
                    $entity->setYear($item['annee']);
                    $entity->setStudio($item['artiste']);
                    $j++;
                    break;
                case 'album':
                    $entity = new PH\Album();
                    $entity->setArtist($item['artiste']);
                    $entity->setYear($item['annee']);
                    $a++;

                    $genres = explode(', ', $item['genre']);
                    foreach ($genres as $genre) {
                        if(!isset($this->genre[$genre])) {
                            $lowl = new PH\Genre();
                            $lowl->setName($genre);
                            $this->em->persist($lowl);
                            $this->genre[$genre] = $lowl;
                        }
                        $entity->addGenre($this->genre[$genre]);
                    }
                    break;
                case 'logiciel':
                    $entity = new PH\Software();
                    $entity->setYear($item['annee']);
                    $entity->setAuthor($item['artiste']);
                    $l++;
                    break;
                case 'autre':
                    $entity = new PH\Other();
                    $o++;
                    break;
                default: continue;
            }
            $entity->setName($item['nom']);
            $entity->setPath($item['chemin']);
            $entity->setSize($item['taille']);
            $entity->setAdded(strtotime($item['date']));

            $status = $item['statut'] == 'ok' ? 'OK' : 'NEEDINFOS';
            $entity->setStatus($status);
            $entity->setDescription($item['description']);
            if (isset($this->images[$item['image']]))
                $entity->setImage($this->images[$item['image']]);
            $this->em->persist($entity);

            switch ($item['type']) {
                case 'film':
                    $this->film[$id] = $entity;
                    break;
                case 'serie':
                    $this->serie[$id] = $entity;
                    break;
                case 'jeu':
                    $this->jeu[$id] = $entity;
                    break;
                case 'album':
                    $this->album[$id] = $entity;
                    break;
                case 'autre':
                    $this->autre[$id] = $entity;
                    break;
                case 'logiciel':
                    $this->logiciel[$id] = $entity;
                    break;
            }
            if($f%100 == 1)
                $this->em->flush();
            if($s%100 == 1)
                $this->em->flush();
            if($a%100 == 1)
                $this->em->flush();
            if($j%100 == 1)
                $this->em->flush();
            if($l%100 == 1)
                $this->em->flush();
            if($o%100 == 1)
                $this->em->flush();
        }
        $this->log($f . ' films importés');
        $this->log($s . ' séries importées');
        $this->log($a . ' albums importés');
        $this->log($j . ' jeux importés');
        $this->log($l . ' logiciels importés');
        $this->log($o . ' autres importés');
        $this->log(count($this->actor) . ' acteurs importés');
        $this->log(count($this->genre) . ' genres importés');
        $this->em->flush();


















        // Seconde passe pour les musiques et episodes
        foreach ($items as $id => $item) {
            if ($item['statut'] == 'not_found' || !($item['type'] == 'musique' || $item['type'] == 'episode'))
                continue;

            switch ($item['type']) {
                case 'musique':
                    $entity = new PH\Music();
                    $entity->setAlbum($this->album[$item['regroupement']]);
                    $m++;
                    break;
                case 'episode':
                    $entity = new PH\Episode();
                    $entity->setSerie($this->serie[$item['regroupement']]);
                    $entity->setSeason($item['artiste']);
                    $entity->setNumber($item['avec']);
                    $e++;
                    break;
            }
            $entity->setName($item['nom']);
            $entity->setPath($item['chemin']);
            $entity->setSize($item['taille']);
            $entity->setAdded(strtotime($item['chemin']));

            $status = $item['statut'] == 'ok' ? 'OK' : 'NEEDINFOS';
            $entity->setStatus($status);
            $entity->setDescription($item['description']);
            if (isset($this->images[$item['image']]))
                $entity->setImage($this->images[$item['image']]);
            $this->em->persist($entity);

            switch ($item['type']) {
                case 'musique':
                    $this->musique[$id] = $entity;
                    break;
                case 'episode':
                    $this->episode[$id] = $entity;
                    break;
            }
            if($e%500 == 1)
                $this->em->flush();
            if($m%500 == 1)
                $this->em->flush();
        }
        $this->log($e . ' épisodes importés');
        $this->log($m . ' musiques importées');
        $this->em->flush();


        // Stats Ponthub
        $items = $this->loadTable('dc_log');
        $i = 0;
        foreach ($items as $id => $item) {
            if (isset($this->users[$item['id_eleve']])) {
                if (isset($this->film[$item['id_fichier']]))
                    $this->film[$item['id_fichier']]->addUser($this->users[$item['id_eleve']]);
                if (isset($this->serie[$item['id_fichier']]))
                    $this->serie[$item['id_fichier']]->addUser($this->users[$item['id_eleve']]);
                if (isset($this->episode[$item['id_fichier']]))
                    $this->episode[$item['id_fichier']]->addUser($this->users[$item['id_eleve']]);
                if (isset($this->album[$item['id_fichier']]))
                    $this->album[$item['id_fichier']]->addUser($this->users[$item['id_eleve']]);
                if (isset($this->musique[$item['id_fichier']]))
                    $this->musique[$item['id_fichier']]->addUser($this->users[$item['id_eleve']]);
                if (isset($this->jeu[$item['id_fichier']]))
                    $this->jeu[$item['id_fichier']]->addUser($this->users[$item['id_eleve']]);
                if (isset($this->logiciel[$item['id_fichier']]))
                    $this->logiciel[$item['id_fichier']]->addUser($this->users[$item['id_eleve']]);
                if (isset($this->autre[$item['id_fichier']]))
                    $this->autre[$item['id_fichier']]->addUser($this->users[$item['id_eleve']]);
            }
            $i++;

            if($i%500 == 1)
                $this->em->flush();
        }
        $this->log($i . ' fichiers Ponthub téléchargés');

        $this->em->flush();
    }













    protected function loadCourses()
    {
        $items = $this->loadTable('cours_liste');
        $i = 0;

        foreach ($items as $id => $item) {
            $entity = new PU\Course();

            $out = array();
            if (preg_match('#.* \(Gr([0-9])\)#', $item['label'], $out)) {
                $group = (int) $out[1];
                $name = str_replace(array('(Gr', ')'), array('', ''), $item['label']);
            } else {
                $group = 0;
                $name = $item['label'];
            }

            $entity->setName($name);
            $entity->setGroup($group);
            $entity->setDepartment($item['departement']);
            $this->em->persist($entity);
            $i++;
        }
        $this->log($i . ' cours importés');
        $this->em->flush();
    }
}
