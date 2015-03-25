angular.module('upont')
    .controller("PH_Liste_Ctrl", ['$scope', '$stateParams', 'elements', 'Paginate', 'Ponthub', function($scope, $stateParams, elements, Paginate, Ponthub) {
        $scope.elements = elements;
        $scope.category = $stateParams.category;
        $scope.lastWeek = moment().subtract(7 , 'days').unix();

        $scope.faIcon = function(element){
            var icon = '';
            switch(element.type){
                case 'game':
                    icon = 'fa-gamepad';
                    break;
                case 'movie':
                case 'serie':
                    icon = 'fa-film';
                    break;
                case 'album':
                    icon = 'fa-music';
                    break;
                case 'other':
                    icon = 'fa-file-o';
                    break;
                case 'software':
                    icon = 'fa-desktop';
                    break;
            }
            return icon;
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
    .controller('PH_Element_Ctrl', ['$scope', '$stateParams', '$q', 'Ponthub', '$window', '$http', 'element', 'episodes', 'musics', function($scope, $stateParams, $q, Ponthub, $window, $http, element, episodes, musics) {
        $scope.element = element;
        $scope.category = $stateParams.category;
        $scope.lastWeek = moment().subtract(7, 'days').unix();
        $scope.type = Ponthub.cat($stateParams.category);
        $scope.musics = musics;
        $scope.openSeason = -1;
        $scope.fleur = null;

        function pingFleur() {
            var defered = $q.defer();
            var bool = false;
            ping('fleur.enpc.fr', function(status) { 
                if (status == 'timeout')
                    bool  = false;
                defered.resolve({test: bool});
            });
            return defered.promise;
        }

        $scope.download = function(url) {
            if ($scope.fleur === null) {
                pingFleur().then(function(result){
                    $scope.fleur = result;
                    downloadFile(url);
                });
            } else {
                downloadFile(url);
            }
        };

        function downloadFile(url) {
            if (!$scope.fleur) {
                alertify.error('Tu n\'es pas sur le réseau des résidences, impossible de télécharger le fichier !');
                return;
            }

            if (!url)
                url = apiPrefix + Ponthub.cat($stateParams.category) + '/' + element.slug + '/download';

            $http.get(url).success(function(data){
                $window.location.href = data.redirect;
            });
        }

        if (episodes) {
            $scope.saisons = [];

            for (var i = 0; i < episodes.length; i++) {
                if (!$scope.saisons[episodes[i].season - 1]) {
                    $scope.saisons[episodes[i].season - 1] = [];
                }
                $scope.saisons[episodes[i].season - 1].push(episodes[i]);
            }
        }

        $scope.open = function(index) {
            $scope.openSeason = $scope.openSeason != index ? index : -1;
        };

        $scope.popular = function(count) {
            return Ponthub.isPopular(count, $stateParams.category);
        };

        $scope.countDownloads = function() {
            var count = 0;
            switch ($scope.category) {
                case 'series':
                    $scope.saisons.forEach(function(entry) {
                        for(var j = 0; j < entry.length; j++) {
                            count += entry[j].downloads;
                        }
                    });
                    return count;
                case 'musiques':
                    for(var k = 0; k < $scope.musics.length; k++) {
                        count += $scope.musics[k].downloads;
                    }
                    return count;
                default:
                    return $scope.element.downloads;
            }
        };
    }])
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
                    elements: ['Paginate', '$stateParams', 'Ponthub', function(Paginate, $stateParams, Ponthub) {
                        return Paginate.get(Ponthub.cat($stateParams.category), 20);
                    }]
                }
            })
            .state("root.ponthub.simple", {
                url: "/:slug",
                templateUrl: "views/ponthub/simple.html",
                controller: 'PH_Element_Ctrl',
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
            });
    }]);
