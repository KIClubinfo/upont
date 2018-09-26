<?php

namespace KI\UserBundle\Command;

use KI\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PhotoUpdateCommand extends ContainerAwareCommand
{
    protected $FACEBOOK_API_URL = 'https://graph.facebook.com/v2.10';

    protected function configure()
    {
        $this
            ->setName('upont:update:photo')
            ->setDescription('Import missing photos from Facebook for the given promo')
            ->addArgument('promo', InputArgument::REQUIRED, 'The promo whose photos are to be updated.')
            ->addArgument('file', InputArgument::REQUIRED, 'Absolute path to a csv containing facebook_name,facebook_id')
            ->addOption('preview', 'p', InputOption::VALUE_NONE, 'Make a preview of the photos to be imported without importing them')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Treat the users regardless whether they already have a photo on uPont')
            ->addOption('interactive', 'i', InputOption::VALUE_NONE, 'For each match, ask interactively whether the photo should be updated')
            ->addOption('similarity-threshold', 's', InputOption::VALUE_REQUIRED, 'Similarity threshold with Fb profiles above which photos are imported in non-preview and non-interactive mode (arbitrary unit, 200 by default)', 200);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $usersRepo = $this->getContainer()->get('doctrine')->getRepository(User::class);
        $curlService = $this->getContainer()->get('ki_core.service.curl');
        $imageService = $this->getContainer()->get('ki_core.service.image');
        $questionHelper = $this->getHelper('question');
        $isPreview = $input->getOption('preview');
        $users = $usersRepo->findByPromo($input->getArgument('promo'));
        $question = new ConfirmationQuestion('Update? ', false, '/^y/i');
        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $csvData = $serializer->decode(file_get_contents($input->getArgument('file')), 'csv');
        $noPhotoCount = 0;
        $notFoundCount = 0;
        $updatedNoPhotoCount = 0;
        $updatedExistingPhotoCount = 0;
        $similarityThreshold = $input->getOption("similarity-threshold");
        $output->writeln('Importing facebook photos for users (> ' . $similarityThreshold . ' similarity score) :');
        foreach ($users as $user) {
            $noPhoto = $user->imageUrl() === 'uploads/others/default-user.png';
            if (!$noPhoto) {
                $noPhotoCount++;
            }
            if ($noPhoto || $input->getOption('all')) {
                // Find best match
                $bestMatch = null;
                $bestPercent = -1;
                foreach ($csvData as $member) {
                    $percent = $this->isSimilar($user, $member);
                    if ($percent > $bestPercent) {
                        $bestPercent = $percent;
                        $bestMatch = $member;
                    }
                }
                if ($bestPercent > $similarityThreshold) {
                    $userFullName = $user->getFirstName() . ' ' . $user->getLastName();
                    $pictureInfo = $noPhoto ? '[Picture MISSING]' : '[Picture exists]';
                    $output->writeln($userFullName . ' <- ' . $bestMatch['name'] . ' (' . $bestPercent . '% similar) ' . $pictureInfo);
                    $updateConfirmation = $input->getOption('interactive') ? $questionHelper->ask($input, $output, $question) : true;
                    if ($updateConfirmation) {
                        if (!$noPhoto) {
                            $updatedExistingPhotoCount++;
                        } else {
                            $updatedNoPhotoCount++;
                        }
                        if (!$isPreview) {
                            $url = '/' . $bestMatch['id'] . '/picture?width=9999&redirect=false';
                            $dataImage = json_decode($curlService->curl($this->FACEBOOK_API_URL . $url), true);
                            $image = $imageService->upload($dataImage['data']['url'], true);
                            $user->setImage($image);
                        }
                    }
                } else {
                    $notFoundCount++;
                }
                $em->flush();
            }
        }
        $output->writeln([
            'End of list',
            '',
            'Students in promo ' . $input->getArgument('promo') . ' : ' . count($users)
        ]);
        if ($input->getOption('all')) {
            $output->writeln([
                'Imported missing photos: ' . $updatedNoPhotoCount,
                'Not found photos: ' . $notFoundCount,
                'Replaced photos: ' . $updatedExistingPhotoCount,
            ]);
        } else {
            $output->writeln([
                'Missing photos in promo : ' . $noPhotoCount,
                'Imported missing photos: ' . $updatedNoPhotoCount,
                'Not found photos: ' . $notFoundCount,
                'Remaining missing photos: ' . ($noPhotoCount - $updatedNoPhotoCount),
            ]);
        }
    }
    // Compare un User uPont et un utilisateur Facebook et essaye de deviner si
    // ce sont les mêmes personnes
    private function isSimilar(User $user, array $member)
    {
        $score = 0;
        $firstName = $this->cleanString($user->getFirstName());
        $lastName = $this->cleanString($user->getLastName());

        $firstNameFb = $this->cleanString(substr($member['name'], 0, strpos($member['name'], ' ')));
        $lastNameFb = $this->cleanString(substr($member['name'], strpos($member['name'], ' ') + 1));

        $score += $this->compareStr($firstName, $firstNameFb);
        // Le match sur le nom de famille est un facteur bien plus important
        // que celui pour le nom
        $score += 3 * $this->compareStr($lastName, $lastNameFb); //Parameter may be adapted
        return $score;
    }

    // Compare deux chaînes de caractères selon un algorithme fait maison
    // dans le but de détecter les personnes ayant un nom différent sur Facebook
    // Ex: Vanlaer -> Vnlr
    // Favorise le match entre des chaînes ayant mêmes caractères au début
    // et à la fin.
    // Favorise les séquences de caractères identiques, et également les
    // caractères qui matchent et qui sont proches les uns des autres
    private function compareStr(string $str1, string $str2)
    {
        $score = 0;
        list($shortName, $longName) = $this->compareStringLength($str1, $str2);
        $currentIndex = 0;
        $lastIndex = -1;
        $matchingRow = 0;
        for ($i = 0; $i < strlen($shortName); $i++) {
            while ($currentIndex < strlen($longName) && $longName[$currentIndex] != $shortName[$i]) {
                $currentIndex += 1;
                $matchingRow = 0;
            }
            if ($currentIndex < strlen($longName)) {
                //Parameters may be adapted
                if ($i == 0 && $currentIndex == 0) {
                    $score += 10;
                } elseif ($i == strlen($shortName) - 1 && $currentIndex == strlen($longName) - 1) {
                    $score += 10;
                }
                if ($lastIndex > -1) {
                    $score += 5 * exp(-($currentIndex - $lastIndex) / 2);
                }
                $lastIndex = $currentIndex;
                $matchingRow += 1;
                $score += exp($matchingRow / 2);
                $currentIndex += 1;
            }
        }
        return $score;
    }

    //Compare 2 string et renvoie la plus petite et la plus grande
    private function compareStringLength(string $str1, string $str2)
    {
        if (strlen($str1) > strlen($str2)) {
            return [$str2, $str1];
        } else {
            return [$str1, $str2];
        }
    }

    // Nettoie une chaine de caracteres:
    // -enlève d'eventuels accents
    // -met tout en minuscule
    private function cleanString(string $str)
    {
        $replacePairs = [
            'Š' => 'S',
            'š' => 's',
            'Ž' => 'Z', 'ž' => 'z',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A',
            'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a',
            'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y'
        ];
        $str = strtr($str, $replacePairs);
        return strtolower($str);
    }
}
