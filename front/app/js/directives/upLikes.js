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
                        if ($scope.objet.dislike) {
                            $scope.objet.dislike = false;
                            $scope.objet.dislikes--;
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
                if (!$scope.objet.dislike) {
                    $resource(apiPrefix + $scope.url + '/dislike').save(function() {
                        $scope.objet.dislikes++;
                        $scope.objet.dislike = true;
                        if ($scope.objet.like) {
                            $scope.objet.like = false;
                            $scope.objet.likes--;
                        }
                    });
                } else {
                    $resource(apiPrefix + $scope.url + '/dislike').remove(function() {
                        $scope.objet.dislikes--;
                        $scope.objet.dislike = false;
                    });
                }
            };
        }],
        templateUrl : 'views/misc/likesEtComments.html'
    };
}]);
