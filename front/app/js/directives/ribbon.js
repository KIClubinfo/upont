angular.module('upont').directive('upRibbon', function() {
    return {
        transclude: true,
        template:
            '<div class="ribbon-wrapper">' +
                '<div class="ribbon" ng-transclude></div>' +
            '</div>',
    };
});
