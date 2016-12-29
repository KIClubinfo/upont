angular.module('upont')
    .controller('Reset_Ctrl', ['$scope', '$http', '$state', '$stateParams', function($scope, $http, $state, $stateParams) {
        $scope.reset = function(password, check) {
            if (!empty(password) && !empty(check)) {
                if (password == check) {
                    $http.post(apiPrefix + 'resetting/token/' + $stateParams.token, {password: password, check: check}).then(
                        function(){
                            alertify.success('Mot de passe réinitialisé !');
                            $state.go('root.login');
                        }
                    );
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
            .state('root.reset', {
                url: 'reset/:token',
                controller: 'Reset_Ctrl',
                templateUrl: 'controllers/public/reset.html',
            });
    }]);
