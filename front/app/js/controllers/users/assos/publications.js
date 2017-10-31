angular.module('upont')
    .controller('Assos_Publications_Ctrl', ['$scope', 'events', 'newsItems', function($scope, events, newsItems) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.predicate = 'user.first_name';
        $scope.reverse = false;

        $scope.pub_info = {
            'draft': {
                description: "Seul les membres du club ont accès aux brouillons dans la liste des publications du club.",
                action: "Créer le brouillon",
                label: "Brouillon",
                ribbon: "Brouillon",
                color: "pink",
                order: 1,
            },
            'scheduled': {
                description: "Cette publication apparaîtra seulement sur le calendrier uPont.",
                action: "Planifier",
                label: "Planification",
                ribbon: "Planifié",
                color: "aqua",
                order: 2,
            },
            'published': {
                description: "Cette publication sera publique sur uPont, vous pourrez envoyer un mail plus tard.",
                action: "Publier",
                label: "Publication",
                ribbon: "Publié",
                color: "yellow",
                order: 3
            },
            'emailed': {
                description: "La publication sera publiée et envoyée par mail à tous les utilisateurs de uPont qui suivent le club.",
                action: "Envoyer par mail",
                label: "Email",
                ribbon: "Email",
                color: "red",
                order: 4
            }
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.simple.publications', {
                url: '',
                templateUrl: 'controllers/users/assos/publications.html',
                data: {
                    title: 'Activités - uPont',
                    top: true
                },
                controller: 'Assos_Publications_Ctrl',
                resolve: {
                    events: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                        return Paginate.get('clubs/' + $stateParams.slug + '/events?sort=-date', 10);
                    }],
                    newsItems: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                        return Paginate.get('clubs/' + $stateParams.slug + '/newsitems?sort=-date', 10);
                    }],
                }
            });
    }]).filter('promoFilter', function() {
        // Filtre spécial qui renvoie les membres selon une année précise
        // En effet, les respos 2A sont d'une année différente
        return function(members, year) {
            var results = [];
            for (var i = 0; i < members.length; i++) {
                // Pas de xor en javasale...
                if ((members[i].user.promo == year && !(members[i].role.match(/2A/g) && members[i].user.promo == year-1)) || (members[i].user.promo != year && (members[i].role.match(/2A/g) && members[i].user.promo == year-1)))
                    results.push(members[i]);
            }
            return results;
        };
    });
