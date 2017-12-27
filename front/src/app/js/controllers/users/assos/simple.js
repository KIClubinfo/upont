import { API_PREFIX } from 'upont/js/config/constants';

angular.module('upont')
    .controller('Assos_Simple_Ctrl', ['$rootScope', '$scope', 'club', 'members', function($rootScope, $scope, club, members) {
        $scope.club = club;
        $scope.members = members;
        $scope.promo = $rootScope.config.promos.assos;
        $rootScope.displayTabs = false;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.simple', {
                url: '/:slug',
                abstract: true,
                controller: 'Assos_Simple_Ctrl',
                templateUrl: 'controllers/users/assos/simple.html',
                resolve: {
                    club: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(API_PREFIX + 'clubs/:slug').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    members: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(API_PREFIX + 'clubs/:slug/users').query({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                }
            });
    }]);
