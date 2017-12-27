class Publications_Ctrl {
    constructor($scope, newsItems, events, courseItems) {
        $scope.events = events;
        $scope.newsItems = newsItems;

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
        for (i = 0; i < courseItems.length; i++) {
            var group = courseItems[i].group;
            $scope.calendarEvents.push({
                type: 'info',
                startsAt: new Date(courseItems[i].start_date*1000),
                endsAt: new Date(courseItems[i].end_date*1000),
                title: '[' + courseItems[i].location + '] ' + courseItems[i].course.name + ((group != '0' && group !== undefined) ? ' (Gr ' + group +')' : ''),
                editable: false,
                deletable: false,
                draggable: false,
                resizable: false,
                incrementsBadgeTotal: true,
            });
        }
    }
}

class Publications_List_Ctrl extends Publications_Ctrl {
    constructor($scope, $rootScope, $resource, $http, newsItems, events, courseItems, Paginate, Achievements, $location) {
        super($scope, newsItems, events, courseItems);
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
                $http.delete(API_PREFIX + 'events/' + $scope.events.data[i].slug + '/attend').then(function(){
                    $scope.events.data[i].attend = false;
                    $scope.events.data[i].attendees--;
                });
            } else {
                $http.post(API_PREFIX + 'events/' + $scope.events.data[i].slug + '/attend').then(function(){
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
                $http.delete(API_PREFIX + 'events/' + $scope.events.data[i].slug + '/decline').then(function(){
                    $scope.events.data[i].pookie = false;
                    $scope.events.data[i].pookies--;
                });
            } else {
                $http.post(API_PREFIX + 'events/' + $scope.events.data[i].slug + '/decline').then(function(){
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
                $http.get(API_PREFIX + 'events/' + publication.slug + '/attendees').then(function(response){
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
                        $resource(API_PREFIX + 'events/' + $scope.events.data[index].slug).delete(function() {
                            $scope.events.data.splice(index, 1);
                        });
                    }
                });
            } else {
                index = $scope.newsItems.data.indexOf(post);

                // On demande confirmation
                alertify.confirm('Veux-tu vraiment supprimer cette news ?', function(e){
                    if (e) {
                        $resource(API_PREFIX + 'newsitems/' + $scope.newsItems.data[index].slug).delete(function() {
                            $scope.newsItems.data.splice(index, 1);
                        });
                    }
                });
            }
        };

        $scope.enableModify = function(post) {
            $scope.item = post.start_date !== undefined ? 'events' : 'newsitems' ;

            if ($scope.item == 'newsitems') {
                $scope.edit = post;
            } else {
                $rootScope.$broadcast('modifyEvent', post);
            }
        };

        $scope.modify = function(post) {
            $http.patch(API_PREFIX + $scope.item + '/' + post.slug, {text: post.text}).then(function(){
                alertify.success('Publication modifiée');
                $scope.edit.text = post.text ;
                $scope.edit = null;
                $rootScope.$broadcast('newNewsitem');
            });
        };
    }
}

export default Publications_List_Ctrl;
