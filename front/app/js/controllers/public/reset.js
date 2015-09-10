angular.module('upont')
    .controller('Request_Ctrl', ['$scope', '$http', '$state', function($scope, $http, $state) {
        $('#login-input').focus();
        $scope.request = function(username) {
            if (!empty(username)) {
                $http.post(apiPrefix + 'resetting/request', {username: username}).success(function(){
                    alertify.success('Mail de réinitialisation envoyé !');
                }).error(function(e){
                    alertify.error('Identifiant non trouvé !');
                    $state.go('root.request');
                });
            } else {
                alertify.error('Donne ton identifiant !');
            }
        };
    }])
    .controller('Reset_Ctrl', ['$scope', '$http', '$state', '$stateParams', function($scope, $http, $state, $stateParams) {
        $('#login-input').focus();
        $scope.reset = function(password, check) {
            if (!empty(password) && !empty(check)) {
                if (password == check) {
                    $http.post(apiPrefix + 'resetting/token/' + $stateParams.token, {password: password, check: check}).success(function(){
                        alertify.success('Mot de passe réinitialisé !');
                        $state.go('root.login');
                    });
                } else {
                    alertify.error('Les deux mots de passe ne sont pas identiques.');
                }
            } else {
                alertify.error('Au moins un des deux champs est vide.');
            }
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.request', {
                url: 'mot-de-passe-oublie',
                controller: 'Request_Ctrl',
                templateUrl: 'views/public/request.html',
            })
            .state('root.reset', {
                url: 'reset/:token',
                controller: 'Reset_Ctrl',
                templateUrl: 'views/public/reset.html',
            });
    }]);
