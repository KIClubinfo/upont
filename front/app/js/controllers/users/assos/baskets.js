angular.module('upont')
    .controller('Baskets_Ctrl', ['$scope', '$rootScope', '$http', 'baskets', 'orders', function($scope, $rootScope, $http, baskets, orders) {
        $scope.baskets = baskets;
        $scope.orders = orders;
        $scope.newBasket = {'name': '', 'content': '', 'price': 0};
        $scope.changedBasket = {};
        $scope.modifying = false;

        $scope.reloadBaskets = function() {
            $http.get(apiPrefix + 'baskets').success(function(data) {
                $scope.baskets = data;
            });
        };

        $scope.addBasket = function(name, content, price) {
            if (!name) {
                alertify.error('Le nom du nouveau panier n\'a pas été renseigné');
                return;
            }
            if (!content) {
                alertify.error('Le contenu du nouveau panier n\'a pas été renseigné');
                return;
            }
            if (!price) {
                alertify.error('Le prix du nouveau panier n\'a pas été renseigné');
                return;
            }

            var params = {
                'name' : name,
                'content' : content,
                'price' : price
            };

            $http.post(apiPrefix + 'baskets', params).success(function() {
                alertify.success('Panier créé !');
                $scope.newBasket = {'name': '', 'content': '', 'price': 0};
                $scope.reloadBaskets();
            });
        };

        $scope.removeBasket = function(slug) {
            $http.delete(apiPrefix + 'baskets/' + slug).success(function() {
                alertify.success('Panier supprimé !');
                $scope.reloadBaskets();
                $scope.reloadOrders();
            });
        };

        $scope.modifyMenu = function(index) {
            $scope.changedBasket = baskets[index];
            $scope.modifying = true;
        };

        $scope.modifyBasket = function(basket) {
            if (!basket.name) {
                alertify.error('Le nom du nouveau panier n\'a pas été renseigné');
                return;
            }
            if (!basket.content) {
                alertify.error('Le contenu du nouveau panier n\'a pas été renseigné');
                return;
            }
            if (!basket.price) {
                alertify.error('Le prix du nouveau panier n\'a pas été renseigné');
                return;
            }

            var params = {
                'name': basket.name,
                'content': basket.content,
                'price': basket.price
            };

            $http.patch(apiPrefix + 'baskets/' + basket.slug, params).success(function() {
                alertify.success('Panier modifié !');
                $scope.reloadBaskets();
                $scope.changedBasket = {};
                $scope.modifying = false;
            });
        };

        $scope.reloadOrders = function() {
            $http.get(apiPrefix + 'baskets-orders').success(function(data) {
                $scope.orders = data;
            });
        };

        $scope.removeOrder = function(slug, username, date) {
            $http.delete(apiPrefix + 'baskets/' + slug + '/order/' + username + '/' + date).success(function() {
                alertify.success('Commande supprimée !');
                $scope.reloadOrders();
            });
        };

        $scope.payOrder = function(slug, username, date, paid) {
            $http.patch(apiPrefix + 'baskets/' + slug + '/order/' + username, {dateRetrieve: date, paid: paid});
        };

    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.assos.simple.baskets', {
                url: '/gestion-paniers',
                templateUrl: 'controllers/users/assos/baskets.html',
                controller: 'Baskets_Ctrl',
                data: {
                    title: 'DVP - uPont',
                    top: true
                },
                resolve: {
                    baskets: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'baskets').query().$promise;
                    }],
                    orders: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'baskets-orders/').query().$promise;
                    }]
                }
            });
    }]);
