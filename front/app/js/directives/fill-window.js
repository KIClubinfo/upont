angular.module('upont').directive('fillWindow', ['$window', function($window) {
    return {
        link: function($scope, $element, $attrs) {
            // Initialisation
            updateSize();

            // On vérifie si le footer change de taille
            $scope.$watch(function() {
                return $('.up-topbar').outerHeight();
            }, updateSize);
            $scope.$watch(function(){
                return $window.innerHeight;
            }, updateSize);

            function updateSize(){
                newHeight = $window.innerHeight - $('.up-topbar').outerHeight();
                $element.css('min-height', newHeight.toString() + 'px');
            }
        }
    };
}]);
