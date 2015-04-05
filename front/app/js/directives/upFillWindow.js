angular.module('upont').directive('upFillWindow', ['$window', function($window) {
    return {
        link: function($scope, $element, $attrs) {
            //Initialisation
            updateSize();

            //On vérifie si le footer changent de taille
            $scope.$watch(function() {
                return $('footer').outerHeight();
            }, updateSize);

            //On observe si le navigateur change de taille
            angular.element($window).on('resize', function() {
                $scope.$apply(updateSize);
            });

            function updateSize(){
                if ($attrs.upFillWindow == 'calendar') {
                    newHeight = $window.innerHeight;
                    $element.height(newHeight.toString() + 'px');
                } else {
                    newHeight = $window.innerHeight;
                    $element.css('min-height', newHeight.toString() + 'px');
                }
            }

        }
    };
}]);
