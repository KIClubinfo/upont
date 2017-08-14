angular.module('upont').directive('upRibbon', function() {
    return {
        transclude: true,
        template:
            '<div class="Ribbon">' +
                '<div class="Ribbon--content" ng-transclude></div>' +
            '</div>',
    };
});
