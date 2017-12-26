angular.module('upont')
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.resources.moderation', {
                url: '/moderation',
                templateUrl: 'controllers/users/resources/moderation.html',
                data: {
                    title: 'Règles de modération - uPont',
                    top: true
                },
            })
        ;
    }]);
