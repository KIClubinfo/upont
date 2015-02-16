<?php

namespace OC\PlatformBundle\DataFixtures\ORM\Publications;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\UpontBundle\Entity\Publications\Newsitem;

class LoadNewsitemFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $newsitem = new Newsitem();
        $newsitem->setTitle('Le Jeu');
        $newsitem->setTextLong('Vous avez perdu au jeu, c\'est bien dommage.
Il existe trois règles fondamentales :
- Tout le monde joue au Jeu (parfois restreint en : "tous les gens qui connaissent le Jeu y jouent", ou alors en "Tu joues continuellement au Jeu").
- Qui pense au jeu y perd immédiatement. Si vous lisez ceci, vous avez donc perdu.
- Chaque défaite doit être annoncée à au moins une personne. Une phrase comme "J\'ai perdu", "J\'ai perdu le jeu", "Le jeu" ou chez quelques joueurs "Ca sent la perte" est ici très souvent employée. De plus, si vous faites perdre quelqu\'un, vous devez aussi l\'annoncer par un "Tu as perdu".');
    	$newsitem->setDate(1414242424);
    	$newsitem->setAuthorClub($this->getReference('club-ki'));
    	$newsitem->setAuthorUser($this->getReference('user-taquet-c'));
        $newsitem->setLikes(array($this->getReference('user-taquet-c')));
        $newsitem->setUnlikes(array($this->getReference('user-trancara')));
        $manager->persist($newsitem);

        $newsitem = new Newsitem();
        $newsitem->setTitle('Slides de la formation Git');
        $newsitem->setTextLong('Comme promis, voici les slides de la formation !');
    	$newsitem->setDate(1418325122);
    	$newsitem->setAuthorClub($this->getReference('club-ki'));
    	$newsitem->setAuthorUser($this->getReference('user-trancara'));
    	$newsitem->setImage($this->getReference('image-newsitem-git'));
        $manager->persist($newsitem);

        $newsitem = new Newsitem();
        $newsitem->setTitle('Disques durs 1 To arrivés !');
        $newsitem->setTextLong('Les disques durs 1 To viennent d\'arriver, ceux qui en ont commandé peuvent venir les chercher dès ce soir 18h (après les cours) au local.');
    	$newsitem->setDate(1417532897);
    	$newsitem->setAuthorClub($this->getReference('club-ki'));
    	$newsitem->setAuthorUser($this->getReference('user-trancara'));
        $manager->persist($newsitem);

        $newsitem = new Newsitem();
        $newsitem->setTitle('Comment créer un club');
        $newsitem->setTextLong('Pour ceux ou celles qui auraient envie de créer un club, voici une page expliquant ce qu\'il faut faire !');
    	$newsitem->setDate(1412831521);
    	$newsitem->setAuthorClub($this->getReference('club-bde'));
    	$newsitem->setAuthorUser($this->getReference('user-dziris'));
        $newsitem->setUnlikes(array($this->getReference('user-trancara'), $this->getReference('user-dziris')));
        $manager->persist($newsitem);

        $newsitem = new Newsitem();
        $newsitem->setTitle('PEP t\'accompagne dans ton projet');
        $newsitem->setTextLong('Cher futur entrepreneur,

En tant que Junior-Entreprise, Ponts Etudes Projets a la possibilité de booster ton projet de création d\'entreprise grâce à Alten, cabinet de conseil en ingénierie et innovation dont nous sommes partenaires, et ce dans le cadre d\'un concours national.

À la clef :
- l\'appui d\'un grand cabinet de conseil en ingénierie pour monter ta boite ;
- l\'hébergement du projet, dont 5 à 10 PFE conduits au sein de la Direction R&D d\'Alten ;
- des formations pour ces stagiaires au management de l\'innovation ;
- et évidemment, TA boîte.

Pour cela, ton projet doit :
- Etre innovant, technologique et moderne ;
- Avoir un potentiel impact sociétal important ;
- Etre réalisable à court terme (6 mois);

Pour plus de détails sur le concours, contacte David (david.dessalles@eleves.enpc.fr). Saches que ton projet, s\'il est lauréat, deviendra NOTRE priorité stratégique de l\'année. Nous t\'attendons avec impatience pour préparer ensemble ta candidature.');
    	$newsitem->setDate(1414462150);
    	$newsitem->setAuthorClub($this->getReference('club-pep'));
    	$newsitem->setAuthorUser($this->getReference('user-guerinh'));
        $manager->persist($newsitem);

        $newsitem = new Newsitem();
        $newsitem->setTitle('Pulls');
        $newsitem->setTextLong('Vous désespériez de voir un jour vos pulls des Ponts pointer le bout de leur nez ? Eh bien ne vous inquiétez pas, cette longue attente est terminée ! Le Bde a le grand plaisir de vous annoncer que oui, ils sont enfin la ! Alors si vous avez commandé le votre, venez le récupérer tout à l\'heure, à partir de 12h30, au local. On sera là bas jusqu\'à 14h30 au moins.');
    	$newsitem->setDate(1417759985);
    	$newsitem->setAuthorClub($this->getReference('club-bde'));
    	$newsitem->setAuthorUser($this->getReference('user-dziris'));
    	$newsitem->setImage($this->getReference('image-newsitem-pulls'));
        $manager->persist($newsitem);

        $manager->flush();
    }

    public function getOrder()
    {
        return 23;
    }
}
