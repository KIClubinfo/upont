import angular from 'angular';

import template_panel from './panel.html';

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
        templateUrl: template_panel,
    };
});
