import { API_PREFIX } from 'upont/js/config/constants';

class Resources_Courses_List_Ctrl {
    constructor($scope, $rootScope, courses, followed, Paginate, $http, $resource, Achievements) {
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
            Paginate.next($scope.courses).then(function(response){
                $scope.courses = response;
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

            Paginate.get(url, 50).then(function(response){
                $scope.courses = response;
                $scope.next();
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
                $resource(API_PREFIX + 'courses/' + $scope.objet.slug + '/like').save(function() {
                    $scope.objet.likes++;
                    $scope.objet.like = true;
                    $scope.isLoading = false;

                    if ($scope.objet.dislike) {
                        $scope.objet.dislike = false;
                        $scope.objet.dislikes--;
                    }
                });
            } else {
                $resource(API_PREFIX + 'courses/' + $scope.objet.slug + '/like').remove(function() {
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
                $resource(API_PREFIX + 'courses/' + $scope.objet.slug + '/dislike').save(function() {
                    $scope.objet.dislikes++;
                    $scope.objet.dislike = true;
                    $scope.isLoading = false;

                    if ($scope.objet.like) {
                        $scope.objet.like = false;
                        $scope.objet.likes--;
                    }
                });
            } else {
                $resource(API_PREFIX + 'courses/' + $scope.objet.slug + '/dislike').remove(function() {
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
            $http.patch(API_PREFIX + 'courses/' + course.slug, {active: course.active}).then(function(){
                $scope.isLoading = false;
            });
        };

        $scope.attend = function(course) {
            if ($scope.isLoading) {
                return;
            }
            $scope.isLoading = true;

            // S'il y a plusieurs groupes pour ce cours on demande lequel sera suivi
            if (course.groups.length != 1) {
                alertify.prompt('Dans quel groupe es-tu ? Groupes valides : ' + course.groups.join(','), function(e, str){
                    if (e) {
                        $http.post(API_PREFIX + 'courses/' + course.slug + '/attend', {group: str}).then(function() {
                            $resource(API_PREFIX + 'own/courses').query(function(data) {
                                $scope.load(data);
                                $scope.isLoading = false;
                                Achievements.check();
                            });
                        }, function(){
                            alertify.error('Groupe invalide !');
                            $scope.isLoading = false;
                        });
                    } else {
                        $scope.isLoading = false;
                    }
                });
            } else {
                $resource(API_PREFIX + 'courses/' + course.slug + '/attend').save(function() {
                    $resource(API_PREFIX + 'own/courses').query(function(data) {
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
            $resource(API_PREFIX + 'courses/' + course.slug + '/attend').delete(function() {
                $resource(API_PREFIX + 'own/courses').query(function(data) {
                    $scope.load(data);
                    $scope.isLoading = false;
                });
            });
        };
    }
}

export default Resources_Courses_List_Ctrl;
