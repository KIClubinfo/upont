angular.module('upont').directive('upFillWindow', ['$window', function($window) {
    return {
        link: function($scope, $element, $attrs) {
            //Initialisation
            updateSize();

            //On vérifie si le footer changent de taille
            $scope.$watch(function() {
                return $('.up-topbar').outerHeight();
            }, updateSize);
            $scope.$watch(function(){
                return $window.innerHeight;
            }, updateSize);

            // On observe si le navigateur change de taille
            angular.element($window).on('resize', function() {
                // $scope.$apply(updateSize);
            });

            function updateSize(){
                if ($attrs.upFillWindow == 'calendar') {
                    newHeight = $window.innerHeight - $('.up-topbar').outerHeight();
                    $element.height(newHeight.toString() + 'px');
                } else {
                    newHeight = $window.innerHeight - $('.up-topbar').outerHeight();
                    $element.css('min-height', newHeight.toString() + 'px');
                }
            }

        }
    };
}]);
