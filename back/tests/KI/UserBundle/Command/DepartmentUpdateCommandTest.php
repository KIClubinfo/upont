<?php

namespace Tests\KI\UserBundle\Command;

use KI\UserBundle\Entity\User;
use KI\UserBundle\Command\DepartmentUpdateCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class DepartmentUpdateCommandTest extends KernelTestCase
{
     /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

     /**
      * {@inheritDoc}
      */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testUpdateDepartment()
    {
        $dreveton = $this->em->getRepository(User::class)->findOneByUsername('matthias.dreveton');

        $application = new Application(static::$kernel);
        $application->add(new DepartmentUpdateCommand());
        $command = $application->find('upont:update:department');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'department' => 'IMI',
            'usernames' => 'archlinux,trezzinl,matthias.dreveton,dsfqsdfefdfq'
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('IMI : 3 élèves sur 4 mis à jour', $output);
        $this->assertContains('Username dsfqsdfefdfq n\'existe pas', $output);
        $this->assertEquals('IMI', $dreveton->getDepartment());
    }

     /**
      * {@inheritDoc}
      */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}
