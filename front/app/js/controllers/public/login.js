angular.module('upont')
    .controller('Login_Ctrl', ['$scope', '$rootScope', '$state', '$location', '$http', 'Permissions', function($scope, $rootScope, $state, $location, $http, Permissions) {
        $('#login-input').focus();
        $scope.login = function(pseudo, mdp, firstTime) {
            if (pseudo.length && mdp.length)
                $http.post(apiPrefix + 'login', {
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
                        Permissions.set(data.token, data.data.roles);
                        alertify.success('Salut ' + data.data.first_name + ' !');

                        if (firstTime) {
                            $state.go('root.users.students.modify');
                        } else {
                            if (typeof $rootScope.urlRef !== 'undefined' && $rootScope.urlRef !== null && $rootScope.urlRef != '/') {
                                $location.path($rootScope.urlRef);
                                $rootScope.urlRef = null;
                            } else {
                                $state.go('root.users.publications.index');
                            }
                        }
                    }
                })
                .error(function(data, status, headers, config) {
                    // Supprime tout token en cas de mauvaise identification
                    Permissions.remove();
                    alertify.error(data.reason);
                });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.login', {
                templateUrl: 'views/public/login.html',
                controller: 'Login_Ctrl'
            });
    }]);
