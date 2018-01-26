<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\PonthubFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OtherRepository")
 */
class Other extends PonthubFile
{
}
