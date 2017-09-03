<?php

namespace KI\UserBundle\Command;

use KI\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhotoUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('upont:update:photo')
            ->setDescription('Import missing photos from Facebook for the given promo')
            ->addArgument('promo', InputArgument::REQUIRED, 'The promo whose photos are to be updated.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $curlService = $this->getContainer()->get('ki_core.service.curl');
        $imageService = $this->getContainer()->get('ki_core.service.image');
        $fbToken = $this->getContainer()->getParameter('facebook_token');

        $users = $repo->findByPromo($input->getArgument('promo'));

        $token = '?access_token=' . $fbToken;

        // Ids des différents groupes facebook
        switch ($input->getArgument('promo')) {
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
        $data = json_decode($curlService->curl($baseUrl . '/' . $id . '/members' . $token . '&limit=10000'), true);

        $bestMatch = null;
        $bestPercent = -1;
        $updateCount = 0;
        $unfoundCount = 0;

        $output->writeln('Fb photo imported for the following people :');

        foreach ($users as $user) {
            if ($user->imageUrl() === 'uploads/others/default-user.png') {
                foreach ($data['data'] as $member) {
                    $percent = $this->isSimilar($user, $member);
                    if ($percent > $bestPercent) {
                        $bestPercent = $percent;
                        $bestMatch = $member;
                    }
                }

                if ($bestPercent > 85) {
                    $url = '/' . $bestMatch['id'] . '/picture' . $token . '&width=9999&redirect=false';
                    $dataImage = json_decode($curlService->curl($baseUrl . $url), true);
                    $image = $imageService->upload($dataImage['data']['url'], true);
                    $user->setImage($image);
                    $updateCount++;
                    $output->writeln($user->getFirstName().' '.$user->getLastName());
                }
                else {
                    $unfoundCount++;
                }

                $em->flush();
            }
        }
        $output->writeln(
          ['End of list',
          '',
          'Students in promo '.$input->getArgument('promo').': '.count($users),
          'Missing photos in promo: '.($updateCount+$unfoundCount),
          'Imported missing photos :'.$updateCount,
          'Remaining missing photos (unfound Facebook profiles): '.$unfoundCount
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
