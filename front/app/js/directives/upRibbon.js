angular.module('upont').directive('upRibbon', function() {
    return {
        restrict: 'E',
        transclude: true,
        template:
            '<div class="ribbon-wrapper">' +
                '<div class="ribbon" ng-transclude></div>' +
            '</div>',
    };
});
