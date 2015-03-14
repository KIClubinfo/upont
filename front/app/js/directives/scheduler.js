angular.module('upont').directive('dhxScheduler', function() {
    return {
        restrict: 'A',
        scope: {

        },
        transclude: true,
        template: '<div class="dhx_cal_navline" ng-transclude></div><div class="dhx_cal_header"></div><div class="dhx_cal_data"></div>',

        link: function($scope, $element, $attrs) {
            //default state of the scheduler
            if (!$scope.scheduler)
                $scope.scheduler = {};
            $scope.scheduler.mode = $scope.scheduler.mode || "month";
            $scope.scheduler.date = $scope.scheduler.date || new Date();
            scheduler.config.readonly = true;

            //watch data collection, reload on changes
            $scope.$watchCollection($attrs.data, function(collection) {
                scheduler.clearAll();
                scheduler.parse(collection, "json");
            });

            //watch mode and date
            $scope.$watch(function() {
                return $scope.scheduler.mode + $scope.scheduler.date.toString();
            }, function(nv, ov) {
                var mode = scheduler.getState();
                if (nv.date != mode.date || nv.mode != mode.mode)
                    scheduler.setCurrentView($scope.scheduler.date, $scope.scheduler.mode);
            }, true);

            // size of scheduler
            $scope.$watch(function() {
                return $element[0].offsetHeight ;
                    // w: $element[0].offsetWidth,;
            }, function() {
                scheduler.setCurrentView();
            });

            //styling for dhtmlx scheduler
            $element.addClass("dhx_cal_container");

            //init scheduler
            scheduler.init($element[0], $scope.scheduler.date, $scope.scheduler.mode);
        }
    };
});
