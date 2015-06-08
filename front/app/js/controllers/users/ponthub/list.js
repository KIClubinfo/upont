angular.module('upont')
    .controller('Ponthub_List_Ctrl', ['$scope', '$stateParams', 'elements', 'Paginate', 'Ponthub', function($scope, $stateParams, elements, Paginate, Ponthub) {
        $scope.elements = elements;
        $scope.category = $stateParams.category;
        $scope.lastWeek = moment().subtract(7 , 'days').unix();

        $scope.faIcon = function(element){
            switch(element.type){
                case 'game':
                    return 'fa-gamepad';
                case 'movie':
                case 'serie':
                    return 'fa-film';
                case 'album':
                    return 'fa-music';
                case 'other':
                    return 'fa-file-o';
                case 'software':
                    return 'fa-desktop';
                default:
                    return '';
            }
        };

        $scope.next = function() {
            Paginate.next($scope.elements).then(function(data){
                $scope.elements = data;
            });
        };

        $scope.popular = function(count) {
            return Ponthub.isPopular(count, $stateParams.category);
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.ponthub', {
                url: 'ponthub',
                templateUrl: 'views/users/ponthub/index.html',
                abstract: true,
                data: {
                    title: 'PontHub - uPont',
                    top: true
                },
                params: {
                    category: 'films'
                }
            })
            // Ce state a besoin d'être enregistré avant le suivant afin que venant de l'exterieur, l'URL "statistiques" ne soit pas interpreté comme une catégorie.
            .state('root.users.ponthub.statistics', {
                url: '/statistiques',
                templateUrl: 'views/users/ponthub/statistics.html',
                controller: 'Ponthub_Statistics_Ctrl',
                data: {
                    top: true
                },
                resolve: {
                    ponthub: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'ponthub/statistics').get().$promise;
                    }]
                }
            })
            .state('root.users.ponthub.category', {
                url: '/:category',
                template: '<div ui-view></div>',
                abstract: true,
                params: {
                    category: 'films'
                }
            })
            .state('root.users.ponthub.category.list', {
                url: '',
                templateUrl: 'views/users/ponthub/list.html',
                controller: 'Ponthub_List_Ctrl',
                resolve: {
                    elements: ['Paginate', '$stateParams', 'Ponthub', function(Paginate, $stateParams, Ponthub) {
                        return Paginate.get(Ponthub.cat($stateParams.category) + '?sort=-added,id', 20);
                    }]
                },

            });
    }]);
