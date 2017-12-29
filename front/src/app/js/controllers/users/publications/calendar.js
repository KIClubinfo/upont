import alertify from 'alertifyjs';

import { API_PREFIX } from 'upont/js/config/constants';

class Publications_Calendar_Ctrl {
    constructor($scope, newsItems, events, courseItems) {
        $scope.events = events;
        $scope.newsItems = newsItems;

        $scope.calendarView = 'day';

        $scope.today = function() {
            $scope.calendarDay = new Date();
            $scope.todayActive = true;
        };
        $scope.tomorrow = function() {
            $scope.calendarDay = new Date(new Date().getTime() + 24 * 3600 * 1000);
            $scope.todayActive = false;
        };
        $scope.today();

        $scope.calendarEvents = [];
        for (var i = 0; i < events.data.length; i++) {
            var type;
            switch (events.data[i].entry_method) {
                case 'Shotgun':
                    type = 'important';
                    break;
                case 'Libre':
                    type = 'warning';
                    break;
                case 'Ferie':
                    continue;
            }
            if (events.data[i]) {
                $scope.calendarEvents.push({
                    type: type,
                    startsAt: new Date(events.data[i].start_date * 1000),
                    endsAt: new Date(events.data[i].end_date * 1000),
                    title: events.data[i].author_club.name + ' : ' + events.data[i].name,
                    editable: false,
                    deletable: false,
                    draggable: false,
                    resizable: false,
                    incrementsBadgeTotal: true
                });
            }
        }
        for (i = 0; i < courseItems.length; i++) {
            var group = courseItems[i].group;
            $scope.calendarEvents.push({
                type: 'info',
                startsAt: new Date(courseItems[i].start_date * 1000),
                endsAt: new Date(courseItems[i].end_date * 1000),
                title: '[' + courseItems[i].location + '] ' + courseItems[i].course.name + (
                    (group != '0' && group !== undefined)
                    ? ' (Gr ' + group + ')'
                    : ''),
                editable: false,
                deletable: false,
                draggable: false,
                resizable: false,
                incrementsBadgeTotal: true
            });
        }
    }
}

export default Publications_Calendar_Ctrl;
