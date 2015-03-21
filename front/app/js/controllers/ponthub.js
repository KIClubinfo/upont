angular.module('upont')
    .controller("PH_Liste_Ctrl", ['$scope', '$stateParams', 'elements', 'Paginate', function($scope, $stateParams, elements, Paginate) {
        $scope.elements = elements;
        $scope.category = $stateParams.category;

        $scope.next = function() {
            Paginate.next($scope.elements).then(function(data){
                $scope.elements = data;
            });
        };
    }])
    .controller('PH_Element_Ctrl', ['$scope', '$stateParams', 'PH_categories', '$window', '$http', 'element', 'episodes', 'musics', function($scope, $stateParams, PH_categories, $window, $http, element, episodes, musics) {
        $scope.element = element;
        $scope.category = $stateParams.category;
        $scope.type = PH_categories($stateParams.category);
        $scope.musics = musics;
        $scope.openSeason = -1;

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
            if (!url)
                url = apiPrefix + PH_categories($stateParams.category) + '/' + element.slug + '/download';

            $http.get(url).success(function(data){
                $window.location.href = data.redirect;
            });
        };

        $scope.open = function(index) {
            $scope.openSeason = $scope.openSeason != index ? index : -1;
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
                    title: 'PontHub - uPont'
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
                    elements: ['Paginate', '$stateParams', 'PH_categories', function(Paginate, $stateParams, PH_categories) {
                        return Paginate.get(PH_categories($stateParams.category), 50);
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
                    musics: ['$resource', '$stateParams', 'PH_categories', function($resource, $stateParams, PH_categories) {
                        if(PH_categories($stateParams.category) != 'albums')
                            return true;
                        return $resource(apiPrefix + ':cat/:slug/musics').query({
                            cat: 'albums',
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                }
            });
    }]);
