angular.module('upont').directive('upPubliText', ['$window', function($window) {
    return {
        scope: {
            string: '='
        },
        controller: ["$scope", function($scope) {
            if ($scope.string.length > 250 || $scope.string.split(/\r\n|\r|\n/).length > 1) {
                $scope.opened = false;
                $scope.content = $scope.string.split(/\r\n|\r|\n/)[0].substring(0, 350) + '... ';
            } else {
                $scope.opened = true;
                $scope.content = nl2br($scope.string);
            }

            $scope.open = function() {
                $scope.opened = true;
                $scope.content = nl2br($scope.string);
            };
        }],
        template: "<div class='up-ticket-texte'><span ng-bind-html='content'>content</span><span ng-if='!opened' ng-click='open()' class='up-ticket-link'>Afficher la suite</span></div>",
    };
}]);
