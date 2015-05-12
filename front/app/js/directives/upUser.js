angular.module('upont').directive('upUser', function() {
    return {
        transclude: true,
        scope: {
            user: '='
        },
        link: {
            post: function(scope, element, args){
                element.addClass('up-user-hover-container');
            }
        },
        controller: ['$scope', '$rootScope', '$resource', '$timeout', function($scope, $rootScope, $resource, $timeout) {
            $scope.hover = false;
            $scope.clubs = [];
            $scope.timer = null;

            $scope.hoverIn = function(){
                if (!$rootScope.hovering && !$scope.hover) {
                    $scope.timer = $timeout(function () {
                        $rootScope.hovering = true;
                        $resource(apiPrefix + 'users/' + $scope.user.username + '/clubs').query(function(data) {
                            $scope.clubs = data;

                            // On ferme tous les autres
                            $rootScope.$broadcast('closeHover');
                            $rootScope.hovering = false;
                            $scope.hover = true;
                        });
                    }, 500);
                }
            };

            $scope.hoverOut = function(){
                $timeout.cancel($scope.timer);
                $scope.hover = false;
                $rootScope.$broadcast('closeHover');
            };

            $scope.$on('closeHover', function() {
                $scope.hover = false;
            });
        }],
        templateUrl: 'views/public/user.html'
    };
});
