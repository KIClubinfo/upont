angular.module('upont')
    .controller('DVP_Ctrl', ['$scope', '$rootScope', '$http', '$q', 'baskets', 'Paginate', function($scope, $rootScope, $http, $q, baskets, Paginate) {
        $scope.baskets = baskets;
        $scope.ordering = false;
        $scope.thursdays = [];
        $scope.months = ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
        $scope.basketOrders = [[],[],[],[]];

        if($rootScope.me.username) {
            $scope.firstName = $rootScope.me.first_name;
            $scope.lastName = $rootScope.me.last_name;
            $scope.email = $rootScope.me.email;
            $scope.phone = $rootScope.me.phone;
        } else {
            $scope.firstName = '';
            $scope.lastName = '';
            $scope.email = '';
            $scope.phone = 0;
        }
        
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

        $scope.post = function(firstName, lastName, email, phone) {
            var promiseArray = [];
            var dateRetrieve;

            if (!firstName) {
                alertify.error('Prénom manquant !');
                return;
            }

            if (!lastName) {
                alertify.error('Nom manquant !');
                return;
            }

            if (!email) {
                alertify.error('Email manquant !');
                return;
            }

            if (!phone) {
                alertify.error('Numéro de téléphone manquant !');
                return;
            }

            for (i=0;i<4;i++) {
                for (j=0;j<baskets.length;j++) {
                    if ($scope.basketOrders[i][j]) {
                        dateRetrieve = $scope.thursdays[i].getTime();
                        dateRetrieve = (dateRetrieve - dateRetrieve%1000)/1000;
                        promiseArray.push($http.post(apiPrefix + 'baskets/' + baskets[j].slug + '/order',
                            {
                                firstName: firstName,
                                lastName: lastName,
                                email: email,
                                phone: phone,
                                dateRetrieve: dateRetrieve
                            }));
                    }
                }
            }

            $q.all(promiseArray).then(function() {
                alertify.success('Commande envoyée !');
                $scope.basketOrders = [[],[],[],[]];
                $scope.get();
            });

            $scope.ordering = false;
        };

        $scope.get = function() {
            $http.get(apiPrefix + 'baskets-orders/' + $rootScope.me.username).success(function(data) {
                $scope.orders = data;
            });
        };

        if ($rootScope.me.username) {
            $scope.get();
        }

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
            })

            .state('root.public.dvp', {
                url: '/paniers',
                templateUrl: 'controllers/public/dvp.html',
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
