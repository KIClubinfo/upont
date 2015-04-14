angular.module('upont')
    .controller("PH_Liste_Ctrl", ['$scope', '$stateParams', 'elements', 'Paginate', 'Ponthub', function($scope, $stateParams, elements, Paginate, Ponthub) {
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
    .controller('PH_Element_Ctrl', ['$scope', '$stateParams', '$q', 'Ponthub', 'StorageService', '$window', '$http', 'element', 'episodes', 'musics', function($scope, $stateParams, $q, Ponthub, StorageService, $window, $http, element, episodes, musics) {
        $scope.element = element;
        $scope.category = $stateParams.category;
        $scope.lastWeek = moment().subtract(7, 'days').unix();
        $scope.type = Ponthub.cat($stateParams.category);
        $scope.musics = musics;
        $scope.openSeason = -1;
        $scope.fleur = null;
        $scope.token = StorageService.get('token');

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
    .controller('PH_Modify_Ctrl', ['$scope', '$stateParams', 'Ponthub', '$http', 'element', function($scope, $stateParams, Ponthub, $http, element) {
        $scope.init = function(element) {
            $scope.element = element;
            $scope.element.tags = $scope.element.tags.join();
            $scope.element.genres = $scope.element.genres.join();
            if ($scope.element.actors_list)
                $scope.element.actors_list = $scope.element.actors_list.join();
        };
        $scope.init(element);

        $scope.category = $stateParams.category;
        $scope.type = Ponthub.cat($stateParams.category);
        $scope.propositions = [];

        var elementSlug = element.name;

        $scope.search = function(criteria) {
            if ($scope.type == 'movies' || $scope.type == 'series') {
                $http.post(apiPrefix + 'imdb/search', {name: criteria}).success(function(data){
                    $scope.propositions = data;

                    if (data.length > 0) {
                        $scope.proposition = data[0].id;
                    } else {
                        alertify.error('Aucun résultat trouvé !');
                    }
                });
            }
        };

        $scope.fill = function(proposition) {
            if ($scope.type == 'movies' || $scope.type == 'series') {
                if (!proposition.length) {
                    alertify.error('Aucun film recherché !');
                } else {
                    $http.post(apiPrefix + 'imdb/infos', {id: proposition}).success(function(data){
                        $scope.element.name = data.title;
                        $scope.element.year = data.year;
                        $scope.element.duration = data.duration;
                        $scope.element.genres = data.genres.join();
                        $scope.element.actors_list = data.actors.join();
                        $scope.element.director = data.director;
                        $scope.element.description = data.description;
                        $scope.element.rating = data.rating;
                        $scope.imageUrl = data.image;
                    });
                }
            }
        };

        $scope.gracenote = function(album) {
            if ($scope.type == 'albums') {
                $http.post(apiPrefix + 'gracenote', {album: album.name, artist: album.artist}).success(function(data){
                    $scope.element.year = data.year;
                    $scope.imageUrl = data.image;
                });
            }
        };

        $scope.submitFile = function(element, imageUrl, imageBase64) {
            var params = {
                'name' : element.name,
                'description' : element.description,
            };

            var genres = [];
            var actors = [];
            var tags = [];
            var list = [];
            var i = 0;

            params.tags = element.tags;
            params.genres = element.genres;

            if (imageUrl !== '') {
                params.image = imageUrl;
            }
            // On donne la priorité à l'image par upload de fichier si elle est remplie
            if (imageBase64) {
                params.image = imageBase64.base64;
            }

            switch ($scope.type) {
                case 'movies':
                case 'series':
                    params.actors = element.actors_list;
                    params.year = element.year;
                    params.duration = element.duration;
                    params.director = element.director;
                    params.vo = element.vo;
                    params.vf = element.vf;
                    params.vost = element.vost;
                    params.vostfr = element.vostfr;
                    params.hd = element.hd;
                    if (element.rating !== '' && element.rating != 'N/A')
                        params.rating = element.rating;
                    break;
                case 'albums':
                    params.year = element.year;
                    params.artist = element.artist;
                    break;
                case 'games':
                    params.year = element.year;
                    params.studio = element.studio;
                    break;
                case 'softwares':
                    params.year = element.year;
                    params.author = element.author;
                    params.version = element.version;
                    break;
                case 'others':
                    break;
            }

            $http.patch(apiPrefix + $scope.type + '/' + element.slug, params).success(function(){
                // On recharge le fichier pour être sûr d'avoir la nouvelle image
                if (elementSlug == element.name) {
                    $http.get(apiPrefix + $scope.type + '/' + element.slug).success(function(data){
                        $scope.init(data);
                    });
                } else {
                    alertify.alert('Le nom apparent du fichier ayant changé, il est nécéssaire de recharger la page...');
                    $state.go('root.channels.liste');
                }
                alertify.success('Modifications prises en compte !');
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider.state("root.ponthub", {
                url: "ponthub/:category",
                templateUrl: "views/ponthub/index.html",
                abstract: true,
                data: {
                    title: 'PontHub - uPont',
                    top: true
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
                        return Paginate.get(Ponthub.cat($stateParams.category) + '?sort=-added', 20);
                    }]
                }
            })
            .state("root.ponthub.simple", {
                url: "/:slug",
                templateUrl: "views/ponthub/simple.html",
                controller: 'PH_Element_Ctrl',
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
            .state("root.ponthub.modify", {
                url: "/:slug/rangement",
                templateUrl: "views/ponthub/modify.html",
                controller: 'PH_Modify_Ctrl',
                data: {
                    top: true
                },
                resolve: {
                    element: ['$resource', '$stateParams', 'Ponthub', function($resource, $stateParams, Ponthub) {
                        return $resource(apiPrefix + ':cat/:slug').get({
                            cat: Ponthub.cat($stateParams.category),
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                }
            });
    }]);
