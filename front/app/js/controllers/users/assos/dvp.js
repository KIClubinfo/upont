angular.module('upont')
    .controller('DVP_Ctrl', ['$scope', '$rootScope', '$http', 'baskets', 'dates', function($scope, $rootScope, $http, baskets, dates) {
        $scope.baskets = baskets;
        $scope.dates = dates;
        $scope.basketOrders = {};

        $scope.loadOrders = function(email) {
            $http.get(apiPrefix + 'baskets-orders/' + email).then(function(response) {
                $scope.orders = response.data;
                for (var i = 0; i < $scope.orders.length; i++) {
                    var order = $scope.orders[i];
                    if (typeof $scope.basketOrders[order.basket.slug] === 'undefined')
                        $scope.basketOrders[order.basket.slug] = {};

                    $scope.basketOrders[order.basket.slug][order.date_retrieve.id] = true;
                }
            });
        };

        if ($rootScope.isLogged) {
            $scope.firstName = $rootScope.me.firstName;
            $scope.lastName = $rootScope.me.lastName;
            $scope.email = $rootScope.me.email;
            $scope.phone = $rootScope.me.phone;
            $scope.loadOrders($scope.email);
        }

        $scope.after = function(item) {
            return item.date_retrieve.date_retrieve >= moment().format();
        };

        $scope.before = function(item) {
            return item.date_retrieve.date_retrieve < moment().format();
        };

        $scope.post = function(firstName, lastName, email, phone) {
            if ($rootScope.isLogged) {
                firstName = $scope.firstName;
                lastName = $scope.lastName;
                email = $scope.email;
                phone = $scope.phone;
            }

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

            var postData = {
                orders: [],
                firstName: firstName,
                lastName: lastName,
                email: email,
                phone: phone,
            };

            for (var i = 0; i < $scope.baskets.length; i++) {
                var currBasket = $scope.basketOrders[baskets[i].slug];
                for (var date in currBasket) {
                    var orderData = {
                        dateRetrieve: date,
                        basket: baskets[i].slug,
                        ordered: currBasket[date]
                    };

                    postData.orders.push(orderData);
                }
            }

            $http.post(apiPrefix + 'baskets-orders', postData).then(function() {
                alertify.success('Commande envoyée !');
                $scope.loadOrders($scope.email);
            });
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
                    }],
                    dates: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'basketdates').query().$promise;
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
                    }],
                    dates: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'basketdates').query().$promise;
                    }]
                }
            });
    }]);
