/* @ngInject */
class Calendar_Ctrl {
    constructor($rootScope, $scope, $filter, events, courseItems, calendarConfig) {
        $scope.events = [];

        // FIXME
        events = events.data;

        for (let i = 0; i < events.length; i++) {
            let type;
            switch (events[i].entry_method) {
            case 'Shotgun':
                type = 'important';
                break;
            case 'Libre':
                type = 'warning';
                break;
            case 'Ferie':
                type = 'success';
                break;
            }
            $scope.events.push({
                color: calendarConfig.colorTypes[type],
                startsAt: new Date(events[i].start_date * 1000),
                endsAt: new Date(events[i].end_date * 1000),
                title: events[i].author_club.name + ' : ' + events[i].name,
                editable: false,
                deletable: false,
                draggable: false,
                resizable: false,
                incrementsBadgeTotal: true
            });
        }

        for (let i = 0; i < courseItems.length; i++) {
            const group = courseItems[i].group;
            $scope.events.push({
                color: calendarConfig.colorTypes.info,
                startsAt: new Date(courseItems[i].start_date * 1000),
                endsAt: new Date(courseItems[i].end_date * 1000),
                title: '[' + courseItems[i].location + '] ' + courseItems[i].course.name + (
                    group !== 0
                        ? ' (Gr ' + group + ')'
                        : ''),
                editable: false,
                deletable: false,
                draggable: false,
                resizable: false,
                incrementsBadgeTotal: true
            });
        }

        $scope.calendarView = 'month';
        $scope.calendarDay = new Date();

        $scope.setView = function(view) {
            $scope.calendarView = view;
        };
    }
}

export default Calendar_Ctrl;
