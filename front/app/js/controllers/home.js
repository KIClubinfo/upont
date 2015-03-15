angular.module('upont')
    .controller('Disconnected_Ctrl', ['$scope', '$rootScope', '$state', 'StorageService', '$http', 'jwtHelper', '$resource', function($scope, $rootScope, $state, StorageService, $http, jwtHelper, $resource) {
        $scope.login = function(pseudo, mdp) {
            if (pseudo.length && mdp.length)
                $http
                .post(apiPrefix + "login", {
                    username: pseudo,
                    password: mdp
                })
                .success(function(data, status, headers, config) {
                    StorageService.set('token', data.token);
                    StorageService.set('droits', data.data.roles);
                    $rootScope.isLogged = true;
                    $resource(apiPrefix + 'users/:slug', {
                        slug: jwtHelper.decodeToken(data.token).username
                    }).get(function(data) {
                        $rootScope.me = data;
                    });
                    if (data.data.first) {
                        $state.go("root.profil");
                        // TODO passer en modal
                        alert("Bienvenue sur uPont 2.0 !\n\n" +
"Dans un premier temps, vérifie bien tes infos (notamment ta photo de profil, que nous avons essayé de récupérer par Facebook de façon automatique)." +
"C'est super important que les infos soient remplies pour pouvoir profiter de uPont au max." +
"\n\n" +
"La version 2 est encore en gros développement, nous avons besoin de ton avis pour l'améliorer de façon continue ! (au moins une mise à jour par semaine sera faite)");
                    } else {
                        $state.go("root.home.connected");
                    }
                })
                .error(function(data, status, headers, config) {
                    // Supprime tout token en cas de mauvaise identification
                    if (StorageService.get('token')) {
                        StorageService.remove('token');
                        StorageService.remove('droits');
                    }
                    $rootScope.isLogged = false;
                });
        };
    }])
    .controller('Publis_Ctrl', ['$scope', '$resource', 'newsItems', 'events', 'Paginate', function($scope, $resource, newsItems, events, Paginate) {
        // $scope.publications = events.concat(newsItems).sort(function(a, b) {
        //     return b.date - a.date;
        // });
        $scope.events = events;
        $scope.newsItems = newsItems;

        $scope.next = function() {
            Paginate.next($scope.newsItems).then(function(data){
                $scope.newsItems = data;
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.home", {
                url: '',
                templateUrl: "views/home/connected.html",
                data: {
                    title: 'uPont - Accueil'
                },
                controller: "Publis_Ctrl",
                resolve: {
                    newsItems: ['Paginate', function(Paginate) {
                        return Paginate.get('own/newsitems?sort=date', 10);
                    }],
                    events: ["$resource", function($resource) {
                        return $resource(apiPrefix + 'own/events').query().$promise;
                    }]
                }
            })
            .state("root.disconnected", {
                templateUrl: "views/home/disconnected.html",
                controller: "Disconnected_Ctrl"
            });
    }]);
