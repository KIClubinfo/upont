angular.module('upont').directive('upPubliText', ['$window', function($window) {
    return {
        scope: {
            string: '='
        },
        link: function(scope, element, attrs){

            // Dans un premier temps, on raccourcit par rapport au nombre de lignes
            var split = scope.string.split(/\r\n|\r|\n/);
            if (split.length > 5 || scope.string.length > 550) {
                scope.opened = false;
                scope.content = '';
                for (var i = 0; i < 4; i++)
                    scope.content += split[i] + '<br>';

                // En cas de publication vraiment très longue, on raccourcit, et on coupe au max à la 4e ligne
                if (scope.content.length > 550) {
                    scope.content = scope.content.substring(0, 350) + '... ';
                    scope.content = scope.content.replace(/<br>\.{3}/, '<br>');
                }
            } else {
                scope.opened = true;
                scope.content = scope.string;
            }

        },
        controller: ["$scope", function($scope) {

            $scope.open = function() {
                $scope.opened = true;
                $scope.content = $scope.string;
            };
        }],
        template: '<div class="up-ticket-texte"><span ng-bind="content"></span><span ng-if="!opened" ng-click="open()">Afficher la suite</span></div>',
    };
}]);
