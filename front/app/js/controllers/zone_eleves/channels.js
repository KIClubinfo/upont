angular.module('upont')
    .controller('ChannelsListe_Ctrl', ['$scope', 'channels', function($scope, channels) {
        $scope.channels = channels;
    }])
    .controller('ChannelsSimple_Ctrl', ['$scope', '$rootScope', '$http', '$resource', '$state', 'channel', 'members', 'events', 'newsItems', 'Paginate', function($scope, $rootScope, $http, $resource, $state, channel, members, events, newsItems, Paginate) {
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

        $scope.attend = function(publication){
            var i = $scope.events.data.indexOf(publication);
            // Si la personne attend déjà on ne fait qu'annuler le attend
            if ($scope.events.data[i].attend) {
                $http.delete(apiPrefix + 'events/' + $scope.events.data[i].slug + '/attend').success(function(data){
                    $scope.events.data[i].attend = false;
                    $scope.events.data[i].attendees--;
                });
            } else {
                $http.post(apiPrefix + 'events/' + $scope.events.data[i].slug + '/attend').success(function(data){
                    $scope.events.data[i].attend = true;
                    $scope.events.data[i].attendees++;

                    // Si la personne n'attendait aps avant
                    if ($scope.events.data[i].pookie) {
                        $scope.events.data[i].pookie = false;
                        $scope.events.data[i].pookies--;
                    }
                });
            }
        };

        $scope.pookie = function(publication){
            var i = $scope.events.data.indexOf(publication);
            // Si la personne pookie déjà on ne fait qu'annuler le pookie
            if ($scope.events.data[i].pookie) {
                $http.delete(apiPrefix + 'events/' + $scope.events.data[i].slug + '/decline').success(function(data){
                    $scope.events.data[i].pookie = false;
                    $scope.events.data[i].pookies--;
                });
            } else {
                $http.post(apiPrefix + 'events/' + $scope.events.data[i].slug + '/decline').success(function(data){
                    $scope.events.data[i].pookie = true;
                    $scope.events.data[i].pookies++;
                    alertify.success('Cet événement ne sera plus affiché par la suite. Tu pourras toujours le retrouver sur la page de l\'assos.');

                    // Si la personne était pookie avant
                    if ($scope.events.data[i].attend) {
                        $scope.events.data[i].attend = false;
                        $scope.events.data[i].attendees--;
                    }
                });
            }
        };

        $scope.showAttendees = function(publication){
            $http.get(apiPrefix + 'events/' + publication.slug + '/attendees').success(function(data){
                $scope.attendees = data;

                var string = '<strong>Personnes participant à l\'événement :</strong><br>';
                for (var i = 0; i < data.length; i++) {
                    if (data[i].username != $rootScope.me.username)
                        string += data[i].nick + ', ';
                }
                string = string.replace(/, $/, '');

                if (publication.attend)
                    string += publication.attendees == 1 ? 'Toi !' : ', toi !';

                alertify.alert(string);
            });
        };

        $scope.deletePost = function(post){
            var index = null;
            if (post.start_date) {
                index = $scope.events.data.indexOf(post);

                // On demande confirmation
                alertify.confirm('Est-ce vraiment ce que tu veux ?', function(e){
                    if (e) {
                        $resource(apiPrefix + 'events/' + $scope.events.data[index].slug).delete(function() {
                            $scope.events.data.splice(index, 1);
                        });
                    }
                });
            } else {
                index = $scope.newsItems.data.indexOf(post);

                // On demande confirmation
                alertify.confirm('Est-ce vraiment ce que tu veux ?', function(e){
                    if (e) {
                        $resource(apiPrefix + 'newsitems/' + $scope.newsItems.data[index].slug).delete(function() {
                            $scope.newsItems.data.splice(index, 1);
                        });
                    }
                });
            }
        };

        $scope.submitClub = function(name, fullName, icon, image, banner) {
            var params = {
                'name' : name,
                'fullName' : fullName,
                'icon' : icon,
            };

            if (image) {
                params.image = image.base64;
            }

            if (banner) {
                params.banner = banner.base64;
            }

            $http.patch(apiPrefix + 'clubs/' + $scope.channel.slug, params).success(function(){
                // On recharge le club pour être sûr d'avoir la nouvelle photo
                if (channelSlug == name) {
                    $http.get(apiPrefix + 'clubs/' + $scope.channel.slug).success(function(data){
                        $scope.channel = data;
                    });
                } else {
                    alertify.alert('Le nom court du club ayant changé, il est nécéssaire de recharger la page du club...');
                    $state.go('root.zone_eleves.channels.liste');
                }
                alertify.success('Modifications prises en compte !');
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
                    $scope.searchResults = data.users;
                });
            }
        };

        $scope.addMember = function(slug, name) {
            // On vérifie que la personne n'est pas déjà membre
            for (var i = 0; i < $scope.members.length; i++) {
                if ($scope.members[i].user.username == slug) {
                    alertify.error('Déjà membre du club !');
                    return;
                }
            }

            alertify.prompt('Rôle :', function(e, role){
                if (e) {
                    $http.post(apiPrefix + 'clubs/' + $scope.channel.slug + '/users/' + slug, {role: role}).success(function(data){
                        alertify.success(name + ' a été ajouté(e) !');
                        $scope.reloadMembers();
                    });
                }
            });
        };

        $scope.removeMember = function(slug) {
            $http.delete(apiPrefix + 'clubs/' + $scope.channel.slug + '/users/' + slug).success(function(data){
                alertify.success('Membre supprimé !');
                $scope.reloadMembers();
            });
        };

        $scope.reloadMembers = function() {
            $http.get(apiPrefix + 'clubs/' + $scope.channel.slug + '/users').success(function(data){
                $scope.members = data;
            });
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state("root.zone_eleves.channels", {
                url: 'assos',
                abstract: true,
                template: '<div ui-view></div>',
                data: {
                    title: "Clubs & Assos - uPont"
                }
            })
            .state("root.zone_eleves.channels.liste", {
                url: "",
                templateUrl: "views/zone_eleves/channels/liste.html",
                controller: 'ChannelsListe_Ctrl',
                resolve: {
                    channels: ["$resource", function($resource) {
                        return $resource(apiPrefix + "clubs?sort=name").query().$promise;
                    }]
                },
                data: {
                    top: true
                }
            })
            .state("root.zone_eleves.channels.simple", {
                url: "/:slug",
                abstract: true,
                controller : 'ChannelsSimple_Ctrl',
                templateUrl: "views/zone_eleves/channels/simple.html",
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
            .state("root.zone_eleves.channels.simple.publications", {
                url: "",
                templateUrl: "views/zone_eleves/channels/simple.publications.html",
                data: {
                    title: 'Activités - uPont',
                    top: true
                }
            })
            .state("root.zone_eleves.channels.simple.presentation", {
                url: "/presentation",
                templateUrl: "views/zone_eleves/channels/simple.presentation.html",
                data: {
                    title: 'Présentation - uPont',
                    top: true
                },
            })
            .state("root.zone_eleves.channels.simple.gestion", {
                url: "/gestion",
                templateUrl: "views/zone_eleves/channels/simple.gestion.html",
                data: {
                    title: 'Gestion - uPont',
                    top: true
                },
            });
    }]).filter('promoFilter', function() {
        // Filtre spécial qui renvoie les membres selon une année précise
        // En effet, les respos 2A sont d'une année différente
        return function(members, year) {
            var results = [];
            for (var i = 0; i < members.length; i++) {
                // Pas de xor en javasale...
                if ((members[i].user.promo == year && !(members[i].role.match(/2A/g) && members[i].user.promo == year-1)) || (members[i].user.promo != year && (members[i].role.match(/2A/g) && members[i].user.promo == year-1)))
                    results.push(members[i]);
            }
            return results;
        };
    });
