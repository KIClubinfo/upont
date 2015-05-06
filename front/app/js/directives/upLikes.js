angular.module('upont').directive('upLikes', function() {
    return {
        scope: {
            objet: '=',
            url: '='
        },
        controller: ['$scope', '$resource', '$http', function($scope, $resource, $http) {
            $scope.isLoading = false;

            if($scope.objet.comments > 0){
                $resource(apiPrefix + $scope.url + '/comments').query(function(data){
                    $scope.comments = data;
                });
                $scope.shownComments = -3;
            }
            else{
                $scope.comments = [];
                $scope.shownComments = $scope.objet.comments;
            }

            $scope.upvote = function() {
                if ($scope.isLoading) {
                    return;
                }
                $scope.isLoading = true;

                if (!$scope.objet.like) {
                    $resource(apiPrefix + $scope.url + '/like').save(function() {
                        $scope.objet.likes++;
                        $scope.objet.like = true;
                        $scope.isLoading = false;

                        if ($scope.objet.dislike) {
                            $scope.objet.dislike = false;
                            $scope.objet.dislikes--;
                        }
                    });
                } else {
                    $resource(apiPrefix + $scope.url + '/like').remove(function() {
                        $scope.objet.likes--;
                        $scope.objet.like = false;
                        $scope.isLoading = false;
                    });
                }
            };

            $scope.downvote = function() {
                if ($scope.isLoading) {
                    return;
                }
                $scope.isLoading = true;

                if (!$scope.objet.dislike) {
                    $resource(apiPrefix + $scope.url + '/dislike').save(function() {
                        $scope.objet.dislikes++;
                        $scope.objet.dislike = true;
                        $scope.isLoading = false;

                        if ($scope.objet.like) {
                            $scope.objet.like = false;
                            $scope.objet.likes--;
                        }
                    });
                } else {
                    $resource(apiPrefix + $scope.url + '/dislike').remove(function() {
                        $scope.objet.dislikes--;
                        $scope.objet.dislike = false;
                        $scope.isLoading = false;
                    });
                }
            };

            $scope.likeComment = function(comment) {
                if ($scope.isLoading) {
                    return;
                }
                $scope.isLoading = true;
                var index = $scope.comments.indexOf(comment);

                // Si la personne like déjà on ne fait qu'annuler le like
                if ($scope.comments[index].like) {
                    $resource(apiPrefix + 'comments/' + $scope.comments[index].id + '/like').remove(function() {
                        $scope.comments[index].like = false;
                        $scope.comments[index].likes--;
                        $scope.isLoading = false;
                    });
                } else {
                    $resource(apiPrefix + 'comments/' + $scope.comments[index].id + '/like').save(function() {
                        $scope.comments[index].like = true;
                        $scope.comments[index].likes++;
                        $scope.isLoading = false;

                        // Si la personne unlikait avant
                        if ($scope.comments[index].dislike) {
                            $scope.comments[index].dislike = false;
                            $scope.comments[index].dislikes--;
                        }
                    });
                }
            };

            $scope.dislikeComment = function(comment) {
                if ($scope.isLoading) {
                    return;
                }
                $scope.isLoading = true;
                var index = $scope.comments.indexOf(comment);

                // Si la personne dislike déjà on ne fait qu'annuler le dislike
                if ($scope.comments[index].dislike) {
                    $resource(apiPrefix + 'comments/' + $scope.comments[index].id + '/dislike').remove(function() {
                        $scope.comments[index].dislike = false;
                        $scope.comments[index].dislikes--;
                        $scope.isLoading = false;
                    });
                } else {
                    $resource(apiPrefix + 'comments/' + $scope.comments[index].id + '/dislike').save(function() {
                        $scope.comments[index].dislike = true;
                        $scope.comments[index].dislikes++;
                        $scope.isLoading = false;

                        // Si la personne unlikait avant
                        if ($scope.comments[index].like) {
                            $scope.comments[index].like = false;
                            $scope.comments[index].likes--;
                        }
                    });
                }
            };

            $scope.openComments = function(){
                $scope.shownComments = $scope.objet.comments;
            };

            $scope.submitComment = function(text){
                $scope.commentText = "";
                if(text.length > 0){
                    $resource(apiPrefix + $scope.url + '/comments').save({ text: nl2br(text) }, function(data){
                        $scope.comments.push(data);
                        if($scope.shownComments < 0)
                            $scope.shownComments--;
                        else
                            $scope.shownComments++;
                        $scope.objet.comments++;
                    });
                }
            };

            $scope.modifyComment = function(comment) {
                var index = $scope.comments.indexOf(comment);

                // On demande confirmation
                alertify.prompt('Tu peux modifier ton message :', function(e, str){
                    if (e) {
                        $http.patch(apiPrefix + 'comments/' + $scope.comments[index].id, {text: str}).success(function() {
                            $scope.comments[index].text = str;
                        });
                    }
                }, $scope.comments[index].text);
            };

            $scope.deleteComment = function(comment) {
                var index = $scope.comments.indexOf(comment);

                // On demande confirmation
                alertify.confirm('Est-ce vraiment ce que tu veux ?', function(e){
                    if (e) {
                        $resource(apiPrefix + 'comments/' + $scope.comments[index].id).delete(function() {
                            $scope.comments.splice(index, 1);
                            if($scope.shownComments < 0)
                                $scope.shownComments++;
                            else
                                $scope.shownComments--;
                            $scope.objet.comments--;
                        });
                    }
                });
            };
        }],
        templateUrl : 'views/elements_publics/likesEtComments.html'
    };
});
