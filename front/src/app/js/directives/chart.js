import angular from 'angular';
import Highcharts from 'highcharts';

angular.module('upont').directive('upChart', function() {
    return {
        priority: 10001,
        scope: {
            config: '='
        },
        link: {
            post: function(scope, element){
                if (scope.config) {
                    element.attr('id', scope.config.chart.renderTo);
                    scope.chart = new Highcharts.Chart(scope.config);
                }
            }
        },
        template: '<div></div>',
    };
});
