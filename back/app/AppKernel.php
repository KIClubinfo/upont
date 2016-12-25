<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new BOMO\IcalBundle\BOMOIcalBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Sentry\SentryBundle\SentryBundle(),

            new KI\ClubinfoBundle\KIClubinfoBundle(),
            new KI\CoreBundle\KICoreBundle(),
            new KI\DvpBundle\KIDvpBundle(),
            new KI\FoyerBundle\KIFoyerBundle(),
            new KI\PonthubBundle\KIPonthubBundle(),
            new KI\PublicationBundle\KIPublicationBundle(),
            new KI\UserBundle\KIUserBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * Modification de la fonction d'initialisation pour qu'en environnement de test,
     * le chargement des ressources du kernel ne soit en mode debug que lors du
     * premier test. Divise le temps des tests par deux environ...
     * NOTE: ne marche pas avec --process-isolation
     * Enlever la première lettre pour activer
     */
    protected function ainitializeContainer()
    {
        static $first = true;

        if ($this->getEnvironment() !== 'test') {
            parent::initializeContainer();
            return;
        }

        $debug = $this->debug;

        if (!$first) {
            $this->debug = false;
        }
        $first = false;

        try {
            parent::initializeContainer();
        } catch (\Exception $e) {
            $this->debug = $debug;
            throw $e;
        }

        $this->debug = $debug;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->environment;
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }


}
