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
        $stateProvider.state('root.zone_eleves.promo', {
                url: 'promo',
                templateUrl: 'views/zone_eleves/promo/index.html',
                abstract: true,
                data: {
                    title: 'Promo - uPont',
                    top: true
                },
            })
            .state('root.zone_eleves.promo.list', {
                url: '',
                templateUrl: 'views/zone_eleves/promo/list.html',
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
