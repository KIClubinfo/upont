angular.module('upont').directive('upLikes', ['$window', function($window) {
    return {
        scope: {
            objet: '=',
            url: '='
        },
        controller: ["$scope", "$resource", function($scope, $resource) {
            $scope.upvote = function() {
                if (!$scope.objet.like) {
                    $resource(apiPrefix + $scope.url + '/like').save(function() {
                        $scope.objet.likes++;
                        $scope.objet.like = true;
                        if ($scope.objet.unlike) {
                            $scope.objet.unlike = false;
                            $scope.objet.unlikes--;
                        }
                    });
                } else {
                    $resource(apiPrefix + $scope.url + '/like').remove(function() {
                        $scope.objet.likes--;
                        $scope.objet.like = false;
                    });
                }
            };

            $scope.downvote = function() {
                if (!$scope.objet.unlike) {
                    $resource(apiPrefix + $scope.url + '/dislike').save(function() {
                        $scope.objet.unlikes++;
                        $scope.objet.unlike = true;
                        if ($scope.objet.like) {
                            $scope.objet.like = false;
                            $scope.objet.likes--;
                        }
                    });
                } else {
                    $resource(apiPrefix + $scope.url + '/dislike').remove(function() {
                        $scope.objet.unlikes--;
                        $scope.objet.unlike = false;
                    });
                }
            };
        }],
        templateUrl : 'views/misc/likesEtComments.html'
    };
}]);
