angular.module('upont')
    .controller('Assos_Public_Ctrl', ['$scope', 'clubs', function($scope, clubs) {
        $scope.clubs = [];
        $scope.assos = [];

        angular.forEach(clubs, function(value, key) {
            if (value.hasOwnProperty('assos') && value.assos === true)
                $scope.assos.push(value);
            else
                $scope.clubs.push(value);
        });
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.public.assos', {
                url: '/assos',
                templateUrl: 'controllers/public/assos.html',
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
