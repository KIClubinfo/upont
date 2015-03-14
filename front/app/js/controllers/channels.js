angular.module('upont')
    .controller('ChannelsListe_Ctrl', ['$scope', 'channels', function($scope, channels) {
        $scope.channels = channels;
    }])
    .controller('ChannelsSimple_Ctrl', ['$scope', 'channel', 'members', 'publications', function($scope, channel, members, publications) {
        $scope.channel = channel;
        $scope.members = members;
        $scope.publications = publications;
        $scope.promo = '017';
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("channels", {
                url: '/channels',
                template: '<div ui-view></div>',
                data: {
                    parent: "channels",
                    defaultChild: "liste",
                    title: "uPont - Clubs & Assos"
                }
            })
            .state("channels.liste", {
                url: "",
                templateUrl: "views/channels/liste.html",
                controller: 'ChannelsListe_Ctrl',
                data: {
                    title: "uPont - Clubs & Assos"
                },
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
                    defaultChild: "publications",
                },
                resolve: {
                    channel: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug").get({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    publications: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug/publications").query({
                            slug: $stateParams.slug
                        }).$promise;
                    }],
                    members: ["$resource", "$stateParams", function($resource, $stateParams) {
                        return $resource(apiPrefix + "clubs/:slug/users").query({
                            slug: $stateParams.slug
                        }).$promise;
                    }]
                },
                controller: 'ChannelsSimple_Ctrl'
            })
            .state("channels.simple.publications", {
                url: "",
                templateUrl: "views/home/publiListe.html",
                data: {
                    toParent: true,
                    title: 'uPont - Publications'
                }
            })
            .state("channels.simple.presentation", {
                url: "/presentation",
                templateUrl: "views/channels/simple.presentation.html",
                controller : 'ChannelsSimple_Ctrl',
                data: {
                    toParent: true,
                    title: 'uPont - Présentation'
                },
            })
            .state("channels.simple.gestion", {
                url: "/gestion",
                templateUrl: "views/channels/simple.gestion.html",
                data: {
                    toParent: true,
                    title: 'uPont - Gestion'
                }
            });
    }]);
