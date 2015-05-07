angular.module('upont').controller('KI_Ctrl', ['$scope', '$resource', function($scope, $resource) {
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.ki', {
                url: '/tutoriels',
                templateUrl: 'views/users/assos/ki.html',
                controller: 'KI_Ctrl',
                data: {
                    title: 'Dépannage - uPont'
                }
            });
    }]);
