<?php

namespace App\Service;

use App\Entity\User;
use Hydra\OAuth2\Provider\Hydra;
use Hydra\SDK\Api\OAuth2Api;
use Hydra\SDK\ApiClient;
use Hydra\SDK\Configuration;
use Hydra\SDK\Model\ConsentRequestAcceptance;

class HydraService
{
    private function createClient()
    {
        $provider = new Hydra([
            'clientId' => 'upont-back',
            'clientSecret' => 'upont-back-secret',
            'domain' => 'http://localhost:4444',
        ]);

        // Get an access token using the client credentials grant.
        // Note that you must separate multiple scopes with a plus (+)
        $accessToken = $provider->getAccessToken(
            'client_credentials',
            [
                'scope' => 'hydra.consent'
            ]
        );

        $config = new Configuration();
        $config->setHost('http://localhost:4444');
        // Use true in production!
        $config->setSSLVerification(false);
        $config->setAccessToken($accessToken);

        // Pass the config into an ApiClient. You will need this client in the next step
        $apiClient = new ApiClient($config);

        $oauthApi = new OAuth2Api($apiClient);

        return $oauthApi;
    }

    public function getConsentRequest($consentRequestId)
    {
        $oauthApi = $this->createClient();

        $consentRequest = $oauthApi->getOAuth2ConsentRequest($consentRequestId);

        return $consentRequest;
    }
}
