<?php

namespace KI\UserBundle\Listener;

use KI\UserBundle\Entity\User;
use KI\UserBundle\Event\UserRegistrationEvent;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\TwigBundle\TwigEngine;

class SendMailUserRegistrationListener
{
    private $swiftMailer;
    private $twigEngine;

    public function __construct(Swift_Mailer $swiftMailer, TwigEngine $twigEngine)
    {
        $this->swiftMailer = $swiftMailer;
        $this->twigEngine = $twigEngine;
    }

    // Check si un achievement donné est accompli, si oui envoie une notification
    public function sendMail(UserRegistrationEvent $event)
    {
        $attributes = $event->getAttributes();
        $email = $event->getUser()->getEmail();
        $username = $event->getUser()->getUsername();

        // Envoi du mail
        $message = Swift_Message::newInstance()
            ->setSubject('Inscription uPont')
            ->setFrom('noreply@upont.enpc.fr')
            ->setTo($email)
            ->setBody($this->twigEngine->render('KIUserBundle::registration.txt.twig', $attributes));

        $this->swiftMailer->send($message);

        $message = Swift_Message::newInstance()
            ->setSubject('[uPont] Nouvelle inscription (' . $username . ')')
            ->setFrom('noreply@upont.enpc.fr')
            ->setTo('root@clubinfo.enpc.fr')
            ->setBody($this->twigEngine->render('KIUserBundle::registration-ki.txt.twig', $attributes));

        $this->swiftMailer->send($message);
    }

    public function importFacebook(UserRegistrationEvent $event, $token){
        set_time_limit(3600);

        $users = $this->repository->findByPromo($promo);
        $curl = $this->get('ki_core.service.curl');
        $images = $this->get('ki_core.service.image');
        $i = 0;

        $token = '?access_token=' . $token;

        // Ids des différents groupes facebook
        switch ($promo) {
            // Attention, toujours préciser l'id facebook de la promo d'après
            // pour avoir les étrangers
            case '014':
                $id = '0';
                break;                // Kohlant'wei
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
                $id = '1739424532975028';
                break;  // WEI'STED
            default:
                throw new \Exception('Promo ' . $promo . ' non prise en charge');
        }

        // On récupère la liste des membres
        $baseUrl = 'https://graph.facebook.com/v2.4';
        $data = json_decode($curl->curl($baseUrl . '/' . $id . '/members' . $token . '&limit=10000'), true);

        // Pour chaque utilisateur on essaye de trouver son profil fb, et si oui
        // on récupère la photo de profil
        $alreadyMatched = [];
        foreach ($users as $user) {
            $bestMatch = null;
            $bestPercent = -1;
            foreach ($data['data'] as $member) {
                $percent = $this->isSimilar($user, $member);
                if ($percent > $bestPercent) {
                    $bestPercent = $percent;
                    $bestMatch = $member;
                }
            }

            if ($bestPercent > 70 && !in_array($user, $alreadyMatched)) {
                $url = '/' . $bestMatch['id'] . '/picture' . $token . '&width=9999&redirect=false';
                $dataImage = json_decode($curl->curl($baseUrl . $url), true);
                $image = $images->upload($dataImage['data']['url'], true);
                $user->setImage($image);
                $alreadyMatched[] = $user;
                $i++;
            }
        }

        $this->manager->flush();
        return $this->json([
            'hits' => $i,
            'fails' => count($users) - $i,
            'ratio' => $i / count($users)
        ]);
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
