angular.module('upont')
    .controller('Ponthub_List_Ctrl', ['$scope', '$stateParams', 'elements', 'Paginate', 'Ponthub', 'StorageService', function($scope, $stateParams, elements, Paginate, Ponthub, StorageService) {
        $scope.elements = elements;
        $scope.category = $stateParams.category;
        $scope.type = Ponthub.cat($stateParams.category);
        $scope.lastWeek = moment().subtract(7 , 'days').unix();
        $scope.token = StorageService.get('token');

        $scope.reload = function(filters) {
            var url = Ponthub.cat($stateParams.category) + '?sort=-added,id';

            Paginate.get(url, 20).then(function(data){
                $scope.elements = data;
            });
        };

        $scope.faIcon = function(category){
            switch(category) {
                case 'jeux':
                    return 'fa-gamepad';
                case 'films':
                case 'series':
                    return 'fa-film';
                case 'musiques':
                    return 'fa-music';
                case 'autres':
                    return 'fa-file-o';
                case 'logiciels':
                    return 'fa-desktop';
                default:
                    return '';
            }
        };
        $scope.icon = $scope.faIcon($stateParams.category);

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
                templateUrl: 'controllers/users/ponthub/index.html',
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
                templateUrl: 'controllers/users/ponthub/statistics.html',
                controller: 'Ponthub_Statistics_Ctrl',
                data: {
                    top: true
                },
                resolve: {
                    ponthub: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'statistics/ponthub').get().$promise;
                    }]
                }
            })
            .state('root.users.ponthub.requests', {
                url: '/demandes',
                controller: 'Ponthub_Requests_Ctrl',
                templateUrl: 'controllers/users/ponthub/requests.html',
                resolve: {
                    requests: ['$resource', '$stateParams', function($resource, $stateParams) {
                        return $resource(apiPrefix + 'requests').query().$promise;
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
            // Idem, le state simple doit être enregistré avant le state de list
            .state('root.users.ponthub.category.simple', {
                url: '/:slug',
                templateUrl: 'controllers/users/ponthub/simple.html',
                controller: 'Ponthub_Element_Ctrl',
                data: {
                    top: true
                },
                resolve: {
                    element: ['$resource', '$stateParams', 'Ponthub', function($resource, $stateParams, Ponthub) {
                        return $resource(apiPrefix + ':cat/:slug').get({
                            cat: Ponthub.cat($stateParams.category),
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    episodes: ['$resource', '$stateParams', 'Ponthub', function($resource, $stateParams, Ponthub) {
                        if(Ponthub.cat($stateParams.category) != 'series')
                            return true;
                        return $resource(apiPrefix + ':cat/:slug/episodes').query({
                            cat: 'series',
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    musics: ['$resource', '$stateParams', 'Ponthub', function($resource, $stateParams, Ponthub) {
                        if(Ponthub.cat($stateParams.category) != 'albums')
                            return true;
                        return $resource(apiPrefix + ':cat/:slug/musics').query({
                            cat: 'albums',
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                }
            })
            .state('root.users.ponthub.category.list', {
                url: '',
                templateUrl: 'controllers/users/ponthub/list.html',
                controller: 'Ponthub_List_Ctrl',
                resolve: {
                    elements: ['Paginate', '$stateParams', 'Ponthub', function(Paginate, $stateParams, Ponthub) {
                        return Paginate.get(Ponthub.cat($stateParams.category) + '?sort=-added,id', 20);
                    }]
                },
            });
    }]);
