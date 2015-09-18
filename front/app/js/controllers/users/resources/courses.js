angular.module('upont')
    .controller('Courses_List_Ctrl', ['$scope', '$rootScope', 'courses', 'followed', 'Paginate', '$http', '$resource', 'Achievements', function($scope, $rootScope, courses, followed, Paginate, $http, $resource, Achievements) {
        $scope.courses = courses;
        $scope.modo = false;
        $scope.search = {
            department: 'all',
            semester: 'all',
            ects: 'all',
        };

        $scope.load = function(followedCourses) {
            if (followedCourses.length === 0) {
                $scope.followed = null;
                $scope.followedIds = null;
                return;
            }
            $scope.followed = {};
            $scope.followedIds = {};
            for (var key in followedCourses){
                if (followedCourses[key].course !== undefined) {
                    $scope.followedIds[followedCourses[key].course.slug] = (followedCourses[key].group !== undefined) ? followedCourses[key].group : true;
                    $scope.followed[followedCourses[key].course.slug] = followedCourses[key].course;
                }
            }
        };
        $scope.load(followed);

        $scope.toggleModo = function() {
            $scope.modo = !$scope.modo;
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

            Paginate.get(url, 50).then(function(data){
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

        $scope.toggleCourse = function(course) {
            if ($scope.isLoading) {
                return;
            }
            $scope.isLoading = true;

            course.active = !course.active;
            $http.patch(apiPrefix + 'courses/' + course.slug, {active: course.active}).success(function(){
                $scope.isLoading = false;
            });
        };

        $scope.attend = function(course) {
            if ($scope.isLoading) {
                return;
            }
            $scope.isLoading = true;

            // S'il y a plusieurs groupes pour ce cours on demande lequel sera suivi
            if (!empty(course.groups) && course.groups[0] != '0') {
                alertify.prompt('Dans quel groupe est-tu ? Groupes valides : ' + course.groups.join(','), function(e, str){
                    if (e) {
                        $http.post(apiPrefix + 'courses/' + course.slug + '/attend', {group: str}).success(function() {
                            $resource(apiPrefix + 'own/courses').query(function(data) {
                                $scope.load(data);
                                $scope.isLoading = false;
                                Achievements.check();
                            });
                        }).error(function(){
                            alertify.error('Groupe invalide !');
                            $scope.isLoading = false;
                        });
                    } else {
                        $scope.isLoading = false;
                    }
                });
            } else {
                $resource(apiPrefix + 'courses/' + course.slug + '/attend').save(function() {
                    $resource(apiPrefix + 'own/courses').query(function(data) {
                        $scope.load(data);
                        $scope.isLoading = false;
                        Achievements.check();
                    });
                });
            }
        };

        $scope.leave = function(course) {
            if ($scope.isLoading) {
                return;
            }
            $scope.isLoading = true;
            $resource(apiPrefix + 'courses/' + course.slug + '/attend').delete(function() {
                $resource(apiPrefix + 'own/courses').query(function(data) {
                    $scope.load(data);
                    $scope.isLoading = false;
                });
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources', {
                url: 'ressources',
                templateUrl: 'controllers/users/resources/index.html',
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
                templateUrl: 'controllers/users/resources/courses.html',
                data: {
                    title: 'Liste des cours - uPont',
                    top: true
                },
                controller: 'Courses_List_Ctrl',
                resolve: {
                    courses: ['Paginate', function(Paginate) {
                        return Paginate.get('courses?sort=name', 50);
                    }],
                    followed: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/courses').query().$promise;
                    }]
                },
            });
    }]);
