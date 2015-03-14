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
                        $state.go("profil");
                        // TODO passer en modal
                        alert("Bienvenue sur uPont 2.0 !\n\n\
Dans un premier temps, vérifie bien tes infos (notamment ta photo de profil, que nous avons essayé de récupérer par Facebook de façon automatique).\n\
C'est super important que les infos soient remplies pour pouvoir profiter de uPont au max.\
\n\n\
La version 2 est encore en gros développement, nous avons besoin de ton avis pour l'améliorer de façon continue ! (au moins une mise à jour par semaine sera faite)");
                    } else {
                        $state.go("home.connected");
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
    .controller('Publis_Ctrl', ['$scope', '$resource', 'newsItems', 'events', function($scope, $resource, newsItems, events) {
        $scope.publications = events.concat(newsItems).sort(function(a, b) {
            return b.date - a.date;
        });
    }])
    // .controller('Event_Ctrl', ['$scope', '$resource', "$stateParams", 'evenement', function($scope, $resource, $stateParams, evenement) {
    //     $scope.evenement = evenement;
    //     $scope.url = 'events/' + $stateParams.slug;
    // }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("home", {
                url: "/",
                template: "<div ui-view></div>",
                data: {
                    parent: "home",
                    defaultChild: "connected"
                },
            })
            .state("home.connected", {
                url: "",
                templateUrl: "views/home/connected.html",
                data: {
                    parent: "home.connected",
                    defaultChild: "liste",
                    title: 'uPont - Accueil'
                },
            })
            .state("home.disconnected", {
                url: "",
                templateUrl: "views/home/disconnected.html",
                controller: "Disconnected_Ctrl"
            })
            .state("home.connected.liste", {
                url: "publications",
                templateUrl: "views/home/publiListe.html",
                controller: "Publis_Ctrl",
                resolve: {
                    newsItems: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/newsitems").query().$promise;
                    }],
                    events: ["$resource", function($resource) {
                        return $resource(apiPrefix + "own/events").query().$promise;
                    }]
                }
            });
    }]);
