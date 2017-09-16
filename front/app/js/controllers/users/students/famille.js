angular.module('upont')
    .controller('Assos_Simple_Ctrl', ['$rootScope', '$scope', 'club', 'members', function($rootScope, $scope, club, members) {
        $scope.club = club;
        $scope.members = members;
        $scope.promo = $rootScope.config.promos.assos;
        $rootScope.displayTabs = false;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.students.family', {
                url: '/:slug',
                abstract: true,
                controller: 'Family_Simple_Ctrl',
                templateUrl: 'controllers/users/students/family.html',
                resolve: {
                    family: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'family/:slug').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    members: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'family/:slug/users').query({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                }
            });
    }]);
