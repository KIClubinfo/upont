angular.module('upont').directive('upChart', function() {
    return {
        priority: 10001,
        scope: {
            config: '='
        },
        link: {
            post: function(scope, element, args){
                element.attr('id', scope.config.chart.renderTo);
                scope.chart = new Highcharts.Chart(scope.config);
            }
        },
        template: '<div></div>',
    };
});
