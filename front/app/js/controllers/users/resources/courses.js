angular.module('upont')
    .controller('Courses_List_Ctrl', ['$scope', 'courses', function($scope, courses) {
        $scope.courses = courses;
    }])
    .controller('Courses_Simple_Ctrl', ['$scope', 'course', 'exercices', function($scope, course, exercices) {
        $scope.course = course;
        $scope.exercices = exercices;
        $scope.predicate = 'exercice.date';
        $scope.reverse = false;
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
                    courses: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses').query().$promise;
                    }]
                },
            })
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
