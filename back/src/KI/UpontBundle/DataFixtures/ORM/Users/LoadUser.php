<?php

namespace KI\UpontBundle\DataFixtures\ORM\Users;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

// Cette fixture est un peu spéciale car elle doit utiliser la classe User de l'UserBundle
class LoadUserFixture extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $user->setUsername('kadaouic');
        $user->setEmail('chaimaa.kadaoui@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setFirstName('Chaïmaa');
        $user->setLastName('Kadaoui');
        $user->setPromo('016');
        $user->setLocation('Campu');
        $user->setDepartment('IMI');
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-user'));
        $user->setImage($this->getReference('image-user-kadaouic'));
        $userManager->updateUser($user);
        $this->addReference('user-kadaouic', $user);

        $user = $userManager->createUser();
        $user->setUsername('taquet-c');
        $user->setEmail('cecile.taquet-gasperini@eleves.enpc.fr');
        $user->setPlainPassword('tata');
        $user->setFirstName('Cécile');
        $user->setLastName('Taquet Gaspérini');
        $user->setPromo('017');
        $user->setDepartment('1A');
        $user->setLocation('Coloc');
        $user->setEnabled(true);
        $user->setToken('VpqtuEGC');
        $user->addGroup($this->getReference('group-admin'));
        $user->setImage($this->getReference('image-user-taquet-c'));
        $userManager->updateUser($user);
        $this->addReference('user-taquet-c', $user);

        $user = $userManager->createUser();
        $user->setUsername('trancara');
        $user->setEmail('alberic.trancart@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setFirstName('Albéric');
        $user->setLastName('Trancart');
        $user->setPromo('016');
        $user->setDepartment('GCC');
        $user->setLocation('Perronet A53');
        $user->setNationality('France');
        $user->setOrigin('CC');
        $user->setPhone('06.45.03.69.58');
        $user->setSkype('alberic.trancart');
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-admin'));
        $user->setImage($this->getReference('image-user-trancara'));
        $userManager->updateUser($user);
        $this->addReference('user-trancara', $user);

        $user = $userManager->createUser();
        $user->setUsername('de-boisc');
        $user->setNickname('Deboissque');
        $user->setEmail('corentin.de-boisset@eleves.enpc.fr');
        $user->setPlainPassword('123');
        $user->setFirstName('Corentin');
        $user->setLastName('De Boisset');
        $user->setPromo('016');
        $user->setLocation('Campu');
        $user->setDepartment('GMM');
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-admin'));
        $user->setImage($this->getReference('image-user-de-boisc'));
        $userManager->updateUser($user);
        $this->addReference('user-de-boisc', $user);

        $user = $userManager->createUser();
        $user->setUsername('guerinh');
        $user->setEmail('henri.guerin@eleves.enpc.fr');
        $user->setPlainPassword('1234567890');
        $user->setFirstName('Henri');
        $user->setLastName('Guérin');
        $user->setPromo('016');
        $user->setLocation('Perronet A44');
        $user->setDepartment('GCC-Archi');
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-user'));
        $user->setImage($this->getReference('image-user-guerinh'));
        $userManager->updateUser($user);
        $this->addReference('user-guerinh', $user);

        $user = $userManager->createUser();
        $user->setUsername('dziris');
        $user->setEmail('safia.dziri@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setFirstName('Safia');
        $user->setLastName('Dziri');
        $user->setPromo('016');
        $user->setDepartment('GCC-Archi');
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-user'));
        $user->setImage($this->getReference('image-user-dziris'));
        $userManager->updateUser($user);
        $this->addReference('user-dziris', $user);

        $user = $userManager->createUser();
        $user->setUsername('muzardt');
        $user->setEmail('theo.muzard@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setFirstName('Théo');
        $user->setLastName('Muzard');
        $user->setPromo('016');
        $user->setLocation('Perronet A54');
        $user->setDepartment('VET');
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-modo'));
        $user->setImage($this->getReference('image-user-muzardt'));
        $userManager->updateUser($user);
        $this->addReference('user-muzardt', $user);

        $user = $userManager->createUser();
        $user->setUsername('donat-bb');
        $user->setEmail('benoit.donat-bouillud@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setFirstName('Benoît');
        $user->setLastName('Donat Bouillud');
        $user->setPromo('016');
        $user->setDepartment('GCC');
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-user'));
        $userManager->updateUser($user);
        $this->addReference('user-donat-bb', $user);

        $user = $userManager->createUser();
        $user->setUsername('bochetc');
        $user->setEmail('bochetc@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setFirstName('Charles');
        $user->setLastName('Bochet');
        $user->setPromo('015');
        $user->setDepartment('GCC-Archi');
        $user->setToken('4wtyfMWp');
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-user'));
        $user->setImage($this->getReference('image-user-bochetc'));
        $userManager->updateUser($user);
        $this->addReference('user-bochetc', $user);

        $user = $userManager->createUser();
        $user->setUsername('vessairc');
        $user->setEmail('vessairc@eleves.enpc.fr');
        $user->setPlainPassword('password');
        $user->setFirstName('Cyrille');
        $user->setLastName('Vessaire');
        $user->setPromo('017');
        $user->setLocation('M333');
        $user->setDepartment('1A');
        $user->setEnabled(true);
        $user->addGroup($this->getReference('group-user'));
        $user->addGroup($this->getReference('group-jardinier'));
        $userManager->updateUser($user);
        $this->addReference('user-vessairc', $user);

        $user = $userManager->createUser();
        $user->setUsername('admissibles');
        $user->setEmail('admissible@clubinfo.enpc.fr');
        $user->setPlainPassword('password');
        $user->setFirstName('Admissible');
        $user->setLastName('Mines-Ponts');
        $user->setPromo('018');
        $user->setEnabled(true);
        $user->setStatsFoyer(true);
        $user->setStatsPonthub(true);
        $user->addGroup($this->getReference('group-admissible'));
        $user->setImage($this->getReference('image-user-admissibles'));
        $userManager->updateUser($user);
        $this->addReference('user-admissibles', $user);

        $user = $userManager->createUser();
        $user->setUsername('gcc');
        $user->setEmail('root@clubinfo.enpc.fr');
        $user->setPlainPassword('password');
        $user->setFirstName('Département');
        $user->setLastName('GCC');
        $user->setDepartment('GCC');
        $user->setEnabled(true);
        $user->setStatsFoyer(false);
        $user->setStatsPonthub(false);
        $user->addGroup($this->getReference('group-exterieur'));
        $user->setImage($this->getReference('image-user-gcc'));
        $userManager->updateUser($user);
        $this->addReference('user-gcc', $user);
    }

    public function getOrder()
    {
        return 3;
    }
}
