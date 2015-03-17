angular.module('upont')
    .controller('Shotgun_Ctrl', ['$scope', '$resource', '$http', 'event', 'shotgun', function($scope, $resource, $http, event, shotgun) {
        $scope.event = event;
        $scope.shotgun = shotgun;
        $scope.shotgunned = false;
        $scope.motivation = '';

        $scope.shotgunEvent = function(){
            if ($scope.motivation === '') {
                $scope.motivation = 'Shotgun !';
            }

            $http.post(apiPrefix + 'events/' + $scope.event.slug + '/shotgun', {motivation: $scope.motivation}).success(function(){
                $resource(apiPrefix + 'events/' + $scope.event.slug + '/shotgun').get(function(data){
                    $scope.shotgun = data;
                    $scope.shotgunned = true;
                });
            });
        };

        $scope.deleteShotgun = function(){
            // TODO modal de confirmation
            $http.delete(url + '/events/' + $scope.event.slug + '/shotgun').success(function(data){
                $scope.shotgun = data;
                $scope.shotgunned = false;
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.shotgun', {
                url: 'shotgun/:slug',
                templateUrl: 'views/misc/shotgun.html',
                controller: 'Shotgun_Ctrl',
                resolve: {
                    event: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'events/:slug').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    shotgun: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'events/:slug/shotgun').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                }
            });
    }]);
