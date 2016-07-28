angular.module('upont')
    .controller('Login_Ctrl', ['$scope', '$rootScope', '$state', '$location', '$http', 'Permissions', 'Achievements', function($scope, $rootScope, $state, $location, $http, Permissions, Achievements) {
        $('#login-input').focus();
        $scope.login = function(pseudo, password, firstTime) {
            if (pseudo.length && password.length)
                $http.post(apiPrefix + 'login', {
                    username: pseudo,
                    password: password
                })
                .success(function(data, status, headers, config) {
                    Permissions.set(data.token, data.data.roles);

                    // Soyons polis
                    if (Permissions.hasRight('ROLE_EXTERIEUR'))
                        alertify.success('Connecté avec succès !');
                    else
                        alertify.success('Salut ' + data.data.first_name + ' !');
                    Achievements.check();

                    if (typeof $rootScope.urlRef !== 'undefined' && $rootScope.urlRef !== null && $rootScope.urlRef != '/') {
                        window.location.href = $rootScope.urlRef ;
                        $rootScope.urlRef = null;
                    } else {
                        $state.go('root.users.publications.index');
                    }
                })
                .error(function(data, status, headers, config) {
                    // Supprime tout token en cas de mauvaise identification
                    Permissions.remove();
                    alertify.error('Mauvais identifiant. Soit l\'identifiant n\'existe pas, soit le mot de passe est incorrect.');
                });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.login', {
                templateUrl: 'controllers/public/login.html',
                controller: 'Login_Ctrl'
            });
    }]);
