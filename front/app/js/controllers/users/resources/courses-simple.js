angular.module('upont')
    .controller('Courses_Simple_Ctrl', ['$scope', 'course', 'exercices', '$http', '$resource', function($scope, course, exercices, $http, $resource) {
        $scope.course = course;
        $scope.course.groups.sort();
        $scope.exercices = exercices;
        $scope.predicate = 'exercice.date';
        $scope.reverse = false;

        $scope.groups = $scope.course.groups.join();
        $scope.submitCourse = function(course, groups) {

            groups = groups.split(',');
            var params = {
                semester: parseInt(course.semester),
                department: course.department,
                groups: groups
            };

            $http.patch(apiPrefix + 'courses/' + course.slug, params).success(function() {
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

            $http.post(apiPrefix + 'courses/' + course.slug + '/exercices', $scope.fd, {
                withCredentials: true,
                headers: {'Content-Type': undefined },
                transformRequest: angular.identity
            }).success(function() {
                $scope.name = '';
                var input = $('#fileUpload');
                input.replaceWith(input.val('').clone(true));
                alertify.success('Annale uploadée !');

                // On recharge les annales
                $resource(apiPrefix + 'courses/' + $scope.course.slug + '/exercices').query(function(data){
                    $scope.exercices = data;
                });
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources.courses.simple', {
                url: '/:slug',
                templateUrl: 'views/users/resources/course.html',
                data: {
                    title: 'Cours - uPont',
                    top: true
                },
                controller: 'Courses_Simple_Ctrl',
                resolve: {
                    course: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'courses/:slug').get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    exercices: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'courses/:slug/exercices').query({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                }
            });
    }]);
