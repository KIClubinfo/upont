angular.module('upont')
    .controller('Tutorials_Ctrl', ['$scope', 'tutos', '$resource', function($scope, tutos, $resource) {
        $scope.tutos = tutos;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources.tutorials', {
                url: '/tutoriels',
                template: '<div ui-view></div>',
                abstract: true,
                data: {
                    title: 'Tutoriels - uPont',
                    top: true
                },
            })
            .state('root.users.resources.tutorials.list', {
                url: '',
                templateUrl: 'views/users/resources/tutorials.html',
                controller: 'Tutorials_Ctrl',
                data: {
                    title: 'Tutoriels - uPont',
                    top: true
                },
                resolve: {
                    tutos: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'tutos').query().$promise;
                    }]
                },
            })
        ;
    }]);
