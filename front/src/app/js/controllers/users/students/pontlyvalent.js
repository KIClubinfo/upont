import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Students_Pontlyvalent_Ctrl {
    constructor($scope, $rootScope, $resource, $http, comments) {
        $scope.searchResultsPost = [];
        $scope.searchPost = '';
        $scope.searchName = '';
        $scope.comments = comments;

        $scope.searchUserPost = function(string) {
            if (string === '') {
                $scope.searchResultsPost = [];
            } else {
                $http.post(API_PREFIX + 'search', {search: 'User/' + string}).then(function(response){
                    $scope.searchResultsPost = response.data.users;
                });
            }
        };

        $scope.reload = function() {
            $resource(API_PREFIX + 'users/pontlyvalent').query(function(data){
                $scope.comments = data;
            });
        };

        $scope.addComment = function(slug, name) {
            alertify.prompt('Entrée pour ' + name + ' :', '', function(e, text) {
                if (e) {
                    if (!text) {
                        alertify.error('Il faut entrer un texte !');
                        return;
                    }

                    $http.post(API_PREFIX + 'users/' + slug + '/pontlyvalent', {text: text}).then(function() {
                        alertify.success('Entrée enregistrée');
                        $scope.reload();
                    }, function (response) {
                        alertify.error(response.data.error.exception[0].message);
                    });
                }
            });
        };

        $scope.deleteComment = function(slug) {
            $http.delete(API_PREFIX + 'users/' + slug + '/pontlyvalent').then(function() {
                alertify.success('Entrée supprimée');
                $scope.reload();
            }, function () {
                alertify.error();
            });
        };

    }
}

export default Students_Pontlyvalent_Ctrl;
