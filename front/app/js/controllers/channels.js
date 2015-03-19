angular.module('upont')
    .controller('ChannelsListe_Ctrl', ['$scope', 'channels', function($scope, channels) {
        $scope.channels = channels;
    }])
    .controller('ChannelsSimple_Ctrl', ['$scope', '$http', '$state', 'channel', 'members', 'events', 'newsItems', 'Paginate', function($scope, $http, $state, channel, members, events, newsItems, Paginate) {
        $scope.channel = channel;
        $scope.members = members;
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.promo = '017';
        $scope.showIcons = false;
        $scope.faIcons = faIcons;
        $scope.search = '';
        $scope.searchResults = [];

        var channelSlug = channel.name;

        $scope.next = function() {
            Paginate.next($scope.newsItems).then(function(data){
                $scope.newsItems = data;
                Paginate.next($scope.events).then(function(data){
                    $scope.events = data;
                });
            });
        };

        $scope.submitClub = function(name, fullName, icon, image) {
            var params = {
                'name' : name,
                'fullName' : fullName,
                'icon' : icon,
            };

            if (image) {
                params.image = image.base64;
            }

            $http.patch(apiPrefix + 'clubs/' + $scope.channel.slug, params).success(function(){
                // On recharge le club pour être sûr d'avoir la nouvelle photo
                if (channelSlug == name) {
                    $http.get(apiPrefix + 'clubs/' + $scope.channel.slug).success(function(data){
                        $scope.channel = data;
                    });
                } else {
                    alertify.alert('Le nom court du club ayant changé, il est nécéssaire de recharger la page du club...');
                    $state.go('root.channels.liste');
                }
            });
        };

        $scope.setIcon = function(icon) {
            $scope.channel.icon = icon;
        };

        $scope.searchUser = function(string) {
            if (string === '') {
                $scope.searchResults = [];
            } else {
                $http.post(apiPrefix + 'search', {search: 'User/' + string}).success(function(data){
                    $scope.searchResults = data;
                });
            }
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.channels", {
                url: 'assos',
                abstract: true,
                template: '<div ui-view></div>',
                data: {
                    title: "Clubs & Assos - uPont"
                }
            })
            .state("root.channels.liste", {
                url: "",
                templateUrl: "views/channels/liste.html",
                controller: 'ChannelsListe_Ctrl',
                resolve: {
                    channels: ["$resource", function($resource) {
                        return $resource(apiPrefix + "clubs?sort=name").query().$promise;
                    }]
                }
            })
            .state("root.channels.simple", {
                url: "/:slug",
                abstract: true,
                controller : 'ChannelsSimple_Ctrl',
                templateUrl: "views/channels/simple.html",
                resolve: {
                    channel: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug").get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    events: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                        return Paginate.get('clubs/' + $stateParams.slug + '/events?sort=-date', 10);
                    }],
                    newsItems: ['$stateParams', 'Paginate', function($stateParams, Paginate) {
                        return Paginate.get('clubs/' + $stateParams.slug + '/newsitems?sort=-date', 10);
                    }],
                    members: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug/users").query({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                },
            })
            .state("root.channels.simple.publications", {
                url: "",
                templateUrl: "views/channels/simple.publications.html",
                data: {
                    title: 'Activités - uPont'
                }
            })
            .state("root.channels.simple.presentation", {
                url: "/presentation",
                templateUrl: "views/channels/simple.presentation.html",
                data: {
                    title: 'Présentation - uPont'
                },
            })
            .state("root.channels.simple.gestion", {
                url: "/gestion",
                templateUrl: "views/channels/simple.gestion.html",
                data: {
                    title: 'Gestion - uPont'
                },
            });
    }]);
