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
            'Post',
            'Newsitem',
            'Event',
            'event_attendee',
            'event_pookie',
        ));

        // Effectue les diverses opérations de migration
        $this->loadUsers();
        $this->loadClubs();
        $this->loadNews();
        $this->loadEvents();

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

    protected function loadUsers()
    {
        $repo = $this->em->getRepository('KIUpontBundle:Users\User');
        $users = $repo->findAll();
        $this->users = $this->usersKey = array();

        foreach ($users as $user) {
            $this->users[] = $user;
            $this->usersKey[] = $user->getUsername();
        }
    }

    protected function loadClubs()
    {
        $repo = $this->em->getRepository('KIUpontBundle:Users\Club');
        $clubs = $repo->findAll();
        $this->clubs = $this->clubsKey = array();

        foreach ($clubs as $club) {
            $this->clubs[] = $club;
            $this->clubsKey[] = strtolower($club->getName());
        }
    }

    protected function loadNews()
    {
        $this->log('===== NEWS =====');
        $items = array();
        $requete = $this->bdd->query('SELECT canal_publication.*, news.titre, news.valeur, club.nom FROM canal_publication JOIN news ON news.id = canal_publication.id LEFT JOIN club ON club.id = canal_publication.id_auteur WHERE canal_publication.type="news" AND canal_publication.type_auteur = "club" AND canal_publication.id_canal <> 56');
        while ($donnees = $requete->fetch())
            $items[$donnees['id']] = $donnees;
        $requete->closeCursor();
        $this->log(count($items));
        $i = 0;

        foreach ($items as $id => $item) {
            if ($key = array_search(strtolower($item['nom']), $this->clubsKey)) {
                $entity = new PU\Newsitem();
                $entity->setName($item['titre']);
                $entity->setText(nl2br($item['valeur']));

                $entity->setAuthorClub($this->clubs[$key]);
                $entity->setDate(strtotime($item['date']));
                $this->em->persist($entity);
                $i++;
            }
        }

        $this->em->flush();
        $this->log($i . ' news importées');
    }

    protected function loadEvents()
    {
        $this->log('===== EVENEMENTS =====');
        $items = array();
        $requete = $this->bdd->query('SELECT canal_publication.*, club.nom, evenement.label, evenement.details, evenement.lieu, evenement.date_deb, evenement.date_fin, evenement.date_shotgun, evenement.mode_inscription FROM canal_publication JOIN evenement ON evenement.id = canal_publication.id LEFT JOIN club ON club.id = canal_publication.id_auteur WHERE canal_publication.type="event" AND canal_publication.type_auteur = "club" AND canal_publication.id_canal <> 56');
        while ($donnees = $requete->fetch())
            $items[$donnees['id']] = $donnees;
        $requete->closeCursor();
        $i = 0;
        $this->events = array();
        $this->log(count($items));

        foreach ($items as $id => $item) {
            if ($key = array_search(strtolower($item['nom']), $this->clubsKey)) {
                $entity = new PU\Event();
                $entity->setName($item['label']);
                $entity->setText(nl2br($item['details']));
                $entity->setDate(strtotime($item['date']));
                $entity->setStartDate(strtotime($item['date_deb']));
                $entity->setEndDate(strtotime($item['date_fin']));

                if ($item['date_shotgun'] != '0000-00-00 00:00:00')
                    $entity->setShotgunDate(strtotime($item['date_shotgun']));

                $entity->setAuthorClub($this->clubs[$key]);
                $entity->setEntryMethod('Libre');
                $entity->setPlace($item['lieu']);
                $this->em->persist($entity);
                $this->events[$id] = $entity;
                $i++;
            }
        }
        $this->log($i . ' events importés');

        $this->em->flush();
    }
}
