angular.module('upont')
    .controller('Assos_Public_Ctrl', ['$scope', 'clubs', function($scope, clubs) {
        $scope.clubs = clubs;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.public.assos', {
                url: '/assos',
                templateUrl: 'views/public/assos.html',
                controller: 'Assos_Public_Ctrl',
                data: {
                    title: 'Clubs & Assos - uPont',
                    top: true
                },
                resolve: {
                    clubs: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'clubs?sort=name').query().$promise;
                    }]
                }
            });
    }]);
