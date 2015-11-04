angular.module('upont')
    .controller('Students_Pontlyvalent_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'Paginate', function($scope, $rootScope, $resource, $http, Paginate) {
        $scope.searchResultsPost = [];
        $scope.searchPost = '';
        $scope.searchName = '';
        var promo = "017";

        if ($rootScope.hasClub('bde')) {
            Paginate.get('users/pontlyvalent').then(function(data) {
                $scope.comments = data;
            });
        }

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

        $scope.reload = function() {
            if ($rootScope.hasClub('bde')) {
                Paginate.first($scope.comments).then(function(data){
                    $scope.comments = data;
                });
            }
        };

        $scope.next = function() {
            if ($rootScope.hasClub('bde')) {
                Paginate.next($scope.comments).then(function(data){
                    $scope.comments = data;
                });
            }
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
                }
            });
    }]);
