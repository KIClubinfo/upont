angular.module('upont')
    .controller('Courses_List_Ctrl', ['$scope', 'courses', 'Paginate', function($scope, courses, Paginate) {
        $scope.courses = courses;
        $scope.search = {
            department: 'all',
            semester: 'all',
            ects: 'all',
        };

        $scope.next = function() {
            Paginate.next($scope.courses).then(function(data){
                $scope.courses = data;
            });
        };

        $scope.reload = function(criterias) {
            var url = 'courses?sort=name';

            if (criterias.department != 'all')
                url += '&department=' + criterias.department;
            if (criterias.semester != 'all')
                url += '&semester=' + criterias.semester;
            if (criterias.ects != 'all')
                url += '&ects=' + criterias.ects;

            Paginate.get(url, 20).then(function(data){
                $scope.courses = data;
            });
        };

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
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources', {
                url: 'ressources',
                templateUrl: 'views/users/resources/index.html',
                abstract: true,
                data: {
                    title: 'Ressources - uPont',
                    top: true
                }
            })
            .state('root.users.resources.courses', {
                url: '/cours',
                abstract: true,
                template: '<div ui-view></div>',
                data: {
                    title: 'Cours - uPont',
                    top: true
                },
            })
            .state('root.users.resources.courses.list', {
                url: '',
                templateUrl: 'views/users/resources/courses.html',
                data: {
                    title: 'Liste des cours - uPont',
                    top: true
                },
                controller: 'Courses_List_Ctrl',
                resolve: {
                    courses: ['Paginate', function(Paginate) {
                        return Paginate.get('courses?sort=name', 20);
                    }]
                },
            });
    }]);
