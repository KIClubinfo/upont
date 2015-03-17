angular.module('upont').directive('upRow', ['$window', function($window) {
    return {
        link: {
            post: function(scope, element, args){
                if(args.hasOwnProperty('left'))
                    element.addClass('left');
                if(args.hasOwnProperty('wrap'))
                    element.addClass('wrap');
                if(args.hasOwnProperty('center'))
                    element.addClass('center');
                if(args.hasOwnProperty('right'))
                    element.addClass('right');
                if(args.hasOwnProperty('padded'))
                    element.addClass('padded');
                if(args.hasOwnProperty('spaceAround'))
                    element.addClass('space-around');
                if(args.hasOwnProperty('spaceBetween'))
                    element.addClass('space-between');
                element.addClass('up-row');
            }
        }
    };
}]);
