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
        controller: ['$scope', '$rootScope', '$resource', function($scope, $rootScope, $resource) {
            $scope.hover = false;
            $scope.clubs = [];

            $scope.hoverIn = function(){
                $resource(apiPrefix + 'users/' + $scope.user.username + '/clubs').query(function(data) {
                    $scope.clubs = data;

                    // On ferme tous les autres
                    $rootScope.$broadcast('closeHover');
                    $scope.hover = true;
                });
            };

            $scope.hoverOut = function(){
                $rootScope.$broadcast('closeHover');
            };

            $scope.$on('closeHover', function() {
                $scope.hover = false;
            });
        }],
        templateUrl: 'views/public/user.html'
    };
});
