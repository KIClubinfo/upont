angular.module('upont')
    .controller('Disconnected_Ctrl', ['$scope', '$rootScope', '$state', 'StorageService', '$http', 'jwtHelper', '$resource', function($scope, $rootScope, $state, StorageService, $http, jwtHelper, $resource) {
        $('#login-input').focus();
        $scope.login = function(pseudo, mdp) {
            if (pseudo.length && mdp.length)
                $http
                .post(apiPrefix + "login", {
                    username: pseudo,
                    password: mdp
                })
                .success(function(data, status, headers, config) {
                    if (data.data.first) {
                        $scope.login(pseudo, mdp);
                        $state.go("root.profile");
                        alertify.alert('Bienvenue sur uPont 2.0 !<br><br>' +
'Dans un premier temps, vérifie bien tes infos (notamment ta photo de profil, que nous avons essayé de récupérer par Facebook de façon automatique).<br>' +
'C\'est super important que les infos soient remplies pour pouvoir profiter de uPont au max.');
                    } else {
                        StorageService.set('token', data.token);
                        StorageService.set('droits', data.data.roles);
                        $rootScope.isLogged = true;
                        $rootScope.init(jwtHelper.decodeToken(data.token).username);
                        alertify.success('Salut ' + data.data.first_name + ' !');
                        $state.go("root.home");
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
                templateUrl: "views/home/disconnected.html",
                controller: "Disconnected_Ctrl"
            });
    }]);
