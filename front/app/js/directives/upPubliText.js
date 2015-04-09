var allowedTags = '<a><br><strong><small><ul><ol><li><i>';
angular.module('upont').directive('upPubliText', ['$filter', '$sce', function($filter, $sce) {
    return {
        scope: {
            string: '='
        },
        link: function(scope, element, attrs){
            //Dans un premier temps, on cherche à savoir si le texte est trop gros
            var split = scope.string.split(/\r\n|\r|\n/);
            scope.overflow = (split.length > 5 || scope.string.length > 550);
            scope.content = $sce.trustAsHtml($filter('stripTags')(scope.string, allowedTags));
        },
        controller: ["$scope", function($scope) {
            $scope.open = function() {
                $scope.overflow = false;
                $scope.content = $sce.trustAsHtml($filter('stripTags')($scope.string, allowedTags));
            };
        }],
        template: '<div class="up-ticket-texte" ng-class="{\'up-overflow\': overflow}" ng-bind-html="content"></div>' +
        '<br><div ng-if="overflow">... <span class="up-link" ng-click="open()">Afficher la suite</span></div>',
    };
}]);
