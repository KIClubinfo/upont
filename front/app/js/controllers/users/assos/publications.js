angular.module('upont')
    .controller('Assos_Publications_Ctrl', ['$scope', 'events', 'newsItems', function($scope, events, newsItems) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.predicate = 'user.first_name';
        $scope.reverse = false;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.simple.publications', {
                url: '',
                templateUrl: 'views/users/assos/publications.html',
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
