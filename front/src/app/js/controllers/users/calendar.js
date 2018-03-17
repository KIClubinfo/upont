import moment from 'moment';
import {API_PREFIX} from 'upont/js/config/constants';

/* @ngInject */
class Calendar_Ctrl {
    constructor($http, $scope, calendar, calendarConfig) {
        $scope.toScope = (calendar) => {
            $scope.calendarEvents = calendar.events;
            $scope.calendarView = calendar.view;
            $scope.calendarDay = calendar.day;
        };
        $scope.toScope(calendar);

        $scope.setView = (view) => {
            Calendar_Ctrl.getCalendar($http, calendarConfig, view, $scope.calendarDay).then(
                (calendar) => $scope.toScope(calendar)
            );
        };
    }

    static getCalendar($http, calendarConfig, view, day) {
        if (day === null) {
            day = moment().toDate();
        }

        const from = moment(day).startOf(view);
        const to = moment(day).endOf(view);

        return $http.get(API_PREFIX + 'own/calendar', {
            params: {
                from: from.toISOString(),
                to: to.toISOString(),
            }
        }).then(
            (response) => {
                return {
                    view,
                    day,
                    events: Calendar_Ctrl.createCalendarEvents(response.data, calendarConfig)
                };
            }
        );
    }

    static createCalendarEvents(calendar, calendarConfig) {
        const calendarEvents = [];

        for (let i = 0; i < calendar.length; i++) {
            let type;
            switch (calendar[i].entry_method) {
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
            calendarEvents.push({
                color: calendarConfig.colorTypes[type],
                startsAt: moment(calendar[i].start_date).toDate(),
                endsAt: moment(calendar[i].end_date).toDate(),
                title: calendar[i].author_club.name + ' : ' + calendar[i].name,
                editable: false,
                deletable: false,
                draggable: false,
                resizable: false,
                incrementsBadgeTotal: true
            });
        }

        // FIXME
        // for (let i = 0; i < courseItems.length; i++) {
        //     const group = courseItems[i].group;
        //     calendarEvents.push({
        //         color: calendarConfig.colorTypes.info,
        //         startsAt: new Date(courseItems[i].start_date * 1000),
        //         endsAt: new Date(courseItems[i].end_date * 1000),
        //         title: '[' + courseItems[i].location + '] ' + courseItems[i].course.name + (
        //             group !== 0
        //                 ? ' (Gr ' + group + ')'
        //                 : ''),
        //         editable: false,
        //         deletable: false,
        //         draggable: false,
        //         resizable: false,
        //         incrementsBadgeTotal: true
        //     });
        // }
    }
}

export default Calendar_Ctrl;
