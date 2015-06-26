angular.module('upont')
    .controller('Courses_List_Ctrl', ['$scope', 'dfl', 'shs', 'a1', 'gcc', 'gi', 'gmm', 'imi', 'segf', 'vet', function($scope, dfl, shs, a1, gcc, gi, gmm, imi, segf, vet) {
        $scope.dfl = dfl;
        $scope.shs = shs;
        $scope.a1 = a1;
        $scope.gcc = gcc;
        $scope.gi = gi;
        $scope.gmm = gmm;
        $scope.imi = imi;
        $scope.segf = segf;
        $scope.vet = vet;
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
                    dfl: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses?department=DFL').query().$promise;
                    }],
                    shs: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses?department=SHS').query().$promise;
                    }],
                    a1: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses?department=1A').query().$promise;
                    }],
                    gcc: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses?department=GCC').query().$promise;
                    }],
                    gi: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses?department=GI').query().$promise;
                    }],
                    gmm: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses?department=GMM').query().$promise;
                    }],
                    imi: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses?department=IMI').query().$promise;
                    }],
                    segf: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses?department=SEGF').query().$promise;
                    }],
                    vet: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'courses?department=VET').query().$promise;
                    }],
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
