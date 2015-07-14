angular.module('upont')
    .controller('Calendar_Ctrl', ['$rootScope', '$scope', '$filter', 'events', 'courseitems', function($rootScope, $scope, $filter, events, courseitems) {
        $scope.events = [];
        for (var i = 0; i < events.length; i++) {
            $scope.events.push({
                'id': events[i].slug,
                'start_date': $filter('date')(events[i].start_date * 1000, 'MM/dd/yyyy HH:mm'),
                'end_date': $filter('date')(events[i].end_date * 1000, 'MM/dd/yyyy HH:mm'),
                'text': events[i].author_club.name + ' : ' + events[i].name
            });
        }

        for (i = 0; i < courseitems.length; i++) {
            var group = courseitems[i].group;
            $scope.events.push({
                'id': courseitems[i].slug,
                'start_date': $filter('date')(courseitems[i].start_date * 1000, 'MM/dd/yyyy HH:mm'),
                'end_date': $filter('date')(courseitems[i].end_date * 1000, 'MM/dd/yyyy HH:mm'),
                'text': '[' + courseitems[i].location + '] ' + courseitems[i].course.name + ((group != '0' && group !== undefined) ? ' (Gr ' + group +')' : '')
            });
        }
    }])
    .config(['$stateProvider', function($stateProvider) {
        $stateProvider
            .state('root.users.calendar', {
                url: 'calendrier',
                templateUrl: 'views/users/calendar.html',
                controller: 'Calendar_Ctrl',
                data: {
                    title: 'Calendrier - uPont'
                },
                resolve: {
                    events: ['$resource', function($resource) {
                        return $resource(apiPrefix + 'own/events?all=true').query().$promise;
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
    }]);
