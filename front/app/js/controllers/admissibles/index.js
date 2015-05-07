angular.module('upont').config(['$stateProvider', function($stateProvider) {
    $stateProvider
        .state('root.admissibles.index', {
            url: 'admissibles',
            templateUrl: 'views/admissibles/index.html',
            data: {
                title: 'Zone admissibles - uPont',
                top: true
            }
        });
}]);
