angular.module('upont')
    .controller('Upont_Ctrl', ['$scope', function($scope) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources.upont', {
                url: '/upont',
                templateUrl: 'controllers/users/resources/upont.html',
                data: {
                    title: 'uPont - uPont',
                    top: true
                },
            });
    }]);
