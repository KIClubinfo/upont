angular.module('upont')
    .controller('Disconnected_Ctrl', ['$scope', '$rootScope', '$state', 'StorageService', '$http', 'jwtHelper', '$resource', '$location', function($scope, $rootScope, $state, StorageService, $http, jwtHelper, $resource, $location) {
        $('#login-input').focus();
        $scope.login = function(pseudo, mdp, firstTime) {
            if (pseudo.length && mdp.length)
                $http
                .post(apiPrefix + "login", {
                    username: pseudo,
                    password: mdp
                })
                .success(function(data, status, headers, config) {
                    if (data.data.first) {
                        $scope.login(pseudo, mdp, true);
                        alertify.alert('Bienvenue sur uPont 2.0 !<br><br>' +
'Dans un premier temps, vérifie bien tes infos (notamment ta photo de profil, que nous avons essayé de récupérer par Facebook de façon automatique).<br>' +
'C\'est super important que les infos soient remplies pour pouvoir profiter de uPont au max.');
                    } else {
                        StorageService.set('token', data.token);
                        StorageService.set('droits', data.data.roles);
                        $rootScope.isLogged = true;
                        $rootScope.init(jwtHelper.decodeToken(data.token).username);
                        alertify.success('Salut ' + data.data.first_name + ' !');

                        if (firstTime) {
                            $state.go("root.zone_eleves.profile");
                        } else {
                            if (typeof $rootScope.urlRef !== 'undefined' && $rootScope.urlRef !== null && $rootScope.urlRef != '/') {
                                $location.path($rootScope.urlRef);
                                $rootScope.urlRef = null;
                            } else {
                                $state.go("root.zone_eleves.home");
                            }
                        }
                    }
                })
                .error(function(data, status, headers, config) {
                    // Supprime tout token en cas de mauvaise identification
                    if (StorageService.get('token')) {
                        StorageService.remove('token');
                        StorageService.remove('droits');
                    }
                    $rootScope.isLogged = false;
                    alertify.error(data.reason);
                });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.disconnected", {
                templateUrl: "views/elements_publics/disconnected.html",
                controller: "Disconnected_Ctrl"
            });
    }]);
