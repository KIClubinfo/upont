angular.module('upont')
    .controller('Request_Ctrl', ['$scope', '$http', function($scope, $http) {
        $('#login-input').focus();
        $scope.request = function(username) {
            if (!empty(username)) {
                $http.post(apiPrefix + 'resetting/request', {username: username}).success(function(){
                    alertify.success('Mail de réinitialisation envoyé !');
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
