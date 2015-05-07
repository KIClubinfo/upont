angular.module('upont')
    .controller('Admin_Assos_Ctrl', ['$scope', function($scope) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.admin.assos', {
                url: '/assos',
                templateUrl: 'views/users/admin/assos.html',
                controller: 'Admin_Assos_Ctrl',
                data: {
                    title: 'Administration des assos - uPont',
                    top: true
                }
            });
    }]);
