angular.module('upont')
    .controller('Calendar_Ctrl', ['$rootScope', '$scope', '$filter', 'events', 'courseitems', function($rootScope, $scope, $filter, events, courseitems) {
        $scope.events = [];
        for (var i = 0; i < events.length; i++) {
            var type;
            switch (events[i].entry_method) {
                case 'Shotgun': type = 'important'; break;
                case 'Libre':   type = 'warning'; break;
                case 'Ferie':   type = 'success'; break;
            }
            $scope.events.push({
                type: type,
                startsAt: new Date(events[i].start_date*1000),
                endsAt: new Date(events[i].end_date*1000),
                title: events[i].author_club.name + ' : ' + events[i].name,
                editable: false,
                deletable: false,
                draggable: false,
                resizable: false,
                incrementsBadgeTotal: true,
            });
        }

        for (i = 0; i < courseitems.length; i++) {
            var group = courseitems[i].group;
            $scope.events.push({
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

        $scope.calendarView = 'month';
        $scope.calendarDay = new Date();

        $scope.setView = function(view) {
            $scope.calendarView = view;
        };
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.calendar', {
                url: 'calendrier',
                templateUrl: 'controllers/users/calendar.html',
                controller: 'Calendar_Ctrl',
                data: {
                    title: 'Calendrier - uPont'
                },
                resolve: {
                    events: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/events').query().$promise;
                    }],
                    courseitems: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/courseitems').query().$promise;
                    }]
                },
                onEnter: ['$rootScope', function($rootScope) {
                    $rootScope.hideFooter = true;
                }],
                onExit: ['$rootScope', function($rootScope) {
                    $rootScope.hideFooter = false;
                }]
            });
    }])
    .config(function(calendarConfigProvider) {
        calendarConfigProvider.setDateFormatter('moment');
        calendarConfigProvider.setDateFormats({
            hour: 'HH:mm',
            datetime: 'D MMM, HH:mm',

        });

        calendarConfigProvider.setTitleFormats({
            day: 'ddd D MMM',
            week: 'Semaine {week}',
        });
        calendarConfigProvider.setDisplayAllMonthEvents(true);

        calendarConfigProvider.setI18nStrings({
            eventsLabel: 'Événements',
            timeLabel: 'Temps'
        });
    });
