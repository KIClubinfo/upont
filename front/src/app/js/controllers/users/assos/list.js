angular.module('upont')
    .controller('Assos_List_Ctrl', ['$rootScope', '$scope', 'clubs', function($rootScope, $scope, clubs) {
        $scope.clubs = clubs;
        $rootScope.displayTabs = true;
        $scope.clubTypes = [
            {name: "Assos", slug: "asso"},
            {name: "Clubs gastronomiques", slug: "club-gastronomique"},
            {name: "Clubs artistiques", slug: "club-artistique"},
            {name: "Clubs de divertissement", slug: "club-divertissement"},
            {name: "Clubs culturels", slug: "club-culturel"}
        ];
        $scope.clubSlugs = $scope.clubTypes.map(function(dict) { return dict.slug; });
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos', {
                url: 'assos',
                abstract: true,
                templateUrl: 'controllers/users/assos/index.html',
                data: {
                    title: 'Clubs & Assos - uPont'
                }
            })
            .state('root.users.assos.list', {
                url: '',
                templateUrl: 'controllers/users/assos/list.html',
                controller: 'Assos_List_Ctrl',
                resolve: {
                    clubs: ['$resource', function($resource) {
                        return $resource(API_PREFIX + 'clubs?sort=fullName').query().$promise;
                    }]
                },
                data: {
                    top: true
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
