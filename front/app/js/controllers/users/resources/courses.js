angular.module('upont')
    .controller('Courses_Ctrl', ['$scope', function($scope) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources', {
                url: 'ressources',
                templateUrl: 'views/users/resources/index.html',
                data: {
                    title: 'Ressources - uPont',
                    top: true
                }
            })
            .state('root.users.resources.courses', {
                url: '/cours',
                abstract: true,
                templateUrl: 'views/users/resources/courses.html',
                data: {
                    title: 'Cours - uPont',
                    top: true
                },
            });
    }]);
