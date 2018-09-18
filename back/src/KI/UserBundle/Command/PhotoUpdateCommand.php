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
            ->addOption('similarity-threshold', 's', InputOption::VALUE_REQUIRED, 'Similarity threshold with Fb profiles above which photos are imported in non-preview and non-interactive mode (arbitrary unit, 200 by default)', 200)
        ;
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
        $output->writeln('Importing facebook photos for users (> ' . $similarityThreshold . ' score similar) :');
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
                        if(!$isPreview) {
                            $url = '/' . $bestMatch['id'] . '/picture?width=9999&redirect=false';
                            $dataImage = json_decode($curlService->curl($this->FACEBOOK_API_URL . $url), true);
                            $image = $imageService->upload($dataImage['data']['url'], true);
                            $user->setImage($image);
                        }
                    }
                }
                else {
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
        }
        else {
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
        
        $firstName_fb = $this->cleanString(substr($member['name'], 0, strpos($member['name'], ' ')));
        $lastName_fb = $this->cleanString(substr($member['name'], strpos($member['name'], ' ') + 1));

        $score += $this->compareStr($firstName, $firstName_fb);
        $score += $this->compareStr($lastName, $lastName_fb) * 3; //Parameter may be adapted
        return $score;
    }

    private function compareStr(string $str1, string $str2)
    {
        $score = 0;
        $good_letters = 0;
        $shortName = $str1;
        $longName = $str2;
        if (strlen($shortName) > strlen($longName)) {
            $tmp = $shortName;
            $shortName = $longName;
            $longName = $tmp;
        }
        $k = 0;
        $last_index = -1;
        $matching_row = 0;
        for ($i = 0 ; $i < strlen($shortName) ; $i++) {
            while ($k < strlen($longName) && $longName[$k] != $shortName[$i]) {
                $k += 1;
                $matching_row = 0;
            }
            if ($k < strlen($longName)) {
                //Parameters may be adapted
                if ($i == 0 && $k == 0) {
                    $score += 10;
                }
                elseif ($i == strlen($shortName)-1 && $k == strlen($longName)-1) {
                    $score += 10;
                }
                if ($last_index > -1) {
                    $score += 5*exp(-($k - $last_index)/2);
                }
                $last_index = $k;
                $matching_row += 1;
                $score += exp($matching_row/2);
                $k += 1;
            }
        }
        return $score;
    }

    private function cleanString(string $str)
    {
        $str = strtolower($str);
        $str = str_replace('é', 'e', $str);
        $str = str_replace('è', 'e', $str);
        $str = str_replace('ê', 'e', $str);
        $str = str_replace('ë', 'e', $str);
        $str = str_replace('î', 'i', $str);
        $str = str_replace('ï', 'i', $str);
        $str = str_replace('â', 'a', $str);
        return $str;
    }
}
