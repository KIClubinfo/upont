angular.module('upont')
    .controller('Students_Pontlyvalent_Ctrl', ['$scope', '$rootScope', '$resource', '$http', '$q', function($scope, $rootScope, $resource, $http, $q) {
        $scope.searchResultsPost = [];
        $scope.searchPost = '';
        $scope.searchResultsPatch = [];
        $scope.searchPatch = '';

        $scope.searchUserPost = function(string) {
            if (string === '') {
                $scope.searchResultsPost = [];
            } else {
                $http.post(apiPrefix + 'search', {search: 'User/' + string}).success(function(data){
                    $scope.searchResultsPost = data.users;
                });
            }
        };

        $scope.searchUserPatch = function(string) {
            if (string === '') {
                $scope.searchResultsPatch = [];
            } else {
                $http.post(apiPrefix + 'search', {search: 'User/' + string}).success(function(data){
                    $scope.searchResultsPatch = data.users;
                });
            }
        };

        $scope.loadComments = function() {
            if ($rootScope.hasClub('bde')) {
                  Paginate.first($scope.allComments).then(function(data){
                    $scope.allComments = data;
                });
            }
        };

        $scope.addComment = function(slug, name) {
            alertify.prompt('Entrée pour ' + name + ' :', function(e, text) {
                if (e) {
                    $http.post(apiPrefix + 'users/' + slug + '/pontlyvalent', {text: text}).success(function() {
                        alertify.success('Entrée enregistrée');
                    }).error(function() {
                        alertify.error('Tu as déjà posté pour cette personne !');
                    });
                }
            });
        };

        $scope.modifyComment = function(slug, name) {
            $http.get(apiPrefix + 'users/' + slug + '/pontlyvalent').success(function(data) {
                if (data.length === 0){
                    alertify.error('Tu n\'as jamais posté pour cette personne !');
                    return;
                }

                alertify.prompt('Modifier l\'entrée pour ' + name + ' :', function(e, text) {
                    if (e) {
                        $http.patch(apiPrefix + 'users/' + slug + '/pontlyvalent', {text: text}).success(function() {
                            alertify.success('Entrée enregistrée');
                        });
                    }
                }, data[0].text);
            });
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.students.pontlyvalent', {
                url: '/pontlyvalent',
                templateUrl: 'controllers/users/students/pontlyvalent.html',
                controller: 'Students_Pontlyvalent_Ctrl',
                data: {
                    title: 'Pontlyvalent - uPont',
                    top: true
                },
            });
    }]);
