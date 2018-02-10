<?php

namespace App\Command;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class PhotoUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('upont:update:photo')
            ->setDescription('Import missing photos from Facebook for the given promo')
            ->addArgument('promo', InputArgument::REQUIRED, 'The promo whose photos are to be updated.')
            ->addArgument('usernames', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'The usernames from the specified promo whose photos are to be updated.')
            ->addOption('preview', 'p', InputOption::VALUE_NONE, 'Make a preview of the photos to be imported without importing them')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Treat the users regardless whether they already have a photo on uPont')
            ->addOption('interactive', 'i', InputOption::VALUE_NONE, 'For each match, ask interactively whether the photo should be updated')
            ->addOption('similarity-threshold', 's', InputOption::VALUE_REQUIRED, 'Similarity threshold with Fb profiles above which photos are imported in non-preview and non-interactive mode (in % between 0 and 100)', 85)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repo = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $curlService = $this->getContainer()->get('ki_core.service.curl');
        $imageService = $this->getContainer()->get('ki_core.service.image');
        $fbToken = $this->getContainer()->getParameter('facebook_token');
        $questionHelper = $this->getHelper('question');

        $users = $repo->findByPromo($input->getArgument('promo'));
        $usernames = $input->getArgument('usernames');
        if ($usernames) {
            foreach ($users as $user) {
                if (!in_array($user, $usernames)) {
                    unset($users[array_search($user, $users)]);
                }
            }
        }
        $question = new ConfirmationQuestion('Update? ', false, '/^y/i');
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

        $updateCount = 0;
        $unfoundCount = 0;
        $notUpdatedInteractivelyCount = 0;
        $updateExistingPhoto = 0;

        $output->writeln('Fb photos '.($input->getOption('preview') ? 'would be ' : '').'imported for the following people (> '.$input->getOption('similarity-threshold').'% similar) :');

        foreach ($users as $user) {
            $bestMatch = null;
            $bestPercent = -1;
            $noPhoto = $user->imageUrl() === 'uploads/others/default-user.png';

            if ($noPhoto || $input->getOption('all')) {
                foreach ($data['data'] as $member) {
                    $percent = $this->isSimilar($user, $member);
                    if ($percent > $bestPercent) {
                        $bestPercent = $percent;
                        $bestMatch = $member;
                    }
                }

                if ($bestPercent > $input->getOption('similarity-threshold')) {
                    $output->writeln($user->getFirstName().' '.$user->getLastName().' <- '.$bestMatch['name'].' ('.$bestPercent.'% similar)'.($input->getOption('all') ? ' ['.($noPhoto ? 'to update' : 'already updated').']' : ''));
                    $updateConfirmation = $input->getOption('interactive') ? $questionHelper->ask($input, $output, $question) : true;
                    if (!$input->getOption('preview') && $updateConfirmation) {
                        $url = '/' . $bestMatch['id'] . '/picture' . $token . '&width=9999&redirect=false';
                        $dataImage = json_decode($curlService->curl($baseUrl . $url), true);
                        $image = $imageService->upload($dataImage['data']['url'], true);
                        $user->setImage($image);
                    }
                    if (!$input->getOption('interactive') || $updateConfirmation) {
                        $updateCount++;
                        if (!$noPhoto) {
                            $updateExistingPhoto++;
                        }
                    }
                    else {
                        $notUpdatedInteractivelyCount++;
                    }
                }
                else {
                    $unfoundCount++;
                }

                $em->flush();
            }
        }

        $userSpecification = $usernames ? 'amongst the '.count($usernames).' specified user'.(count($usernames) > 1 ? 's ' : ' ') : ' ';
        $output->writeln(
            ['End of list',
            '',
            'Students in promo '.$input->getArgument('promo').' '.$userSpecification.': '.count($users)
        ]);
        if ($input->getOption('all')) {
            $output->writeln(
                [($input->getOption('preview') && !$input->getOption('interactive') ? 'To be i' : 'I').'mported missing photos: '.($updateCount-$updateExistingPhoto),
                'Non-updated photos: '.($unfoundCount+$notUpdatedInteractivelyCount),
                'Replaced photos: '.$updateExistingPhoto
            ]);
        }
        else {
            $output->writeln(
                ['Missing photos in promo '.$userSpecification.': '.($updateCount+$unfoundCount+$notUpdatedInteractivelyCount),
                ($input->getOption('preview') && !$input->getOption('interactive') ? 'To be i' : 'I').'mported missing photos: '.$updateCount,
                'Remaining missing photos (unfound or refused Facebook profiles): '.($unfoundCount+$notUpdatedInteractivelyCount)
            ]);
        }
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
