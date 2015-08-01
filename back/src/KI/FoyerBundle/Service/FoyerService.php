<?php

namespace KI\FoyerBundle\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContext;
use KI\UserBundle\Entity\Achievement;
use KI\UserBundle\Event\AchievementCheckEvent;
use KI\CoreBundle\Service\CurlService;

// Échange des informations avec l'API du Foyer
class FoyerService
{
    protected $curlService;
    protected $dispatcher;
    protected $securityContext;

    public function __construct(CurlService $curlService, EventDispatcherInterface $dispatcher, SecurityContext $securityContext)
    {
        $this->curlService     = $curlService;
        $this->dispatcher      = $dispatcher;
        $this->securityContext = $securityContext;
    }

    protected $error = null;
    protected $curl;
    protected $token;
    protected $balance;

    public function initialize($user = null)
    {
        if ($this->error !== null)
            return;

        if ($user === null)
            $user = $this->securityContext->getToken()->getUser();
        // On vérifie que la personne a le droit de consulter les stats
        if ($user !== $this->securityContext->getToken()->getUser()
            && ($user->getStatsFoyer() === false || $user->getStatsFoyer() === null)
            && !$this->securityContext->isGranted('ROLE_ADMIN')) {
            $this->error = true;
            return;
        }

        // Recupere l'id foyer correspondant
        $response = $this->curlService->curl('http://dev-foyer.enpc.org/uPonts/qui.php?prenom='.urlencode($user->getFirstName()).'&nom='.urlencode($user->getLastName()));
        $data = json_decode($response, true);

        $this->error = $data['erreur'] != '';
        $this->token = $this->error ? null : $data['resultat'][0]['id'];

        if ($this->token === null)
            return;

        $response = $this->curlService->curl('http://dev-foyer.enpc.org/uPonts/stats.php?id='.$this->token);
        $data = json_decode($response, true);
        $this->balance = $data['solde'];

        if (!empty($this->balance)) {
            if ($this->balance < 0) {
                $achievementCheck = new AchievementCheckEvent(Achievement::FOYER);
                $this->dispatcher->dispatch('upont.achievement', $achievementCheck);
            } else {
                $achievementCheck = new AchievementCheckEvent(Achievement::FOYER_BIS);
                $this->dispatcher->dispatch('upont.achievement', $achievementCheck);
            }
        }
    }

    public function hasFailed() { return $this->error; }

    public function balance() { return $this->balance; }

    public function rankings()
    {
        $response = $this->curlService->curl('http://dev-foyer.enpc.org/uPonts/rankings.php');
        $data = json_decode($response, true);

        return array(
            'rankings' => $data['ranking_eleves']
        );
    }

    public function statistics()
    {
        if ($this->token === null)
            return;

        $response = $this->curlService->curl(
            'http://dev-foyer.enpc.org/uPonts/stats.php?id='.$this->token);
        $data = json_decode($response, true);

        return array(
            'numberBeers'   => $data['nbConsos'],
            'litersDrunk'   => $data['litresBus'],
            'beers'         => $data['consommations'],
            'liters'        => $data['litres'],
            'stackedLiters' => $data['litresCumules'],
            'perBeer'       => $data['nbParProduit']
        );
    }
}
