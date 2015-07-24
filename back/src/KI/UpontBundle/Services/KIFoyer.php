<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use KI\UpontBundle\Entity\Users\Achievement;
use KI\UpontBundle\Event\AchievementCheckEvent;

// Échange des informations avec l'API du Foyer
class KIFoyer extends ContainerAware
{
    protected $error = null;
    protected $curl;
    protected $token;
    protected $balance;

    public function initialize($user = null)
    {
        if ($this->error !== null)
            return;

        if ($user === null)
            $user = $this->container->get('security.context')->getToken()->getUser();
        // On vérifie que la personne a le droit de consulter les stats
        if ($user !== $this->container->get('security.context')->getToken()->getUser()
            && ($user->getStatsFoyer() === false || $user->getStatsFoyer() === null)
            && !$this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->error = true;
            return;
        }

        // Recupere l'id foyer correspondant
        $this->curl = $this->container->get('ki_upont.curl');
        $response = $this->curl->curl('http://dev-foyer.enpc.org/uPonts/qui.php?prenom='.urlencode($user->getFirstName()).'&nom='.urlencode($user->getLastName()));
        $data = json_decode($response, true);

        $this->error = $data['erreur'] != '';
        $this->token = $this->error ? null : $data['resultat'][0]['id'];

        if ($this->token === null)
            return;

        $response = $this->curl->curl('http://dev-foyer.enpc.org/uPonts/stats.php?id='.$this->token);
        $data = json_decode($response, true);
        $this->balance = $data['solde'];

        if (!empty($this->balance)) {
            if ($this->balance < 0) {
                $dispatcher = $this->container->get('event_dispatcher');
                $achievementCheck = new AchievementCheckEvent(Achievement::FOYER);
                $dispatcher->dispatch('upont.achievement', $achievementCheck);
            } else {
                $dispatcher = $this->container->get('event_dispatcher');
                $achievementCheck = new AchievementCheckEvent(Achievement::FOYER_BIS);
                $dispatcher->dispatch('upont.achievement', $achievementCheck);
            }
        }
    }

    public function hasFailed() { return $this->error; }

    public function balance() { return $this->balance; }

    public function rankings()
    {
        $response = $this->curl->curl('http://dev-foyer.enpc.org/uPonts/rankings.php');
        $data = json_decode($response, true);

        return array(
            'rankings' => $data['ranking_eleves']
        );
    }

    public function statistics()
    {
        if ($this->token === null)
            return;

        $response = $this->curl->curl(
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
