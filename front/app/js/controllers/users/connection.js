angular.module('upont')
.config(['$stateProvider', function($stateProvider) {
    $stateProvider
        .state('root.users.connection', {
            url: 'connection',
            templateUrl: 'controllers/users/connection.html',
            controller: 'Aside_Ctrl',
            data: {
                title: 'Tableau de bord - uPont',
                top: true
            }
        });
}]);
