angular.module('upont').directive('upPubliText', ['$window', function($window) {
    return {
        scope: {
            string: '='
        },
        controller: ["$scope", function($scope) {
            if ($scope.string.length > 550) {
                $scope.opened = false;
                $scope.content = $scope.string.substring(0, 350) + '... ';
            } else {
                $scope.opened = true;
                $scope.content = $scope.string;
            }

            $scope.open = function() {
                $scope.opened = true;
                $scope.content = $scope.string;
            };
        }],
        template: "<div class='up-ticket-texte'><span ng-bind-html='content'>content</span><span ng-if='!opened' ng-click='open()' class='up-ticket-link'>Afficher la suite</span></div>",
    };
}]);