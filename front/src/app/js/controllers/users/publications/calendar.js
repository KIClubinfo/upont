import moment from 'moment';

/* @ngInject */
class Publications_Calendar_Ctrl {
    constructor($scope, newsItems, events, courseItems) {
        $scope.events = events;
        $scope.newsItems = newsItems;

        $scope.calendarView = 'day';

        const now = moment();
        const tomorrow = now.clone().add(1, 'day');

        $scope.today = function() {
            $scope.calendarDay = now.toDate();
            $scope.todayActive = true;
        };
        $scope.tomorrow = function() {
            $scope.calendarDay = tomorrow.toDate();
            $scope.todayActive = false;
        };
        $scope.today();

        $scope.calendarEvents = [];
        for (let i = 0; i < events.data.length; i++) {
            let type;
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
                    startsAt: moment(events.data[i].start_date).toDate(),
                    endsAt: moment(events.data[i].end_date).toDate(),
                    title: events.data[i].author_club.name + ' : ' + events.data[i].name,
                    editable: false,
                    deletable: false,
                    draggable: false,
                    resizable: false,
                    incrementsBadgeTotal: true
                });
            }
        }
        for (let i = 0; i < courseItems.length; i++) {
            const group = courseItems[i].group;
            $scope.calendarEvents.push({
                type: 'info',
                startsAt: new Date(courseItems[i].start_date * 1000),
                endsAt: new Date(courseItems[i].end_date * 1000),
                title: '[' + courseItems[i].location + '] ' + courseItems[i].course.name + (
                    (group !== '0' && group !== undefined)
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
