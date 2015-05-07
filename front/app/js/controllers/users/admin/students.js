angular.module('upont')
    .controller('Admin_Students_Ctrl', ['$scope', function($scope) {

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.admin', {
                url: 'admin',
                templateUrl: 'views/users/admin/index.html',
                abstract: true,
                data: {
                    title: 'Administration - uPont',
                    top: true
                }
            })
            .state('root.users.admin.students', {
                url: '/eleves',
                templateUrl: 'views/users/admin/students.html',
                controller: 'Admin_Students_Ctrl',
                data: {
                    title: 'Administration des élèves - uPont',
                    top: true
                }
            });
    }]);
