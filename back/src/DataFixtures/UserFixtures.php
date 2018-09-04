<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

// Cette fixture est un peu spéciale car elle doit utiliser la classe User de l'UserBundle
class UserFixtures extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        $user = $userManager->createUser();
        $user->setUsername('matthias.dreveton');
        $user->setEmail('matthias.dreveton@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Matthias');
        $user->setLastName('Dreveton');
        $user->setPromo('019');
        $user->setDepartment('1A');
        $user->setNationality('Français');
        $user->setOrigin('Concours Commun');
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->setEnabled(true);
        $user->addGroupUser($this->getReference('group-user'));
        $userManager->updateUser($user);
        $this->addReference('user-matthias.dreveton', $user);

        $user = $userManager->createUser();
        $user->setUsername('archlinux');
        $user->setEmail('philippe.ferreira-de-sousa@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Philippe');
        $user->setLastName('Ferreira De Sousa');
        $user->setPromo('019');
        $user->setLocation('Meunier');
        $user->setDepartment('1A');
        $user->setNationality('Français');
        $user->setOrigin('Concours Commun MP');
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->setEnabled(true);
        $user->addGroupUser($this->getReference('group-admin'));
        $user->setImage($this->getReference('image-user-archlinux'));
        $userManager->updateUser($user);
        $this->addReference('user-archlinux', $user);

        $user = $userManager->createUser();
        $user->setUsername('trezzinl');
        $user->setEmail('louis.trezzini@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Louis');
        $user->setLastName('Trezzini');
        $user->setPromo('018');
        $user->setLocation('Nowhere');
        $user->setDepartment('IMI');
        $user->setNationality('Français');
        $user->setOrigin('Concours Commun MP');
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->setEnabled(true);
        $user->addGroupUser($this->getReference('group-admin'));
        $user->setImage($this->getReference('image-user-trezzinl'));
        $userManager->updateUser($user);
        $this->addReference('user-trezzinl', $user);

        $user = $userManager->createUser();
        $user->setUsername('kadaouic');
        $user->setEmail('chaimaa.kadaoui@eleves.test.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Chaïmaa');
        $user->setLastName('Kadaoui');
        $user->setPromo('016');
        $user->setLocation('Campu');
        $user->setDepartment('IMI');
        $user->setNationality('Maroc');
        $user->setOrigin('Concours Commun [CK]');
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->setEnabled(true);
        $user->addGroupUser($this->getReference('group-user'));
        $user->setImage($this->getReference('image-user-kadaouic'));
        $userManager->updateUser($user);
        $this->addReference('user-kadaouic', $user);

        $user = $userManager->createUser();
        $user->setUsername('taquet-c');
        $user->setEmail('cecile.taquet-gasperini@eleves.enpc.fr');
        $user->setPlainPassword('tata');
        $user->setLoginMethod('form');
        $user->setFirstName('Cécile');
        $user->setLastName('Taquet Gaspérini');
        $user->setPromo('020'); // En vrai, 017
        $user->setDepartment('1A');
        $user->setOrigin('CC - MP [CTG]');
        $user->setLocation('Coloc');
        $user->setNationality('Française');
        $user->setEnabled(true);
        $user->setToken('VpqtuEGC');
        $user->addGroupUser($this->getReference('group-admin'));
        $user->setImage($this->getReference('image-user-taquet-c'));
        $userManager->updateUser($user);
        $this->addReference('user-taquet-c', $user);

        $user = $userManager->createUser();
        $user->setUsername('trancara');
        $user->setEmail('alberic.trancart@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Albéric');
        $user->setLastName('Trancart');
        $user->setPromo('016');
        $user->setDepartment('GCC');
        $user->setLocation('Perronet A53');
        $user->setNationality('France');
        $user->setOrigin('CC - PSI [AT]');
        $user->setPhone('03.14.15.92.65');
        $user->setSkype('alberic.trancart');
        $user->setBalance(20.7);
        $user->setEnabled(true);
        $user->addGroupUser($this->getReference('group-admin'));
        $user->setImage($this->getReference('image-user-trancara'));
        $userManager->updateUser($user);
        $this->addReference('user-trancara', $user);

        $user = $userManager->createUser();
        $user->setUsername('de-boisc');
        $user->setNickname('Deboissque');
        $user->setEmail('corentin.de-boisset@eleves.test.enpc.fr');
        $user->setPlainPassword('123');
        $user->setLoginMethod('form');
        $user->setFirstName('Corentin');
        $user->setLastName('De Boisset');
        $user->setPromo('016');
        $user->setLocation('Campu');
        $user->setOrigin('CC - PC [CdB]');
        $user->setDepartment('GMM');
        $user->setEnabled(true);
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->addGroupUser($this->getReference('group-admin'));
        $user->setImage($this->getReference('image-user-de-boisc'));
        $userManager->updateUser($user);
        $this->addReference('user-de-boisc', $user);

        $user = $userManager->createUser();
        $user->setUsername('guerinh');
        $user->setEmail('henri.guerin@eleves.enpc.fr');
        $user->setPlainPassword('1234567890');
        $user->setLoginMethod('form');
        $user->setFirstName('Henri');
        $user->setLastName('Guérin');
        $user->setPromo('016');
        $user->setLocation('Perronet A44');
        $user->setOrigin('CC - PC [HG]');
        $user->setNationality('Nantes');
        $user->setDepartment('GCC-Archi');
        $user->setEnabled(true);
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->addGroupUser($this->getReference('group-user'));
        $user->setImage($this->getReference('image-user-guerinh'));
        $userManager->updateUser($user);
        $this->addReference('user-guerinh', $user);

        $user = $userManager->createUser();
        $user->setUsername('dziris');
        $user->setEmail('safia.dziri@eleves.test.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Safia');
        $user->setLastName('Dziri');
        $user->setPromo('016');
        $user->setOrigin('CC [SD]');
        $user->setDepartment('GCC-Archi');
        $user->setEnabled(true);
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->addGroupUser($this->getReference('group-user'));
        $user->setImage($this->getReference('image-user-dziris'));
        $userManager->updateUser($user);
        $this->addReference('user-dziris', $user);

        $user = $userManager->createUser();
        $user->setUsername('muzardt');
        $user->setEmail('theo.muzard@eleves.test.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Théo');
        $user->setLastName('Muzard');
        $user->setPromo('016');
        $user->setLocation('Perronet A54');
        $user->setOrigin('CC - MP [TM]');
        $user->setDepartment('VET');
        $user->setNationality('Troll');
        $user->setEnabled(true);
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->addGroupUser($this->getReference('group-modo'));
        $user->setImage($this->getReference('image-user-muzardt'));
        $userManager->updateUser($user);
        $this->addReference('user-muzardt', $user);

        $user = $userManager->createUser();
        $user->setUsername('donat-bb');
        $user->setEmail('benoit.donat-bouillud@eleves.test.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Benoît');
        $user->setLastName('Donat Bouillud');
        $user->setPromo('016');
        $user->setOrigin('CC [BDB]');
        $user->setDepartment('GCC');
        $user->setNationality('France');
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->setEnabled(true);
        $user->addGroupUser($this->getReference('group-user'));
        $userManager->updateUser($user);
        $this->addReference('user-donat-bb', $user);

        $user = $userManager->createUser();
        $user->setUsername('bochetc');
        $user->setEmail('bochetc@eleves.test.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Charles');
        $user->setLastName('Bochet');
        $user->setPromo('015');
        $user->setOrigin('Concours Commun - PSI* [CB015]');
        $user->setDepartment('GCC-Archi');
        $user->setToken('4wtyfMWp');
        $user->setEnabled(true);
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->addGroupUser($this->getReference('group-user'));
        $user->setImage($this->getReference('image-user-bochetc'));
        $userManager->updateUser($user);
        $this->addReference('user-bochetc', $user);

        $user = $userManager->createUser();
        $user->setUsername('vessairc');
        $user->setEmail('vessairc@eleves.test.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Cyrille');
        $user->setLastName('Vessaire');
        $user->setPromo('017');
        $user->setLocation('M333');
        $user->setOrigin('CC - MP');
        $user->setDepartment('1A');
        $user->setEnabled(true);
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->addGroup($this->getReference('group-user'));
        $user->addGroup($this->getReference('group-jardinier'));
        $userManager->updateUser($user);
        $this->addReference('user-vessairc', $user);

        $user = $userManager->createUser();
        $user->setUsername('peluchom');
        $user->setEmail('peluchom@eleves.test.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Mathias');
        $user->setLastName('Peluchon');
        $user->setPromo('020');
        $user->setDepartment('1A');
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-user'));
        $userManager->updateUser($user);
        $this->addReference('user-peluchom', $user);

        $user = $userManager->createUser();
        $user->setUsername('admissibles');
        $user->setEmail('admissible@clubinfo.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Admissible');
        $user->setLastName('Mines-Ponts');
        $user->setPromo('018');
        $user->setNationality('Un peu de tout');
        $user->setEnabled(true);
        $user->setStatsFoyer(true);
        $user->setStatsPonthub(true);
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->addGroup($this->getReference('group-admissible'));
        $user->setImage($this->getReference('image-user-admissibles'));
        $userManager->updateUser($user);
        $this->addReference('user-admissibles', $user);

        $user = $userManager->createUser();
        $user->setUsername('gcc');
        $user->setEmail('root@clubinfo.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Département');
        $user->setLastName('GCC');
        $user->setOrigin('CC');
        $user->setDepartment('GCC');
        $user->setNationality('Beton');
        $user->setEnabled(true);
        $user->setStatsFoyer(false);
        $user->setStatsPonthub(false);
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->addGroup($this->getReference('group-exterieur'));
        $user->setImage($this->getReference('image-user-gcc'));
        $userManager->updateUser($user);
        $this->addReference('user-gcc', $user);

        $user = $userManager->createUser();
        $user->setUsername('externe-foyer');
        $user->setEmail('nobody@clubinfo.enpc.fr');
        $user->setPlainPassword('password');
        $user->setLoginMethod('form');
        $user->setFirstName('Externe');
        $user->setLastName('Foyer');
        $user->setEnabled(true);
        $user->setMailEvent(false);
        $user->setMailModification(false);
        $user->setMailShotgun(false);
        $user->addGroup($this->getReference('group-exterieur'));
        $userManager->updateUser($user);
    }

    public function getDependencies()
    {
        return [
            GroupFixtures::class,
            ImageFixtures::class
        ];
    }
}
