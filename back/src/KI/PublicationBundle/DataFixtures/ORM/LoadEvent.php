<?php

namespace KI\PublicationBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KI\PublicationBundle\Entity\Event;

class LoadEventFixture extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $event = new Event();
        $event->setName('Passation');
        $event->setText('Une fois n\'est pas coutume, c\'est au KI d\'ouvrir le bal des passations durant cette période de campagne !<br><br>Plusieurs choses à savoir donc : la réunion de passation aura lieu VENDREDI 16 JANVIER À 12H30 EN P102.<br>Si vous ne pouvez vraiment pas venir, il vous suffit d\'envoyer un mail au KI en précisant le ou les poste(s) qui vous intéresse(nt).<br><br>Être au KI c\'est l\'occasion de faire partie d\'un club essentiel de la vie aux Ponts, de participer à plein de projets et aussi d\'apprendre de nombreuses choses. Ne soyez pas effrayés par le coté technique : la majorité des choses à faire ne requièrent pas de compétences particulières en informatique (trésorerie, vente de disques durs et de câbles ethernet, dépannage des chambres, rangement de PontHub...). Le plus important n\'est pas d\'être ultracalé mais plutôt d\'être motivé.<br><br>Je vous rappelle les postes pour lesquels vous pouvez postuler :<br>Prez, vice-prez, trésorier, sec gen, respo com, modérateur, respo hébergement, respo LAN, respo PontHub...<br>Bien sûr ce n\'est pas une liste restrictive, le principe est que c\'est VOUS qui décidez de ce que VOUS voulez faire pour contribuer aux différents projets.<br><br>Encore une fois, n\'hésitez pas à venir, et même si vous n\'y connaissez rien, on vous trouvera un poste afin que vous puissiez être utile au club. Une seule condition : il faut être motivé (et ça commence par venir à la réunion).');
        $event->setDate(1420653127);
        $event->setAuthorClub($this->getReference('club-ki'));
        $event->setAuthorUser($this->getReference('user-trancara'));
        $event->setEntryMethod('Libre');
        $event->setStartDate(mktime(0, 0, 0) + 36*3600);
        $event->setEndDate(mktime(0, 0, 0) + 36.5*3600);
        $event->setPlace('P102');
        $event->addAttendee($this->getReference('user-taquet-c'));
        $event->addAttendee($this->getReference('user-de-boisc'));
        $event->setLikes(array($this->getReference('user-taquet-c')));
        $event->setDislikes(array($this->getReference('user-trancara')));
        $manager->persist($event);

        $event = new Event();
        $event->setName('Le Faucon MilLANium');
        $event->setText('Un nouvel espoir pour vous après cette période de partiels !<br><br>Venez assister les troupes d\'Obi LAN Kenobi dans leur combat contre le vil empereur PLANpatine! Viens dégainer ton sabre LANser comme LANakin au 4ème étage de Prony le mercredi 17 décembre à partir de 21 heures. Nous revisiterons des épisodes comme LANttaque des clones à travers Star Wars Empire at War - Forces of Corruption (RTS - style Age Of Empires) et Star Wars Battlefront 2 (FPS - style counter strike) tout en dégustant les victuailles habituelles des LANs.<br><br>Tl;dr: mercredi 17 décembre de 21 heures à 1 heures en P402, thème Star Wars.<br>Venez nombreux, ce n\'est pas une LAN Solo! Les jeux seront fournis sur place mais pour gagner du temps téléchargez les sur uPont avant de venir.');
        $event->setDate(1418634252);
        $event->setAuthorClub($this->getReference('club-ki'));
        $event->setAuthorUser($this->getReference('user-muzardt'));
        $event->setEntryMethod('Libre');
        $event->setStartDate(1418846400);
        $event->setEndDate(1418860800);
        $event->setPlace('P402');
        $event->addAttendee($this->getReference('user-taquet-c'));
        $event->addAttendee($this->getReference('user-de-boisc'));
        $event->addAttendee($this->getReference('user-muzardt'));
        $manager->persist($event);

        $event = new Event();
        $event->setName('Jeux de Rôles');
        $event->setText('C\'est l\'heure de se mettre aux jeux de rôle, préparez vos sorts et vos pavois de feu +7 !<br>Que tu ne sois pas initié aux lancers de d20 ou que la magie profane n\'ait plus de secrets pour toi, nous serons ravis de t\'accueillir pour partir à l\'assaut des méchants les plus maléfiques.<br><br>À quel jeu allons-nous jouer me demandez-vous ?<br>Eh bien il s\'agit du très célèbre Donjons et Dragons (D&D pour les intimes) dont la version 5.0 vient de sortir pour célébrer les 40 ans du succès continu du plus populaire et passionnant des jeux de rôle.<br>En pratique, la première partie se déroulera en P102 (juste derrière la Médiatek, à coté du foyer), et aura lieu le Mardi 21 octobre à 21h pour la soirée. Comme toute bonne partie de jeux de rôle, des victuailles seront présentes en ravitaillement (mange bien avant quand même) !<br>Il n\'y a rien à apporter mis à part ta tête, toutefois les offrandes au MD et autres tentatives de corruption sont bien sûr acceptées...<br><br>Pour le modalités de participation, par la nature même du jeu il y aura très peu de places disponibles donc ça va se faire au shotgun Mercredi 15 octobre à 20h précises. Bien sûr une belle motivation est demandée et décidera à la fin en cas de doute ;)<br><br>Alors ? Qu\'attends tu ? Prends ton grimoire et ton épée longue et pars à la découverte de terres inexplorées !<br><br>PS: un Glyphe de Garde FP6 a été déposé à l\'entrée, merci de ne pas le déclencher...');
        $event->setDate(1413038106);
        $event->setAuthorClub($this->getReference('club-mediatek'));
        $event->setAuthorUser($this->getReference('user-trancara'));
        $event->setEntryMethod('Shotgun');
        $event->setStartDate(1413918000);
        $event->setEndDate(1413930600);
        $event->setShotgunDate(1413396000);
        $event->setPlace('P102');
        $event->addAttendee($this->getReference('user-taquet-c'));
        $event->addAttendee($this->getReference('user-trancara'));
        $event->addAttendee($this->getReference('user-guerinh'));
        $manager->persist($event);

        $event = new Event();
        $event->setName('Formations PEP - Objectif recrutement');
        $event->setText('Soirée de formations poussées en vue du recrutement de la Toussaint.<br><br>Dîner/cocktail assuré par PEP.<br><br>OUVERT AUX COTISANTS UNIQUEMENT.');
        $event->setDate(1411836660);
        $event->setAuthorClub($this->getReference('club-pep'));
        $event->setAuthorUser($this->getReference('user-guerinh'));
        $event->setEntryMethod('Libre');
        $event->setStartDate(1413999000);
        $event->setEndDate(1414009800);
        $event->setPlace('Amphi Navier');
        $event->addAttendee($this->getReference('user-taquet-c'));
        $event->addAttendee($this->getReference('user-guerinh'));
        $manager->persist($event);

        $event = new Event();
        $event->setName('Interne de Noël');
        $event->setText('Doux 1A, Très cher 2A, étranger,<br><br>Voici venue la période de Noël, le temps des cadeaux, le retour des guirlandes qui illuminent les foyers… En cette fin d\'année, il est d\'usage de faire plaisir à votre entourage.<br>Votre BDE adoré, avant de tirer sa révérence, tient lui aussi à vous offrir une surprise de taille.<br><br>Jeudi 18 décembre, après avoir reçu le T-Ponch autour d\'un merveilleux petit-déjeuner le matin, vous êtes attendus nombreux le soir même pour l\'Interne de Noël (Flantier), qui aura pour thème … HOLLYWOOD ! Mais attention, pas n\'importe lequel : un Hollywood aux couleurs de Noël, brillant de mille feux des films cultes américains et des Blockbusters plus récents. Le thème est suffisamment large pour vous laisser aller à votre fantaisie ; Soyez-donc originaux ! Surtout, n\'oubliez pas que votre déguisement doit compter au moins un "accessoire de Noël", 25 décembre oblige.<br><br>La veillée aura lieu à partir de 22h en salle polyvalente spécialement décorée pour l\'occasion.<br>Notez que cette fois-ci, il n\'y aura pas de pizzas car nous préférons vous étonner avec tout un tas de surprises au cours de la soirée. Il y aura bien-sûr quelques gourmandises, mais veillez à bien manger avant !<br><br>Le BDE veut mettre le paquet pour sa dernière interne. On espère que vous serez présents au rendez-vous !');
        $event->setDate(1418254132);
        $event->setAuthorClub($this->getReference('club-bde'));
        $event->setAuthorUser($this->getReference('user-dziris'));
        $event->setEntryMethod('Libre');
        $event->setStartDate(mktime(0, 0, 0) + 9*3600);
        $event->setEndDate(mktime(0, 0, 0) + 15*3600);
        $event->setPlace('Salle Polyvalente');
        $event->addAttendee($this->getReference('user-taquet-c'));
        $event->addAttendee($this->getReference('user-trancara'));
        $event->addAttendee($this->getReference('user-guerinh'));
        $event->addAttendee($this->getReference('user-de-boisc'));
        $event->addAttendee($this->getReference('user-donat-bb'));
        $event->addAttendee($this->getReference('user-muzardt'));
        $manager->persist($event);

        $event = new Event();
        $event->setName('Don Giovanni');
        $event->setText('Bonjour à tous,<br><br>Pour cette nouvelle année, on commence avec un opéra que vous connaissez déjà surement tous : Don Giovanni.<br><br>Derrière ses hardiesses et sa quête effrénée des femmes, ce sont Dieu, les hommes et l’ordre du monde que Don Giovanni raille et défie.<br>C’est en cela que sa chute sera inéluctable et son châtiment foudroyant.<br>L’opéra de Mozart – un dramma giocoso, c’est-à-dire un drame joyeux – dit tout cela avec une force irrépressible.<br>Le livret habilement troussé de Lorenzo da Ponte reprend le mythe de Tirso de Molina, dont Molière a aussi fait son Dom Juan ; mais la musique de Mozart, dès l’ouverture, pare l’ensemble d’une dimension métaphysique qui dépasse de très loin l’anecdote.<br>Don Giovanni est la quintessence du génie mozartien, une sorte d’absolu du genre, où le haut et le bas de la nature humaine se côtoient, où flirtent le tragique et le grotesque, le sublime et le dérisoire, les élans spirituels et les plaisirs de la chair. Le tout coulé dans la plus divine musique jamais écrite.<br>Celle qui fera dire à Richard Wagner que Don Giovanni est « l’opéra des opéras ».<br><br>La mise en scène très noire et désormais devenue légendaire a été faite par le metteur en scène et cinéaste autrichien Michael Haneke.');
        $event->setDate(1421778600);
        $event->setAuthorClub($this->getReference('club-bda'));
        $event->setAuthorUser($this->getReference('user-donat-bb'));
        $event->setEntryMethod('Shotgun');
        $event->setStartDate(mktime(0, 0, 0) + 40*3600);
        $event->setEndDate(mktime(0, 0, 0) + 44*3600);
        $event->setShotgunDate(time() + 3600);
        $event->setPlace('Opéra Bastille');
        $event->setShotgunLimit(12);
        $event->setShotgunText('Viens chercher la place chez moi');
        $manager->persist($event);

        $manager->flush();
    }

    public function getOrder()
    {
        return 22;
    }
}
