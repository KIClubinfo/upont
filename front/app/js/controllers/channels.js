angular.module('upont')
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("channels", {
                url: '/channels',
                template: '<div ui-view></div>',
                data: {
                    parent: "channels",
                    defaultChild: "liste"
                }
            })
            .state("channels.liste", {
                url: "",
                templateUrl: "views/channels/liste.html",
                controller: ['$scope', 'channels', function($scope, channels) {
                    $scope.channels = channels;
                }],
                resolve: {
                    channels: ["$resource", function($resource) {
                        return $resource(apiPrefix + "clubs?sort=name").query().$promise;
                    }]
                }
            })
            .state("channels.simple", {
                url: "/:slug",
                templateUrl: "views/channels/simple.html",
                data: {
                    toParent: true,
                    parent: "channels.simple",
                    defaultChild: "publications"
                },
                resolve: {
                    channel: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug").get({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                },
                controller: ["$scope", "channel", function($scope, channel) {
                    $scope.channel = channel;
                }]
            })
            .state("channels.simple.publications", {
                url: "",
                templateUrl: "views/channels/simple.publications.html",
                controller: ["$scope", "publications", function($scope, publications) {
                    $scope.publications = publications;
                }],
                data: {
                    toParent: true
                },
                resolve: {
                    publications: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/" + $stateParams.slug + "/publications").query().$promise;
                    }]
                }
            })
            .state("channels.simple.presentation", {
                url: "/presentation",
                templateUrl: "views/channels/simple.presentation.html",
                // controller : "ChannelSimple_Ctrl",
                data: {
                    toParent: true
                }
            })
            .state("channels.simple.gestion", {
                url: "/gestion",
                templateUrl: "views/channels/simple.gestion.html",
                // controller : "ChannelSimple_Ctrl",
                data: {
                    toParent: true
                }
            });
    }]);
