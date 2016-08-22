angular.module('upont')
    .controller('DVP_Ctrl', ['$scope', '$rootScope', '$http', '$q', 'baskets', function ($scope, $rootScope, $http, $q, baskets) {
        $scope.baskets = baskets;
        $scope.thursdays = [];
        $scope.basketOrders = {};
        $scope.holidays = [
            moment('2015-10-29'),
            moment('2015-12-24'),
            moment('2015-12-31'),
        ];

        $scope.loadOrders = function (email) {
            $http.get(apiPrefix + 'baskets-orders/' + email).success(function (data) {
                $scope.orders = data;
                for (var i = 0; i < $scope.orders.length; i++) {
                    $scope.orders[i].date_retrieve = moment($scope.orders[i].date_retrieve);

                    var order = $scope.orders[i];
                    if(typeof $scope.basketOrders[order.basket.slug] === 'undefined')
                        $scope.basketOrders[order.basket.slug] = {};
                    $scope.basketOrders[order.basket.slug][order.date_retrieve.format('YYYY-MM-DD')] = {
                        ordered: true,
                        disabled: true,
                    };
                }
            });
        };

        if($rootScope.isLogged) {
            $scope.email = $rootScope.me.email;
            $scope.loadOrders($scope.email);
        }

        $scope.thursdays = [
            moment().day(4),
            moment().day(4 + 7),
            moment().day(4 + 14),
            moment().day(4 + 21)
        ];

        $scope.after = function(item){
            return item['date_retrieve'].format('YYYY-MM-DD') >= moment().format('YYYY-MM-DD');
        };

        $scope.before = function(item){
            return item['date_retrieve'].format('YYYY-MM-DD') < moment().format('YYYY-MM-DD');
        };

        $scope.post = function (firstName, lastName, email, phone) {
            var promiseArray = [];

            if (!$rootScope.isLogged) {
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
            }

            for (var i = 0; i < $scope.baskets.length; i++) {
                var currBasket = $scope.basketOrders[baskets[i].slug];
                for (var date in currBasket) {
                    if (currBasket[date].ordered && !currBasket[date].disabled) {
                        var orderData = {
                            dateRetrieve: date
                        };

                        if (!$rootScope.isLogged) {
                            orderData.firstName = $scope.firstName;
                            orderData.lastName = $scope.lastName;
                            orderData.email = $scope.email;
                            orderData.phone = $scope.phone;
                        }

                        promiseArray.push($http.post(apiPrefix + 'baskets/' + baskets[i].slug + '/order', orderData));
                    }
                }
            }

            $q.all(promiseArray).then(function () {
                alertify.success('Commande envoyée !');
                $scope.loadOrders($scope.email);
            });
        };

    }])
    .config(['$stateProvider', function ($stateProvider) {
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
                    baskets: ['$resource', function ($resource) {
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
                    baskets: ['$resource', function ($resource) {
                        return $resource(apiPrefix + 'baskets').query().$promise;
                    }]
                }
            });
    }]);
