angular.module('upont')
    .controller("PH_Liste_Ctrl", ['$scope', '$stateParams', 'elements', function($scope, $stateParams, elements) {
        $scope.elements = elements;
        $scope.category = $stateParams.category;
    }])
    .controller("PH_Element_Ctrl", ['$scope', 'element', 'episodes', function($scope, element, episodes) {
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
        $stateProvider.state("ponthub", {
                url: "/ponthub",
                templateUrl: "views/ponthub/index.html",
                data: {
                    defaultChild: "category",
                    parent: "ponthub"
                },
            })
            .state("ponthub.category", {
                url: "/:category",
                template: '<div ui-view></div>',
                data: {
                    parent: 'ponthub.category',
                    defaultChild: 'liste'
                },
                params: {
                    category: 'films'
                }
            })
            .state("ponthub.category.liste", {
                url: "",
                templateUrl: "views/ponthub/liste.html",
                controller: 'PH_Liste_Ctrl',
                resolve: {
                    elements: ['$resource', '$stateParams', 'PH_categories', function($resource, $stateParams, PH_categories) {
                        return $resource(apiPrefix + ":cat").query({
                            cat: PH_categories($stateParams.category)
                        }).$promise;
                    }]
                },
                data:{
                    toParent: true
                }
            })
            .state("ponthub.category.simple", {
                url: "/:slug",
                templateUrl: "views/ponthub/simple.html",
                controller: 'PH_Element_Ctrl',
                resolve: {
                    element: ['$resource', '$stateParams', 'PH_categories', function($resource, $stateParams, PH_categories) {
                        return $resource(apiPrefix + ':cat/:slug').get({
                            cat: PH_categories($stateParams.category),
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    episodes: ['$resource', '$stateParams', 'PH_categories', function($resource, $stateParams, PH_categories) {
                        if(PH_categories($stateParams.category) != 'series')
                            return true;
                        return $resource(apiPrefix + ':cat/:slug/episodes').query({
                            cat: 'series',
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                },
                data:{
                    toParent: true
                }
            });
    }]);
