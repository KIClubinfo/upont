angular.module('upont')
    .controller('Publications_Ctrl', ['$scope', 'newsItems', 'events', 'messages', 'courseitems', function($scope, newsItems, events, messages, courseitems) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.messages = messages;

        $scope.calendarView = 'day';

        $scope.today = function() {
            $scope.calendarDay = new Date();
            $scope.todayActive = true;
        };
        $scope.tomorrow = function() {
            $scope.calendarDay = new Date(new Date().getTime() + 24*3600*1000);
            $scope.todayActive = false;
        };
        $scope.today();

        $scope.calendarEvents = [];
        for (var i = 0; i < events.data.length; i++) {
            var type;
            switch (events.data[i].entry_method) {
                case 'Shotgun': type = 'important'; break;
                case 'Libre':   type = 'warning'; break;
                case 'Ferie':   continue;
            }
            if (events.data[i]) {
                $scope.calendarEvents.push({
                    type: type,
                    startsAt: new Date(events.data[i].start_date*1000),
                    endsAt: new Date(events.data[i].end_date*1000),
                    title: events.data[i].author_club.name + ' : ' + events.data[i].name,
                    editable: false,
                    deletable: false,
                    draggable: false,
                    resizable: false,
                    incrementsBadgeTotal: true,
                });
            }
        }
        for (i = 0; i < courseitems.length; i++) {
            var group = courseitems[i].group;
            $scope.calendarEvents.push({
                type: 'info',
                startsAt: new Date(courseitems[i].start_date*1000),
                endsAt: new Date(courseitems[i].end_date*1000),
                title: '[' + courseitems[i].location + '] ' + courseitems[i].course.name + ((group != '0' && group !== undefined) ? ' (Gr ' + group +')' : ''),
                editable: false,
                deletable: false,
                draggable: false,
                resizable: false,
                incrementsBadgeTotal: true,
            });
        }
    }])
    .controller('Publications_List_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'newsItems', 'events', 'Paginate', 'Achievements', '$location', function($scope, $rootScope, $resource, $http, newsItems, events, Paginate, Achievements, $location) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.edit = null;

        $scope.next = function() {
            Paginate.next($scope.newsItems).then(function(response){
                $scope.newsItems = response;
            });

            Paginate.next($scope.events).then(function(response){
                $scope.events = response;
            });
        };

        $scope.$on('newEvent', function(event, args) {
            Paginate.first($scope.events).then(function(response){
                $scope.events = response;
            });
        });

        $scope.$on('newNewsitem', function(event, args) {
            Paginate.first($scope.newsItems).then(function(response){
                $scope.newsItems = response;
            });
        });

        $scope.attend = function(publication){
            var i = $scope.events.data.indexOf(publication);
            // Si la personne attend déjà on ne fait qu'annuler le attend
            if ($scope.events.data[i].attend) {
                $http.delete(apiPrefix + 'events/' + $scope.events.data[i].slug + '/attend').then(function(){
                    $scope.events.data[i].attend = false;
                    $scope.events.data[i].attendees--;
                });
            } else {
                $http.post(apiPrefix + 'events/' + $scope.events.data[i].slug + '/attend').then(function(){
                    $scope.events.data[i].attend = true;
                    $scope.events.data[i].attendees++;

                    // Si la personne n'attendait pas avant
                    if ($scope.events.data[i].pookie) {
                        $scope.events.data[i].pookie = false;
                        $scope.events.data[i].pookies--;
                    }
                    Achievements.check();
                });
            }
        };

        $scope.pookie = function(publication){
            var i = $scope.events.data.indexOf(publication);
            // Si la personne pookie déjà on ne fait qu'annuler le pookie
            if ($scope.events.data[i].pookie) {
                $http.delete(apiPrefix + 'events/' + $scope.events.data[i].slug + '/decline').then(function(){
                    $scope.events.data[i].pookie = false;
                    $scope.events.data[i].pookies--;
                });
            } else {
                $http.post(apiPrefix + 'events/' + $scope.events.data[i].slug + '/decline').then(function(){
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

        // On peut participer/masquer un événement via l'url
        var query = $location.search();
        if (query.action) {
            if (query.action == 'participer' && $scope.events.data[0].attend !== true) {
                $scope.attend($scope.events.data[0]);
            }
            if (query.action == 'masquer' && $scope.events.data[0].pookie !== true) {
                $scope.pookie($scope.events.data[0]);
            }
        }

        $scope.toggleAttendees = function(publication){
            publication.displayAttendees = !publication.displayAttendees;

            if (publication.userlist === undefined) {
                $http.get(apiPrefix + 'events/' + publication.slug + '/attendees').then(function(response){
                    publication.userlist = response.data;
                });
            }
        };

        $scope.delete = function(post){
            var index = null;
            if (post.start_date) {
                index = $scope.events.data.indexOf(post);

                // On demande confirmation
                alertify.confirm('Veux-tu vraiment supprimer cet évènement ?', function(e){
                    if (e) {
                        $resource(apiPrefix + 'events/' + $scope.events.data[index].slug).delete(function() {
                            $scope.events.data.splice(index, 1);
                        });
                    }
                });
            } else {
                index = $scope.newsItems.data.indexOf(post);

                // On demande confirmation
                alertify.confirm('Veux-tu vraiment supprimer cette news ?', function(e){
                    if (e) {
                        $resource(apiPrefix + 'newsitems/' + $scope.newsItems.data[index].slug).delete(function() {
                            $scope.newsItems.data.splice(index, 1);
                        });
                    }
                });
            }
        };

        function clone(copy, obj) {
            for (var attr in obj) {
                if (obj.hasOwnProperty(attr)) copy[attr] = obj[attr];
            }
        }

        $scope.enableModify = function(post) {
            $scope.item = post.start_date !== undefined ? 'events' : 'newsitems' ;
            $scope.editSlug = post.slug;
            $scope.initialPost = Object.assign({}, post);

            if ($scope.item == 'newsitems') {
                $scope.$broadcast('modifyNewsitem', post);
            } else {
                $scope.$broadcast('modifyEvent', post);
            }
        };

        $scope.cancelModify = function(post) {
            $scope.editSlug = null;
            clone(post, $scope.initialPost);
        };

        $scope.$on('modifiedNewsitem', function(event) {
            $scope.editSlug = null;
        });
        $scope.$on('modifiedEvent', function(event) {
            $scope.editSlug = null;
        });
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.publications', {
                url: '',
                template: '<div ui-view></div>',
                abstract: true,
                data: {
                    title: 'Accueil - uPont',
                    top: true
                }
            })
            .state('root.users.publications.index', {
                url: '',
                templateUrl: 'controllers/users/publications/index.html',
                data: {
                    title: 'Accueil - uPont',
                    top: true
                },
                controller: 'Publications_Ctrl',
                resolve: {
                    newsItems: ['Paginate', 'Permissions', '$rootScope', function(Paginate, Permissions, $rootScope) {

                        // Si c'est l'administration on ne charge que le seul club de l'user actuel
                        if (Permissions.hasRight('ROLE_EXTERIEUR'))
                            return Paginate.get('clubs/' + Permissions.username() + '/newsitems?sort=-date', 10);
                        return Paginate.get('own/newsitems?sort=-date', 10);
                    }],
                    events: ['Paginate', 'Permissions', '$rootScope', function(Paginate, Permissions, $rootScope) {
                        // Si c'est l'administration on ne charge que le seul club de l'user actuel
                        if (Permissions.hasRight('ROLE_EXTERIEUR'))
                            return Paginate.get('clubs/' + Permissions.username() + '/events?sort=-date', 10);
                        return Paginate.get('own/events', 10);
                    }],
                    messages: ['Paginate', function(Paginate) {
                        return Paginate.get('newsitems?sort=-date&limit=10&name=message');
                    }],
                    courseitems: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/courseitems').query().$promise;
                    }]
                }
            })
            .state('root.users.publications.simple', {
                url: 'publications/:slug',
                templateUrl: 'controllers/users/publications/list.html',
                data: {
                    title: 'Publication - uPont',
                    top: true
                },
                controller: 'Publications_Ctrl',
                resolve: {
                    newsItems: ['Paginate', '$stateParams', function(Paginate, $stateParams) {
                        return Paginate.get('newsitems?slug=' + $stateParams.slug);
                    }],
                    events: ['Paginate', '$stateParams', function(Paginate, $stateParams) {
                        return Paginate.get('events?slug=' + $stateParams.slug);
                    }],
                    messages: ['Paginate', '$stateParams', function(Paginate, $stateParams) {
                        return Paginate.get('newsitems?slug=' + $stateParams.slug);
                    }],
                    courseitems: function($resource) {
                        return [];
                    }
                }
            });
    }]);
