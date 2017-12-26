class Tour_Ctrl {
    constructor($scope, $rootScope, $http, $state, Achievements) {
        const steps = [
            {
                state: 'root.users.publications.index',
                icon: 'sign-in',
                text: '<strong>Bienvenue sur uPont !</strong><br>' +
                'Que tu sois nouveau.elle ou déjà vieux con, cet intranet déborde tellement de fonctionnalités que le KI te propose d\'en faire le tour de façon interactive !<br>' +
                'Une surprise t\'attend même à la fin <i class="fa fa-smile-o"></i>'
            },
            {
                state: 'root.users.publications.index',
                icon: 'star',
                text: 'Comme tu as pu le remarquer, ce site est doté d\'un système d\'<strong>achievements.</strong><br>' +
                'Tu as dû gagner le premier d\'entre eux en te connectant ! D\'autres arriveront certainement tout au long de ce tour.<br>' +
                'Note aussi la <strong>barre de recherche</strong> en haut à gauche, elle est <strong>ultra-efficace !</strong>'
            },
            {
                state: 'root.users.publications.index',
                icon: 'pencil',
                text: 'Commençons par cette page d\'accueil. Tous les événements et news des clubs que tu suis sont affichés. Tu peux aussi <strong>publier des messages persos</strong>, et, si tu fais partie d\'un club, <strong>créer des événements/news pour ce club</strong> ! Ah... tu peux aussi disliker toutes sortes de choses... et c\'est anonyme ;-p #betterThanFacebook'
            },
            {
                state: 'root.users.students.modify',
                params: {slug: $rootScope.username},
                icon: 'user',
                text: 'Je te propose de modifier ton profil afin de partir du bon pied.<br>' +
                '<strong>Avoir des infos à jour est super important</strong>, si tout le monde les remplit tu pourras profiter au max des fonctionnalités de uPont comme la <strong>synchronisation des contacts</strong> ou le <strong>jeu de la Réponse D !</strong>'
            },
            {
                state: 'root.users.students.modify',
                params: {slug: $rootScope.username},
                icon: 'calendar',
                text: 'Accessoirement, sur cette page tu peux aussi synchroniser le calendrier uPont avec ton Mac/iBidule/Thunderbird/etc.<br>' +
                '<strong>Attends ? Quel calendrier ? ...</strong>'
            },
            {
                state: 'root.users.calendar',
                icon: 'calendar',
                text: 'Le calendrier contient <strong>tous les événements des clubs que tu suis</strong> mais aussi tes cours !<br>' +
                '<strong>Quoi ? Il est synchronisé avec mes cours ?</strong>'
            },
            {
                state: 'root.users.resources.courses.list',
                icon: 'graduation-cap',
                text: 'En effet ! C\'est ici que tu as la liste des cours et que <strong>tu peux choisir ceux que tu suis !</strong><br>' +
                'Par exemple, essaye de suivre un cours en cliquant sur le symbole <i class="fa fa-eye"></i>. Il sera <strong>automatiquement ajouté au calendrier</strong> dès qu\'il y aura une prochaine séance de ce cours !'
            },
            {
                state: 'root.users.resources.courses.list',
                icon: 'graduation-cap',
                text: 'En cliquant sur la page d\'un cours, tu pourras en plus <strong>accéder à des annales</strong> si jamais tu te sens une humeur de pookie ou que les partiels sont dans moins de 3 heures. Remarque aussi la présence d\'un <strong>panneau latéral</strong> permettant de trier les cours facilement...'
            },
            {
                state: 'root.users.ponthub.category.list',
                icon: 'arrow-down',
                text: 'Mais attends ! <strong>Tu peux télécharger autre chose que le dernier exam de strat fi !</strong><br>' +
                'Ceci est PontHub (prononcer [Pont\'teub]), l\'endroit où tu viendras télécharger toutes les séries et derniers films de vacances !<br>' +
                'Attention, <strong>ce service n\'est accessible que depuis les résidences...</strong>'
            },
            {
                state: 'root.users.students.game',
                icon: 'gamepad',
                text: 'Si tu cherches une autre façon de te distraire, il y a <strong>la Réponse D !</strong><br>' +
                'Dans ce jeu qui poussera ta mémoire dans ses derniers retranchements, tu devras retrouver qui est qui...<br>' +
                '<strong>Essaye une ou deux parties pour voir !</strong>'
            },
            {
                state: 'root.users.students.list',
                icon: 'users',
                text: 'Si tu as besoin d\'un peu d\'aide, tu peux toujours retrouver quelqu\'un via la recherche ou bien dans le <strong>Ponts\'binoscope</strong> et sa fonction de tri. Les pages de profil des élèves sont tellement détaillées que tu sauras <strong>combien de litres ils ont bu ou combien ils ont téléchargé sur PontHub</strong> s\'ils ont accepté de le partager !'
            },
            {
                state: 'root.users.assos.foyer-playlist',
                icon: 'beer',
                text: 'En parlant de Foyer, tu pourras trouver le <strong>Hall Of Fame des plus gros buveurs</strong> sur cette page.<br>' +
                'Si ça te chante, tu peux même <strong>ajouter le dernier clip de Booba pour qu\'il soit passé au prochain Foyer !</strong>'
            },
            {
                state: 'root.users.assos.ki',
                icon: 'download',
                text: 'En passant par là, voici la page de dépannge du KI, où tu pourras <strong>rapport des bugs/suggérer des améliorations sur uPont, mais aussi crier au secours si ton ordi plante !</strong> C\'est super important pour nous d\'avoir du feedback, c\'est ce qui nous permet d\'avoir un intranet aussi exceptionnel !'
            },
            {
                state: 'root.users.resources.upont',
                icon: 'smile-o',
                text: 'Et voilà, c\'est fini ! Enfin, <strong>ce n\'est que le début</strong>...<br>' +
                'uPont est encore en développement, <strong>n\'oublie pas de partager tes idées !</strong><br>' +
                'Comme promis, clique encore une fois la flèche suivante et tu auras un cadeau bien mérité...'
            }
        ];
        $scope.numberSteps = steps.length;
        $scope.step = 5;

        $scope.off = function() {
            // On demande confirmation
            alertify.confirm('Veux-tu quitter le tutoriel ? Tu pourras toujours le réactiver depuis la page de profil.', function(e){
                if (e) {
                    $http.patch($rootScope.url + 'users/' + $rootScope.username, {tour: true}).then(function(){
                        $rootScope.me.tour = true;
                        alertify.success('Tutoriel masqué !');
                    });
                }
            });
        };

        $scope.previous = function() {
            if ($scope.step > 0) {
                if ($scope.step === 9 && !$rootScope.isStudentNetwork) {
                    $scope.loadStep($scope.step - 2);
                } else {
                  $scope.loadStep($scope.step - 1);
                }
            }
        };

        $scope.next = function() {
            if ($scope.step + 1 < steps.length) {
                if ($scope.step === 7 && !$rootScope.isStudentNetwork) {
                    $scope.loadStep($scope.step + 2);
                } else {
                    $scope.loadStep($scope.step + 1);
                }
            } else if ($scope.step + 1 == steps.length) {
                $http.patch($rootScope.url + 'users/' + $rootScope.username + '?achievement=unlocked', {tour: true}).then(function(){
                    $rootScope.me.tour = true;
                    Achievements.check();
                });
            }
        };

        $scope.loadStep = function(step) {
            $state.go(steps[step].state, steps[step].params).then(function(){
                $scope.step = step;
                $scope.icon = steps[step].icon;
                $scope.text = steps[step].text;
            });
        };

        if ($rootScope.me !== undefined && ($rootScope.me.tour === undefined || !$rootScope.me.tour)) {
            $scope.step = 0;
            $scope.icon = steps[0].icon;
            $scope.text = steps[0].text;
        }

        $rootScope.$on('tourEnabled', function() {
            $scope.step = 0;
            $scope.loadStep(0);
        });
    }
}

export default Tour_Ctrl;
