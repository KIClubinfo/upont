angular.module('upont')
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.admissibles', {
                url: 'admissibles',
                abstract: true,
                template: '<div ui-view></div>'
            })
            .state('root.admissibles.index', {
                url: '',
                templateUrl: 'views/admissibles/index.html',
                data: {
                    title: 'Zone admissibles - uPont',
                    top: true
                }
            });
    }]);
