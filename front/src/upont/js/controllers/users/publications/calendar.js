import moment from 'moment';
import Calendar_Ctrl from "../calendar";

/* @ngInject */
class Publications_Calendar_Ctrl extends Calendar_Ctrl {
    constructor($http, $scope, calendar, calendarConfig) {
        const today = moment().toDate();
        const tomorrow = moment().add(1, 'day').toDate();

        $scope.todayActive = true;

        $scope.today = function() {
            $scope.calendarDay = today;
            $scope.todayActive = true;
        };
        $scope.tomorrow = function() {
            $scope.calendarDay = tomorrow;
            $scope.todayActive = false;
        };

        super($http, $scope, calendar, calendarConfig);
    }
}

export default Publications_Calendar_Ctrl;
