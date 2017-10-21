angular.module('upont')
    .controller('Administration_Publications_Ctrl', ['$scope', 'events', 'newsItems', function($scope, events, newsItems) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.predicate = 'user.firstname';
        $scope.reverse = false;
        $scope.pub_info = {
            'Draft': {
                description: "Seul les membres du club ont accès aux brouillons dans la liste des publications du club.",
                action: "Créer le brouillon",
                label: "Brouillon",
                ribbon: "Brouillon",
                color: "pink",
                order: 1,
            },
            'Scheduled': {
                description: "Cette publication apparaîtra seulement sur le calendrier uPont.",
                action: "Planifier",
                label: "Planification",
                ribbon: "Planifié",
                color: "aqua",
                order: 2,
            },
            'Published': {
                description: "Cette publication sera publique sur uPont, vous pourrez envoyer un mail plus tard.",
                action: "Publier",
                label: "Publication",
                ribbon: "Publié",
                color: "yellow",
                order: 3
            },
            'Emailed': {
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
            .state('root.users.administration.publications', {
                url: '',
                controller : 'Administration_Publications_Ctrl',
                templateUrl: 'controllers/users/administration/publications.html',
                data: {
                    title: 'Activité - uPont',
                    top: true
                },
                resolve: {
                    events: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                        return Paginate.get('clubs/' + $stateParams.slug + '/events?sort=-date', 10);
                    }],
                    newsItems: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                        return Paginate.get('clubs/' + $stateParams.slug + '/newsitems?sort=-date', 10);
                    }],
                }
            });
    }]);
