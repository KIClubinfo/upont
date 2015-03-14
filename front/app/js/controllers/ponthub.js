angular.module('upont')
    .controller("PH_Liste_Ctrl", ['$scope', '$stateParams', 'elements', function($scope, $stateParams, elements) {
        $scope.elements = elements;
        $scope.category = $stateParams.category;
    }])
    .controller("PH_Element_Ctrl", ['$scope', '$http', 'element', 'episodes', function($scope, $http, element, episodes) {
        $scope.element = element;
        if(episodes){
            $scope.saisons = [];
            for (var i = 0; i < episodes.length; i++) {
                if (!$scope.saisons[episodes[i].season - 1]) {
                    $scope.saisons[episodes[i].season - 1] = [];
                }
                $scope.saisons[episodes[i].season - 1].push(episodes[i]);
            }
        }
        $scope.download = function(url) {
            $http.get(url + '/download');
        };
    }])
    .factory('PH_categories', function(){
        return function(category){
            switch (category) {
                case 'films':
                    return 'movies';
                case 'jeux':
                    return'games';
                case 'logiciels':
                    return'softwares';
                case 'musiques':
                    return 'albums';
                case 'autres':
                    return 'others';
                case 'series':
                    return 'series';
            }
        };
    })
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider.state("root.ponthub", {
                url: "ponthub/:category",
                templateUrl: "views/ponthub/index.html",
                abstract: true,
                data: {
                    title: 'uPont - PontHub'
                },
                params: {
                    category: 'films'
                }
            })
            .state("root.ponthub.liste", {
                url: "",
                templateUrl: "views/ponthub/liste.html",
                controller: 'PH_Liste_Ctrl',
                resolve: {
                    elements: ['$resource', '$stateParams', 'PH_categories', function($resource, $stateParams, PH_categories) {
                        return $resource(apiPrefix + ":cat").query({
                            cat: PH_categories($stateParams.category)
                        });
                    }]
                }
            })
            .state("root.ponthub.simple", {
                url: "/:slug",
                templateUrl: "views/ponthub/simple.html",
                controller: 'PH_Element_Ctrl',
                resolve: {
                    element: ['$resource', '$stateParams', 'PH_categories', function($resource, $stateParams, PH_categories) {
                        return $resource(apiPrefix + ':cat/:slug').get({
                            cat: PH_categories($stateParams.category),
                            slug: $stateParams.slug
                        });
                    }],
                    episodes: ['$resource', '$stateParams', 'PH_categories', function($resource, $stateParams, PH_categories) {
                        if(PH_categories($stateParams.category) != 'series')
                            return true;
                        return $resource(apiPrefix + ':cat/:slug/episodes').query({
                            cat: 'series',
                            slug: $stateParams.slug
                        });
                    }],
                }
            });
    }]);
