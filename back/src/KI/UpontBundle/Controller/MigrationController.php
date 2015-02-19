<?php

namespace KI\UpontBundle\Controller;

use PDO;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use KI\UpontBundle\Entity\Ponthub;
use KI\UpontBundle\Entity\Publications;
use KI\UpontBundle\Entity\Users\User;
use KI\UpontBundle\Entity\Users\Club;
use KI\UpontBundle\Entity\Users\ClubUser;
use KI\UpontBundle\Entity\Image;
use KI\UpontBundle\Entity;

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
        $this->bdd = new PDO('mysql:host=localhost; dbname=enpcintra', 'root', $_bddpass, array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC)) ;
        $this->bdd->exec("SET CHARACTER SET utf8");
        $this->bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->em = $manager;

        // Effectue les diverses opérations de migration
        $this->loadImages();
        $this->loadUsers();
        $this->loadClubs();
        //$this->loadNews();
        //$this->loadEvents(); // event eleve
        //$this->loadDepannage();
        //$this->loadCourses();
        //$this->loadPonthub();
        //$this->loadMdtk();
        //$this->loadContent();
        //$this->loadPontlyvalent();
        //$this->loadSondage();

        return $this->log;
    }

    protected function log($message)
    {
        $this->log .= $message . "\n";
    }

    protected function truncate($table)
    {
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $truncateSql = $platform->getTruncateTableSQL($table);
        $connection->executeUpdate($truncateSql);
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }

    protected function loadTable($table)
    {
        $items = array();
        $requete = $this->bdd->query('SELECT * FROM ' . $table);
        while($donnees = $requete->fetch())
            $items[$donnees['id']] = $donnees;
        $requete->closeCursor();
        return $items;
    }

    protected function loadImages()
    {
        $this->log('===== IMAGES =====');
        $items = $this->loadTable('image');
        $this->truncate('Image');
        $i = 0;
        $this->images = array();

        $path = __DIR__ . '/../../../../web/uploads/tmp/';
        $fs = new Filesystem();

        foreach($items as $id => $item) {
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
        $this->truncate('fos_user');
        $i = 0;
        $this->users = array();

        foreach($items as $id => $item) {
            $entity = new User();
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
        $this->truncate('Club');
        $i = 0;
        $this->clubs = array();

        foreach($items as $id => $item) {
            $entity = new Club();
            $entity->setShortName($item['nom']);
            $entity->setName($item['nom_long']);
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
        $this->truncate('ClubUser');
        $i = 0;

        foreach($items as $id => $item) {
            if ($this->clubs[$item['id_club']] == null || $this->users[$item['id_eleve']] == null)
                continue;

            $entity = new ClubUser();
            $entity->setClub($this->clubs[$item['id_club']]);
            $entity->setUser($this->users[$item['id_eleve']]);
            $entity->setRole($item['role']);
            $this->em->persist($entity);
            $i++;
        }
        $this->em->flush();
        $this->log($i . ' membres de clubs importés');
    }

















    protected function loadPonthub()
    {
        $this->log('===== CLUBS =====');
        $items = $this->loadTable('club');
        $this->truncate('Club');
        $i = 0;
        $this->clubs = array();

        foreach($items as $id => $item) {
            $entity = new Club();
            $entity->setShortName($item['nom']);
            $entity->setName($item['nom_long']);
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
        $this->truncate('ClubUser');
        $i = 0;

        foreach($items as $id => $item) {
            if ($this->clubs[$item['id_club']] == null || $this->users[$item['id_eleve']] == null)
                continue;

            $entity = new ClubUser();
            $entity->setClub($this->clubs[$item['id_club']]);
            $entity->setUser($this->users[$item['id_eleve']]);
            $entity->setRole($item['role']);
            $this->em->persist($entity);
            $i++;
        }
        $this->em->flush();
        $this->log($i . ' membres de clubs importés');
    }
}
