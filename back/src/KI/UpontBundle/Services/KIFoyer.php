<?php

namespace KI\UpontBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;

// Échange des informations avec l'API du Foyer
class KIFoyer extends ContainerAware
{
    protected $error = null;
    protected $curl;
    protected $token;
    protected $balance;
    
    public function initialize()
    {
        if ($this->error != null)
            return;
            
        $this->curl = $this->container->get('ki_upont.curl');
        $user = $this->container->get('security.context')->getToken()->getUser();
        
        // Recupere l'id foyer correspondant
        $response = $this->curl->curl('http://dev-foyer.enpc.org/uPonts/qui.php?prenom=' . urlencode($user->getFirstName()) . '&nom=' . urlencode($user->getLastName()));
        $data = json_decode($response, true);
        
        $this->error = $data['erreur'] != '';       
        $this->token = $this->error ? null : $data['resultat'][0]['id'];
        
        if ($this->token === null)
            return;
        
        $response = $this->curl->curl('http://dev-foyer.enpc.org/uPonts/stats.php?id=' . $this->token);
        $data = json_decode($response, true);
        $this->balance = $data['solde'];
    }
    
    public function hasFailed() { return $this->error; }
    
    public function balance() { return $this->balance; }
      
    public function rankings()
    {
        if ($this->token === null)
            return;
        
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
            'http://dev-foyer.enpc.org/uPonts/stats.php?id=' . $this->token);
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
