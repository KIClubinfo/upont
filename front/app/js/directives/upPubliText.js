var allowedTags = '<a><br><strong><small><ul><ol><li><pre><i>';
angular.module('upont').directive('upPubliText', ['$filter', '$sce', function($filter, $sce) {
    return {
        scope: {
            string: '='
        },
        link: function(scope, element, attrs){
            //Dans un premier temps, on raccourcit par rapport au nombre de lignes
            var string;
            var split = scope.string.split(/\r\n|\r|\n/);

            if (split.length > 5 || scope.string.length > 550) {
                scope.opened = false;
                string = '';
                for (var i = 0; i < 4; i++)
                    string += split[i] + '<br>';
                // En cas de publication vraiment très longue, on raccourcit
                if (string.length > 550)
                    scope.content = $sce.trustAsHtml($filter('stripTags')((string.substring(0, 350) + '... ').replace(/<br>\.{3}$/, '<br>'), allowedTags));
                else
                    scope.content = $sce.trustAsHtml($filter('stripTags')(string, allowedTags));
            } else {
                scope.opened = true;
                scope.content = $sce.trustAsHtml($filter('stripTags')(scope.string, allowedTags));
            }
        },
        controller: ["$scope", function($scope) {
            $scope.open = function() {
                $scope.opened = true;
                $scope.content = $sce.trustAsHtml($filter('stripTags')($scope.string, allowedTags));
            };
        }],
        template: '<span class="up-ticket-texte"><span ng-bind-html="content"></span><span ng-if="!opened" class="up-link" ng-click="open()">Afficher la suite</span></span>',
    };
}]);
