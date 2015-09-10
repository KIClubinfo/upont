angular.module('upont')
    .controller('Administration_Modify_Ctrl', ['$scope', '$http', '$sce', '$filter', function($scope, $http, $sce, $filter) {
        
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.administration.modify', {
                url: '/gestion',
                controller : 'Administration_Modify_Ctrl',
                templateUrl: 'controllers/users/administration/modify.html',
                data: {
                    title: 'Gestion - uPont',
                    top: true
                }
            });
    }]);
