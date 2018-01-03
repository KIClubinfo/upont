import alertify from 'alertifyjs';
import angular from 'angular';

import { API_PREFIX } from 'upont/js/config/constants';

/* @ngInject */
class Resources_Courses_Simple_Ctrl {
    constructor($scope, course, exercices, $http, $resource) {
        $scope.course = course;
        $scope.exercices = exercices;
        $scope.predicate = 'exercice.date';
        $scope.reverse = false;

        if ($scope.course.groups !== undefined) {
            $scope.course.groups.sort();
            $scope.groups = $scope.course.groups.join();
        } else {
            $scope.course.groups = [];
            $scope.groups = '';
        }

        $scope.submitCourse = function(course, groups) {
            groups = groups.split(',');
            var params = {
                semester: parseInt(course.semester),
                department: course.department,
                groups: groups,
                ects: parseFloat(course.ects),
                active: course.active
            };

            $http.patch(API_PREFIX + 'courses/' + course.slug, params).then(function() {
                $scope.course.groups = groups;
                $scope.course.groups.sort();
                alertify.success('Modifications prises en compte !');
            });
        };

        $scope.fd = null;
        $scope.name = '';

        $scope.uploadFile = function(files) {
            $scope.fd = new FormData();
            $scope.fd.append('file', files[0]);
        };

        $scope.submitExercice = function(name) {
            if (name === undefined || name === '' || $scope.fd === null) {
                alertify.error('Un des champs n\'a pas été rempli !');
            }

            $scope.fd.append('name', name);

            $http.post(API_PREFIX + 'courses/' + course.slug + '/exercices', $scope.fd, {
                withCredentials: true,
                headers: {'Content-Type': undefined },
                transformRequest: angular.identity
            }).then(function() {
                $scope.name = '';
                $scope.files = [];
                alertify.success('Annale uploadée !');

                // On recharge les annales
                $resource(API_PREFIX + 'courses/' + $scope.course.slug + '/exercices').query(function(data){
                    $scope.exercices = data;
                });
            });
        };

        $scope.removeExercice = function(course, exercice) {
            $resource(API_PREFIX + 'courses/' + course.slug + '/exercices/' + exercice.slug).delete(function() {
                $resource(API_PREFIX + 'courses/' + course.slug + '/exercices').query(function(data){
                    $scope.exercices = data;
                });
                alertify.success('Annale supprimée.');
            });
        };

        $scope.isLoading = false;
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
    }
}

export default Resources_Courses_Simple_Ctrl;
