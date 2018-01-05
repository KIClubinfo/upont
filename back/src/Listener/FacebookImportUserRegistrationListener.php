<?php

namespace App\Listener;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\CurlService;
use App\Service\ImageService;
use App\Entity\User;

use App\Event\UserRegistrationEvent;

class FacebookImportUserRegistrationListener
{
    private $curlService;
    private $imageService;
    private $manager;
    private $fbToken;

    public function __construct(CurlService $curlService, ImageService $imageService, EntityManagerInterface $manager, $facebookToken)
    {
        $this->curlService = $curlService;
        $this->imageService = $imageService;
        $this->manager = $manager;
        $this->fbToken = $facebookToken;
    }

    public function facebookImport(UserRegistrationEvent $event){
        $user = $event->getUser();

        $token = '?access_token=' . $this->fbToken;

        // Ids des différents groupes facebook
        switch ($user->getPromo()) {
            // Attention, toujours préciser l'id facebook de la promo d'après
            // pour avoir les étrangers
            case '015':
                $id = '359646667495742';
                break;  // Wei't spirit
            case '016':
                $id = '1451446761806184';
                break; // Wei't the phoque
            case '017':
                $id = '737969042997359';
                break;  // F'wei'ght Club
            case '018':
                $id = '1739424532975028';
                break;  // WEI'STED
            case '019':
                $id = '313192685791329';
                break;  // WEI'T FOR IT
            case '020':
                $id = '313192685791329';
                break;  // WEI'T FOR IT
            default:
                return;
        }

        // On récupère la liste des membres
        $baseUrl = 'https://graph.facebook.com/v2.10';
        $data = json_decode($this->curlService->curl($baseUrl . '/' . $id . '/members' . $token . '&limit=10000'), true);

        $bestMatch = null;
        $bestPercent = -1;
        foreach ($data['data'] as $member) {
            $percent = $this->isSimilar($user, $member);
            if ($percent > $bestPercent) {
                $bestPercent = $percent;
                $bestMatch = $member;
            }
        }

        if ($bestPercent > 85) {
            $url = '/' . $bestMatch['id'] . '/picture' . $token . '&width=9999&redirect=false';
            $dataImage = json_decode($this->curlService->curl($baseUrl . $url), true);
            $image = $this->imageService->upload($dataImage['data']['url'], true);
            $user->setImage($image);
        }

        $this->manager->flush();
    }

    // Compare un User uPont et un utilisateur Facebook et essaye de deviner si
    // ce sont les mêmes personnes
    private function isSimilar(User $user, array $member)
    {
        $percent = 0;
        similar_text($user->getFirstName() . ' ' . $user->getLastName(), $member['name'], $percent);
        return $percent;
    }
}
