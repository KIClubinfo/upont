import alertify from 'alertifyjs';
import angular from 'angular';

import { API_PREFIX } from 'upont/js/config/constants';

import { nl2br } from 'upont/js/php';

import template_likes from './likes.html';

// TODO refactor
// TODO remove dependency on trustAsHtml filter
angular.module('upont').directive('upLikes', function() {
    return {
        scope: {
            show: '=?',
            objet: '=',
            url: '='
        },
        controller: ['$scope', '$resource', '$http', '$rootScope', function($scope, $resource, $http, $rootScope) {
            $scope.isLoading = false;
            $scope.commentText = '';
            if ($scope.show !== false) {
                $scope.show = true;
            }

            if($scope.objet.comments > 0){
                $resource(API_PREFIX + $scope.url + '/comments').query(function(data){
                    $scope.comments = data;
                });
                $scope.shownComments = -5;
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
                    $resource(API_PREFIX + $scope.url + '/like').save(function() {
                        $scope.objet.likes++;
                        $scope.objet.like = true;
                        $scope.isLoading = false;

                        if ($scope.objet.dislike) {
                            $scope.objet.dislike = false;
                            $scope.objet.dislikes--;
                        }
                    });
                } else {
                    $resource(API_PREFIX + $scope.url + '/like').remove(function() {
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
                    $resource(API_PREFIX + $scope.url + '/dislike').save(function() {
                        $scope.objet.dislikes++;
                        $scope.objet.dislike = true;
                        $scope.isLoading = false;

                        if ($scope.objet.like) {
                            $scope.objet.like = false;
                            $scope.objet.likes--;
                        }
                    });
                } else {
                    $resource(API_PREFIX + $scope.url + '/dislike').remove(function() {
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
                    $resource(API_PREFIX + 'comments/' + $scope.comments[index].id + '/like').remove(function() {
                        $scope.comments[index].like = false;
                        $scope.comments[index].likes--;
                        $scope.isLoading = false;
                    });
                } else {
                    $resource(API_PREFIX + 'comments/' + $scope.comments[index].id + '/like').save(function() {
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
                    $resource(API_PREFIX + 'comments/' + $scope.comments[index].id + '/dislike').remove(function() {
                        $scope.comments[index].dislike = false;
                        $scope.comments[index].dislikes--;
                        $scope.isLoading = false;
                    });
                } else {
                    $resource(API_PREFIX + 'comments/' + $scope.comments[index].id + '/dislike').save(function() {
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

            $scope.submitComment = function($event, text) {

                if ($event.keyCode != 13 || $event.shiftKey)
                    return false;

                $event.preventDefault();
                if(text.length > 0){
                    $resource(API_PREFIX + $scope.url + '/comments').save({ text: nl2br(text) }, function(data){
                        $scope.comments.push(data);
                        if($scope.shownComments < 0)
                            $scope.shownComments--;
                        else
                            $scope.shownComments++;
                        $scope.objet.comments++;
                    });
                }
                return true;
            };

            $scope.modifyComment = function(comment) {
                var index = $scope.comments.indexOf(comment);

                // On demande confirmation
                alertify.prompt('Tu peux modifier ton message :', function(e, str){
                    if (e) {
                        $http.patch(API_PREFIX + 'comments/' + $scope.comments[index].id, {text: str}).then(function() {
                            $scope.comments[index].text = str;
                        });
                    }
                }, $scope.comments[index].text);
            };

            $scope.deleteComment = function(comment) {
                var index = $scope.comments.indexOf(comment);

                // On demande confirmation
                alertify.confirm('Est-ce vraiment ce que tu veux ?', () => {
                    $resource(API_PREFIX + 'comments/' + $scope.comments[index].id).delete(function() {
                        $scope.comments.splice(index, 1);
                        if($scope.shownComments < 0)
                            $scope.shownComments++;
                        else
                            $scope.shownComments--;
                        $scope.objet.comments--;
                    });
                });
            };
        }],
        templateUrl : template_likes
    };
});
