angular.module('upont').directive('upRow', ['$window', function($window) {
    return {
        link: {
            post: function(scope, element, args){
                if(args.hasOwnProperty('left'))
                    element.addClass('left');
                if(args.hasOwnProperty('wrap'))
                    element.addClass('wrap');
                if(args.hasOwnProperty('right'))
                    element.addClass('right');
                element.addClass('up-row');
            }
        }
    };
}]);