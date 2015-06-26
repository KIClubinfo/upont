angular.module('upont').directive('upCourses', function() {
    return {
        transclude: true,
        scope: {
            courses: '=',
        },
        controller: ['$scope', '$resource', '$http', '$rootScope', function($scope, $resource, $http, $rootScope) {
            $scope.isLoading = false;
            $scope.commentText = '';
            $scope.objet = null;

            $scope.upvote = function(objet) {
                $scope.objet = objet;
                if ($rootScope.isAdmissible)
                    return;

                if ($scope.isLoading) {
                    return;
                }
                $scope.isLoading = true;

                if (!$scope.objet.like) {
                    $resource(apiPrefix + 'courses/' + $scope.objet.slug + '/like').save(function() {
                        $scope.objet.likes++;
                        $scope.objet.like = true;
                        $scope.isLoading = false;

                        if ($scope.objet.dislike) {
                            $scope.objet.dislike = false;
                            $scope.objet.dislikes--;
                        }
                    });
                } else {
                    $resource(apiPrefix + 'courses/' + $scope.objet.slug + '/like').remove(function() {
                        $scope.objet.likes--;
                        $scope.objet.like = false;
                        $scope.isLoading = false;
                    });
                }
            };

            $scope.downvote = function(objet) {
                $scope.objet = objet;
                if ($rootScope.isAdmissible)
                    return;

                if ($scope.isLoading) {
                    return;
                }
                $scope.isLoading = true;

                if (!$scope.objet.dislike) {
                    $resource(apiPrefix + 'courses/' + $scope.objet.slug + '/dislike').save(function() {
                        $scope.objet.dislikes++;
                        $scope.objet.dislike = true;
                        $scope.isLoading = false;

                        if ($scope.objet.like) {
                            $scope.objet.like = false;
                            $scope.objet.likes--;
                        }
                    });
                } else {
                    $resource(apiPrefix + 'courses/' + $scope.objet.slug + '/dislike').remove(function() {
                        $scope.objet.dislikes--;
                        $scope.objet.dislike = false;
                        $scope.isLoading = false;
                    });
                }
            };
        }],
        templateUrl : 'views/users/resources/course-template.html'
    };
});
