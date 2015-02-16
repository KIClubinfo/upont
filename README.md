Résumé
======

Version 2.0 de YouPont, l'intranet de l'ENPC.


Installation
============

Windows
-------

- Installer Linux
- Installer Linux
- Installer Linux
- Non sérieusement **vous allez galérer** autrement, prenez au moins une VM ou mettez ça sur une clé Live.

Linux
-----

- Télécharger le [script d'installation](http://git.enpc.org/ki/youpont/raw/master/install.sh)
- Modifier le début du script d'installation suivant ses paramètres
- Avoir réglé convenablement le proxy partout si nécessaire. (apt-get, export)
- Éxecuter le script d'installation.

Windows
-------

Vous êtes toujours là ?
Bon ben vous pouvez toujours essayer... dans l'esprit il faut reprendre le script susnommé et reproduire les mêmes installations sur Windows. Il faut notamment faire gaffe à la variable PATH qu'il faut parfois changer afin de pouvoir accéder à des commandes générales (php...) depuis la console windows.

Mac
-------

Mêmes conseils que pour Windows, sauf qu'au moins vous aurez la chance d'avoir un terminal décent.


Participer au projet
====================

Les règles de base :

- Toujours lancer le script d'update après un pull.
- Pour le back, le script clear reset l'environnement afin de débugger les tests.
- Pour le back, **toujours vérifier que les tests passent avant de commiter.**


Aussi :

- Suivre le [board Trello](https://trello.com/b/a7pIk6zk/youpont) et s'assigner des tâches en cours
- L'[appli mobile](http://localhost/v2-pg/) est visible en navigateur
- Le [front](http://localhost/v2-ng/) aussi
- Une explication générale du fonctionnement de uPont est disponible dans le manuel du KI


Pour apprendre :

- Tuto [Git](http://openclassrooms.com/courses/gerez-vos-codes-source-avec-git)
- Tuto [HTML/CSS](http://openclassrooms.com/courses/apprenez-a-creer-votre-site-web-avec-html5-et-css3)
- Tuto [PHP](http://openclassrooms.com/courses/concevez-votre-site-web-avec-php-et-mysql)
- Tuto [MVC](http://openclassrooms.com/courses/concevez-votre-site-web-avec-php-et-mysql/organiser-son-code-selon-l-architecture-mvc)
- Tuto [Symfony](http://openclassrooms.com/courses/developpez-votre-site-web-avec-le-framework-symfony2) (très costaud si vous n'avez jamais vu de MVC)
- Tuto [Angular](http://openclassrooms.com/courses/angular-js)


La doc utile :

- [API uPont](http://localhost/v2-api/)
- [Symfony](http://symfony.com/doc/current/index.html)
- [FOSRestBundle](http://symfony.com/doc/master/bundles/FOSRestBundle/index.html)
- [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/index.md)
- [NelmioAPIDocBundle](https://github.com/nelmio/NelmioApiDocBundle/blob/master/Resources/doc/index.md)
- [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md)
- [JMSSerializerBundle](http://jmsyst.com/bundles/JMSSerializerBundle)
- [Angular](https://docs.angularjs.org/api)
- [OnsenUI](http://onsen.io/guide/overview.html)


Règles de codage
================


Préambule
---------

Pour citer un grand homme,
> First off, I'd suggest printing out a copy of the GNU coding standards, and NOT read it.  Burn them, it's a great symbolic gesture. *Linus Torvalds*


Conseils pour le back (PHP)
---------------------------

- L'indentation se fait avec 4 espaces blancs et **4 espaces blancs uniquement**. Configure donc votre éditeur pour qu'il insère des espaces blancs au lieu de tabulations.
- Il faudrait aussi configurer l'éditeur afin qu'il retire automatiquement les espaces blancs en fin de ligne (remove trailing spaces) lors de l'enregistrement du fichier.
- Si possible, une contrainte à respecter serait de ne pas depasser 80 caractères en longueur de ligne (c'est la norme).
- De manière générale, essayer de garder les mêmes noms de fichier/variable d'un dossier/fichier à l'autre.
- Respecter la structure des namespace ! Si votre classe est KI\UpontBundle\Entity\Manger\Classe1, alors le fichier correspondant est forcément KI/UpontBundle/Entity/Manger/Classe1.php.
- Les bonnes pratiques ennoncées ci dessous sont de Dennis M. Ritchie, inventeur du C, alors prenez en de la graine.


Maintenant des exemples d'indentation respectable, le reste n'est que poussière :

    // Les commentaires tous comme ceux-ci (norme
    // Même en multiligne
    // L'usage de /* est réservé pour les annotations qui s'écrivent comme ceci

    /**
     * Commentaire
     * @Assert\Type("string")
     */

    // Les if : pas d'accolades si toutes les briques du if sont monolignes
    // Noter les espaces entre if et (
    if ($test)
        doThis();
    else
        doThat();

    // Sinon
    if ($test <= 3 ) {
        echo 'miam';
    } else {
        $bouffe = 'carotte';
        echo $bouffe;
    }

    // Boucles
    while($a === 'b') {
        doSomeStuff();
    }

    foreach($array as $key => $value) {
        doThat($a, $b);
    }

    // Les switch
    switch ($suffix) {
    case 'G':
    case 'g':
        $mem++;
        break;
    case 'M':
    case 'm':
        $mem--;
        break;
    case 'K':
    case 'k':
        $mem += 3;

    default:
        break;
    }

    // Les classes ont des accolades dans le retour à la ligne
    class Chien
    {
        // On préfère protected à private si c'est pas nécéssaire
        protected $variable;

        // Idem pour les fonctions sauf si elles tiennent en un ligne
        public function veryLong()
        {
            echo 1;
            echo 2;
        }

        public function returnTrue() { return false; }
    }

    // Le nommage des fonctions/variables se fait en anglais en camelCase
    $goodVariable
    $Mauvaise_Variable

    // Préférer l'usage des simple quotes ' au lieu des double "
    // On insère des espaces de chaque coté autour de == <= >= += -= + - = * / . etc.
    // On n'écarte pas les parenthèses dans les fonctions
    pasBien( $a, $b, $c);
    bien($a, $b, $c);



Conseils pour le front (Javasale)
---------------------------------

Coco amuse-toi
