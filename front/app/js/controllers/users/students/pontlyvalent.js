angular.module('upont')
    .controller('Students_Pontlyvalent_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'Paginate', 'comments', function($scope, $rootScope, $resource, $http, Paginate, comments) {
        $scope.searchResultsPost = [];
        $scope.searchPost = '';
        $scope.searchName = '';
        var promo = "017";
        $scope.comments = comments;

        $scope.searchUserPost = function(string) {
            if (string === '') {
                $scope.searchResultsPost = [];
            } else {
                $http.post(apiPrefix + 'search', {search: 'User/' + string}).success(function(data){
                    $scope.searchResultsPost = data.users;
                });
            }
        };

        $scope.reload = function() {
            Paginate.first($scope.comments).then(function(data){
                $scope.comments = data;
            });
        };

        $scope.addComment = function(slug, name) {
            //On get l'user pour voir s'il est dans la bonne promo
            $http.get(apiPrefix + 'users/' + slug).success(function(data) {
                if (data.promo != promo) {
                    alertify.error('Tu ne peux poster que pour des 017 !');
                    return;
                }

                //On regarde s'il y a déjà une entrée pour cet user de la part de l'utilisateur connecté
                $http.get(apiPrefix + 'users/' + slug + '/pontlyvalent').success(function(data) {
                    if (data.length === 0) {
                        //Si oui, on post
                        alertify.prompt('Entrée pour ' + name + ' :', function(e, text) {
                            if (e) {
                                if (!text) {
                                    alertify.error('Il faut entrer un texte !');
                                    return;
                                }

                                $http.post(apiPrefix + 'users/' + slug + '/pontlyvalent', {text: text}).success(function() {
                                    alertify.success('Entrée enregistrée');
                                    $scope.reload();
                                });
                            }
                        });
                    } else {
                        //Sinon on patch
                        alertify.prompt('Modifier l\'entrée pour ' + name + ' :', function(e, text) {
                            if (e) {
                                if (!text) {
                                    alertify.error('Il faut entrer un texte !');
                                    return;
                                }
                                
                                $http.patch(apiPrefix + 'users/' + slug + '/pontlyvalent', {text: text}).success(function() {
                                    alertify.success('Entrée enregistrée');
                                    $scope.reload();
                                });
                            }
                        }, data[0].text);
                    }
                });
            });
        };

        $scope.deleteComment = function(slug) {
            $http.delete(apiPrefix + 'users/' + slug + '/pontlyvalent').success(function() {
                alertify.success('Entrée supprimée');
                $scope.reload();
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
                    comments: ['Paginate', function(Paginate) {
                        return Paginate.get('users/pontlyvalent');
                    }]
                },
                data: {
                    title: 'Pontlyvalent - uPont',
                    top: true
                },
            });
    }]);
