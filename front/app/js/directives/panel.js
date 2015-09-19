angular.module('upont').directive('upPanel', function() {
    return {
        transclude: true,
        scope: {
            title: '@'
        },
        controller: ['$scope', function($scope) {
            $scope.isShown = false;

            $scope.toggle = function() {
                $scope.isShown = !$scope.isShown;
            };
        }],
        templateUrl: 'directives/panel.html',
    };
});
