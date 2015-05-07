angular.module('upont')
    .controller('Students_List_Ctrl', ['$scope', 'users', 'Paginate', function($scope, users, Paginate) {
        $scope.users = users;

        $scope.next = function() {
            Paginate.next($scope.users).then(function(data){
                $scope.users = data;
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider.state('root.users.students', {
                url: 'eleves',
                templateUrl: 'views/users/students/index.html',
                abstract: true,
                data: {
                    title: 'Élèves - uPont',
                    top: true
                },
            })
            .state('root.users.students.list', {
                url: '',
                templateUrl: 'views/users/students/list.html',
                controller: 'Students_List_Ctrl',
                resolve: {
                    users: ['Paginate', function(Paginate) {
                        return Paginate.get('users', 20);
                    }]
                },
                data: {
                    top: true
                }
            });
    }]);
