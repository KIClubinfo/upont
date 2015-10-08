angular.module('upont')
    .controller('Baskets_Ctrl', ['$scope', '$rootScope', '$http', 'baskets', function($scope, $rootScope, $http, baskets) {
        $scope.baskets = baskets;
        $scope.newBasket = {'name': '', 'content': '', 'price': 0};

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
                $scope.reloadBaskets();
            });
        };

        $scope.removeBasket = function(slug) {
            $http.delete(apiPrefix + 'baskets/' + slug).success(function() {
                alertify.success('Panier supprimé !');
                $scope.reloadBaskets();
            });
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
                    }]
                }
            });
    }]);