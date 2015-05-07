angular.module('upont')
    .controller('Tutorials_Ctrl', ['$scope', function($scope) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources.tutorials', {
                url: '/tutoriels',
                abstract: true,
                templateUrl: 'views/users/resources/tutorials.html',
                data: {
                    title: 'Tutoriels - uPont',
                    top: true
                },
            });
    }]);
