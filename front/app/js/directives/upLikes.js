angular.module('upont').directive('upLikes', function() {
    return {
        scope: {
            objet: '=',
            url: '='
        },
        controller: ["$scope", "$resource", function($scope, $resource) {
            if($scope.objet.comments > 0)
                $resource(apiPrefix + $scope.url + '/comments').query(function(data){
                    $scope.comments = data;
                });
            else
                $scope.comments = [];
            $scope.shownComments = -3;

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

            $scope.openComments = function(){
                $scope.shownComments = $scope.objet.comments;
            };

            $scope.submitComment = function(){
                // if(text.length > 0){
                    $resource(apiPrefix + $scope.url + '/comments').save({ text: $scope.commentText }, function(){
                        $scope.comments.push({ text: $scope.commentText, author: $scope.$root.me });
                        $scope.commentText = '';
                        if($scope.shownComments < 0)
                            $scope.shownComments--;
                        else
                            $scope.shownComments++;
                        $scope.objet.comments++;
                    });
                // }
            };
        }],
        templateUrl : 'views/misc/likesEtComments.html'
    };
});
