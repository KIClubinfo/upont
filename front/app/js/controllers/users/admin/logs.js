angular.module('upont')
    .controller('Admin_Logs_Ctrl', ['$scope', function($scope) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.admin.logs', {
                url: '/logs',
                templateUrl: 'views/users/admin/logs.html',
                controller: 'Admin_Logs_Ctrl',
                data: {
                    title: 'Logs - uPont',
                    top: true
                }
            });
    }]);
