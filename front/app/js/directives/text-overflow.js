angular.module('upont').directive('upOverflow', ['$sce', function($sce) {
    return {
        scope: {
            string: '='
        },
        link: function(scope, element, attrs){
            //Dans un premier temps, on cherche à savoir si le texte est trop gros
            var split = scope.string.split(/\r\n|\r|\n/);
            scope.overflow = (split.length > 5 || scope.string.length > 550);
            scope.content = $sce.trustAsHtml(scope.string);
        },
        controller: ['$scope', function($scope) {
            $scope.open = function() {
                $scope.overflow = false;
                $scope.content = $sce.trustAsHtml($scope.string);
            };
        }],
        template: '<div ng-class="{\'Overflow\': overflow}">' +
            '<div ng-bind-html="content"></div>' +
            '<div class="Overflow--toggle" ng-if="overflow" col="end stretch"><span class="Link" ng-click="open()">Afficher la suite...</span></div>' +
        '</div>',
    };
}]);
