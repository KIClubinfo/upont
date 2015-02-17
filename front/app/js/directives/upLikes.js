angular.module('upont').directive('upLikes', ['$window', function($window) {
    return {
        scope: {
            objet: '=',
            url: '='
        },
        controller: ["$scope", "$resource", function($scope, $resource) {
            $scope.upvote = function(url) {
                if (!$scope.objet.like) {
                    $resource(apiPrefix + url + '/like').save(function() {
                        $scope.objet.likes++;
                        $scope.objet.like = true;
                        if ($scope.objet.unlike) {
                            $scope.objet.unlike = false;
                            $scope.objet.unlikes--;
                        }
                    });
                } else {
                    $resource(apiPrefix + url + '/like').remove(function() {
                        $scope.objet.likes--;
                        $scope.objet.like = false;
                    });
                }
            };

            $scope.downvote = function(url) {
                if (!$scope.objet.unlike) {
                    $resource(apiPrefix + url + '/unlike').save(function() {
                        $scope.objet.unlikes++;
                        $scope.objet.unlike = true;
                        if ($scope.objet.like) {
                            $scope.objet.like = false;
                            $scope.objet.likes--;
                        }
                    });
                } else {
                    $resource(apiPrefix + url + '/unlike').remove(function() {
                        $scope.objet.unlikes--;
                        $scope.objet.unlike = false;
                    });
                }
            };
        }],
        template: '<div class="up-likes col-xs-6 col-sm-2 col-sm-offset-2" ng-click="upvote(url);" ng-class="{active : objet.like}"><span class="glyphicon glyphicon-thumbs-up"></span> {{ objet.likes }}</div>' +
            '<div class="up-likes col-xs-6 col-sm-2" ng-click="downvote(url);" ng-class="{active : objet.unlike}"><span class="glyphicon glyphicon-thumbs-down"></span> {{ objet.unlikes }}</div>' +
            '<div class="col-xs-12 col-sm-4"><span class="glyphicon glyphicon-comment"></span></div>',
    };
}]);
