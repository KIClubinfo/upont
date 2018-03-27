import moment from 'moment';
import {API_PREFIX} from 'upont/js/config/constants';

/* @ngInject */
class Calendar_Ctrl {
    constructor($http, $scope, calendar, calendarConfig) {
        $scope.loading = false;

        $scope.toScope = (calendar) => {
            $scope.calendarEvents = calendar.events;
            $scope.calendarView = calendar.view;
            $scope.calendarDay = calendar.day;
        };
        $scope.toScope(calendar);

        const updateView = () => {
            if ($scope.loading) {
                return;
            }

            $scope.loading = true;
            Calendar_Ctrl.getCalendar($http, calendarConfig, $scope.calendarView, $scope.calendarDay).then(
                (calendar) => $scope.toScope(calendar),
                () => console.error('Failed to update calendar view'),
            ).finally(() => {
                $scope.loading = false;
            });
        }

        // If any of these scope variables change, update the view by fetching events from the API
        $scope.$watchGroup(
            ['calendarView', 'calendarDay'],
            () => updateView(),
        );
    }

    static getCalendar($http, calendarConfig, view, day) {
        if (day == null) {
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
            },
            () => console.error('Failed to retrieve calendar')
        );
    }

    static createCalendarEvents(calendar, calendarConfig) {
        const calendarEvents = [];

        for (const entry of calendar) {
            let type;
            switch (entry.entry_method) {
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
                startsAt: moment(entry.start_date).toDate(),
                endsAt: moment(entry.end_date).toDate(),
                title: entry.author_club.name + ' : ' + entry.name,
                editable: false,
                deletable: false,
                draggable: false,
                resizable: false,
                incrementsBadgeTotal: true
            });
        }

        // FIXME show courses

        return calendarEvents;
    }
}

export default Calendar_Ctrl;
