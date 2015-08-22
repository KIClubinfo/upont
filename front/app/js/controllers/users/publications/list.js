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
            if (events.data[i]) {
                $scope.calendarEvents.push({
                    type: 'warning',
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
    .controller('Publications_List_Ctrl', ['$scope', '$rootScope', '$resource', '$http', 'newsItems', 'events', 'Paginate', 'Achievements', function($scope, $rootScope, $resource, $http, newsItems, events, Paginate, Achievements) {
        $scope.events = events;
        $scope.newsItems = newsItems;
        $scope.edit = null;

        $scope.next = function() {
            Paginate.next($scope.newsItems).then(function(data){
                $scope.newsItems = data;
            });
        };

        $scope.$on('newEvent', function(event, args) {
            Paginate.first($scope.events).then(function(data){
                $scope.events = data;
            });
        });

        $scope.$on('newNewsitem', function(event, args) {
            Paginate.first($scope.newsItems).then(function(data){
                $scope.newsItems = data;
            });
        });

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

        $scope.enableModify = function(post) {
            $scope.edit = post;
        };

        $scope.modify = function(post) {
            var item = post.start_date !== undefined ? 'events' : 'newsitems' ;
            $http.patch(apiPrefix + item + '/' + post.slug, {text: post.text}).success(function(data){
                alertify.success('Publication modifiée !');
                $scope.edit = null;
            });
        };
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
                templateUrl: 'views/users/publications/index.html',
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
                        return Paginate.get('own/events');
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
                templateUrl: 'views/users/publications/list.html',
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
                    }]
                }
            });
    }]);
