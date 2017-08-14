angular.module('upont')
.config(['$stateProvider', function($stateProvider) {
    $stateProvider
        .state('root.users.dashboard', {
            url: 'dashboard',
            templateUrl: 'controllers/users/dashboard.html',
            controller: 'Aside_Ctrl',
            data: {
                title: 'Tableau de bord - uPont',
                top: true
            }
        });
}]);
