import angular from 'angular';

// Remplace les images svg par leur data afin de pouvoir les modifier via CSS
angular.module('upont').directive('svgImage', ['$http', function ($http) {
    return {
        restrict: 'E',
        link: function (scope, element) {
            var imgURL = element.attr('src');
            var request = $http.get(imgURL, {'Content-Type': 'application/xml'});

            scope.manipulateImgNode = function (data, elem) {
                var $svg = angular.element(data)[4];
                var imgClass = elem.attr('class');
                if (typeof(imgClass) !== 'undefined') {
                    var classes = imgClass.split(' ');
                    for (var i = 0; i < classes.length; ++i) {
                        $svg.classList.add(classes[i]);
                    }
                }
                $svg.removeAttribute('xmlns:a');
                return $svg;
            };

            request.then(function(response) {
                element.replaceWith(scope.manipulateImgNode(response.data, element));
            });
        }
    };
}]);
