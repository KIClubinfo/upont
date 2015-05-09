angular.module('upont')
    .controller('Students_List_Ctrl', ['$scope', 'users', 'Paginate', function($scope, users, Paginate) {
        $scope.users = users;
        $scope.search = {
            promo: 'all',
            department: 'all',
            nationality: 'all',
            origin: 'all',
            gender: 'all',
        };

        $scope.next = function() {
            Paginate.next($scope.users).then(function(data){
                $scope.users = data;
            });
        };

        $scope.reload = function(criterias) {
            var url = 'users?sort=-promo,department,firstName,lastName';

            if (criterias.promo != 'all')
                url += '&promo=' + criterias.promo;
            if (criterias.department != 'all')
                url += '&department=' + criterias.department;
            if (criterias.nationality != 'all')
                url += '&nationality=' + criterias.nationality;
            if (criterias.origin != 'all')
                url += '&origin=' + criterias.origin;
            if (criterias.gender != 'all')
                url += '&promo=' + criterias.gender;

            Paginate.get(url, 20).then(function(data){
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
                        return Paginate.get('users?sort=-promo,department,firstName,lastName', 20);
                    }]
                },
                data: {
                    top: true
                }
            });
    }]);
