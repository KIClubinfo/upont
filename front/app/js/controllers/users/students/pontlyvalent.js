angular.module('upont')
    .controller('Students_Pontlyvalent_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'comments', function($scope, $rootScope, $resource, $http, comments) {
        $scope.searchResultsPost = [];
        $scope.searchPost = '';
        $scope.searchName = '';
        var promo = '018';
        $scope.comments = comments;

        $scope.searchUserPost = function(string) {
            if (string === '') {
                $scope.searchResultsPost = [];
            } else {
                $http.post(apiPrefix + 'search', {search: 'User/' + string}).then(function(response){
                    $scope.searchResultsPost = response.data.users;
                });
            }
        };

        $scope.reload = function() {
            $resource(apiPrefix + 'users/pontlyvalent').query(function(data){
                $scope.comments = data;
            });
        };

        $scope.addComment = function(slug, name) {
            alertify.prompt('Entrée pour ' + name + ' :', function(e, text) {
                if (e) {
                    if (!text) {
                        alertify.error('Il faut entrer un texte !');
                        return;
                    }

                    $http.post(apiPrefix + 'users/' + slug + '/pontlyvalent', {text: text}).then(function() {
                        alertify.success('Entrée enregistrée');
                        $scope.reload();
                    }, function (response) {
                        alertify.error(response.data.error.exception[0].message);
                    });
                }
            });
        };

        $scope.deleteComment = function(slug) {
            $http.delete(apiPrefix + 'users/' + slug + '/pontlyvalent').then(function() {
                alertify.success('Entrée supprimée');
                $scope.reload();
            }, function () {
                alertify.error();
            });
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.students.pontlyvalent', {
                url: '/pontlyvalent',
                templateUrl: 'controllers/users/students/pontlyvalent.html',
                controller: 'Students_Pontlyvalent_Ctrl',
                resolve: {
                    comments: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'users/pontlyvalent').query().$promise;
                    }]
                },
                data: {
                    title: 'Pontlyvalent - uPont',
                    top: true
                },
            });
    }]);
