angular.module('upont')
    .controller('Login_Ctrl', ['$scope', '$rootScope', '$state', '$location', '$http', 'Permissions', function($scope, $rootScope, $state, $location, $http, Permissions) {
        $('#login-input').focus();
        $scope.login = function(pseudo, password, firstTime) {
            if (pseudo.length && password.length)
                $http.post(apiPrefix + 'login', {
                    username: pseudo,
                    password: password
                })
                .success(function(data, status, headers, config) {
                    Permissions.set(data.token, data.data.roles);
                    alertify.success('Salut ' + data.data.first_name + ' !');

                    if (typeof $rootScope.urlRef !== 'undefined' && $rootScope.urlRef !== null && $rootScope.urlRef != '/') {
                        $location.path($rootScope.urlRef);
                        $rootScope.urlRef = null;
                    } else {
                        $state.go('root.users.publications.index');
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
