// En fonction d'un booléen, applique la classe active ou inactive
module.directive('upActive', function() {
    return {
        restrict: 'A',
        link: function link(scope, element, attrs) {
            var bool;

            function updateClass() {
                if (bool) {
                    element.removeClass('inactive');
                    element.addClass('active');
                } else {
                    element.removeClass('active');
                    element.addClass('inactive');
                }
            }

            scope.$watch(attrs.upActive, function(value) {
                bool = value;
                updateClass();
            });
        }
    };
});
