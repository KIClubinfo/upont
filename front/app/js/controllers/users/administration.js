angular.module('upont')
    .controller('Administration_Ctrl', ['$scope', function($scope) {
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.administration', {
                url: 'publications',
                templateUrl: 'views/users/administration.html',
                controller: 'Administration_Ctrl'
            });
    }]);
