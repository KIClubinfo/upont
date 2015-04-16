angular.module('upont')
    .controller('Profile_Simple_Ctrl', ['$scope', 'user', function($scope, user) {
        $scope.user = user;
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.promo.simple', {
                url: '/:slug',
                templateUrl: 'views/promo/simple.html',
                controller: 'Profile_Simple_Ctrl',
                resolve: {
                    user: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'users/:slug').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                },
                data: {
                    title: 'Profil - uPont',
                    top: true
                }
            });
    }]);
