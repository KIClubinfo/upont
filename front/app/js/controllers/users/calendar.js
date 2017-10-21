angular.module('upont')
    .controller('Calendar_Ctrl', ['$rootScope', '$scope', '$filter', 'events', 'courseitems', 'calendarConfig', function($rootScope, $scope, $filter, events, courseitems, calendarConfig) {
        $scope.events = [];
        for (var i = 0; i < events.length; i++) {
            var type;
            switch (events[i].entry_method) {
                case 'Shotgun': type = 'important'; break;
                case 'Libre':   type = 'warning'; break;
                case 'Ferie':   type = 'success'; break;
            }
            $scope.events.push({
                color: calendarConfig.colorTypes[type],
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
                color: calendarConfig.colorTypes.info,
                startsAt: new Date(courseitems[i].start_date*1000),
                endsAt: new Date(courseitems[i].end_date*1000),
                title: '[' + courseitems[i].location + '] ' + courseitems[i].course.name + (group !== 0 ? ' (Gr ' + group +')' : ''),
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
                        return $resource(apiPrefix + 'own/events?publicationState=Scheduled,Published,Emailed').query().$promise;
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
    .config(['calendarConfig', function(calendarConfig) {
        calendarConfig.dateFormatter = 'moment';

        calendarConfig.allDateFormats.moment.date.hour = 'HH:mm';
        calendarConfig.allDateFormats.moment.date.datetime = 'D MMM, HH:mm';

        calendarConfig.allDateFormats.moment.title.day = 'ddd D MMM';

        calendarConfig.displayAllMonthEvents = true;
        calendarConfig.displayEventEndTimes = true;
        calendarConfig.showTimesOnWeekView = true;

        calendarConfig.i18nStrings.eventsLabel = 'Événements';
        calendarConfig.i18nStrings.timeLabel = 'Temps';
        calendarConfig.i18nStrings.weekNumber = 'Semaine {week}';
    }]);
