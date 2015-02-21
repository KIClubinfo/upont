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
                        return $resource(apiPrefix + "clubs/:slug/publications").query({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                }
            })
            .state("channels.simple.presentation", {
                url: "/presentation",
                templateUrl: "views/channels/simple.presentation.html",
                controller : ["$scope", "channel", "membres", function($scope, channel, membres) {
                    $scope.channel = channel;
                    $scope.membres = membres;
                }],
                data: {
                    toParent: true
                },
                resolve: {
                    channel: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug").get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    membres: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug/users").query({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                }
            })
            .state("channels.simple.gestion", {
                url: "/gestion",
                templateUrl: "views/channels/simple.gestion.html",
                data: {
                    toParent: true
                }
            });
    }]);
