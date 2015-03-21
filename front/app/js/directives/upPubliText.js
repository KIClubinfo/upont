angular.module('upont').directive('upPubliText', ['$window', function($window) {
    return {
        scope: {
            string: '='
        },
        controller: ["$scope", function($scope) {
            // Dans un premier temps, on raccourcit par rapport au nombre de lignes
            var split = $scope.string.split(/\r\n|\r|\n/);
            if (split.length > 5 || $scope.string.length > 250) {
                $scope.opened = false;
                $scope.content = '';
                for (var i = 0; i < 4; i++)
                    $scope.content += split[i] + '<br>';

                // En cas de ligne vraiment très longue, on raccourcit aussi
                if ($scope.content.length > 250) {
                    $scope.content = $scope.content.substring(0, 350) + '... ';
                    $scope.content = $scope.content.replace(/<br>\.{3}/, '<br>');
                }
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
