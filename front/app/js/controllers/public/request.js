angular.module('upont')
    .controller('Request_Ctrl', ['$scope', '$http', function($scope, $http) {
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
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.request', {
                url: 'mot-de-passe-oublie',
                controller: 'Request_Ctrl',
                templateUrl: 'controllers/public/request.html',
            });
    }]);
