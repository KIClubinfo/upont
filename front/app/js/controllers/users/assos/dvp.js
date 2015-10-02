angular.module('upont')
    .controller('DVP_Ctrl', ['$scope', '$rootScope', '$http', 'members', 'baskets', 'Paginate', function($scope, $rootScope, $http, members, baskets, Paginate) {
        $scope.baskets = baskets;
        //$scope.date = new date();

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.dvp', {
                url: '/paniers',
                templateUrl: 'controllers/users/assos/dvp.html',
                controller: 'DVP_Ctrl',
                data: {
                    title: 'DVP - uPont',
                    top: true
                },
                resolve: {
                    members: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'clubs/dvp/users').query().$promise;
                    }],
                    baskets: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'baskets').query().$promise;
                    }]
                }
            });
    }]);
