angular.module('upont')
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources.administration', {
                url: '/administration',
                templateUrl: 'controllers/users/resources/administration.html',
                data: {
                    title: 'Infos Administration - uPont',
                    top: true
                },
            })
        ;
    }]);
