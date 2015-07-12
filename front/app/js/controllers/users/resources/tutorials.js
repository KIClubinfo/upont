angular.module('upont')
    .controller('Tutorials_Ctrl', ['$scope', function($scope) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources.tutorials', {
                url: '/tutoriels',
                abstract: true,
                templateUrl: 'views/users/resources/tutorials.html',
                data: {
                    title: 'Tutoriels - uPont',
                    top: true
                },
            })
            .state('root.users.resources.administration', {
                url: '/administration',
                templateUrl: 'views/users/resources/administration.html',
                data: {
                    title: 'Infos Administration - uPont',
                    top: true
                },
            })
            .state('root.users.resources.moderation', {
                url: '/moderation',
                templateUrl: 'views/users/resources/moderation.html',
                data: {
                    title: 'Règles de modération - uPont',
                    top: true
                },
            })
        ;
    }]);
