angular.module('upont').directive('upRow', ['$window', function($window) {
    return {
        link: function(scope, element, args){
            element.children().each(function(){

                angular.element(this).wrap('<div></div>');
            });

            if(args.hasOwnProperty('padded'))
                element.addClass('padded');
            if(args.hasOwnProperty('left'))
                element.addClass('left');

            element.addClass('up-row');
        }
    };
}]);