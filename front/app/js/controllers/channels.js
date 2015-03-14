angular.module('upont')
    .controller('ChannelsListe_Ctrl', ['$scope', 'channels', function($scope, channels) {
        $scope.channels = channels;
    }])
    .controller('ChannelsSimple_Ctrl', ['$scope', 'channel', 'members', 'events', 'newsItems', function($scope, channel, members, events, newsItems) {
        $scope.channel = channel;
        $scope.members = members;
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.promo = '017';
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.channels", {
                url: 'channels',
                abstract: true,
                template: '<div ui-view></div>',
                data: {
                    title: "uPont - Clubs & Assos"
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
                templateUrl: "views/channels/simple.html",
                resolve: {
                    channel: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug").get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    events: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug/events").query({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    newsItems: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug/newsItems").query({
                            slug: $stateParams.slug
                        }).$promise;
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
                templateUrl: "views/home/liste-publis.html",
                controller: 'ChannelsSimple_Ctrl',
                data: {
                    title: 'uPont - Publications'
                }
            })
            .state("root.channels.simple.presentation", {
                url: "/presentation",
                templateUrl: "views/channels/simple.presentation.html",
                controller : 'ChannelsSimple_Ctrl',
                data: {
                    title: 'uPont - Présentation'
                },
            })
            .state("root.channels.simple.gestion", {
                url: "/gestion",
                controller: 'ChannelsSimple_Ctrl',
                templateUrl: "views/channels/simple.gestion.html",
                data: {
                    title: 'uPont - Gestion'
                }
            });
    }]);
