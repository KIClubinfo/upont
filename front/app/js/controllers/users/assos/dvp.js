angular.module('upont')
    .controller('DVP_Ctrl', ['$scope', '$rootScope', '$http', 'baskets', 'Paginate', function($scope, $rootScope, $http, baskets, Paginate) {
        $scope.baskets = baskets;
        $scope.ordering = false;
        $scope.thursdays = [];
        $scope.months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
        $scope.basketOrders = [[],[],[],[]];

        for (i=0;i<4;i++) {
            for (j=0;j<baskets.length;j++) {
                $scope.basketOrders[i][j] = false;
            }
        }


        $scope.order = function() {
            $scope.ordering = true;
            var d = new Date();
            var diff;
            
            //We compute the next four thursdays.
            if (d.getDay() > 1 || d.getHours() > 12) {
                diff = 4 - d.getDay() + 7;
                for (i=0;i<4;i++) {
                    $scope.thursdays[i] = new Date();
                    $scope.thursdays[i].setDate(d.getDate() + diff + i*7);
                }
            }
            else {
                diff = 4 - d.getDay();
                for (i=0;i<4;i++) {
                    $scope.thursdays[i] = new Date();
                    $scope.thursdays[i].setDate(d.getDate() + diff + i*7);
                }
            }

        };

        $scope.post = function() {
            alertify.success('C\'est parti !');
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.dvp', {
                url: '/paniers',
                templateUrl: 'controllers/users/assos/dvp.html',
                controller: 'DVP_Ctrl',
                data: {
                    title: 'DVP - uPont',
                    top: true
                },
                resolve: {
                    baskets: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'baskets').query().$promise;
                    }]
                }
            });
    }]);
