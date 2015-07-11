angular.module('upont').directive('upPane', function() {
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
        templateUrl: 'views/public/pane.html',
    };
});
