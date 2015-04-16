angular.module('upont')
    .controller('Profile_List_Ctrl', ['$scope', 'users', 'Paginate', function($scope, users, Paginate) {
        $scope.users = users;

        $scope.next = function() {
            Paginate.next($scope.users).then(function(data){
                $scope.users = data;
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider.state('root.promo', {
                url: 'promo',
                templateUrl: 'views/promo/index.html',
                abstract: true,
                data: {
                    title: 'Promo - uPont',
                    top: true
                },
            })
            .state('root.promo.list', {
                url: '',
                templateUrl: 'views/promo/list.html',
                controller: 'Profile_List_Ctrl',
                resolve: {
                    users: ['Paginate', function(Paginate) {
                        return Paginate.get('users', 20);
                    }]
                },
                data: {
                    title: 'Profil - uPont',
                    top: true
                }
            });
    }]);
