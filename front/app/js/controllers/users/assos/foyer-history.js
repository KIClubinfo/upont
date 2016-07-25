angular.module('upont')
    .controller('Foyer_History_Ctrl', ['$scope', '$rootScope', '$http', 'Paginate', function($scope, $rootScope, $http, Paginate) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.foyer-history', {
                url: '/foyer/historique',
                templateUrl: 'controllers/users/assos/foyer-history.html',
                controller: 'Foyer_History_Ctrl',
                data: {
                    title: 'Historique foyer - uPont',
                    top: true
                },
            });
    }]);
